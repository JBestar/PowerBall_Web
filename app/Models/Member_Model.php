<?php 
namespace App\Models;

use CodeIgniter\Model;
use Config\Database;
use CodeIgniter\Database\Query;

class Member_Model extends Model {

    
    protected $table      = 'member';
    protected $primaryKey = 'mb_fid';

    protected $returnType = 'object'; 
        
    protected $allowedFields = ['mb_uid', 'mb_pwd', 'mb_level', 'mb_emp_fid', 'mb_nickname', 
        'mb_email', 'mb_phone', 'mb_bank_name', 'mb_bank_own', 'mb_bank_num', 'mb_bank_pwd',
        'mb_time_join', 'mb_time_last', 'mb_time_bet', 'mb_time_call', 'mb_ip_join', 'mb_ip_last', 
        'mb_money', 'mb_point', 'mb_grade', 'mb_color', 'mb_state_active', 'mb_state_bet', 'mb_state_delete', 'mb_state_test', 'mb_state_alarm', 
        'mb_game_pb', 'mb_game_ps', 'mb_game_ks', 'mb_game_bb', 'mb_game_bs', 'mb_game_cs', 'mb_game_sl', 'mb_game_eo', 'mb_game_co', 'mb_game_hl', 
        'mb_game_pb_ratio', 'mb_game_pb2_ratio', 'mb_game_cs_ratio', 'mb_game_sl_ratio', 'mb_game_hl_ratio', 
        'mb_game_pb_percent', 'mb_game_pb2_percent',
        'mb_live_id', 'mb_live_uid', 'mb_live_money', 
        'mb_slot_uid', 'mb_slot_money', 
        'mb_fslot_id', 'mb_fslot_uid', 'mb_fslot_money', 
        'mb_kgon_id', 'mb_kgon_uid', 'mb_kgon_money' ,
        'mb_gslot_uid', 'mb_gslot_money', 
        'mb_hslot_token', 'mb_hslot_money', 
        'mb_hold_uid', 'mb_hold_money', 
        'mb_rave_id', 'mb_rave_uid', 'mb_rave_money' ,
        'mb_treem_uid', 'mb_treem_money' ,
        'mb_sigma_uid', 'mb_sigma_money' ,
    ];
  
    private $getFields = ['mb_fid', 'mb_uid', 'mb_level','mb_emp_fid', 'mb_nickname', 'mb_time_call', 'mb_ip_join', 'mb_ip_last',
        'mb_money', 'mb_point', 'mb_grade', 'mb_state_active', 'mb_state_bet', 'mb_state_delete', 'mb_state_test', 'mb_state_alarm', 'mb_state_view', 
        'mb_game_pb', 'mb_game_ps', 'mb_game_ks', 'mb_game_bb', 'mb_game_bs', 'mb_game_cs', 'mb_game_sl',  'mb_game_eo', 'mb_game_co', 'mb_game_hl', 
        'mb_game_pb_ratio', 'mb_game_pb2_ratio', 'mb_game_cs_ratio', 'mb_game_sl_ratio', 'mb_game_hl_ratio', 
        'mb_live_id', 'mb_live_uid', 'mb_live_money', 
        'mb_slot_uid', 'mb_slot_money', 
        'mb_fslot_id', 'mb_fslot_uid', 'mb_fslot_money',
        'mb_kgon_id', 'mb_kgon_uid', 'mb_kgon_money',
        'mb_gslot_uid', 'mb_gslot_money', 
        'mb_hslot_token', 'mb_hslot_money', 
        'mb_hold_uid', 'mb_hold_money', 
        'mb_rave_id', 'mb_rave_uid', 'mb_rave_money' ,
        'mb_treem_uid', 'mb_treem_money' ,
        'mb_sigma_uid', 'mb_sigma_money' ,
    ];


    public function getByUid($uid, $all = false){
    
        if($all){
            return $this->where('mb_uid', $uid)
                        ->first();    
        }

        return $this->select($this->getFields)
                    ->where('mb_uid', $uid)
                    ->first();
    }

    public function getByFid($fid){
        
        return $this->select($this->getFields)
                    ->find($fid);
    }
    
    public function getByFids($fids){
    
        if(count($fids) < 1)
            return [];
        return $this->select($this->getFields)
                    ->whereIn('mb_fid', $fids)
                    ->findAll();
    }

    public function getByName($name, $fid = 0){
        
        $where = "mb_nickname = '".$name."' ";
        if($fid > 0)
            $where.= "AND mb_fid != '".$fid."' ";

        return $this->select($this->getFields)
                    ->where($where)
                    ->first(); 
        
    }
    
    public function getByBankName($bank, $name){
        
        $where = "mb_bank_name = '".$bank."' ";
        $where.= "AND mb_bank_own = '".$name."' ";
        $where.= "AND mb_state_active != '".PERMIT_DELETE."' ";

        return $this->select($this->getFields)
                    ->where($where)
                    ->first(); 
        
    }
    
    public function getMemberByLevel($level, $bLowLev = false, $mbFid = 0)
    {

        $where =" mb_state_active != '".PERMIT_DELETE."' ";
        if ($bLowLev) {
            $where .= 'AND mb_level <= '.$level;
        } else {
            $where .= 'AND mb_level = '.$level;
        }
        if($mbFid > 0)
            $where .= " AND mb_fid = '".$mbFid."' ";
        
        return $this->where($where)->findAll();
        
    }

    public function updateData($member, $data){
        
        return $this->update($member->mb_fid, $data);
    }

    public function updateLiveInfo($member){
        $data = [
            'mb_live_id' => $member->mb_live_id,
            'mb_live_uid' => $member->mb_live_uid,
            'mb_live_money' => $member->mb_live_money,
        ];
        return $this->update($member->mb_fid, $data);

    }

    public function updateLiveMoney($member){
        $data = [
            'mb_live_money' => $member->mb_live_money,
        ];
        return $this->update($member->mb_fid, $data);
    }

    public function updateSlotInfo($member){
        $data = [
            'mb_slot_uid' => $member->mb_slot_uid,
            'mb_slot_money' => $member->mb_slot_money,
        ];
        return $this->update($member->mb_fid, $data);
    }

    public function updateSlotMoney($member){
        $data = [
            'mb_slot_money' => $member->mb_slot_money,
        ];
        return $this->update($member->mb_fid, $data);
    }

    public function updateFslotInfo($member){
        $data = [
            'mb_fslot_id' => $member->mb_fslot_id,
            'mb_fslot_uid' => $member->mb_fslot_uid,
            'mb_fslot_money' => $member->mb_fslot_money,
        ];
        return $this->update($member->mb_fid, $data);

    }

    public function updateFslotMoney($member){
        $data = [
            'mb_fslot_money' => $member->mb_fslot_money,
        ];
        return $this->update($member->mb_fid, $data);

    }

    
    public function updateKgonInfo($member){
        $data = [
            'mb_kgon_id' => $member->mb_kgon_id,
            'mb_kgon_uid' => $member->mb_kgon_uid,
            'mb_kgon_money' => $member->mb_kgon_money,
        ];
        return $this->update($member->mb_fid, $data);

    }

    public function updateKgonMoney($member){
        $data = [
            'mb_kgon_money' => $member->mb_kgon_money,
        ];
        return $this->update($member->mb_fid, $data);

    }

    public function updateGslotInfo($member){
        $data = [
            'mb_gslot_uid' => $member->mb_gslot_uid,
            'mb_gslot_money' => $member->mb_gslot_money,
        ];
        return $this->update($member->mb_fid, $data);

    }

    public function updateGslotMoney($member){
        $data = [
            'mb_gslot_money' => $member->mb_gslot_money,
        ];
        return $this->update($member->mb_fid, $data);

    }

    public function updateHslotInfo($member){
        $data = [
            'mb_hslot_token' => $member->mb_hslot_token,
            'mb_hslot_money' => $member->mb_hslot_money,
        ];
        return $this->update($member->mb_fid, $data);

    }

    public function updateHslotMoney($member){
        $data = [
            'mb_hslot_money' => $member->mb_hslot_money,
        ];
        return $this->update($member->mb_fid, $data);

    }

    public function updateHoldInfo($member){
        $data = [
            'mb_hold_uid' => $member->mb_hold_uid,
            'mb_hold_money' => $member->mb_hold_money,
        ];
        return $this->update($member->mb_fid, $data);
    }

    public function updateHoldMoney($member){
        $data = [
            'mb_hold_money' => $member->mb_hold_money,
        ];
        return $this->update($member->mb_fid, $data);
    }

    public function getByHoldId($id, $fid = 0){
        
        $where = "mb_hold_uid = '".$id."' ";
        if($fid > 0)
            $where.= "AND mb_fid != '".$fid."' ";

        return $this->select($this->getFields)
                    ->where($where)
                    ->first(); 
    }

    public function updateRaveInfo($member){
        $data = [
            'mb_rave_id' => $member->mb_rave_id,
            'mb_rave_uid' => $member->mb_rave_uid,
            'mb_rave_money' => $member->mb_rave_money,
        ];
        return $this->update($member->mb_fid, $data);
    }

    public function updateRaveMoney($member){
        $data = [
            'mb_rave_money' => $member->mb_rave_money,
        ];
        return $this->update($member->mb_fid, $data);
    }
    
    public function updateTreemInfo($member){
        $data = [
            'mb_treem_uid' => $member->mb_treem_uid,
            'mb_treem_money' => $member->mb_treem_money,
        ];
        return $this->update($member->mb_fid, $data);
    }

    public function updateTreemMoney($member){
        $data = [
            'mb_treem_money' => $member->mb_treem_money,
        ];
        return $this->update($member->mb_fid, $data);
    }

    public function updateSigmaInfo($member){
        $data = [
            'mb_sigma_uid' => $member->mb_sigma_uid,
            'mb_sigma_money' => $member->mb_sigma_money,
        ];
        return $this->update($member->mb_fid, $data);
    }

    public function updateSigmaMoney($member){
        $data = [
            'mb_sigma_money' => $member->mb_sigma_money,
        ];
        return $this->update($member->mb_fid, $data);
    }

    public function updateBetTm($member){
        $data = [
            'mb_time_bet' => date("Y-m-d H:i:s"),
        ];
        return $this->update($member->mb_fid, $data);
    }

    public function updateCallTm($member){
        $data = [
            'mb_time_call' => date("Y-m-d H:i:s"),
        ];
        return $this->update($member->mb_fid, $data);
    }

    public function updateLogin($member){
        $data = [
            'mb_time_last' => date("Y-m-d H:i:s"),
            'mb_ip_last' => $member->mb_ip_last,
        ];
        return $this->update($member->mb_fid, $data);
    }

    public function updateRewards($data) {
        if(count($data) < 1)
            return 1;

        $batch = [];
        foreach($data as $item){
            $insert = [
                'mb_fid' => $item['mb_fid'],
                'mb_point' => $item['mb_point']
            ];
            $batch[] = $insert;
        } 

         return $this->updateBatch($batch, 'mb_fid');    //return updated Count
    }

    public function updateAlarmState($uid, $arrReqData)
    {

        if (!array_key_exists('mb_state_alarm', $arrReqData)) {
            return false;
        }
			
        return $this->builder()->set('mb_state_alarm', $arrReqData['mb_state_alarm'])
        ->where('mb_uid', $uid)
        ->update();
    }

    public function updateAssets(&$objUser, $inMoney , $inPoint = 0, $iChange=-1, $spec=""){

        if(is_null($objUser))
            return false;
        
        $inMoney = floatval($inMoney);
        $inPoint = floatval($inPoint);

        if($inMoney == 0 && $inPoint == 0)
            return true;
        $strSql1 = 'SELECT mb_money FROM '.$this->table;
        $strSql1 .= ' WHERE mb_fid='.$objUser->mb_fid;

        $strSql2 = "UPDATE ".$this->table." SET ";
        if($inMoney != 0){
            $strSql2.= "mb_money = mb_money";
            $strSql2.= $inMoney > 0 ? " + ":" ";
            $strSql2.= $inMoney;   
            $strSql2.= ", mb_change = ".$iChange;
            $strSql2.= ", mb_spec = '".$spec."'";
        }
        
        if($inPoint != 0){
            $strSql2.= $inMoney != 0 ? " , ":" ";

            $strSql2.= "mb_point = mb_point";
            $strSql2.= $inPoint > 0 ? " + ":" ";
            $strSql2.= $inPoint;
        }

        $strSql2.= " WHERE mb_fid=".$objUser->mb_fid;
        if($inMoney < 0){
            $strSql2.= " AND mb_money >= ".abs($inMoney);
        }
        $this->db->transBegin();

        $objMember = $this->db->query($strSql1)->getRow();
        $objUpdate = $this->db->query($strSql2);
        $affectedRows = $objUpdate->connID->affected_rows;

        $bResult = false;
        if ($this->db->transStatus() === false) {
            $this->db->transRollback();
            $bResult = false;
        } else {
            $this->db->transCommit();
            if(!is_null($objMember))
                $objUser->mb_money = $objMember->mb_money;
            if($affectedRows > 0)
                $bResult = true;
        }

        return $bResult;
    }

    public function login($uid, $pwd){

        // $where = " mb_uid = ".$this->db->escape($uid)." AND ".$this->db->escape($pwd)." ";
        // $result = $this->where($where)
        //     ->first();   

        // $query = $this->db->getLastQuery();
        // writeLog($query);

        // return $result;

        // $sql = "SELECT * FROM ".$this->table." WHERE mb_uid = :id: AND mb_pwd = :pwd:";
        // $query = $this->db->query($sql, [
        //     'id'     => $uid,
        //     'pwd'   => $pwd,
        // ]);
        // return $query -> getRow();

        // $db = $this->db;
        // $sql = "SELECT * FROM ".$this->table." WHERE mb_uid = :id: AND mb_pwd = :pwd:";
        // $query = $db->prepare(static function ($db) {
        //         $sql = "SELECT * FROM ".$this->table." WHERE mb_uid = ? AND mb_pwd = ?";
        //         return (new Query($db))->setQuery($sql);
        //     }
        // );
        // return $query -> getRow();

        return $this->where([
            'mb_uid' => $uid,
            'mb_pwd' => $pwd ])
            ->first();    
    }
    
    
    public function getEmpMemberByFid($fid)
    {
        $strTbColum = " ".implode(", ", $this->getFields);
        $strTbRColum = " r.".implode(", r.", $this->getFields);

        $strSQL = 'WITH RECURSIVE tbmember ('.$strTbColum.') AS';
        $strSQL .= ' ( SELECT '.$strTbColum.' FROM '.$this->table." WHERE mb_fid = '".$fid."'";
        $strSQL .= ' UNION ALL SELECT '.$strTbRColum.' FROM '.$this->table.' r ';
        $strSQL .= ' INNER JOIN tbmember ON r.mb_fid = tbmember.mb_emp_fid )';
        $strSQL .= ' SELECT * FROM tbmember ';
        
        $strSQL .=  " ORDER BY mb_level DESC ";
        return $this->db->query($strSQL)->getResult();
    }

    public function isPermitMember($objMember, $iGame = 0){

        if(is_null($objMember))
            return false;

        if($objMember->mb_level > LEVEL_COMPANY)
            return true;

        $arrMember = $this->getEmpMemberByFid($objMember->mb_fid);
        if(count($arrMember) < 1)
            return false;

        if($arrMember[0]->mb_level != LEVEL_COMPANY)
            return false;

        foreach($arrMember as $member){
            if(getMemberState($member, $iGame) === false)
                return false;
        }
        
        return true;
    }


    function getEmployeeRatio($objMember, $nAmount, $iGame,  $iMode=0){
       

        $arrRatio = [];
        
        if(is_null($objMember)) return $arrRatio;
        if($nAmount == 0) return $arrRatio;
        
        $arrMember = $this->getEmpMemberByFid($objMember->mb_fid);
        
        if(is_null($arrMember) || count($arrMember) < 1)
            return $arrRatio;
        
        $cntMem = count($arrMember);
        $member = reset($arrMember);
        if($member->mb_level != LEVEL_COMPANY)
            return $arrRatio;
        
        if($cntMem == 1){
            $ratio = [];
            
            $fRatio = getRatioByGame($member, $iGame, $iMode);
            $ratio['point'] = $fRatio * $nAmount / 100.0; // floor($fRatio * $nAmount / 100.0);
            
            if($ratio['point'] > 0){
                $ratio['mb_fid'] = $member->mb_fid;
                $ratio['mb_uid'] = $member->mb_uid;
                $ratio['mb_point'] = $member->mb_point + $ratio['point'];
    
                $arrRatio[] = $ratio;
            }
    
        } else {
            for($idx = 0 ; $idx < $cntMem-1; $idx ++){

                $fRatio_1 = getRatioByGame($arrMember[$idx], $iGame, $iMode);
                $fRatio_2 = getRatioByGame($arrMember[$idx+1], $iGame, $iMode);
                if($fRatio_1 >= 100) return $arrRatio;
                if($fRatio_1 < $fRatio_2) return $arrRatio;
    
                $ratio = [];
                $fRatio = $fRatio_1 - $fRatio_2;
                $ratio['point'] = $fRatio * $nAmount / 100.0; //floor($fRatio * $nAmount / 100.0);
                
                if($ratio['point'] > 0){
                    $ratio['mb_fid'] = $arrMember[$idx]->mb_fid;
                    $ratio['mb_uid'] = $arrMember[$idx]->mb_uid;
                    $ratio['mb_point'] = $arrMember[$idx]->mb_point + $ratio['point']; 
                    $arrRatio[] = $ratio;
                }
            }

            $ratio = [];
            $member = end($arrMember);
            $fRatio = getRatioByGame($member, $iGame, $iMode);
            $ratio['point'] = $fRatio * $nAmount / 100.0; //floor($fRatio * $nAmount / 100.0);
            
            if($ratio['point'] > 0){
                $ratio['mb_fid'] = $member->mb_fid;
                $ratio['mb_uid'] = $member->mb_uid;
                $ratio['mb_point'] = $member->mb_point + $ratio['point'];
                $arrRatio[] = $ratio;
            }
        }
        
               
        return $arrRatio;
    
    }

    
    function register($arrData){
        
        if(!array_key_exists('member_id', $arrData))
            return RESULT_ERROR;
        $objMember = $this->getByUid($arrData['member_id']);

        if(!is_null($objMember)){
            if($objMember->mb_state_active == PERMIT_DELETE)
            $this->delete($objMember->mb_fid); 
        else
            return RESULT_EXIST_ID;
        }
        
        if(!array_key_exists('nickname', $arrData))
            return RESULT_ERROR;
        $objMember = $this->getByName($arrData['nickname']);

        if(!is_null($objMember))
            return RESULT_EXIST_NAME;

        if(!array_key_exists('proposer', $arrData))
            return RESULT_ERROR;
        else if(strlen($arrData['proposer']) > 0){
            $objEmp = $this->getByUid($arrData['proposer'], true);
            
            if(is_null($objEmp) || $objEmp->mb_state_active != PERMIT_OK)
                return RESULT_EMP_ERROR;
            
            $minLevel = LEVEL_MIN;
            if(array_key_exists('app.level_limit', $_ENV) && intval($_ENV['app.level_limit']) > 0 ){
                $minLevel = LEVEL_MAX - intval($_ENV['app.level_limit']);
            }

            if($objEmp->mb_level <= $minLevel)
                return RESULT_EMP_ERROR;

            if($objEmp->mb_level > LEVEL_COMPANY){
                // $arrData['mb_level'] = LEVEL_COMPANY;
                // $arrData['mb_emp_fid'] = 0;
                return RESULT_EMP_ERROR;
            } else {
                $arrData['mb_level'] = $objEmp->mb_level - 1;
                $arrData['mb_emp_fid'] = $objEmp->mb_fid;
            } 

            $arrData['mb_color'] = $objEmp->mb_color;
            $arrData['mb_state_test'] = $objEmp->mb_state_test;

        } else return RESULT_ERROR;        

        if(strlen($arrData['password']) == 0)
            return RESULT_ERROR;
        $data = [
            'mb_uid' => $arrData['member_id'],
            'mb_pwd' => $arrData['password'],
            'mb_level' => $arrData['mb_level'],
            'mb_emp_fid' => $arrData['mb_emp_fid'],
            'mb_nickname' => $arrData['nickname'],
            'mb_time_join' => date("Y-m-d H:i:s"),
            'mb_ip_join' => $arrData['ip'],
            'mb_phone' => $arrData['contact'],
            'mb_grade' => GRADE_1,
            'mb_color' => $arrData['mb_color'],        
            'mb_state_active' => PERMIT_REQ,
            'mb_state_test' => $arrData['mb_state_test'],
            'mb_bank_name' => $arrData['bank_name'],
            'mb_bank_own' => $arrData['name'],
            'mb_bank_num' => $arrData['account_number'],
            'mb_bank_pwd' => $arrData['refund_password'],
            'mb_game_pb' => STATE_ACTIVE,
            'mb_game_ps' => STATE_ACTIVE,
            'mb_game_ks' => STATE_ACTIVE,
            'mb_game_bb' => STATE_ACTIVE,
            'mb_game_bs' => STATE_ACTIVE,
            'mb_game_cs' => STATE_ACTIVE,
            'mb_game_sl' => STATE_ACTIVE,
            'mb_game_eo' => STATE_ACTIVE,
            'mb_game_co' => STATE_ACTIVE,
            'mb_game_hl' => STATE_ACTIVE,
            'mb_game_pb_percent' => '100',
            'mb_game_pb2_percent' => '100',
        ];
        $objMember = $this->getByBankName($arrData['bank_name'], $arrData['name']);
        if(!is_null($objMember)){
            $data['mb_state_delete'] = STATE_ACTIVE;
        }

        $insertId = $this->insert($data);

        if($insertId >= 0)   //if success, return true
            return RESULT_OK;
        return RESULT_ERROR;
    }

    
}