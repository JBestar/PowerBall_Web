<?php

namespace App\Controllers;

use App\Models\PowerballDraw_Model;

class Lottery extends BaseController
{
    /**
     * 추첨 결과 조회/생성 API (5분 주기, CSPRNG 추첨)
     * 기존 필드명(n1~n5, p1, sum, dw_id) + 새 필드(round, number, powerball, numberSum) 호환
     */
    public function getDrawResult()
    {
        $model  = new PowerballDraw_Model();
        $result = $model->getOrGenerate(time());

        $n1 = (int) $result->ball1;
        $n2 = (int) $result->ball2;
        $n3 = (int) $result->ball3;
        $n4 = (int) $result->ball4;
        $n5 = (int) $result->ball5;
        $number = sprintf('%02d%02d%02d%02d%02d', $n1, $n2, $n3, $n4, $n5);

        $json = [
            'round'     => (int) $result->round,
            'ball1'     => $n1,
            'ball2'     => $n2,
            'ball3'     => $n3,
            'ball4'     => $n4,
            'ball5'     => $n5,
            'powerball' => (int) $result->powerball,
            'ball_sum'  => (int) $result->ball_sum,
            'drawn_at'  => $result->drawn_at,
            'n1'        => $n1,
            'n2'        => $n2,
            'n3'        => $n3,
            'n4'        => $n4,
            'n5'        => $n5,
            'p1'        => (int) $result->powerball,
            'sum'       => (int) $result->ball_sum,
            'number'    => $number,
            'numberSum' => (int) $result->ball_sum,
            'dw_id'     => (int) $result->round,
        ];

        return $this->response->setJSON($json);
    }
}
