<?php

namespace App\Controllers;

use App\Models\MemConf_Model;
use App\Models\Domain_Model;
use App\Models\BoardWrite_Model;
use App\Models\BoardPhoto_Model;

class Home extends BaseController
{
    public function index()
    {
        // inner-right iframe용: URI가 frame/dayLog(또는 .../frame/dayLog)이면 반드시 dayLog만 반환 (메인 헤더 중복 방지)
        $path = $this->request->uri->getPath();
        $path = trim($path, '/');
        if (strpos($path, 'frame/dayLog') !== false || preg_match('#^frame/#', $path)) {
            return $this->frameDayLog();
        }

        $this->setLanguage();
        $headInfo = $this->getSiteConf();
        $headInfo['lang'] = $this->session->lang;
        // 1. 도메인 체크 (선배님 로직)
        if($_ENV['app.name'] == APP_ATM && strpos($_SERVER['HTTP_HOST'], "xn--hi5b6a25g9xy.com") === 0){
		    $this->response->redirect(site_furl('/domain'));
        } 
        // 2. 로그인 필수 설정인 경우 로그인 페이지로
        else if(!is_login(true) && array_key_exists('app.login', $_ENV) && $_ENV['app.login'] == 1){
            echo view('home/login', $headInfo);
        }
        // 2-1. mainFrame(핵심 inner-right) 전용: view=dayLog → 별도 파일에서 수행
        else if($this->request->getGet('view') === 'dayLog'){
            $dayLogDate = $this->request->getGet('date');
            if ($dayLogDate && preg_match('/^\d{4}-\d{2}-\d{2}$/', $dayLogDate)) {
                // 유효한 날짜만 사용
            } else {
                $dayLogDate = date('Y-m-d');
            }
            $dayLogData = array_merge($headInfo, [
                'site_title' => ($headInfo['site_name'] ?? '파워볼게임').' : 실시간 파워볼 분석 커뮤니티',
                'date' => $dayLogDate,
            ]);
            echo view('home/dayLog', $dayLogData);
            return;
        }
        // 2-2. iframe(mainFrame)에서 전체 레이아웃이 아닌 내용만 표시 — 헤더 중복 방지
        else if($this->request->getGet('frame') === 'mainFrame'){
            $dayLogDate = $this->request->getGet('date');
            if (!$dayLogDate || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $dayLogDate)) {
                $dayLogDate = date('Y-m-d');
            }
            $dayLogData = array_merge($headInfo, [
                'site_title' => ($headInfo['site_name'] ?? '파워볼게임').' : 실시간 파워볼 분석 커뮤니티',
                'date' => $dayLogDate,
                'frame_mainFrame' => true,
            ]);
            echo view('home/dayLog', $dayLogData);
            return;
        }
        // 2-3. 미니뷰 iframe 전용 (dayLog 내 "미니뷰 열기" 시 로드)
        else if($this->request->getGet('view') === 'powerballMiniView'){
            $headInfo = $this->getSiteConf();
            $miniViewData = array_merge($headInfo, [
                'remain_time' => 300,
                'time_round'  => 0,
                'last_round'  => '',
                'last_result' => '',
            ]);
            echo view('home/powerballMiniView', $miniViewData);
            return;
        }
        // 3. 메인 대시보드 화면 띄우기
        else {
            $objMember = null;
            if(is_login(true)){
                $user_id = $this->session->user_id;
                $objMember = $this->modelMember->getByUid($user_id);
                $this->sess_action();                
            }
            // 공지 목록 (메인 롤링용, 선배님 스타일)
            $boards = [];
            try {
                $boards = $this->modelNotice->getBoards();
                $boards = is_array($boards) ? array_slice($boards, 0, 10) : [];
            } catch (\Throwable $e) {
                $boards = [];
            }
            // 리스트박스용 게시 목록 (유머/분석픽공유/자유) - DB 조회
            $boardWriteModel = new BoardWrite_Model();
            $list_humor = $boardWriteModel->getListForMain('humor', 10);
            $list_pick  = $boardWriteModel->getListForMain('pick', 10);
            $list_free  = $boardWriteModel->getListForMain('free', 10);
            $boardPhotoModel = new BoardPhoto_Model();
            $list_photo = $boardPhotoModel->getListForMain(14);
            $navInfo = getNavInfo($objMember);
            $viewData = array_merge($headInfo, $navInfo, [
                'objMember'  => $objMember,
                'boards'     => $boards,
                'list_humor' => $list_humor,
                'list_pick'  => $list_pick,
                'list_free'  => $list_free,
                'list_photo' => $list_photo,
            ]);
            echo view('home/main', $viewData);
        }
    }

    /**
     * iframe(mainFrame) 전용 — dayLog만 반환 (메인 헤더/레이아웃 없음)
     * 라우트: get('frame/dayLog', 'Home::frameDayLog')
     */
    public function frameDayLog()
    {
        $this->setLanguage();
        $headInfo = $this->getSiteConf();
        $headInfo['lang'] = $this->session->lang ?? 'ko';
        $dayLogDate = $this->request->getGet('date');
        if (!$dayLogDate || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $dayLogDate)) {
            $dayLogDate = date('Y-m-d');
        }
        $dayLogData = array_merge($headInfo, [
            'site_title' => ($headInfo['site_name'] ?? '파워볼게임') . ' : 실시간 파워볼 분석 커뮤니티',
            'date' => $dayLogDate,
            'frame_mainFrame' => true,
        ]);
        $html = view('home/dayLog', $dayLogData);
        $this->response->setBody($html);
        $this->response->setHeader('Content-Type', 'text/html; charset=UTF-8');
        $this->response->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate');
        return $this->response;
    }

	public function domain(){
        if($_ENV['app.name'] == APP_ATM){
            $headInfo = $this->getSiteConf();
        
            $domainModel = new Domain_Model();
            $domains = [];
            $arrDomain = $domainModel->search();
            foreach($arrDomain as $objDomain){
                array_push($domains, $objDomain->conf_domain);
            }
    
            $headInfo['check_domain'] = "에이티엠.com";
            $headInfo['height'] = count($domains) * 60 + 230;
            $headInfo['domains'] = $domains;
            echo view('home/domain', $headInfo);
        } else 
		    $this->response->redirect(site_furl('/'));
        
	}

	public function getaddr(){
		$ip = $this->request->getIPAddress();
		echo "IP ADDRESS is <".$ip.">.";
	}

	public function logout(){

		$sess_id = $this->session->session_id;
		writeLog("[home] logout (".$sess_id.")");
        
		$this->sess_destroy();
		$this->response->redirect(site_furl('/'));
	}

    public function loginip(){
		$this->setLanguage();
        $headInfo = $this->getSiteConf();

        if(!is_login(true)){
            echo view('home/loginip', $headInfo);
        } else {
            $this->response->redirect(site_furl('/'));
        }
	}


    public function mypage()
    {
		$this->setLanguage();
        if($_ENV['app.name'] == APP_ATM && strpos($_SERVER['HTTP_HOST'], "xn--hi5b6a25g9xy.com") === 0){
		    $this->response->redirect(site_furl('/domain'));
        } else if(!is_login(true)){
            print "<script> alert('".lang("common.session_expired")."'); self.close(); </script>";
        } else{
            $this->sess_action();                

            $tab = $this->request->getVar('tab');
            $user_id = $this->session->user_id;
            $objMember = $this->modelMember->getByUid($user_id);
            $navInfo = getNavInfo($objMember);
            $navInfo['lang'] = $this->session->lang;

            if($tab != "my_qna" && $tab != "my_memo" && $tab != "notice" && $tab != "my_point"){
                $tab = "my_info";
            }
            $navInfo['tab'] = $tab;

            $tmNow = time();
            $navInfo['start_at'] = date('Y-m-d', strtotime("-1 month", $tmNow));
            $navInfo['end_at'] = date('Y-m-d', $tmNow);

            $arrSoundConf = $this->modelConfsite->getSoundConf();  
            $navInfo['alarm_name'] = $arrSoundConf[3]->conf_content;
            $navInfo['alarm_volume'] = $arrSoundConf[3]->conf_active;

            echo view('home/mypage', $navInfo);
        }

    }

	public function pt_login(){
		
        $this->response->redirect(site_furl("/pt"));
        // else {
		// 	$port = intval($_SERVER['SERVER_PORT']);
		// 	if($port > 0)
		// 		$port += 1;
		// 	else $port = '81';
		// 	$this->response->redirect('http://'.$_SERVER['SERVER_NAME'].':'.$port);
		// }
		
	}
    public function chat()
    {
        $headInfo = $this->getSiteConf();
        $objMember = null;
        $userToken = '';
        if (is_login(true)) {
            $user_id = $this->session->user_id;
            $objMember = $this->modelMember->getByUid($user_id);
            $userToken = md5($this->session->session_id . ($objMember->mb_uid ?? ''));
        }
        $data = [
            'site_title'   => $headInfo['site_title'] ?? $headInfo['site_name'] ?? '파워볼 채팅',
            'server_time'  => time(),
            'objMember'    => $objMember,
            'userToken'    => $userToken,
        ];
        return view('home/chat', $data);
    }
}
