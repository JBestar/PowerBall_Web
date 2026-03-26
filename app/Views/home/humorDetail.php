<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8"/>
	<meta http-equiv="X-UA-Compatible" content="IE=edge"/>
	<title>유머 내용</title>
	<?php $local = rtrim(site_furl(''), '/'); ?>
	<link rel="stylesheet" href="<?php echo $local; ?>/css/common_logged.css?v=<?php echo time(); ?>" type="text/css"/>
	<style>
		body { color:#000; }
		.wrap { padding:14px; }
		.title { font-size:16px; font-weight:bold; margin-bottom:10px; }
		.content { border:1px solid #d5d5d5; padding:10px; white-space:pre-wrap; word-break:break-word; background:#fff; }
		.meta { color:#777; font-size:12px; margin-top:8px; }
		.btn { display:inline-block; margin-top:10px; background:#efefef; color:#000; font-weight:bold; border:1px solid #cecece; padding:8px 14px; cursor:pointer; }
	</style>
</head>
<body>
	<div class="wrap">
		<?php if (!empty($post)): ?>
			<div class="title"><?= esc($post->title ?? '') ?></div>
			<div class="content"><?= esc($post->content ?? '') ?></div>
			<div class="meta">등록자: <?= esc($post->mb_uid ?? '') ?> / id: <?= (int)($post->id ?? 0) ?></div>
			<div style="text-align:right;">
				<a class="btn" href="#" onclick="window.close(); return false;">닫기</a>
			</div>
		<?php else: ?>
			<div class="content">유머 내용을 찾을 수 없습니다.</div>
		<?php endif; ?>
	</div>

	<script type="text/javascript">
		// 등록/수정 후 메인 리스트를 갱신
		try {
			if (window.opener && !window.opener.closed) {
				window.opener.location.reload();
			}
		} catch (e) {}
	</script>
</body>
</html>

