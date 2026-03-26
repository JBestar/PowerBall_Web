<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org">
<html xmlns="http://www.w3.org">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <title>파워볼게임 - 채팅</title>
    
    <?php $cdn = 'https://static.powerballgame.co.kr'; $local = rtrim(site_furl(''), '/'); ?>
    <!-- 선배님 CDN 우선, 실패 시 로컬 -->
    <link rel="stylesheet" href="<?php echo $local; ?>/css/chat.css?v=<?php echo time(); ?>" type="text/css" />
    <script type="text/javascript" src="<?php echo $cdn; ?>/js/jquery-1.11.2.min.js" onerror="this.onerror=null;var s=document.createElement('script');s.src='<?php echo $local; ?>/js/jquery-1.11.2.min.js';document.body.appendChild(s);"></script>
    <script type="text/javascript" src="<?php echo $cdn; ?>/js/jquery.number.min.js" onerror="this.onerror=null;var s=document.createElement('script');s.src='<?php echo $local; ?>/js/jquery.number.min.js';document.body.appendChild(s);"></script>
    <script type="text/javascript" src="<?php echo $local; ?>/js/jquery.jplayer.min.2.9.2.js"></script>
    <script type="text/javascript" src="<?php echo $local; ?>/js/chat_simple.js?v=<?php echo time(); ?>"></script>

    <!-- 2. 로그인 정보 및 설정 (PHP 자동 연동) -->
    <script type="text/javascript">
        window.ACTION_BASE_URL = '<?= rtrim(esc(site_furl("")), "/") ?>/';
        window.CHAT_USER_ID = '<?= is_login(true) && isset($objMember) ? esc($objMember->mb_uid) : "" ?>';
        window.CHAT_IS_ADMIN = <?= (isset($objMember) && isset($objMember->mb_level) && (int)$objMember->mb_level >= 100) ? 'true' : 'false' ?>;
    </script>
    <style>
        /* chat.css 우선 사용, 여기선 chat.css에 없는 최소 보완만 적용 */
        #debug-icon-link, #debug-bar, #debug-toolbar-container, .debug-bar, [id^="debug-"] { display:none !important; }
        * { box-sizing: border-box; }
        #roomInputWrap { position: relative; }
        #roomMsg {
            width: calc(100% - 54px);
            min-height: 22px;
            height: 22px;
            max-height: 58px; /* 3줄 */
            resize: none;
            box-sizing: border-box;
            color: #000;
            line-height: 17px;
            padding: 2px 5px;
            border: 1px solid #949494;
            background: #fff;
            overflow-y: auto;
        }
        #roomSendBtn {
            position: absolute;
            top: 2px;
            right: 2px;
            width: 48px;
            height: 22px;
            border: 1px solid #0e609c;
            background: #127ccb;
            color: #fff;
            font-weight: bold;
            cursor: pointer;
        }
        #roomList li { height: auto; min-height: 24px; line-height: 1.5; }
        #roomList li > div { white-space: pre-wrap; word-break: break-word; }
    </style>
</head>

<body>
    <div style="width:100%;margin-bottom:5px;">
		<div style="height:25px;line-height:25px;background-color:#4C4C4C;color:#fff;text-align:center;border:1px solid #151515;" id="chatTimer"><b class="minute"><?= sprintf('%02d', (int) floor(((int)($remain_time ?? 300)) / 60)) ?></b>분 <b class="second"><?= sprintf('%02d', ((int)($remain_time ?? 300)) % 60) ?></b>초 후 <b><span id="timeRound"><?= (int)($time_round ?? 1) ?></span>회차</b> 결과 발표
        </div>
		<div style="position:relative;height:40px;border-left:1px solid #CECECE;border-right:1px solid #CECECE;border-bottom:1px solid #676767;">
		<div style="position:absolute;top:0;left:-1px;"><img src="<?= site_furl('images/graphFlag_p.png') ?>" width="27" height="27">
        </div>
		<div id="powerballPointBetGraph">
		    <div class="oddChart">
		    	<span class="oddBar" style="width: 100px;"></span>
		    	<span class="oddPer" style="right: 100px;">100%</span>
		    </div>
		    <div class="vsChart"></div>
		        <div class="evenChart">
			        <span class="evenBar" style="width: 0px;"></span>
			        <span class="evenPer" style="left: 0px;">0%</span>
		        </div>
	    	</div>
		</div>
	</div>
    <!-- [채팅 본문 영역] 유저들의 대화와 각종 목록이 담기는 곳 -->
    <div class="box-chatting">
        <!-- 1. 상단 정보 및 컨트롤러 영역 -->
        <div class="btn-etc">
            <!-- 접속자 수 표시 (아이콘 + 숫자) -->
            <span class="cnt">
                <div class="sp-bl_pp"></div> <!-- 사람 모양 아이콘 (Sprite 이미지) -->
                <span id="connectUserCnt" rel="0">0</span> 명 
                <span id="loginUserCnt"></span> <!-- 로그인한 유저 수 (필요시 출력) -->
            </span>

            <!-- 2. 상단 탭 메뉴 (채팅, 접속자, 방목록 등 전환 버튼) -->
            <ul class="ul-1">
                <li>
                    <a href="#" onclick="chatManager('popupChat'); return false;" title="새창" class="sp-btn_chat1"></a>
                </li>
                <!-- 2. 글씨 크게 키우기 -->
                <li style="background-color: rgb(243, 243, 243);">
                    <a href="#" onclick="fontZoom(1); return false;" title="글씨크게" class="sp-btn_chat2"></a>
                </li>
                <!-- 3. 글씨 작게 줄이기 -->
                <li style="background-color: rgb(243, 243, 243);">
                    <a href="#" onclick="fontZoom(-1); return false;" title="글씨작게" class="sp-btn_chat3"></a>
                </li>
                <!-- 4. 채팅창 메시지 싹 지우기 (청소) -->
                <li>
                    <a href="#" onclick="chatManager('clearChat'); return false;" title="채팅창 지우기" class="sp-btn_chat4"></a>
                </li>
                <!-- 5. 채팅창 강제 새로고침 -->
                <li>
                    <a href="#" onclick="chatManager('refresh'); return false;" title="새로고침" class="sp-btn_chat5"></a>
                </li>
                <!-- 6. 효과음 끄기/켜기 설정 (id="soundBtn") -->
                <li style="background-color: rgb(243, 243, 243);">
                    <a href="#" onclick="return false;" id="soundBtn" title="소리끄기" class="sp-btn_chat_sound on"></a>
                </li>
            </ul>
        </div>
        <!-- 2. 탭 메뉴 (채팅 / 접속자 / 방목록 선택창) -->
        <div class="table-type-1">
            <ul class="ul-1" id="channelList">                 
                <!-- 1. 연병장 (메인 채널, 현재 활성화 'on') -->
                <li class="channel1">
                    <a href="#channel1" type="channel1">연병장</a>
                </li>
                <!-- 2. 방채팅 (개별 개설된 방 목록) -->
                <li class="roomList">
                    <a href="#roomList" type="roomList" class="on">방채팅</a>
                </li>
                <!-- 3. 접속자 (현재 채널 접속 유저 리스트) -->
                <li class="connectList">
                    <a href="#connectList" type="connectList">접속자</a>
                </li>
                <!-- 4. 채팅규정 (이용 수칙 안내) -->
                <!-- 선배님은 마지막 칸이라 오른쪽 테두리를 없애는(border-right:none) 디테일을 쓰셨네요! -->
                <li class="rule" style="border-right:none;">
                    <a href="#rule" type="rule">채팅규정</a>
                </li>
            </ul>
        </div>
        <!-- 3. [채팅 목록] 실제 메시지가 쏟아지는 메인 광장 -->
        <div id="chatListBox" style="position:relative; display:none;">
            <!-- 1. 뉴스 티커 (상단에 공지가 흘러가는 곳) -->
            <div id="news-ticker-slide" class="ticker" style="height: 15px;">
                <ul>
                    <!-- 자바스크립트가 여기에 한 줄 공지를 채워 넣습니다 -->
                    <li>[공지] 클린한 채팅 문화를 함께 만들어가요!</li>
                </ul>
            </div>
            <!-- 2. 메시지 박스 (실제 대화가 쌓이는 곳) -->
            <!-- 선배님은 높이를 547px로 아주 칼같이 맞추셨네요! -->
            <ul class="list-chatting" id="msgBox" style="height: 547px;">
                <!-- 여기에 <li> 태그 형태로 유저들의 메시지가 실시간으로 추가됩니다 -->
            </ul>
            <!-- 3. 입력창 영역 (내용 입력 및 전송 버튼) -->
            <p class="input-chatting">
                <!-- 텍스트 입력 안내 라벨 -->
                <label for="msg" class="label">내용을 입력해 주세요.</label>           
                <!-- 실제 글을 쓰는 입력칸 (autocomplete="off"로 자동완성 방지) -->
                <input type="text" name="msg" id="msg" class="input-1" autocomplete="off">           
                <!-- 전송 버튼 (sp-btn_enter 클래스로 엔터 아이콘 이미지 사용) -->
                <input type="button" class="input-2 sp-btn_enter" id="sendBtn">
                <!-- [하단 스크롤 버튼] 새 메시지 왔을 때 맨 아래로 내리는 버튼 -->
                <a href="#" class="scrollBottom" id="scrollBottom" style="display: none;"></a>
            </p>
        </div>
        <!-- 4. [접속자 목록] 현재 채팅방에 누가 있는지 보여주는 곳 -->
        <div id="connectListBox" style="display:none;">
            <ul class="list-connect" id="connectList" style="height: 574px;">
                <!-- JS로 접속자 리스트가 채워집니다 -->
            </ul>
        </div>
        <!-- 5. [방 목록] 다른 채널이나 대화방으로 이동하는 리스트 -->
        <div id="roomListBox">
            <!-- 방채팅 전용 입력창 -->
            <p id="roomInputWrap" class="input-chatting" style="display:block;">
                <textarea name="roomMsg" id="roomMsg" autocomplete="off" placeholder="내용을 입력해 주세요."></textarea>
                <input type="button" id="roomSendBtn" value="전송">
            </p>
            <!-- 하단 채팅방 리스트 영역 -->
            <ul class="list-chatting" id="roomList" style="height:520px;">
                <!-- 여기에 실시간으로 방 목록이 추가됩니다 -->
            </ul>
        </div>

        <!-- 6. [규정 안내] 채팅방 이용 규칙 및 제재 안내 -->
        <!-- [규정 안내 영역] 채팅방 이용 수칙 및 제재 기준 -->
        <div id="ruleBox" style="height: 573px; display:none; overflow-y:auto; background:#fff;">
            <!-- 1. 벙어리(채팅 금지) 사유 안내 -->
            <div class="borderBox" style="margin:10px; padding:10px; border:1px solid #ddd;">
                <div class="tit" style="font-weight:bold; color:#d9534f; margin-bottom:10px;">▶벙어리 사유</div>
                <ul style="list-style:none; padding-left:5px; line-height:1.8; font-size:12px; color:#666;">
                    <li>- 한 화면에 두번 이상 같은 글 반복 작성</li>
                    <li>- 상대 비방, 반말 또는 욕설</li>
                    <li>- 비매너 채팅</li>
                    <li>- 회원간 싸움 및 분란 조장</li>
                    <li>- 결과 거짓 중계</li>
                    <li>- 운영진의 판단하에 운영정책에 위배되는 행위</li>
                </ul>
            </div>
            <!-- 2. 접속 차단(영구 제재) 사유 안내 -->
            <div class="borderBox" style="margin:10px; padding:10px; border:1px solid #ddd;">
                <div class="tit" style="font-weight:bold; color:#d9534f; margin-bottom:10px;">▶접속 차단 사유</div>
                <ul style="list-style:none; padding-left:5px; line-height:1.8; font-size:12px; color:#666;">
                    <li>- 개인정보 발언 및 공유</li>
                    <li>- 타 사이트 홍보 및 발언</li>
                    <li>- 불법 프로그램 홍보</li>
                    <li>- 운영진 및 사이트 비방</li>
                    <li>- 지속적인 비매너 채팅</li>
                    <li>- 부모 및 성적 관련 욕설</li>
                </ul>
            </div>
            <!-- 3. 파워볼게임 간편주소 안내 (ruleBox 내부 하단) -->
            <div class="borderBox" style="margin:10px; padding:10px; border:1px solid #ddd;">
                <!-- 제목: 파워볼게임 간편주소 -->
                <div class="tit" style="font-weight:bold; color:#d9534f; margin-bottom:10px;">▶파워볼게임 간편주소</div>
                
                <!-- 주소 리스트 -->
                <ul style="list-style:none; padding-left:5px; line-height:1.8; font-size:12px; color:#666;">
                    <li>- powerballgame.co.kr</li>
                    <li>- 파워볼게임.com</li>
                </ul>
            </div>
        </div>
    </div>


    <!-- [하단] 숨겨진 유틸리티 (유저 메뉴 및 사운드) -->
    <div id="userLayer" style="display:none; position:absolute; background:#fff; border:1px solid #000; padding:10px; z-index:9999;"></div>
    <div id="powerballResultSound" style="width:0; height:0;">
        <audio id="jp_audio_0" preload="metadata" src="https://powerballgame.co.kr"></audio>
    </div>

    <script type="text/javascript">
        (function () {
            var remainSeconds = 0;
            var timerStarted = false;
            var syncInFlight = false;
            var lastServerSyncAt = 0;

            function parseIntSafe(v, d) {
                var n = parseInt(v, 10);
                return isNaN(n) ? d : n;
            }

            function initChatTimerState() {
                var $timer = document.getElementById('chatTimer');
                if (!$timer) return;

                var minEl = $timer.querySelector('.minute');
                var secEl = $timer.querySelector('.second');
                var m = parseIntSafe(minEl ? minEl.textContent : '', 0);
                var s = parseIntSafe(secEl ? secEl.textContent : '', 0);
                remainSeconds = Math.max(0, (m * 60) + s);

                // 값이 비어있거나 비정상이면 기본 5분으로 시작
                if (remainSeconds <= 0) remainSeconds = 300;
            }

            function syncChatTimerFromServer(force) {
                var now = Date.now();
                if (!force && now - lastServerSyncAt < 5000) return;
                if (syncInFlight) return;
                syncInFlight = true;
                $.ajax({
                    type: 'POST',
                    url: (window.ACTION_BASE_URL || '/'),
                    dataType: 'json',
                    data: { view: 'action', action: 'ajaxChatTimer' }
                }).done(function (resp) {
                    if (!resp || resp.state !== 'success') return;
                    var srvRound = parseIntSafe(resp.time_round, 1);
                    var srvRemain = parseIntSafe(resp.remain_seconds, 300);
                    if (srvRound > 0) {
                        var rEl = document.getElementById('timeRound');
                        if (rEl) rEl.textContent = String(srvRound);
                    }
                    remainSeconds = Math.max(0, srvRemain);
                    renderChatTimer();
                    lastServerSyncAt = Date.now();
                }).always(function () {
                    syncInFlight = false;
                });
            }

            function renderChatTimer() {
                var $timer = document.getElementById('chatTimer');
                if (!$timer) return;

                var minEl = $timer.querySelector('.minute');
                var secEl = $timer.querySelector('.second');
                if (!minEl || !secEl) return;

                var m = Math.floor(remainSeconds / 60);
                var s = remainSeconds % 60;
                minEl.textContent = (m < 10 ? '0' : '') + m;
                secEl.textContent = (s < 10 ? '0' : '') + s;
            }

            function tickChatTimer() {
                if (remainSeconds <= 0) {
                    remainSeconds = 300;
                    var roundEl = document.getElementById('timeRound');
                    if (roundEl) {
                        var roundNo = parseIntSafe(roundEl.textContent, 0);
                        if (roundNo > 0) roundEl.textContent = String(roundNo + 1);
                    }
                }

                remainSeconds -= 1;
                if (remainSeconds < 0) remainSeconds = 0;
                renderChatTimer();
                syncChatTimerFromServer(false);
            }

            function startChatTimer() {
                if (timerStarted) return;
                timerStarted = true;
                initChatTimerState();
                renderChatTimer();
                syncChatTimerFromServer(true);
                setInterval(tickChatTimer, 1000);
            }

            function applyChatViewportCompensation() {
                // 내부 스크롤은 숨기고, 부족 높이는 부모 iframe 높이로 보상
                document.documentElement.style.overflowY = 'hidden';
                document.body.style.overflowY = 'hidden';
                document.documentElement.style.overflowX = 'hidden';
                document.body.style.overflowX = 'hidden';

                // 부모(top_banner) 오버레이가 iframe 상단을 침범하면 그만큼만 보상
                var extraTop = 0;
                try {
                    if (window.parent && window.parent !== window && window.parent.document) {
                        var frameEl = window.parent.document.getElementById('chatFrame');
                        var topBannerEl = window.parent.document.querySelector('#guide_banner .top_banner');
                        if (frameEl && topBannerEl && frameEl.getBoundingClientRect && topBannerEl.getBoundingClientRect) {
                            var frameRect = frameEl.getBoundingClientRect();
                            var bannerRect = topBannerEl.getBoundingClientRect();
                            extraTop = Math.max(0, Math.ceil(bannerRect.bottom - frameRect.top));
                        }
                    }
                } catch (e) {}

                document.body.style.paddingTop = extraTop + 'px';

                // 내용이 잘리지 않도록 부모 chatFrame 높이를 현재 내용 기준으로 확장
                try {
                    if (window.parent && window.parent !== window && window.parent.document) {
                        var frameEl2 = window.parent.document.getElementById('chatFrame');
                        if (frameEl2) {
                            var bodyH = Math.max(
                                document.body.scrollHeight || 0,
                                document.documentElement.scrollHeight || 0
                            );
                            var targetH = Math.max(575, Math.ceil(bodyH));
                            frameEl2.style.height = targetH + 'px';
                        }
                    }
                } catch (e2) {}
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', function () {
                    startChatTimer();
                    applyChatViewportCompensation();
                });
            } else {
                startChatTimer();
                applyChatViewportCompensation();
            }
            window.addEventListener('resize', applyChatViewportCompensation);
            setTimeout(applyChatViewportCompensation, 80);
            setTimeout(applyChatViewportCompensation, 250);
        })();
    </script>

</body>
</html>
