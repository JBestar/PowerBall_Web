<?php 
namespace App\Models;

use CodeIgniter\Model;

class SessTry_Model extends Model {

    protected $table      = 'sess_try';
    protected $primaryKey = 'log_fid';

    protected $returnType = 'object'; 

    protected $allowedFields = ['log_uid', 'log_pwd', 'log_ip', 'log_result', 'log_time']; 

    public function add($uid, $pwd, $ip, $result ){
        
        $data = [
            'log_uid' => $uid,
            'log_pwd' => $pwd,
            'log_ip' => $ip,
            'log_result' => $result,
            'log_time' => date("Y-m-d H:i:s"),
        ];
        
        return $this->insert($data);
    }

    
    public function getByIp($ip){
        
        return $this->where('log_ip', $ip)
                    ->orderBy('log_fid', 'desc')
                    ->first();

    }

    public function getByUid($uid){
        $where = "log_uid = '".$uid."' ";
        $count = 10;

        return $this->where($where)
                ->orderBy('log_fid', 'DESC')
                ->findAll($count); 
    }
}