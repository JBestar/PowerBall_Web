// window.open
function windowOpen(src,target,width,height,scroll)
{
	var wid = (screen.availWidth - width) / 2;
	var hei = (screen.availHeight - height) / 2;
	var opt = 'width='+width+',height='+height+',top='+hei+',left='+wid+',resizable=no,status=no,scrollbars='+scroll;
	window.open(src,target,opt);
}

// trim
String.prototype.trim = function()
{
	return this.replace(/(^\s*)|(\s*$)/g,'');
}

// only number
function onlyNumber()
{
	if((event.keyCode < 48) || (event.keyCode > 57))
	{
		event.returnValue = false;
	}
}

function checkToggle(status)
{
	if(status == true)
	{
		$('.chk').attr('checked',true);
	}
	else
	{
		$('.chk').attr('checked',false);
	}
}

function frameAutoResize(frameId,height)
{
	$('#'+frameId).css('height',height);
}

function maskInit()
{
	var maskHeight = $(document).height() + $(document).scrollTop();
	var maskWidth = $(window).width() + $(document).scrollLeft();
	$('.modalMask').css({'width':maskWidth,'height':maskHeight});
	$('.modalMask').fadeIn();
}

function loginPop(type,rtnUrl)
{
	if(!rtnUrl)
	{
		rtnUrl = encodeURIComponent(window.location.href);
	}

	maskInit();
	layerPop('login',type,rtnUrl);
}

function layerPop(type,idx,num)
{
	$.get('?view=layerPop&type='+type+'&idx='+idx+'&num='+num,function(data){
		$('#layerPop').html(data);
		$('.layerPop').each(function(e){
			//var posT = ($(window).height() / 2) - ($(this).height() / 2) + $(document).scrollTop();
			//var posL = $(this).offset().left - ($(this).width() / 2) - $(document).scrollLeft();

			var posT = $(this).height() / 2;
			var posL = $(this).width() / 2;

			$(this).css({'top':-posT,'left':-posL});
		});
	});
}

// loginCheck
function loginCheck(url)
{
	if(loginYN == 'Y')
	{
		mainFrame.location.href = url;
	}
	else
	{
		loginPop();
	}
}

// set cookie
function setCookie(name,value,expiredays)
{
	var todayDate = new Date();
	todayDate.setDate( todayDate.getDate() + expiredays );
	document.cookie = name + '=' + escape( value ) + '; path=/; expires=' + todayDate.toGMTString() + ';'
}

// get cookie
function getCookie(name)
{
	var nameOfCookie = name + '=';
	var x = 0;
	while ( x <= document.cookie.length ){
		var y = (x+nameOfCookie.length);
		if ( document.cookie.substring( x, y ) == nameOfCookie ) {
			if ( (endOfCookie=document.cookie.indexOf( ';', y )) == -1 )
				endOfCookie = document.cookie.length;
			return unescape( document.cookie.substring( y, endOfCookie ) );
		}
		x = document.cookie.indexOf( ' ', x ) + 1;
		if ( x == 0 )
			break;
	}
	return '';
}

// gift popup
function giftPop(useridKey,type)
{
	windowOpen('/?view=giftBox&useridKey='+useridKey+'&type='+type,'gift',420,400,'no');
}

function fontZoom(num)
{
	var zoomFontSize = parseFloat($('#msgBox').css('font-size')) + num;
	var zoomLineHeight = parseFloat($('#msgBox p').css('line-height')) + num;

	if(zoomFontSize > 18 || zoomFontSize < 11)
	{
		return false;
	}

	setCookie('fontSize',zoomFontSize,365);

	$('#msgBox').css('font-size',zoomFontSize+'px');
	$('#msgBox p').css('line-height',zoomLineHeight+'px');
}

// user layer handler
function userLayerHandler(e)
{
	var target = $(e.target);

	if(target.is('a'))
	{
		if(target.attr('rel').substring(0,5) == 'guest')
		{
			$('#userLayer').hide();
		}
		else
		{
			eval(setUserLayer(target.attr('rel'),target.attr('title'),e));
			$('#userLayer').show();
		}
		e.stopPropagation();
	}
	else if(target.parent().is('a'))
	{
		if(target.parent().attr('rel').substring(0,5) == 'guest')
		{
			$('#userLayer').hide();
		}
		else
		{
			eval(setUserLayer(target.parent().attr('rel'),target.parent().attr('title'),e));
			$('#userLayer').show();
		}
		e.stopPropagation();
	}
}

// user layer set
function setUserLayer(useridKey,nickname,e)
{
	var str = '';

	if(loginYN == 'Y' && this.useridKey != useridKey)
	{
		str += '<ul>';
		str += '<li><a href="#" onclick="giftPop(\''+useridKey+'\',\'bullet\');return false;"><em class="ico"></em><span class="txt">총알 선물하기</span></a></li>';
		str += '<li><a href="#" onclick="chatManager(\'memo\',\''+nickname+'\');return false;"><em class="ico"></em><span class="txt">쪽지 보내기</span></a></li>';
		str += '<li><a href="#" onclick="chatManager(\'talk\',\''+nickname+'\');return false;"><em class="ico"></em><span class="txt">1:1채팅</span></a></li>';

		if(useridKey == 'dc5de9ce5f7cfb22942da69e58156b68' || useridKey == '98fcb9f71155698ab70389d897d7345b')
		{
			str += '<li><a href="#" onclick="chatManager(\'whisper\',\''+nickname+'\');return false;"><em class="ico"></em><span class="txt">귓속말</span></a></li>';
		}

		if(roomIdx != 'lobby' && is_admin)
		{
			str += '<li><a href="#" onclick="chatManager(\'muteOn\',\''+nickname+'\');return false;"><em class="ico"></em><span class="txt">벙어리(5분)</span></a></li>';
			str += '<li><a href="#" onclick="chatManager(\'muteOnTime1\',\''+nickname+'\');return false;"><em class="ico"></em><span class="txt">벙어리(1시간)</span></a></li>';
			str += '<li><a href="#" onclick="chatManager(\'muteOnTime\',\''+nickname+'\');return false;"><em class="ico"></em><span class="txt">벙어리(영구)</span></a></li>';
			str += '<li><a href="#" onclick="chatManager(\'muteOff\',\''+nickname+'\');return false;"><em class="ico"></em><span class="txt">벙어리해제</span></a></li>';
			str += '<li><a href="#" onclick="chatManager(\'banipOn\',\''+nickname+'\');return false;"><em class="ico"></em><span class="txt">아이피차단</span></a></li>';
			str += '<li><a href="#" onclick="chatManager(\'banipOff\',\''+nickname+'\');return false;"><em class="ico"></em><span class="txt">아이피차단해제</span></a></li>';
		}

		str += '<li><a href="#" onclick="chatManager(\'friendList\',\''+nickname+'\');return false;"><em class="ico"></em><span class="txt">친구추가</span></a></li>';
		str += '<li><a href="#" onclick="chatManager(\'blackList\',\''+nickname+'\');return false;"><em class="ico"></em><span class="txt">블랙리스트</span></a></li>';
		str += '</ul>';
	}

	$('#unickname').html(nickname);

	$('#userLayer .ubody').remove();

	if(str)
	{
		$('#userLayer').append('<div class="ubody">'+str+'</div>');
	}

	var bettingStr = '';

	$.ajax({
		type:'POST',
		dataType:'json',
		url:'/',
		data:{
			view:'action',
			action:'bettingResultLayer',
			useridKey:useridKey
		},
		timeout:1000,
		success:function(data,textStatus){
			bettingStr += '<ul>';
			bettingStr += '<li>올킬 - <span class="'+data.totalWinClass+'">'+data.totalWinFix+'</span>연승</li>';
			bettingStr += '<li>파워볼홀짝 - <span class="'+data.powerballOddEvenWinClass+'">'+data.powerballOddEvenWinFix+'</span>연승, <span class="win">'+data.powerballOddEvenWin+'</span>승<span class="lose">'+data.powerballOddEvenLose+'</span>패('+data.powerballOddEvenRate+')</li>';
			bettingStr += '<li>파워볼언더오버 - <span class="'+data.powerballUnderOverWinClass+'">'+data.powerballUnderOverWinFix+'</span>연승, <span class="win">'+data.powerballUnderOverWin+'</span>승<span class="lose">'+data.powerballUnderOverLose+'</span>패('+data.powerballUnderOverRate+')</li>';
			bettingStr += '<li>숫자합홀짝 - <span class="'+data.numberOddEvenWinClass+'">'+data.numberOddEvenWinFix+'</span>연승, <span class="win">'+data.numberOddEvenWin+'</span>승<span class="lose">'+data.numberOddEvenLose+'</span>패('+data.numberOddEvenRate+')</li>';
			bettingStr += '<li>숫자합언더오버 - <span class="'+data.numberUnderOverWinClass+'">'+data.numberUnderOverWinFix+'</span>연승, <span class="win">'+data.numberUnderOverWin+'</span>승<span class="lose">'+data.numberUnderOverLose+'</span>패('+data.numberUnderOverRate+')</li>';
			bettingStr += '<li>숫자합대중소 - <span class="'+data.numberPeriodWinClass+'">'+data.numberPeriodWinFix+'</span>연승, <span class="win">'+data.numberPeriodWin+'</span>승<span class="lose">'+data.numberPeriodLose+'</span>패('+data.numberPeriodRate+')</li>';

			bettingStr += '</ul>';

			$('#userLayer .game').html(bettingStr);

			// layer position
			var layerTop = 0;
			var layerBottom = $('body').height() - e.pageY - $('#userLayer').height();

			if(layerBottom < 0)
			{
				layerTop = e.pageY - $('#userLayer').height();
			}
			else
			{
				layerTop = e.pageY;
			}

			$('#userLayer').css({'left':e.pageX + 10,'top':layerTop});
		},
		error:function (xhr,textStatus,errorThrown){
			//alert('error'+(errorThrown?errorThrown:xhr.status));
		}
	});
}

function browserWsChk()
{
	var result = false;

	// 안드로이드
	if(navigator.userAgent.indexOf('Android') != -1)
	{
		if(navigator.userAgent.indexOf('Chrome') != -1)
		{
			result = true;
		}
		else if(navigator.userAgent.indexOf('Opera/9') != -1)
		{
			result = true;
		}
		else if(navigator.userAgent.indexOf('Firefox/16') != -1)
		{
			result = true;
		}
		else
		{
			result = false;
		}
	}
	else
	{
		result = true;
	}

	return result;
}

function browserIEChk()
{
	var word;
	var version = 'N/A';

	var agent = navigator.userAgent.toLowerCase();
	var name = navigator.appName;

	// IE old version ( IE 10 or Lower )
	if(name == 'Microsoft Internet Explorer')
	{
		word = 'msie ';
	}
	else
	{
		if(agent.search('trident') > -1)	// IE 11
		{
			word = 'trident/.*rv:';
		}
		else if(agent.search('edge/') > -1)	// Microsoft Edge
		{
			word = 'edge/';
		}
	}

	var reg = new RegExp(word + "([0-9]{1,})(\\.{0,}[0-9]{0,1})");

	if(reg.exec(agent) != null) version = RegExp.$1 + RegExp.$2;

	if(version != 'N/A' && version <= 10)
	{
		return true;
	}
	else
	{
		return false;
	}
}

$(document).ready(function(){
	$('#chat-tab > li.config').hover(function(){
		$(this).css('background-color','#fff');
	},function(){
		$(this).css('background-color','#f3f3f3');
	});

	$('.btn-etc .ul-1 > li').hover(function(){
		$(this).css('background-color','#fff');
	},function(){
		$(this).css('background-color','#f3f3f3');
	});
});