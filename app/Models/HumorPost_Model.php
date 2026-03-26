<?php

namespace App\Models;

use CodeIgniter\Model;

class HumorPost_Model extends Model
{
    protected $table = 'humor_post';
    protected $primaryKey = 'id';
    protected $returnType = 'object';
    protected $allowedFields = ['mb_uid', 'title', 'content', 'comment_count', 'created_at'];
    protected $useTimestamps = false;

    /**
     * chat_message 스타일로 런타임 테이블 보장
     */
    public function ensureTable(): void
    {
        $db = \Config\Database::connect();
        $table = $db->prefixTable($this->table);
        $sql = "CREATE TABLE IF NOT EXISTS `{$table}` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `mb_uid` VARCHAR(64) NOT NULL,
            `title` VARCHAR(200) NOT NULL,
            `content` TEXT NOT NULL,
            `comment_count` INT UNSIGNED NOT NULL DEFAULT 0,
            `created_at` DATETIME NOT NULL,
            PRIMARY KEY (`id`),
            KEY `idx_created_at` (`created_at`),
            KEY `idx_mb_uid` (`mb_uid`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
        $db->query($sql);
    }

    public function getLatest(int $limit = 12): array
    {
        $this->ensureTable();

        $rows = $this->orderBy('id', 'DESC')
            ->findAll($limit);

        return is_array($rows) ? $rows : [];
    }

    public function findById(int $id)
    {
        $this->ensureTable();
        if ($id <= 0) return null;
        return $this->find($id);
    }

    public function updateById(int $id, array $data): bool
    {
        $this->ensureTable();
        if ($id <= 0) return false;
        $data = array_intersect_key($data, array_flip($this->allowedFields));
        if (!$data) return false;
        return (bool) $this->update($id, $data);
    }

    public function deleteById(int $id): bool
    {
        $this->ensureTable();
        if ($id <= 0) return false;
        return (bool) $this->delete($id);
    }
}

