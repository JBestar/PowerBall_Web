<?php

namespace App\Controllers;

use App\Models\PowerballDraw_Model;

/**
 * 크론 전용 엔드포인트 (5분 정각 추첨 등).
 * 외부 cron 서비스(cron-job.org 등)에서 URL 호출로 정각 추첨을 실행할 수 있다.
 */
class Cron extends BaseController
{
    /**
     * 5분 정각(XX:00, XX:05, … XX:55) 추첨 실행.
     * GET /cron/draw?key=설정한비밀키
     *
     * .env에 CRON_DRAW_KEY=원하는비밀문자열 설정 후, 매 5분마다 호출하도록 cron 등록.
     * 예: */5 * * * * curl -s "https://도메인/cron/draw?key=원하는비밀문자열"
     */
    public function draw()
    {
        $key = $this->request->getGet('key');
        $expected = env('CRON_DRAW_KEY', '');

        if ($expected === '' || $key !== $expected) {
            return $this->response
                ->setStatusCode(403)
                ->setBody('Forbidden')
                ->setHeader('Content-Type', 'text/plain');
        }

        $now    = time();
        $minute = (int) date('i', $now);

        if (($minute % 5) !== 0) {
            return $this->response->setJSON([
                'ok'    => true,
                'draw'  => false,
                'msg'   => 'Not a 5-min slot',
                'time'  => date('Y-m-d H:i:s', $now),
            ]);
        }

        try {
            $model = new PowerballDraw_Model();
            $draw  = $model->getOrGenerate($now);

            $created = $draw && isset($draw->round);
            return $this->response->setJSON([
                'ok'       => true,
                'draw'     => $created,
                'round'    => $draw->round ?? null,
                'drawn_at' => $draw->drawn_at ?? null,
                'time'     => date('Y-m-d H:i:s', $now),
            ]);
        } catch (\Throwable $e) {
            return $this->response
                ->setStatusCode(500)
                ->setJSON(['ok' => false, 'error' => $e->getMessage()]);
        }
    }
}
