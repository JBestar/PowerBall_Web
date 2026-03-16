function ladderResultTimer(divId)
{
	if(remainTime == 0)
	{
		remainTime = 300;
	}

	remainTime--;

	var remain_i = Math.floor(remainTime / 60);
	var remain_s = Math.floor(remainTime % 60);

	$('#'+divId).find('.minute').text(remain_i);
	$('#'+divId).find('.second').text(remain_s);
}

// update result
function updateResult(data)
{
	$('#lotteryBox .play').show();
	$('#ladderReady').hide();

	$('#beforeResult').html($('#lotteryResult').html());
	$('#lotteryResult').empty();

	$('#timeRound').text(parseInt(data.round)+1);
	$('#lastRound').text(data.round);

	var numberList = '';
	var ballArr = new Array();
	for(var i=0;i<5;i++)
	{
		if(i == 0)
		{
			numberList += data.number.substring(0,2);
			ballArr[i] = data.number.substring(0,2);
		}
		else
		{
			numberList += ', '+data.number.substring(i*2,i*2+2);
			ballArr[i] = data.number.substring(i*2,i*2+2);
		}
	}

	numberList += ', <span style="color:#66ffff;" class="b">'+parseInt(data.powerball)+'</span>, <span style="color:#fff;" class="b">'+data.numberSum+'</span>';
	ballArr[5] = parseInt(data.powerball).toString();

	$('#lastResult').html(numberList);

	$('.nextRound').text(parseInt(data.round)+1);
	$('.lastRound').text(data.round);

	setTimeout(function(){

		for(var i=0;i<7;i++)
		{
			if(i == 6)
			{
				setTimeout(function(){
					$('#lotteryBox .play').hide();
					$('#ladderReady').show();
				},12000);
			}
			else
			{
				showNumber(ballArr[i]);
			}
		}
	},2000);
}

var i = 0;
function showNumber(num)
{
	setTimeout(function(){
		
		ballColor = ballColorSel(num);
		$('#lotteryBall').show();
		$('#lotteryBall').html('<span class="ball_'+ballColor+'">'+num+'</span>');

		TweenMax.to(document.getElementById('lotteryBall'),1,{bezier:{type:'cubic',values:[{x:175,y:-5},{x:-50,y:5},{x:-20,y:300},{x:345,y:210}],autoRotate:false},ease:Power1.easeInOut,
			onStart:function(){
				$('#lotteryResult').append('<span id="ballNumber_'+num+'" class="ball_'+ballColor+'"><span class="ballNumber">'+num+'</span></span>');
				$('#ballNumber_'+num).hide();
			},
			onComplete:function(){
				$('#lotteryResult span').show();
				$('#lotteryBall').html('').hide();
			}
		});
	},2000*i);

	i++;

	if(i == 6)
	{
		i = 0;
	}
}

function ballColorSel(num)
{
	switch(num)
	{
		case '01':
		case '1':
		case '05':
		case '5':
		case '09':
		case '9':
		case '13':
		case '17':
		case '21':
		case '25':
			var ballColor = 'red';
			break;
		case '02':
		case '2':
		case '06':
		case '6':
		case '10':
		case '14':
		case '18':
		case '22':
		case '26':
			var ballColor = 'yellow';
			break;
		case '03':
		case '3':
		case '07':
		case '7':
		case '11':
		case '15':
		case '19':
		case '23':
		case '27':
			var ballColor = 'green';
			break;
		case '0':
		case '04':
		case '4':
		case '08':
		case '8':
		case '12':
		case '16':
		case '20':
		case '24':
		case '28':
			var ballColor = 'blue';
			break;
	}
	return ballColor;
}

$(document).ready(function(){
	setInterval(function(){
		ladderResultTimer('ladderTimer');
	},1000);
});

$(document).ready(function(){
	$('#betBox .btn').click(function(){

		var type = $(this).attr('type');
		var val = $(this).attr('val');
		var totalPoint = 0;

		$('#betBox .btn').each(function(){
			if($(this).attr('type') == type)
			{
				$(this).removeClass('on');
			}
		});

		$(this).addClass('on');

		$('#betBox .btn').each(function(){

			if($(this).attr('type') == 'powerballOddEven' || $(this).attr('type') == 'numberOddEven' || $(this).attr('type') == 'powerballUnderOver' || $(this).attr('type') == 'numberUnderOver' || $(this).attr('type') == 'numberPeriod')
			{
				if($(this).hasClass('on'))
				{
					$('#'+$(this).attr('type')).val($(this).attr('val'));
				}
			}
			else if($(this).attr('type') == 'powerballOddEvenP' || $(this).attr('type') == 'numberOddEvenP' || $(this).attr('type') == 'powerballUnderOverP' || $(this).attr('type') == 'numberUnderOverP' || $(this).attr('type') == 'numberPeriodP')
			{
				if($(this).hasClass('on'))
				{
					totalPoint += parseInt($(this).attr('val'));
					$('#point_'+$(this).attr('type')).val(parseInt($(this).attr('val')));
				}
			}
		});

		var btnSelectLength = $('#betBox .btn.on').length;

		$('.totalPoint em').text($.number(parseInt($('#selectPoint').val() * btnSelectLength)));
		$('#point').val(parseInt($('#selectPoint').val()));
	});
});

function powerballBetting()
{
	var fn = document.forms.bettingForm;

	if(!fn.powerballOddEven.value && !fn.numberOddEven.value && !fn.powerballUnderOver.value && !fn.numberUnderOver.value && !fn.numberPeriod.value)
	{
		//alert('다섯개 중 한개 이상을 선택하세요.');
		modalMsg('다섯개 중 한개 이상을 선택하세요.');
		return false;
	}
	else
	{
		$.ajax({
			type:'POST',
			dataType:'json',
			url:'/',
			data:$('#bettingForm').serialize(),
			success:function(data,textStatus){
				if(data.state == 'success')
				{
					//alert(data.msg);
					modalMsg(data.msg);
				}
				else
				{
					if(data.msg == 'CAPTCHA')
					{
						$('#betBox').hide();
						$('#captchaBox').show();
						$('#captchaImg').html('<img src="/captcha.php?type=pointBet&time='+new Date().getTime()+'">');
					}
					else
					{
						//alert(data.msg);
						modalMsg(data.msg);
					}
				}
			},
			error:function (xhr,textStatus,errorThrown){
				//alert('error'+(errorThrown?errorThrown:xhr.status));
			}
		});
	}
}

function resetPowerballBetting()
{
	var fn = document.bettingForm;
	fn.reset();

	$('#powerballOddEven').val('');
	$('#numberOddEven').val('');
	$('#powerballUnderOver').val('');
	$('#numberUnderOver').val('');
	$('#numberPeriod').val('');
	$('#point').val('');

	$('#betBox .btn').each(function(){
		$(this).removeClass('on');
	});
	$('.totalPoint em').text(0);
}

function pointCal()
{
	$('#point').val(parseInt($('#selectPoint').val()));
	$('.totalPoint em').text($.number(parseInt($('#selectPoint').val() * $('#betBox .btn.on').length)));
}

function toggleBetting()
{
	if($('#betBox').css('display') == 'block')
	{
		setCookie('POINTBETLAYER','N');

		$('#betBox').hide();
		$('.bettingBtn a').text('픽 열기');
	}
	else
	{
		setCookie('POINTBETLAYER','Y');

		$('#betBox').show();
		$('.bettingBtn a').text('픽 닫기');
	}
}

function toggleMiniView()
{
	if($('#ladderResultBox').css('display') == 'block')
	{
		setCookie('MINIVIEWLAYER','N');

		$('#ladderResultBox').hide();
		$('.miniViewBtn a').text('미니뷰 열기');

		try{
			parent.miniViewControl('close');
		}
		catch(e){}
	}
	else
	{
		setCookie('MINIVIEWLAYER','Y');

		$('#ladderResultBox').show();
		$('.miniViewBtn a').text('미니뷰 닫기');

		try{
			parent.miniViewControl('open');
		}
		catch(e){}
	}
}

$(document).ready(function(){

	var currentKey = 1;
	var numberArr = [];

	// 키패드 클릭시
	$('.pad li').click(function(){
		
		var isreset = $(this).hasClass('reset');
		var isdelete = $(this).hasClass('delete');

		if (isreset || isdelete)
		{
			return false;
		}
		else
		{
			if (numberArr.length >= 2)
			{
				//alert('로봇 방지 숫자는 2자만 가능합니다.');
				modalMsg('로봇 방지 숫자는 2자만 가능합니다.');
				return false;
			}

			var numberVal = $(this).text();
			$('#captchaNum'+currentKey).val(numberVal);
			
			var captchaNumArr = numberArr.push(numberVal);
			var captchaNum = numberArr.join('');
			$('#captchaNum').val(captchaNum);
			
			currentKey++;
		}
	});
	
	// reset
	$('.pad li.reset').click(function(){
		numberArr = [];
		$('#captchaNum').val('');
		
		for(var i=1;i<=currentKey;i++)
		{
			$('#captchaNum'+i).val('');
		}
		
		currentKey = 1;
	});
	
	// delete
	$('.pad li.delete').click(function(){
		numberArr.pop();
		captchaNum = numberArr.join('');
		$('#captchaNum').val(captchaNum);
		
		if(currentKey > 1)
		{
			$('#captchaNum'+(currentKey-1)).val('');
			currentKey--;
		}
	});
});

function runCaptcha()
{
	var fn = document.forms.captchaForm;

	if(!fn.captchaNum.value || $('#captchaNum').val().length != 2)
	{
		//alert('좌측 숫자를 입력하세요.');
		modalMsg('좌측 숫자를 입력하세요.');
		return false;
	}
	else
	{
		$.ajax({
			type:'POST',
			dataType:'json',
			url:'/',
			data:$('#captchaForm').serialize(),
			success:function(data,textStatus){
				if(data.state == 'success')
				{
					$('.pad li.reset').click();

					$('#betBox').show();
					$('#captchaBox').hide();

					powerballBetting();
				}
				else
				{
					if(data.code == 'MISMATCH')
					{
						//alert(data.msg);
						modalMsg(data.msg);

						$('#captchaImg img').attr('src','/captcha.php?type=pointBet&time='+new Date().getTime());
						$('.pad li.reset').click();
					}
					else
					{
						//alert(data.msg);
						modalMsg(data.msg);
					}
				}
			},
			error:function (xhr,textStatus,errorThrown){
				//alert('error'+(errorThrown?errorThrown:xhr.status));
			}
		});
	}
}

function modalMsg(msg)
{
	if(!$('#dialog').length)
	{
		var dialogLayer = '<div id="dialog" title="안내"><p></p></div>';
		$('body').append(dialogLayer);
	}

	$('#dialog p').html(msg);

	$('#dialog').dialog({
		modal:true,
		buttons:{
			'확인':function(){
				$(this).dialog('close');
			}
		}
	});
}