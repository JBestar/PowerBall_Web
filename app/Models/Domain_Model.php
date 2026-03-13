<?php 
namespace App\Models;

use CodeIgniter\Model;

class Domain_Model extends Model {

    protected $table      = 'conf_domain';
    protected $primaryKey = 'conf_id';

    protected $returnType = 'object'; 
    protected $allowedFields = ['conf_domain', 'conf_status', 'conf_active', 'conf_prop']; 


    public function add($arrData){
        
        $data = [
            'conf_domain' => trim($arrData['domain']),
            'conf_active' => STATE_ACTIVE,
        ];
        
        return $this->insert($data);
    }
        
	function deleteByFid($fid){
    	return $this->delete($fid);
    }
    
    function search()
    {
        $strSql = "SELECT * FROM ".$this->table;
        $strSql.= " WHERE conf_active = ".STATE_ACTIVE;
        $query = $this -> db -> query($strSql);
        $result = $query -> getResult();
        
        return $result; 

    }


}