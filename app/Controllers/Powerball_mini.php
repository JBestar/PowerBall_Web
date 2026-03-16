<?php

namespace App\Controllers;

/**
 * 파워볼 애니메이션 전용 메인 컨트롤러
 * 추첨 데이터 생성 및 애니메이션 뷰 호출에 집중
 */
class Home extends BaseController
{
    public function index()
    {
        // 1. 모델 로드 및 현재 시간 설정 (5분 주기 추첨, powerball_draws 테이블)
        $drawModel = new \App\Models\PowerballDraw_Model();
        $currentTime = time();

        // 2. 현재 회차 결과 가져오기 (없으면 모델에서 자동 생성 및 DB 저장)
        $currentDraw = $drawModel->getOrGenerate($currentTime);
        // 직전 회차 가져오기
        $two = $drawModel->orderBy('round', 'DESC')->findAll(2);
        $prevDraw = isset($two[1]) ? $two[1] : null;

        // 3. 뷰에 전달할 최소한의 데이터 구성
        $viewData = [
            'draw_data' => [
                'last_id'     => $currentDraw->round ?? null,
                'numbers'     => [
                    (int) $currentDraw->ball1,
                    (int) $currentDraw->ball2,
                    (int) $currentDraw->ball3,
                    (int) $currentDraw->ball4,
                    (int) $currentDraw->ball5,
                ],
                'powerball'   => (int) $currentDraw->powerball,
                'server_time' => $currentTime,
                'sum'         => (int) ($currentDraw->ball_sum ?? 0),
                'prev_id'     => $prevDraw ? (int) $prevDraw->round : null,
                'prev_numbers'=> $prevDraw ? [(int)$prevDraw->ball1,(int)$prevDraw->ball2,(int)$prevDraw->ball3,(int)$prevDraw->ball4,(int)$prevDraw->ball5] : [],
                'prev_power'  => $prevDraw ? (int) $prevDraw->powerball : null,
                'prev_sum'    => $prevDraw ? (int) $prevDraw->ball_sum : null,
            ],
            // 에러 방지를 위한 headInfo 기본값 (View에서 참조 시 에러 방지)
            'headInfo' => [
                'lang' => 'ko'
            ]
        ];

        // 4. 메인 애니메이션 뷰 호출
        return view('home/main', $viewData);
    }

    /**
     * 실시간 추첨 결과 API (JS Polling용)
     */
    public function get_live_status()
    {
        $drawModel = new \App\Models\PowerballDraw_Model();
        $current = $drawModel->getLatest();

        return $this->response->setJSON([
            'status' => 'success',
            'data'   => $current,
            'time'   => time(),
        ]);
    }
}
