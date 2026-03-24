<?php

namespace App\Models;

use CodeIgniter\Model;

class ChatMessage_Model extends Model
{
    protected $table = 'chat_message';
    protected $primaryKey = 'id';
    protected $returnType = 'object';
    protected $allowedFields = ['mb_uid', 'message', 'created_at'];
    protected $useTimestamps = false;

    public function ensureTable(): void
    {
        $db = \Config\Database::connect();
        $table = $db->prefixTable($this->table);
        $sql = "CREATE TABLE IF NOT EXISTS `{$table}` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `mb_uid` VARCHAR(64) NOT NULL,
            `message` VARCHAR(500) NOT NULL,
            `created_at` DATETIME NOT NULL,
            PRIMARY KEY (`id`),
            KEY `idx_created_at` (`created_at`),
            KEY `idx_mb_uid` (`mb_uid`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
        $db->query($sql);
    }
}

