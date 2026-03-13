<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>실시간 파워볼게임 미니뷰</title>
    <?php $cdn = 'https://static.powerballgame.co.kr'; $local = rtrim(site_furl(''), '/'); ?>
    <link rel="stylesheet" href="<?php echo $cdn; ?>/css/common.css?v=201905194" type="text/css" onerror="this.onerror=null;this.href='<?php echo $local; ?>/css/common.css?v=201905194'"/>
    <link rel="stylesheet" href="<?php echo $cdn; ?>/css/sprites.css?201905194" type="text/css" onerror="this.onerror=null;this.href='<?php echo $local; ?>/css/sprites.css?v=201905194'"/>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css" onerror="this.onerror=null;this.href='<?php echo $local; ?>/css/jquery-ui.css'"/>
</head>
<body>
<div id="ladderResultBox" style="display:none;">
    <div class="title">
        <div>실시간</div>
        <div>파워볼게임</div>
        <div>미니뷰</div>
        <a href="#" onclick="toggleMiniView(); return false;" class="close">닫기</a>
    </div>
    <div class="content">
        <div class="leftBox">
            <div style="position:absolute;top:4px;left:219px;z-index:99;">
                <a href="<?php echo site_furl(''); ?>?referer=miniViewBtn" target="_blank" class="link"></a>
            </div>
            <div id="ladderReady" style="display:block;">
                <div class="box">
                    <div class="time" id="ladderTimer">
                        <em class="minute">5</em>분 <em class="second">0</em>초 후 <span id="timeRound"><?= (int)($time_round ?? 0) ?></span> 회차 결과 발표
                    </div>
                </div>
                <div class="result" style="color:#FFFF00;line-height:20px;font-size:12px;">
                    [<span id="lastRound"><?= esc($last_round ?? '') ?></span>회차] 결과는<br>
                    [<span id="lastResult" style="font-size:15px;"><?= esc($last_result ?? '-') ?></span>] 입니다.
                </div>
            </div>
            <!-- 추첨결과에 따라 공 번호·색상은 JS(updateResult/showNumber/ballColorSel)에서 동적 생성 -->
            <div id="lotteryBox" style="display:none;">
                <div id="lotteryBall" style="display:none;"></div>
                <div class="play" style="display:none;"><img src="https://simg.powerballgame.co.kr/images/lottery_play.gif" height="265" alt="" onerror="this.src='<?php echo rtrim(site_furl(""), "/"); ?>/images/lottery_play.gif';"></div>
            </div>
        </div>
        <div class="rightBox">
            <div class="tit"><span class="lastRound" id="lastRoundTit"><?= esc($last_round ?? '') ?></span> 회차 결과</div>
            <div id="lotteryResultBox">
                <div id="lotteryResult"></div>
            </div>
            <div class="tit2">이전 회차 결과</div>
            <div id="beforeResult"></div>
        </div>
    </div>
</div>

<div id="toggle_in" style="position:relative;width:830px;">
    <div class="bettingBtn">
        <a href="#" onclick="toggleBetting(); return false;" class="betting">픽 열기</a>
    </div>
    <div class="miniViewBtn">
        <a href="#" class="miniView">미니뷰 열기</a>
    </div>
    <div class="betBox" id="betBox" style="display:none;">
        <div class="title">픽</div>
        <form name="bettingForm" id="bettingForm" method="post" action="<?php echo site_furl(''); ?>">
            <input type="hidden" name="view" value="action">
            <input type="hidden" name="action" value="betting">
            <input type="hidden" name="actionType" value="bet">
            <input type="hidden" name="powerballOddEven" id="powerballOddEven" value="">
            <input type="hidden" name="numberOddEven" id="numberOddEven" value="">
            <input type="hidden" name="powerballUnderOver" id="powerballUnderOver" value="">
            <input type="hidden" name="numberUnderOver" id="numberUnderOver" value="">
            <input type="hidden" name="numberPeriod" id="numberPeriod" value="">
            <input type="hidden" name="point" id="point" value="">
            <ul class="betting">
                <li>
                    <span class="titleBox">파워볼</span>
                    <div style="margin-top:5px;"><span class="titleBox">숫자합</span></div>
                </li>
                <li>
                    <span class="btn" type="powerballOddEven" val="odd">홀</span>
                    <span class="btn" type="powerballOddEven" val="even">짝</span>
                    <div style="margin-top:5px;">
                        <span class="btn" type="numberOddEven" val="odd">홀</span>
                        <span class="btn" type="numberOddEven" val="even">짝</span>
                    </div>
                </li>
                <li>
                    <span class="btn" type="powerballUnderOver" val="under">언더</span>
                    <span class="btn" type="powerballUnderOver" val="over">오버</span>
                    <div style="margin-top:5px;">
                        <span class="btn" type="numberUnderOver" val="under">언더</span>
                        <span class="btn" type="numberUnderOver" val="over">오버</span>
                    </div>
                </li>
                <li>
                    <div style="margin-top:5px;">
                        <span class="btn" type="numberPeriod" val="big">대</span>
                        <span class="btn" type="numberPeriod" val="middle">중</span>
                        <span class="btn" type="numberPeriod" val="small">소</span>
                    </div>
                </li>
                <li class="btnBox" style="position:relative;">
                    <div class="left">
                        <span class="pick" onclick="powerballBetting();">픽</span>
                    </div>
                    <div class="right">
                        <div class="reset" onclick="resetPowerballBetting();">리셋</div>
                        <div class="point">
                            <span class="totalPoint">총 포인트 <em>0</em></span>
                            <select id="selectPoint"><option value="100">100</option><option value="500">500</option><option value="1000">1000</option></select>
                            <a href="<?php echo site_furl(''); ?>?view=bettingLog" target="mainFrame" class="log">픽 내역</a>
                        </div>
                    </div>
                </li>
            </ul>
        </form>
    </div>
    <div class="bet_captchaBox" id="captchaBox" style="display:none;">
        <div class="title">로봇 <span>방지</span></div>
        <form name="captchaForm" id="captchaForm" method="post" action="<?php echo site_furl(''); ?>">
            <input type="hidden" name="view" value="action">
            <input type="hidden" name="action" value="betting">
            <input type="hidden" name="actionType" value="captcha">
            <input type="hidden" name="captchaNum" id="captchaNum" value="">
            <div class="captchaImg" id="captchaImg"></div>
            <div class="inputBox">
                <p class="form_notice" style="margin-top:15px;"><strong>좌측 숫자</strong> 입력</p>
                <div class="auth_guide">
                    <input type="text" maxlength="1" name="captchaNum1" id="captchaNum1" readonly>
                    <input type="text" maxlength="1" name="captchaNum2" id="captchaNum2" readonly>
                </div>
                <p class="form_notice">키패드를 마우스로 클릭해 입력해 주세요.</p>
            </div>
            <div class="keypadBox">
                <div class="keypad">
                    <ul class="pad">
                        <li class="num1">1</li><li class="num7">7</li><li class="num3">3</li><li class="num8">8</li><li class="num9">9</li><li class="num4">4</li>
                        <li class="reset">모두 지우기</li>
                        <li class="num2">2</li><li class="num5">5</li><li class="num6">6</li><li class="num0">0</li>
                        <li class="delete">삭제</li>
                    </ul>
                </div>
            </div>
            <div class="btnBox">
                <div class="btn" onclick="runCaptcha();">확인</div>
            </div>
        </form>
    </div>
</div>

<script>
window.POWERBALL_AJAX_URL = '<?php echo site_furl(''); ?>';
window.POWERBALL_BASE_URL = '<?php echo site_furl(''); ?>';
var remainTime = <?= (int)($remain_time ?? 300) ?>;
function setCookie(n,v){ try{ document.cookie = n+'='+v+'; path=/'; } catch(e){} }
function getCookie(n){ var m = document.cookie.match(new RegExp('(^| )'+n+'=([^;]+)')); return m ? m[2] : ''; }
</script>
<script src="<?php echo $cdn; ?>/js/jquery-1.11.2.min.js" onerror="this.onerror=null;var s=document.createElement('script');s.src='<?php echo $local; ?>/js/jquery-1.11.2.min.js';document.body.appendChild(s);"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js" onerror="this.onerror=null;var s=document.createElement('script');s.src='<?php echo $local; ?>/js/jquery-ui.js';document.body.appendChild(s);"></script>
<script>window.jQuery && !$.fn.number && ($.number = function(n){ return n == null ? '0' : String(n); });</script>
<script src="<?php echo $cdn; ?>/js/TweenMax.min.js" onerror="this.onerror=null;var s=document.createElement('script');s.src='<?php echo $local; ?>/js/TweenMax.min.js';document.body.appendChild(s);"></script>
<script src="<?php echo $cdn; ?>/js/powerballMiniView.js?2019012107" onerror="this.onerror=null;var s=document.createElement('script');s.src='<?php echo $local; ?>/js/powerballMiniView.js';document.body.appendChild(s);"></script>
<script>
$(function(){
    window.showLadderResultBox = function(){
        setCookie('MINIVIEWLAYER','Y');
        $('#ladderResultBox').show();
        $('.miniViewBtn a.miniView').text('미니뷰 닫기');
    };
    window.hideLadderResultBox = function(){
        setCookie('MINIVIEWLAYER','N');
        $('#ladderResultBox').hide();
        $('.miniViewBtn a.miniView').text('미니뷰 열기');
    };
    if(getCookie('MINIVIEWLAYER') == 'Y'){
        showLadderResultBox();
    }
    $('.miniViewBtn a.miniView').on('click', function(e){
        e.preventDefault();
        var isVisible = $('#ladderResultBox').is(':visible');
        var fn = (window.parent && window.parent.miniViewControl) || (window.top && window.top.miniViewControl);
        console.log('[미니뷰] 버튼 클릭, ladderResultBox visible:', isVisible, '| parent.miniViewControl:', !!(window.parent && window.parent.miniViewControl), '| top.miniViewControl:', !!(window.top && window.top.miniViewControl), '| 호출할 fn:', !!fn);
        if (isVisible) {
            hideLadderResultBox();
            try { if (fn) { fn('close'); console.log('[미니뷰] miniViewControl("close") 호출 완료'); } else { console.warn('[미니뷰] miniViewControl 없음 - close 스킵'); } } catch(err) { console.error('[미니뷰] miniViewControl(close) 에러:', err); }
        } else {
            showLadderResultBox();
            try { if (fn) { fn('open'); console.log('[미니뷰] miniViewControl("open") 호출 완료'); } else { console.warn('[미니뷰] miniViewControl 없음 - open 스킵'); } } catch(err) { console.error('[미니뷰] miniViewControl(open) 에러:', err); }
        }
        return false;
    });
});
</script>
</body>
</html>
