<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * 추첨 결과 (테이블: draw_results)
 * - 5분마다 1회 추첨, 일반볼 1~28 중 5개(중복 없음), 파워볼 0~9 중 1개
 * - CSPRNG(random_int) + Fisher-Yates 셔플로 추측 불가능하게 구현
 *
 * 컬럼 의미:
 * - drawn_at  : 추첨 시각. 해당 회차가 “몇 시 몇 분”에 진행된 추첨인지 나타내는 공식 시각.
 *               반드시 5분 단위(XX:00, XX:05, XX:10, … XX:55)만 저장.
 * - created_at: 레코드가 DB에 실제로 INSERT된 시각(시스템 기록용). 테이블 기본값 CURRENT_TIMESTAMP.
 */
class PowerballDraw_Model extends Model
{
    protected $table         = 'draw_results';
    protected $primaryKey    = 'id';
    protected $returnType    = 'object';
    protected $useAutoIncrement = true;
    protected $allowedFields = [
        'round', 'ball1', 'ball2', 'ball3', 'ball4', 'ball5',
        'powerball', 'ball_sum', 'drawn_at', 'created_at'
    ];
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';

    /** 추첨 주기(초): 5분 = 300초 */
    public const DRAW_INTERVAL = 300;

    /** 일반볼 범위 1~28, 5개 / 파워볼 범위 0~9, 1개 */
    public const BALL_MIN = 1;
    public const BALL_MAX = 28;
    public const BALL_COUNT = 5;
    public const POWERBALL_MIN = 0;
    public const POWERBALL_MAX = 9;

    /**
     * 최신 추첨 1건 조회
     */
    public function getLatest(): ?object
    {
        return $this->orderBy('round', 'DESC')->first();
    }

    /**
     * 회차로 1건 조회
     */
    public function getByRound(int $round): ?object
    {
        return $this->where('round', $round)->first();
    }

    /**
     * Fisher-Yates 셔플 (CSPRNG 사용으로 추측 불가능)
     * random_int()는 암호학적으로 안전한 난수.
     */
    private function secureShuffle(array $items): array
    {
        $n = count($items);
        for ($i = $n - 1; $i > 0; $i--) {
            $j = random_int(0, $i);
            $t = $items[$i];
            $items[$i] = $items[$j];
            $items[$j] = $t;
        }
        return $items;
    }

    /**
     * 일반볼 5개 + 파워볼 1개 추첨 (고급 알고리즘)
     * - 1~28 풀을 Fisher-Yates로 셔플 후 앞 5개 사용 (중복 불가)
     * - 셔플을 3회 반복해 예측 불가능성 강화 (Triple Mix)
     * - 파워볼은 0~9에서 random_int로 1개
     */
    public function performDraw(): array
    {
        $balls = range(self::BALL_MIN, self::BALL_MAX);

        for ($mix = 0; $mix < 3; $mix++) {
            $balls = $this->secureShuffle($balls);
        }

        $selected = array_slice($balls, 0, self::BALL_COUNT);
        sort($selected, SORT_NUMERIC);

        $powerball = random_int(self::POWERBALL_MIN, self::POWERBALL_MAX);
        $ballSum   = array_sum($selected);

        return [
            'ball1'     => $selected[0],
            'ball2'     => $selected[1],
            'ball3'     => $selected[2],
            'ball4'     => $selected[3],
            'ball5'     => $selected[4],
            'powerball' => $powerball,
            'ball_sum'  => $ballSum,
        ];
    }

    /**
     * 다음 회차 번호 계산 (마지막 round + 1, 없으면 1)
     */
    private function getNextRound(): int
    {
        $row = $this->selectMax('round')->first();
        return $row && isset($row->round) ? (int) $row->round + 1 : 1;
    }

    /**
     * 현재 시각이 속한 5분 슬롯(XX:00, XX:05, XX:10, … XX:55) 기준으로,
     * 해당 슬롯에 추첨이 없으면 새로 추첨, 있으면 기존 결과 반환.
     * @param int|null $currentTime unix timestamp (null이면 time() 사용)
     * @return object 추첨 결과 (round, ball1~5, powerball, ball_sum, drawn_at 등)
     */
    public function getOrGenerate(?int $currentTime = null): object
    {
        $currentTime = $currentTime ?? time();
        $latest = $this->getLatest();

        $currentSlot = (int) floor($currentTime / self::DRAW_INTERVAL);
        $lastDrawSlot = $latest ? (int) floor(strtotime($latest->drawn_at) / self::DRAW_INTERVAL) : -1;
        $shouldDraw  = ! $latest || ($currentSlot > $lastDrawSlot);

        if (! $shouldDraw) {
            return $latest;
        }

        $nextRound = $this->getNextRound();
        $draw      = $this->performDraw();
        // 추첨 시각은 반드시 5분 단위(XX:00, XX:05, XX:10, … XX:55)로 저장
        $drawSlot  = (int) floor($currentTime / self::DRAW_INTERVAL);
        $drawnAt   = date('Y-m-d H:i:s', $drawSlot * self::DRAW_INTERVAL);

        $this->insert([
            'round'     => $nextRound,
            'ball1'     => $draw['ball1'],
            'ball2'     => $draw['ball2'],
            'ball3'     => $draw['ball3'],
            'ball4'     => $draw['ball4'],
            'ball5'     => $draw['ball5'],
            'powerball' => $draw['powerball'],
            'ball_sum'  => $draw['ball_sum'],
            'drawn_at'  => $drawnAt,
        ]);

        $id = $this->getInsertID();
        return (object) array_merge(
            ['id' => $id, 'round' => $nextRound, 'drawn_at' => $drawnAt],
            $draw
        );
    }

    /**
     * API/뷰용 공통 포맷 (기존 draw 형식 호환: number 문자열, round, powerball, numberSum)
     */
    public function formatForApi(object $draw): array
    {
        $n = [
            (string) $draw->ball1,
            (string) $draw->ball2,
            (string) $draw->ball3,
            (string) $draw->ball4,
            (string) $draw->ball5,
        ];
        $number = sprintf(
            '%02d%02d%02d%02d%02d',
            (int) $draw->ball1,
            (int) $draw->ball2,
            (int) $draw->ball3,
            (int) $draw->ball4,
            (int) $draw->ball5
        );
        return [
            'round'      => (int) ($draw->round ?? 0),
            'number'     => $number,
            'powerball'  => (int) ($draw->powerball ?? 0),
            'numberSum'  => (int) ($draw->ball_sum ?? 0),
            'drawn_at'   => $draw->drawn_at ?? null,
        ];
    }

    /**
     * 회차별 분석 데이터 한 행 포맷 (dayLog tmpl_dayLog용)
     * 파워볼 구간 A(0~2) B(3~4) C(5~6) D(7~9), 숫자합 구간 A~F, 대/중/소
     */
    public static function formatForDayLogRow(object $draw, int $rowIndex): array
    {
        $pb   = (int) ($draw->powerball ?? 0);
        $sum  = (int) ($draw->ball_sum ?? 0);
        $time = $draw->drawn_at ? date('H:i', strtotime($draw->drawn_at)) : '';

        if ($pb <= 2) {
            $powerballPeriod = 'A (0~2)';
        } elseif ($pb <= 4) {
            $powerballPeriod = 'B (3~4)';
        } elseif ($pb <= 6) {
            $powerballPeriod = 'C (5~6)';
        } else {
            $powerballPeriod = 'D (7~9)';
        }

        if ($sum <= 35) {
            $numberSumPeriod = 'A (15~35)';
        } elseif ($sum <= 49) {
            $numberSumPeriod = 'B (36~49)';
        } elseif ($sum <= 57) {
            $numberSumPeriod = 'C (50~57)';
        } elseif ($sum <= 65) {
            $numberSumPeriod = 'D (58~65)';
        } elseif ($sum <= 78) {
            $numberSumPeriod = 'E (66~78)';
        } else {
            $numberSumPeriod = 'F (79~130)';
        }

        if ($sum <= 64) {
            $numberPeriod = '소 (15~64)';
        } elseif ($sum <= 80) {
            $numberPeriod = '중 (65~80)';
        } else {
            $numberPeriod = '대 (81~130)';
        }

        $round = (int) ($draw->round ?? 0);
        $blockNumber = (string) $round;
        $blockHashKey = substr(md5($round . ($draw->drawn_at ?? '')), 0, 5);

        $numberStr = sprintf(
            '%02d, %02d, %02d, %02d, %02d',
            (int) $draw->ball1,
            (int) $draw->ball2,
            (int) $draw->ball3,
            (int) $draw->ball4,
            (int) $draw->ball5
        );

        return [
            'trClass'             => ($rowIndex % 2 === 0) ? 'trOdd' : 'trEven',
            'round'               => $round,
            'todayRound'          => $round,
            'time'                => $time,
            'blockNumber'         => $blockNumber,
            'blockHashKey'        => $blockHashKey,
            'powerball'           => $pb,
            'powerballPeriod'     => $powerballPeriod,
            'powerballOddEven'    => ($pb % 2 === 1) ? 'odd' : 'even',
            'powerballUnderOver'  => $pb <= 4 ? 'under' : 'over',
            'number'              => $numberStr,
            'numberSum'           => $sum,
            'numberSumPeriod'     => $numberSumPeriod,
            'numberPeriod'        => $numberPeriod,
            'numberOddEven'       => ($sum % 2 === 1) ? 'odd' : 'even',
            'numberUnderOver'     => $sum <= 72 ? 'under' : 'over',
        ];
    }
}
