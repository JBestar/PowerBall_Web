<?php 
namespace App\Models;

use CodeIgniter\Model;

class ConfSite_Model extends Model {

    protected $table      = 'conf_site';
    protected $primaryKey = 'conf_id';

    // protected $useAutoIncrement = true;
    protected $returnType = 'object'; //'array', 'object' or Result Object
    // protected $useSoftDeletes = true;

    // save(), insert(), update()
    // protected $allowedFields = ['conf_id', 'conf_memo', 'conf_content'];  

    // protected $useTimestamps = false;
    // protected $createdField  = 'created_at';
    // protected $updatedField  = 'updated_at';
    // protected $deletedField  = 'deleted_at';

    // protected $validationRules    = [];
    // protected $validationMessages = [];
    // protected $skipValidation     = false;

    //find(), insert(), update(), delete()

    public function getSiteName(){

        $objConf = $this->find(CONF_SITENAME);
        
        if(!is_null($objConf)){
            return $objConf->conf_content;
        }
        return "";
    }

    public function getMainNotice(){

        $objConf = $this->find(CONF_NOTICE_MAIN);
        if(!is_null($objConf)){
            return $objConf->conf_content;
        }
        return "";
    }
    
    public function getUrgentNotice(){

        $objConf = $this->find(CONF_NOTICE_URGENT);
        if(!is_null($objConf)){
            return $objConf->conf_content;
        }
        return "";
    }

    public function getBankNotice(){

        $objConf = $this->find(CONF_NOTICE_BANK);
        if(!is_null($objConf)){
            return $objConf->conf_content;
        }
        return "";
    }

    public function getChargeManual(){

        $objConf = $this->find(CONF_CHARGE_MANUAL);
        if(!is_null($objConf)){
            return $objConf->conf_content;
        }
        return "";
    }

    public function getDischarManul(){

        $objConf = $this->find(CONF_DISCHA_MANUAL);
        if(!is_null($objConf)){
            return $objConf->conf_content;
        }
        return "";
    }

    public function IsMaintain(){

        $objConf = $this->find(CONF_MAINTAIN);
        if(!is_null($objConf) && $objConf->conf_active == STATE_ACTIVE) {
            return true;
        }
        return false;
    }
    

    public function msgMaintain(){

        $objConf = $this->find(CONF_MAINTAIN);
        if(!is_null($objConf) && $objConf->conf_active == STATE_ACTIVE) {
            return $objConf->conf_content;
        }
        return "";
    }

    public function IsMultiLogin(){

        $objConf = $this->find(CONF_MULTI_LOGIN);
        if(!is_null($objConf) && $objConf->conf_active == STATE_ACTIVE) {
            return true;
        }
        return false;
    }

    public function IsGamePerFull(){

        $objConf = $this->find(CONF_GAMEPER_FULL);
        if(!is_null($objConf) && $objConf->conf_active == STATE_ACTIVE) {
            return true;
        }
        return false;
    }

    public function IsNoticeDt(){

        $objConf = $this->find(CONF_NOTICE_DT);
        if(!is_null($objConf) && $objConf->conf_active == STATE_ACTIVE) {
            return true;
        }
        return false;
    }

    public function getSiteConf(){
        $confIds = [CONF_SITENAME, CONF_BPG_DENY, CONF_EVOL_DENY, CONF_SLOT_DENY, 
            CONF_CAS_DENY, CONF_EOS5_DENY, CONF_EOS3_DENY, CONF_COIN5_DENY, CONF_COIN3_DENY,
            CONF_PBG_DENY, CONF_AUTOAPPS, CONF_HOLD_DENY, CONF_DHP_DENY, CONF_SPK_DENY];  
        return $this->find($confIds);
    }
    
    public function getNoticeConf(){
        $confIds = [CONF_NOTICE_MAIN, CONF_NOTICE_BANK, CONF_NOTICE_URGENT];  
        return $this->find($confIds);
    }

    public function getSoundConf(){
        $confIds = [CONF_SOUND_1, CONF_SOUND_2, CONF_SOUND_3, CONF_SOUND_4];  
        return $this->find($confIds);
    }

    public function readBetConf(){
        $confIds = [CONF_BET_NL_DENY, CONF_BET_NP_DENY, CONF_BET_N2P_DENY, CONF_BET_PN_DENY, CONF_BET_N2P_4EN, CONF_BET_PAN_TYPE];  
        $arrConf = $this->find($confIds);

        foreach($arrConf as $objConf){
			switch($objConf->conf_id){
				case CONF_BET_NL_DENY:	$_ENV['bet.nl_deny'] = $objConf->conf_active == STATE_ACTIVE?true:false;
					break;
				case CONF_BET_NP_DENY:	$_ENV['bet.np_deny'] = $objConf->conf_active == STATE_ACTIVE?true:false;
					break;
				case CONF_BET_N2P_DENY:	$_ENV['bet.n2p_deny'] = $objConf->conf_active == STATE_ACTIVE?true:false;
					break;
				case CONF_BET_PN_DENY: $_ENV['bet.pn_deny'] = $objConf->conf_active == STATE_ACTIVE?true:false;
					break;
                case CONF_BET_N2P_4EN: $_ENV['bet.n2p_4en'] = $objConf->conf_active == STATE_ACTIVE?true:false;
					break;
                case CONF_BET_PAN_TYPE: $_ENV['bet.pan_type'] = $objConf->conf_active ;
					break;
				default:break;
			}
		}
    }

    public function readMemConf(){
        $confIds = [CONF_TRANS_DENY, CONF_RETURN_DENY, CONF_TRANS_LV1, CONF_RETURN_LV1, 
            CONF_TRANS_LVS, CONF_DEPOSIT_PLAY, CONF_WITHDRAW_PLAY, CONF_DELAY_PLAY];  
        $arrConf = $this->find($confIds);
        $_ENV['mem.trans_deny'] = false;
        $_ENV['mem.return_deny'] = false;
        $_ENV['mem.trans_lv1'] = false;
        $_ENV['mem.return_lv1'] = false;
        $_ENV['mem.depodeny_play'] = false;
        $_ENV['mem.withdeny_play'] = false;
        $_ENV['mem.trans_lvs'] = [];
        $_ENV['mem.delay_play'] = DELAY_PLAYING;

        foreach($arrConf as $objConf){
			switch($objConf->conf_id){
				case CONF_TRANS_DENY:	$_ENV['mem.trans_deny'] = $objConf->conf_active == STATE_ACTIVE?true:false;
					break;
                case CONF_RETURN_DENY:	$_ENV['mem.return_deny'] = $objConf->conf_active == STATE_ACTIVE?true:false;
					break;
                case CONF_TRANS_LV1:	$_ENV['mem.trans_lv1'] = $objConf->conf_active == STATE_ACTIVE?true:false;
					break;
                case CONF_RETURN_LV1:	$_ENV['mem.return_lv1'] = $objConf->conf_active == STATE_ACTIVE?true:false;
					break;
                case CONF_DEPOSIT_PLAY:	$_ENV['mem.depodeny_play'] = $objConf->conf_active == STATE_ACTIVE?true:false;
					break;
                case CONF_WITHDRAW_PLAY:	$_ENV['mem.withdeny_play'] = $objConf->conf_active == STATE_ACTIVE?true:false;
					break;
                case CONF_TRANS_LVS:	
					$lvs = explode(',', $objConf->conf_content);
                    foreach($lvs as $lv){
                        $lv = trim($lv);
                        if(strlen($lv) > 0 && !in_array($lv, $_ENV['mem.trans_lvs']))
                            array_push($_ENV['mem.trans_lvs'], intval($lv));
                    }
                    break;
                case CONF_DELAY_PLAY:	$_ENV['mem.delay_play'] = intval($objConf->conf_active);
					break;
				default:break;
			}
		}

    }

    public function getMainGameImg(){

        $conf['gameimg_pb'] = 'main_quick01_bg.png';
        $conf['gameimg_cs'] = 'main_quick02_bg.png';
        $conf['gameimg_sl'] = 'main_quick03_bg.png';
        $conf['gameimg_kg'] = 'main_quick04_bg.png';

        $objConf = $this->find(CONF_MAIN_GAMEIMG);
        if(!is_null($objConf)) {
            $imgs = explode('#', $objConf->conf_content);
            if(count($imgs) > 3){
                $conf['gameimg_pb'] = trim($imgs[0]);
                $conf['gameimg_cs'] = trim($imgs[1]);
                $conf['gameimg_sl'] = trim($imgs[2]);
                $conf['gameimg_kg'] = trim($imgs[3]);
            }
		}
        return $conf;
    }

    public function getExchangePolicy(){
        $confIds = [CONF_CHARGE_MANUAL, CONF_DISCHA_MANUAL];  
        return $this->find($confIds);
    }

    public function isBetCancelEnable(){
        $objConf = $this->find(CONF_BET_CANCEL);
        if(!is_null($objConf) && $objConf->conf_active == STATE_ACTIVE)
            return false;
        return true;
    }
    
    public function isBetConfirm(){
        $objConf = $this->find(CONF_BET_CONFIRM_DENY);
        if(!is_null($objConf) && $objConf->conf_active == STATE_ACTIVE)
            return false;
        return true;
    }
}