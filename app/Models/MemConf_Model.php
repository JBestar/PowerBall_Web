<?php
namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\I18n\Time;

class MemConf_Model extends Model 
{
    protected $table = 'member_conf';
    protected $returnType = 'object'; 
    protected $allowedFields = [
        'conf_mb_fid', 
        'conf_mb_uid', 
        'conf_num_1', 
        'conf_num_2', 
        'conf_num_3', 
        'conf_num_4', 
        'conf_num_5', 
        'conf_str_1', 
        'conf_str_2', 
        'conf_str_3', 
        'conf_str_4', 
        'conf_str_5', 
        
    ];
    protected $primaryKey = 'conf_fid';
    private $mMemberTable = 'member';

    function gets(){
    	$strSql = "SELECT ".$this->table.".*, member.mb_nickname FROM ".$this->table;
    	$strSql .= " JOIN member ON ".$this->table.".conf_mb_fid = member.mb_fid ";
    	$query = $this -> db -> query($strSql);
        $result = $query -> getResult();
        
        return $result;  
    }

    function getByMember($mbFid){
        return $this->where('conf_mb_fid', $mbFid)->first();
    }

    
    public function register($data)
    {
        try {
            return $this->insert($data);
        } catch (\Exception $e) {  
            return false;
        }
        return false;

    }
    
    public function updateData($conf, $data){
        return $this->update($conf->conf_fid, $data);
    }

}