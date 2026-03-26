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

    public function ensureTable(): void
    {
        $db = \Config\Database::connect();
        $table = $db->prefixTable($this->table);
        $sql = "CREATE TABLE IF NOT EXISTS `{$table}` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `wr_id` INT UNSIGNED NOT NULL DEFAULT 0,
            `title` VARCHAR(200) NOT NULL DEFAULT '',
            `file_path` VARCHAR(255) NOT NULL DEFAULT '',
            `created_at` DATETIME DEFAULT NULL,
            `mb_uid` INT UNSIGNED DEFAULT NULL,
            PRIMARY KEY (`id`),
            KEY `idx_created` (`created_at`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
        $db->query($sql);
    }

    /**
     * 메인 포토 리스트박스용 목록 (최신순)
     */
    public function getListForMain(int $limit = 14): array
    {
        $this->ensureTable();
        $rows = $this->orderBy('id', 'DESC')
            ->findAll($limit);
        return is_array($rows) ? $rows : [];
    }
}
