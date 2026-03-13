<?php 
namespace App\Models;

use CodeIgniter\Model;

class Sess_Model extends Model {

    protected $table      = 'sess';
    protected $primaryKey = 'sess_fid';
    protected $returnType = 'object'; 

    protected $allowedFields = ['sess_id', 'sess_mb_fid', 'sess_mb_uid', 'sess_ip', 'sess_join', 'sess_update', 'sess_action', 'sess_type', 'sess_spec']; 

    public function add($member, $sessId, $type=0){
        $this->deleteBySess($sessId);
        
        $dtNow = date("Y-m-d H:i:s");
        $data = [
            'sess_id' => $sessId,
            'sess_mb_fid' => $member->mb_fid,
            'sess_mb_uid' => $member->mb_uid,
            'sess_ip' => $member->mb_ip_last,
            'sess_join' => $dtNow,
            'sess_update' => $dtNow,
            'sess_action' => $dtNow,
            'sess_type' => SESS_TYPE_SITE,
            'sess_spec' => $type,
        ];
        
        return $this->insert($data);
    }
    
    public function getBySess($sessId){
        
        return $this->where('sess_id', $sessId)
                    ->first();
    }

    
    public function getByUid($uid, $bSite = true){
        $where = "sess_mb_uid = '".$uid."' ";
        if($bSite)
            $where.= " AND sess_type = ".SESS_TYPE_SITE." ";
        else 
            $where.= " AND sess_type <> ".SESS_TYPE_SITE." ";

        return $this->where($where)
                    ->first();

    }

    public function updateLast($sessId){
        
        $data = [
            'sess_update' => date("Y-m-d H:i:s"),
        ];
        
        return $this->set($data)
                    ->where('sess_id', $sessId)
                    ->update();
    }

    public function updateAction($sessId){
        
        $data = [
            'sess_action' => date("Y-m-d H:i:s"),
        ];
        
        return $this->set($data)
                    ->where('sess_id', $sessId)
                    ->update();
    }

    public function deleteBySess($sess){
        
        $data = [
            'sess_id' => $sess,
        ];
        
        return $this->where($data)
                    ->delete();

    }

    public function deleteLast(){
        $tmLast = strtotime("-2 minutes", time());

        return $this->where('sess_update <', date("Y-m-d H:i:s", $tmLast))
                    ->delete();
    }

}