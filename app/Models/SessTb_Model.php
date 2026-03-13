<?php 
namespace App\Models;

use CodeIgniter\Model;

class SessTb_Model extends Model {

    protected $table      = 'sessions';
    protected $primaryKey = 'id';
    protected $returnType = 'object'; 

    protected $allowedFields = []; 

    
    public function getById($id){
        
        return $this->where('id', $id)
                    ->first();
    }

    public function isActiveId($id, $uid){
        $sess = $this->getById($id);
        if(is_null($sess))
            return false;
        // writeLog("Sessions Id=".$id." Uid=".$uid);
        // writeLog("Sessions Id=".$sess->id." Data=".$sess->data);

        $data = explode(";", $sess->data);
        foreach ($data as $info) {
            // writeLog("Sessions info=".$info);

            if (strpos($info,'user_id')!==false && strpos($info, ':"'.$uid)!==false){
                return true;
            }
        }
        return false;
    }
    


}