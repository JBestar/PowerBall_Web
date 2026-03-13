<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * 메인 페이지 리스트박스(유머/분석픽공유/자유)용 게시 목록
 * 테이블: board_write (board_write.sql 로 생성)
 */
class BoardWrite_Model extends Model
{
    protected $table      = 'board_write';
    protected $primaryKey = 'id';
    protected $returnType = 'object';
    protected $allowedFields = ['bo_table', 'wr_id', 'title', 'comment_count', 'created_at'];

    /**
     * 메인 리스트박스용 목록 조회 (좌/우 컬럼에 나눠 쓸 수 있도록 최신순 limit건)
     * @param string $bo_table humor | pick | free
     * @param int $limit
     * @return array
     */
    public function getListForMain(string $bo_table, int $limit = 10): array
    {
        $rows = $this->where('bo_table', $bo_table)
            ->orderBy('id', 'DESC')
            ->findAll($limit);
        return is_array($rows) ? $rows : [];
    }
}
