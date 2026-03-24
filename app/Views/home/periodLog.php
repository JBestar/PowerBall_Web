<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?= esc($site_title ?? '기간별 분석') ?></title>
	<meta http-equiv="content-type" content="text/html; charset=utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<?php $local = rtrim(site_furl(''), '/'); ?>
	<link rel="stylesheet" href="<?= $local ?>/css/jquery.qtip.min.css" type="text/css"/>
	<link rel="stylesheet" href="<?= $local ?>/css/common.css?v=<?= time() ?>" type="text/css"/>
	<link rel="stylesheet" href="<?= $local ?>/css/sprites.css?v=<?= time() ?>" type="text/css"/>
	<link rel="stylesheet" href="<?= $local ?>/css/jquery-ui.css?v=<?= @filemtime(FCPATH.'css/jquery-ui.css') ?: time() ?>" type="text/css"/>
	<link rel="shortcut icon" href="favicon.ico"/>
	<script type="text/javascript" src="<?= $local ?>/js/jquery-1.11.2.min.js"></script>
	<script type="text/javascript" src="<?= $local ?>/js/jquery-ui.js"></script>
	<script type="text/javascript" src="<?= $local ?>/js/jquery.qtip.min.js"></script>
	<script type="text/javascript" src="<?= $local ?>/js/default.js?v=<?= time() ?>"></script>
	<script type="text/javascript">
	//<![CDATA[
	function searchLog(){
		try{ document.logForm.submit(); }catch(e){}
		return false;
	}
	var PERIODLOG_MIN_HEIGHT = 500;
	function heightResize() {
		function setFrameHeight(h) {
			try {
				if (window.parent && window.parent.frameAutoResize) {
					window.parent.frameAutoResize('mainFrame', h);
				} else if (window.parent && window.parent.document) {
					var frame = window.parent.document.getElementById('mainFrame');
					if (frame) frame.style.height = h + 'px';
				}
			} catch (e) {}
		}
		var docHeight = document.body.scrollHeight || document.documentElement.scrollHeight || document.body.offsetHeight || 0;
		var totalHeight = Math.max(PERIODLOG_MIN_HEIGHT, Math.ceil(docHeight));
		setFrameHeight(totalHeight);
	}
	$(document).ready(function(){
		// datepicker는 body 끝 스크립트에서 초기화 (ui-datepicker-div 충돌 방지)
		// tooltip
		$('[title!=""]').qtip({
			position:{ my:'top center', at:'botom center' },
			style:{ classes:'tooltip_dark' }
		});
		setTimeout(heightResize, 100);
	});
	$(window).on('load', function(){ setTimeout(heightResize, 200); });
	$(window).resize(function(){ heightResize(); });
	//]]>
	</script>
</head>
<body onload="">

	<table width="100%" border="0" class="defaultTable">
		<colgroup>
			<col width="25%"/><col width="25%"/><col width="25%"/><col width="25%"/>
		</colgroup>
		<tbody><tr>
			<th class="menu" style="position:relative;"><a href="/?view=dayLog" class="tab1">일자별 분석<div style="position:absolute;top:5px;left:20px;"><img src="/images/realtime_bt.gif" width="37" height="19"></div></a></th>
			<th class="menu"><a href="/?view=latestLog" class="tab2">최근 분석<div style="position:absolute;top:5px;left:20px;"><img src="/images/realtime_bt.gif" width="37" height="19"></div></a></th>
			<th class="menu on"><a href="/?view=periodLog" class="tab3 on">기간별 분석</a></th>
			<th class="menu"><a href="/?view=patternAnalyze" class="tab5">패턴별 분석</a></th>
		</tr></tbody>
	</table>

	<form name="logForm" method="get" action="<?= esc(site_furl('')) ?>" onsubmit="return searchLog();">
		<input type="hidden" name="view" value="periodLog">

		<div class="periodBox">
			<div class="dateBox">
				<input type="text" name="startDate" value="<?= esc($startDate ?? date('Y-m-d', strtotime('-14 day'))) ?>" class="dateInput sp-dayspace_bg datepicker" readonly="true">
				<div class="bar">~</div>
				<input type="text" name="endDate" value="<?= esc($endDate ?? date('Y-m-d')) ?>" class="dateInput sp-dayspace_bg datepicker" readonly="true">
				<a href="#" onclick="searchLog();return false;" class="sp-search rollover"></a>
			</div>

			<div class="btnBox">
				<?php $dt = (string)($dateType ?? ''); ?>
				<a href="/?view=periodLog&amp;dateType=2day" class="sp-day<?= $dt === '2day' ? ' on' : '' ?>">2일</a>
				<a href="/?view=periodLog&amp;dateType=4day" class="sp-day<?= $dt === '4day' ? ' on' : '' ?>">4일</a>
				<a href="/?view=periodLog&amp;dateType=7day" class="sp-day<?= $dt === '7day' ? ' on' : '' ?>">7일</a>
				<a href="/?view=periodLog&amp;dateType=15day" class="sp-day<?= $dt === '15day' ? ' on' : '' ?>">15일</a>
				<a href="/?view=periodLog&amp;dateType=30day" class="sp-day<?= $dt === '30day' ? ' on' : '' ?>">한달</a>
			</div>
		</div>
	</form>

	<?php
		$stats = $periodStats ?? [];
		$overall = $stats['overall'] ?? ['total'=>0,'cnt'=>[],'per'=>[],'streak'=>[]];
		$oc = $overall['cnt'] ?? [];
		$op = $overall['per'] ?? [];
		$os = $overall['streak'] ?? [];
	?>

	<table width="100%" border="1" style="margin-top:10px;" class="powerballBox">
		<colgroup>
			<col width="9.5%"/><col width="9.5%"/><col width="9.5%"/><col width="9.5%"/>
			<col width="9.5%"/><col width="9.5%"/><col width="9.5%"/><col width="9.5%"/>
			<col width="8%"/><col width="8%"/><col width="8%"/>
		</colgroup>
		<tbody><tr>
			<th height="30" colspan="11" class="title" style="position:relative;">전체 분석 데이터<span style="position:absolute;top:6px;right:10px;color:#969696;" class="siteLink">copyright <a href="/?referer=dayLogBtn" target="_blank" class="titleCopy">powerballgame.co.kr</a></span></th>
		</tr>
		<tr class="subTitle">
			<th height="20" colspan="4">파워볼 기준</th>
			<th colspan="4">숫자합 기준</th>
			<th colspan="3">대중소 기준</th>
		</tr>
		<tr class="thirdTitle">
			<th height="20">홀</th><th>짝</th><th>언더</th><th>오버</th>
			<th>홀</th><th>짝</th><th>언더</th><th>오버</th>
			<th>대</th><th>중</th><th>소</th>
		</tr>
		<tr>
			<td height="60" align="center"><div class="sp-data_odd"><span class="num"><?= (int)($oc['pbOdd'] ?? 0) ?></span></div><span class="oddColor">(<?= (float)($op['pbOdd'] ?? 0) ?>%)</span></td>
			<td align="center"><div class="sp-data_even"><span class="text2"><?= (int)($oc['pbEven'] ?? 0) ?></span></div><span class="evenColor">(<?= (float)($op['pbEven'] ?? 0) ?>%)</span></td>
			<td align="center"><div class="sp-data_under"><?= (int)($oc['pbUnder'] ?? 0) ?></div><span class="oddColor">(<?= (float)($op['pbUnder'] ?? 0) ?>%)</span></td>
			<td align="center"><div class="sp-data_over"><?= (int)($oc['pbOver'] ?? 0) ?></div><span class="evenColor">(<?= (float)($op['pbOver'] ?? 0) ?>%)</span></td>

			<td align="center"><div class="sp-data_odd"><?= (int)($oc['sumOdd'] ?? 0) ?></div><span class="oddColor">(<?= (float)($op['sumOdd'] ?? 0) ?>%)</span></td>
			<td align="center"><div class="sp-data_even"><?= (int)($oc['sumEven'] ?? 0) ?></div><span class="evenColor">(<?= (float)($op['sumEven'] ?? 0) ?>%)</span></td>
			<td align="center"><div class="sp-data_under"><?= (int)($oc['sumUnder'] ?? 0) ?></div><span class="oddColor">(<?= (float)($op['sumUnder'] ?? 0) ?>%)</span></td>
			<td align="center"><div class="sp-data_over"><?= (int)($oc['sumOver'] ?? 0) ?></div><span class="evenColor">(<?= (float)($op['sumOver'] ?? 0) ?>%)</span></td>

			<td align="center" class="periodBox"><div><?= (int)($oc['big'] ?? 0) ?></div><span class="evenColor">(<?= (float)($op['big'] ?? 0) ?>%)</span></td>
			<td align="center" class="periodBox"><div><?= (int)($oc['middle'] ?? 0) ?></div><span class="middleColor">(<?= (float)($op['middle'] ?? 0) ?>%)</span></td>
			<td align="center" class="periodBox"><div><?= (int)($oc['small'] ?? 0) ?></div><span class="oddColor">(<?= (float)($op['small'] ?? 0) ?>%)</span></td>
		</tr>
		<tr>
			<td height="30" align="center"><span class="oddText"><?= (int)($os['pbOdd'] ?? 0) ?></span>연속</td>
			<td align="center"><span class="evenText"><?= (int)($os['pbEven'] ?? 0) ?></span>연속</td>
			<td align="center"><span class="oddText"><?= (int)($os['pbUnder'] ?? 0) ?></span>연속</td>
			<td align="center"><span class="evenText"><?= (int)($os['pbOver'] ?? 0) ?></span>연속</td>
			<td align="center"><span class="oddText"><?= (int)($os['sumOdd'] ?? 0) ?></span>연속</td>
			<td align="center"><span class="evenText"><?= (int)($os['sumEven'] ?? 0) ?></span>연속</td>
			<td align="center"><span class="oddText"><?= (int)($os['sumUnder'] ?? 0) ?></span>연속</td>
			<td align="center"><span class="evenText"><?= (int)($os['sumOver'] ?? 0) ?></span>연속</td>
			<td align="center"><span class="evenText"><?= (int)($os['big'] ?? 0) ?></span>연속</td>
			<td align="center"><span class="middleText"><?= (int)($os['middle'] ?? 0) ?></span>연속</td>
			<td align="center"><span class="oddText"><?= (int)($os['small'] ?? 0) ?></span>연속</td>
		</tr>
	</tbody></table>

	<?php $mm = $stats['maxMin'] ?? []; ?>
	<table width="100%" border="1" style="margin-top:10px;" class="powerballBox">
		<colgroup>
			<col width="9%"/><col width="13%"/>
			<col width="13%"/><col width="13%"/><col width="13%"/>
			<col width="13%"/><col width="13%"/><col width="13%"/>
		</colgroup>
		<tbody><tr>
			<th height="30" colspan="8" class="title" style="position:relative;">검색 기간내 최대/최소 통계 데이터 <span style="font-weight:normal;">(무효처리 있는 날짜 제외)</span><span style="position:absolute;top:6px;right:10px;color:#969696;" class="siteLink">copyright <a href="/?referer=dayLogBtn" target="_blank" class="titleCopy">powerballgame.co.kr</a></span></th>
		</tr>
		<tr class="subTitle">
			<th height="20" colspan="2"></th>
			<th colspan="3">최대</th>
			<th colspan="3">최소</th>
		</tr>
		<tr class="thirdTitle">
			<th height="20" colspan="2">구분</th>
			<th>날짜</th><th>횟수</th><th>%</th>
			<th>날짜</th><th>횟수</th><th>%</th>
		</tr>
		<?php foreach ($mm as $row): ?>
		<tr>
			<td height="35" align="center">
				<?php if ($row['icon'] === 'sp-odd'): ?><div class="sp-odd"></div>
				<?php elseif ($row['icon'] === 'sp-even'): ?><div class="sp-even"></div>
				<?php elseif ($row['icon'] === 'big'): ?>대
				<?php elseif ($row['icon'] === 'middle'): ?>중
				<?php else: ?>소<?php endif; ?>
			</td>
			<td align="center"><span class="<?= ($row['key']==='middle') ? 'middleText' : (($row['key']==='pbEven'||$row['key']==='sumEven'||$row['key']==='big') ? 'evenText' : 'oddText') ?>"><?= esc($row['label']) ?></span></td>
			<td align="center"><span class="date"><?= esc($row['max']['date'] ?? '-') ?></span></td>
			<td align="center"><span class="evenText"><?= isset($row['max']) ? ((int)$row['max']['cnt']).'번' : '-' ?></span></td>
			<td align="center"><span class="evenText"><?= isset($row['max']) ? number_format((float)$row['max']['per'], 2).'%': '-' ?></span></td>
			<td align="center"><span class="date"><?= esc($row['min']['date'] ?? '-') ?></span></td>
			<td align="center"><span class="oddText"><?= isset($row['min']) ? ((int)$row['min']['cnt']).'번' : '-' ?></span></td>
			<td align="center"><span class="oddText"><?= isset($row['min']) ? number_format((float)$row['min']['per'], 2).'%': '-' ?></span></td>
		</tr>
		<?php endforeach; ?>
	</tbody></table>

	<?php $daily = $stats['daily'] ?? []; ?>
	<table width="100%" border="1" id="ladderLogBox" class="powerballBox" style="margin-top:10px;">
		<colgroup>
			<col width="8%"/>
			<col width="8.5%"/><col width="8.5%"/><col width="8.5%"/><col width="8.5%"/>
			<col width="8.5%"/><col width="8.5%"/><col width="8.5%"/><col width="8.5%"/>
			<col width="8.5%"/><col width="8%"/><col width="8%"/>
		</colgroup>
		<tbody>
		<tr class="subTitle">
			<th height="20" rowspan="2">날짜</th>
			<th colspan="4">파워볼 기준</th>
			<th colspan="4">숫자합 기준</th>
			<th colspan="3">대중소 기준</th>
		</tr>
		<tr class="thirdTitle">
			<th>홀</th><th>짝</th><th>언더</th><th>오버</th>
			<th>홀</th><th>짝</th><th>언더</th><th>오버</th>
			<th>대</th><th>중</th><th>소</th>
		</tr>
		<?php foreach ($daily as $i => $d): ?>
			<?php
				$trClass = ($i % 2 === 0) ? 'trEven' : 'trOdd';
				$c = $d['cnt'] ?? []; $p = $d['per'] ?? []; $s = $d['streak'] ?? [];
			?>
			<tr class="<?= $trClass ?>">
				<td height="70" rowspan="2" align="center"><span class="date"><?= esc($d['date'] ?? '') ?></span></td>
				<td align="center"><div class="oddText"><?= (int)($c['pbOdd'] ?? 0) ?></div><span class="oddColor">(<?= number_format((float)($p['pbOdd'] ?? 0), 2) ?>%)</span></td>
				<td align="center"><div class="evenText"><?= (int)($c['pbEven'] ?? 0) ?></div><span class="evenColor">(<?= number_format((float)($p['pbEven'] ?? 0), 2) ?>%)</span></td>
				<td align="center"><div class="oddText"><?= (int)($c['pbUnder'] ?? 0) ?></div><span class="oddColor">(<?= number_format((float)($p['pbUnder'] ?? 0), 2) ?>%)</span></td>
				<td align="center"><div class="evenText"><?= (int)($c['pbOver'] ?? 0) ?></div><span class="evenColor">(<?= number_format((float)($p['pbOver'] ?? 0), 2) ?>%)</span></td>

				<td align="center"><div class="oddText"><?= (int)($c['sumOdd'] ?? 0) ?></div><span class="oddColor">(<?= number_format((float)($p['sumOdd'] ?? 0), 2) ?>%)</span></td>
				<td align="center"><div class="evenText"><?= (int)($c['sumEven'] ?? 0) ?></div><span class="evenColor">(<?= number_format((float)($p['sumEven'] ?? 0), 2) ?>%)</span></td>
				<td align="center"><div class="oddText"><?= (int)($c['sumUnder'] ?? 0) ?></div><span class="oddColor">(<?= number_format((float)($p['sumUnder'] ?? 0), 2) ?>%)</span></td>
				<td align="center"><div class="evenText"><?= (int)($c['sumOver'] ?? 0) ?></div><span class="evenColor">(<?= number_format((float)($p['sumOver'] ?? 0), 2) ?>%)</span></td>

				<td align="center"><div class="evenText"><?= (int)($c['big'] ?? 0) ?></div><span class="evenColor">(<?= number_format((float)($p['big'] ?? 0), 2) ?>%)</span></td>
				<td align="center"><div class="middleText"><?= (int)($c['middle'] ?? 0) ?></div><span class="middleColor">(<?= number_format((float)($p['middle'] ?? 0), 2) ?>%)</span></td>
				<td align="center"><div class="oddText"><?= (int)($c['small'] ?? 0) ?></div><span class="oddColor">(<?= number_format((float)($p['small'] ?? 0), 2) ?>%)</span></td>
			</tr>
			<tr class="<?= $trClass ?>">
				<td height="25" align="center"><span class="oddText"><?= (int)($s['pbOdd'] ?? 0) ?></span>연속</td>
				<td align="center"><span class="evenText"><?= (int)($s['pbEven'] ?? 0) ?></span>연속</td>
				<td align="center"><span class="oddText"><?= (int)($s['pbUnder'] ?? 0) ?></span>연속</td>
				<td align="center"><span class="evenText"><?= (int)($s['pbOver'] ?? 0) ?></span>연속</td>
				<td align="center"><span class="oddText"><?= (int)($s['sumOdd'] ?? 0) ?></span>연속</td>
				<td align="center"><span class="evenText"><?= (int)($s['sumEven'] ?? 0) ?></span>연속</td>
				<td align="center"><span class="oddText"><?= (int)($s['sumUnder'] ?? 0) ?></span>연속</td>
				<td align="center"><span class="evenText"><?= (int)($s['sumOver'] ?? 0) ?></span>연속</td>
				<td align="center"><span class="evenText"><?= (int)($s['big'] ?? 0) ?></span>연속</td>
				<td align="center"><span class="middleText"><?= (int)($s['middle'] ?? 0) ?></span>연속</td>
				<td align="center"><span class="oddText"><?= (int)($s['small'] ?? 0) ?></span>연속</td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>

	<script type="text/javascript">
	//<![CDATA[
	// body 끝에서 초기화 (DOM 확실히 준비된 뒤, ui-datepicker-div 충돌 방지)
	(function(){
		var $ = window.jQuery;
		if (!$ || !$.fn.datepicker) return;
		var opts = {
			dateFormat: 'yy-mm-dd',
			prevText: '이전 달', nextText: '다음 달',
			monthNames: ['1월','2월','3월','4월','5월','6월','7월','8월','9월','10월','11월','12월'],
			monthNamesShort: ['1월','2월','3월','4월','5월','6월','7월','8월','9월','10월','11월','12월'],
			dayNames: ['일','월','화','수','목','금','토'],
			dayNamesShort: ['일','월','화','수','목','금','토'],
			dayNamesMin: ['일','월','화','수','목','금','토'],
			showMonthAfterYear: true, yearSuffix: '년', maxDate: '+0d',
			appendTo: 'body',
			beforeShow: function(input, inst) {
				setTimeout(function() {
					if (inst.dpDiv && inst.dpDiv.length) inst.dpDiv.css('z-index', 2147483647);
				}, 0);
			}
		};
		$('input[name="startDate"], input[name="endDate"]').datepicker(opts).on('click', function() {
			$(this).datepicker('show');
		});
	})();
	//]]>
	</script>
</body>
</html>

