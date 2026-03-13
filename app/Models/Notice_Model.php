<?php 
namespace App\Models;

use CodeIgniter\Model;

class Notice_Model extends Model {

    protected $table      = 'board_notice';
    protected $primaryKey = 'notice_fid';

    protected $returnType = 'object'; 
    protected $allowedFields = ['notice_type', 'notice_title', 'notice_content', 'notice_answer', 
        'notice_mb_uid', 'notice_emp_fid', 'notice_read_count', 'notice_time_create', 'notice_state_active', 
        'notice_state_delete', 'notice_client_delete']; 

    public function getBoards(){

        $getFields = ['notice_fid', 'notice_type', 'notice_title', 'notice_content', 'notice_mb_uid', 
            'notice_time_create', 'notice_state_active', 'notice_state_delete', 'mb_grade', 'mb_nickname']; 

        $joinTable = "member";

        $where = "notice_type = '".NOTICE_BOARD."' ";
        $where.= "AND notice_state_active = '".STATE_ACTIVE."' ";
        $where.= "AND notice_state_delete = '".STATE_DISABLE."' ";

        return $this->select($getFields)
                    ->join($joinTable, $joinTable.'.mb_uid = '.$this->table.'.notice_mb_uid')  
                    ->where($where)
                    ->orderBy('notice_fid', 'DESC')
                    ->findAll(); 
        
    }

    public function getBoardById($fid){
        $getFields = ['notice_fid', 'notice_type', 'notice_title', 'notice_content', 'notice_mb_uid', 
        'notice_time_create', 'notice_state_active', 'notice_state_delete', 'mb_grade', 'mb_nickname']; 

        $joinTable = "member";

        $where = "notice_fid = '".$fid."' ";
        $where.= "AND notice_type = '".NOTICE_BOARD."' ";
        $where.= "AND notice_state_active = '".STATE_ACTIVE."' ";
        $where.= "AND notice_state_delete = '".STATE_DISABLE."' ";

        return $this->select($getFields)
                    ->join($joinTable, $joinTable.'.mb_uid = '.$this->table.'.notice_mb_uid')  
                    ->where($where)
                    ->first();
    }

    
    public function registerNotice($data)
    {
        try {
            return $this->insert($data);
        } catch (\Exception $e) {  
            return false;
        }
        return false;

    }

    public function deleteByClient($reqData){
        
        $where = " notice_mb_uid = ".$this->db->escape($reqData['send_uid'])." ";
        
        if($reqData['notice_id'] > 0)
            $where.= " AND notice_fid = ".$this->db->escape($reqData['notice_id'])." ";
        else {
            if(array_key_exists('notice_type', $reqData)){
                $where.= " AND notice_type = ".$this->db->escape($reqData['notice_type'])." ";
            }
            else $where.= " AND (notice_type = '".NOTICE_MSG_ALL."' OR notice_type = '".NOTICE_MSG."') ";
        }
            
        return $this->set('notice_client_delete', STATE_ACTIVE)
                    ->where($where)
                    ->update();
    }

    
    public function readMsg($reqData){
        $where = " notice_mb_uid = ".$this->db->escape($reqData['send_uid'])." ";
        if($reqData['notice_id'] > 0)
            $where.= " AND notice_fid = ".$this->db->escape($reqData['notice_id'])." ";
        $where.= " AND (notice_type = '".NOTICE_MSG_ALL."' OR notice_type = '".NOTICE_MSG."') ";

        return $this->set('notice_read_count', STATE_ACTIVE)
                    ->where($where)
                    ->update();

    }

    
    public function unreadMsg($mb_uid){
        $where = " notice_mb_uid = '".$mb_uid."' ";
        $where.= " AND (notice_type = '".NOTICE_MSG_ALL."' OR notice_type = '".NOTICE_MSG."') ";
        $where.= " AND notice_client_delete = '".STATE_DISABLE."' ";
        $where.= " AND notice_state_active = '".STATE_ACTIVE."' ";
        $where.= " AND notice_read_count = '0' ";
        
        $data = $this->where($where)
                     ->findAll(); 
        return count($data);

    }

    public function readCus($reqData){
        
        $where = " notice_fid = ".$this->db->escape($reqData['notice_id'])." ";
        $where .= " AND notice_mb_uid = ".$this->db->escape($reqData['send_uid'])." ";
        $where.= " AND notice_type = '".NOTICE_CUSTOMER."' ";

        return $this->set('notice_state_active', STATE_VERIFY)
                    ->where($where)
                    ->update();

    }
    
    public function unreadCus($mb_uid){
        $where = " notice_mb_uid = '".$mb_uid."' ";
        $where.= " AND notice_type = '".NOTICE_CUSTOMER."' ";
        $where.= " AND notice_client_delete = '".STATE_DISABLE."' ";
        $where.= " AND notice_state_active = '".STATE_ACTIVE."' ";
        
        $data = $this->where($where)
                     ->findAll(); 
        return count($data);

    }

    
    public function searchBodCount($reqData){

        // $getFields = ['notice_fid', 'notice_type', 'notice_title', 'notice_content', 'notice_answer', 'notice_mb_uid', 
        //     'notice_time_create', 'notice_state_active', 'notice_client_delete' ]; 

        $where = "notice_type = '".NOTICE_BOARD."' ";
        $where.= "AND notice_state_active = '".STATE_ACTIVE."' ";
        $where.= "AND notice_state_delete = '".STATE_DISABLE."' ";
        
        $data = $this//->select($getFields)
                    ->where($where)
                    ->findAll(); 
        return count($data);
    }

    public function searchBodList($reqData){
        
        // $getFields = ['notice_fid', 'notice_type', 'notice_title', 'notice_content', 'notice_answer', 'notice_mb_uid', 
        //     'notice_time_create', 'notice_state_active', 'notice_client_delete' ]; 

        $where = "notice_type = '".NOTICE_BOARD."' ";
        $where.= "AND notice_state_active = '".STATE_ACTIVE."' ";
        $where.= "AND notice_state_delete = '".STATE_DISABLE."' ";
        if(array_key_exists('popup', $reqData))
            $where.= "AND notice_read_count = '".$reqData['popup']."' ";

        $page = $reqData['page'];
        $count = $reqData['count'];
        if($page < 1)
            return NULL;
        if($count < 1)
            return NULL;
        return $this //->select($getFields)
                    ->where($where)
                    ->orderBy('notice_fid', 'DESC')
                    ->findAll($count, $count*($page-1)); 
    }

    public function searchCusCount($reqData){

        // $getFields = ['notice_fid', 'notice_type', 'notice_title', 'notice_content', 'notice_answer', 'notice_mb_uid', 
        //     'notice_time_create', 'notice_state_active', 'notice_client_delete', 'mb_grade', 'mb_nickname']; 

        $joinTable = "member";

        $where = "notice_type = '".NOTICE_CUSTOMER."' ";
        $where.= "AND notice_client_delete = '".STATE_DISABLE."' ";
        if(array_key_exists('send_uid', $reqData) ){
            $where.= "AND notice_mb_uid = ".$this->db->escape($reqData['send_uid'])." ";
        }
        $data = $this //->select($getFields)
                    ->join($joinTable, $joinTable.'.mb_uid = '.$this->table.'.notice_mb_uid')  
                    ->where($where)
                    ->findAll(); 
        return count($data);
    }

    public function searchCusList($reqData){
        
        $getFields = ['notice_fid', 'notice_type', 'notice_title', 'notice_content', 'notice_answer', 'notice_mb_uid', 
            'notice_time_create', 'notice_state_active', 'notice_client_delete', 'mb_grade', 'mb_nickname']; 

        $joinTable = "member";

        $where = "notice_type = '".NOTICE_CUSTOMER."' ";
        $where.= "AND notice_client_delete = '".STATE_DISABLE."' ";
        if(array_key_exists('send_uid', $reqData) ){
            $where.= "AND notice_mb_uid = ".$this->db->escape($reqData['send_uid'])." ";
        }
        $page = $reqData['page'];
        $count = $reqData['count'];
        if($page < 1)
            return NULL;
        if($count < 1)
            return NULL;
        return $this->select($getFields)
                    ->join($joinTable, $joinTable.'.mb_uid = '.$this->table.'.notice_mb_uid')  
                    ->where($where)
                    ->orderBy('notice_fid', 'DESC')
                    ->findAll($count, $count*($page-1)); 
    }


    
    public function searchMsgCount($reqData){

        // $getFields = ['notice_fid', 'notice_type', 'notice_title', 'notice_content', 'notice_mb_uid', 
        //     'notice_time_create', 'notice_state_active', 'notice_client_delete', 'mb_grade', 'mb_nickname']; 

        $joinTable = "member";

        $where = " (notice_type = '".NOTICE_MSG_ALL."' OR notice_type = '".NOTICE_MSG."') ";
        $where.= "AND notice_client_delete = '".STATE_DISABLE."' ";
        $where.= "AND notice_state_active = '".STATE_ACTIVE."' ";
        if(array_key_exists('send_uid', $reqData) ){
            $where.= "AND notice_mb_uid = ".$this->db->escape($reqData['send_uid'])." ";
        }
        $data = $this//->select($getFields)
                    ->join($joinTable, $joinTable.'.mb_uid = '.$this->table.'.notice_mb_uid')  
                    ->where($where)
                    ->findAll(); 
        return count($data);
    }

    public function searchMsgList($reqData){
        
        // $getFields = ['notice_fid', 'notice_type', 'notice_title', 'notice_content', 'notice_mb_uid', 'notice_read_count', 
        //     'notice_time_create', 'notice_state_active', 'notice_client_delete', 'mb_grade', 'mb_nickname']; 

        $joinTable = "member";

        $where = " (notice_type = '".NOTICE_MSG_ALL."' OR notice_type = '".NOTICE_MSG."') ";
        $where.= "AND notice_client_delete = '".STATE_DISABLE."' ";
        $where.= "AND notice_state_active = '".STATE_ACTIVE."' ";
        if(array_key_exists('send_uid', $reqData) ){
            $where.= "AND notice_mb_uid = ".$this->db->escape($reqData['send_uid'])." ";
        }
        $page = $reqData['page'];
        $count = $reqData['count'];
        if($page < 1)
            return NULL;
        if($count < 1)
            return NULL;
        return $this//->select($getFields)
                    ->join($joinTable, $joinTable.'.mb_uid = '.$this->table.'.notice_mb_uid')  
                    ->where($where)
                    ->orderBy('notice_fid', 'DESC')
                    ->findAll($count, $count*($page-1)); 
    }


}