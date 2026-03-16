<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\PowerballDraw_Model;

/**
 * 5분 정각(XX:00, XX:05, XX:10, … XX:55)에 추첨을 실행하기 위한 CLI 명령.
 * 크론으로 매 5분마다 실행하면, 해당 시각에 추첨이 수행·저장된다.
 *
 * 사용 예 (Linux/Mac crontab):
 *   */5 * * * * cd /path/to/powerball && php spark draw:run
 *
 * Windows 작업 스케줄러: 5분마다 위 명령 실행하도록 작업 등록.
 */
class DrawRunCommand extends BaseCommand
{
    protected $group       = 'Draw';
    protected $name        = 'draw:run';
    protected $description = '5분 정각 추첨 실행 (cron에서 */5 * * * * 로 호출)';
    protected $usage       = 'draw:run';

    protected $arguments = [];
    protected $options    = [];

    public function run(array $params = [])
    {
        $now    = time();
        $minute = (int) date('i', $now);

        // 5분 단위(XX:00, XX:05, …)가 아닌 시각에 실행된 경우 무시
        if (($minute % 5) !== 0) {
            CLI::write('[' . date('Y-m-d H:i:s', $now) . '] Not a 5-min slot; skip.', 'yellow');
            return 0;
        }

        try {
            $model = new PowerballDraw_Model();
            $draw  = $model->getOrGenerate($now);

            if ($draw && isset($draw->round)) {
                CLI::write(
                    '[' . date('Y-m-d H:i:s', $now) . '] Draw done: round=' . $draw->round
                    . ', drawn_at=' . ($draw->drawn_at ?? '') . '.',
                    'green'
                );
            } else {
                CLI::write('[' . date('Y-m-d H:i:s', $now) . '] No new draw (slot already has result).', 'cyan');
            }
        } catch (\Throwable $e) {
            CLI::error('Draw run failed: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
