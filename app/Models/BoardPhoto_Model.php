<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * 포토 리스트용 모델
 * 실제 이미지 파일은 public/uploads/photos/ 에 저장하고, file_path만 DB에 저장
 */
class BoardPhoto_Model extends Model
{
    protected $table      = 'board_photo';
    protected $primaryKey = 'id';
    protected $returnType = 'object';
    protected $allowedFields = ['wr_id', 'title', 'file_path', 'created_at', 'mb_uid'];

    /**
     * 메인 포토 리스트박스용 목록 (최신순)
     */
    public function getListForMain(int $limit = 14): array
    {
        $rows = $this->orderBy('id', 'DESC')
            ->findAll($limit);
        return is_array($rows) ? $rows : [];
    }
}
