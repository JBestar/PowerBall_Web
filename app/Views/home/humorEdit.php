<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8"/>
	<meta http-equiv="X-UA-Compatible" content="IE=edge"/>
	<title>유머 수정</title>
	<?php $local = rtrim(site_furl(''), '/'); ?>
	<link rel="stylesheet" href="<?php echo $local; ?>/css/common_logged.css?v=<?php echo time(); ?>" type="text/css"/>
	<style>
		body { color:#000; }
		.defaultTable { width:100%; border-collapse:collapse; table-layout:fixed; }
		.defaultTable td, .defaultTable th { border:1px solid #d5d5d5; padding:6px; }
		.input { width:100%; border:1px solid #949494; padding:6px; box-sizing:border-box; }
		textarea.input { min-height:220px; resize:vertical; }
		.btn { display:inline-block; background:#127CCB; color:#fff; font-weight:bold; border:1px solid #0e609c; padding:8px 14px; cursor:pointer; }
	</style>
</head>
<body>
	<div style="padding:12px;">
		<form method="post" action="/?view=humorEdit&id=<?= (int)($post->id ?? 0) ?>">
			<table class="defaultTable">
				<tr>
					<th style="width:120px; text-align:center;">제목</th>
					<td><input type="text" name="title" class="input" maxlength="200" value="<?= esc($post->title ?? '') ?>" /></td>
				</tr>
				<tr>
					<th style="width:120px; text-align:center;">내용</th>
					<td><textarea name="content" class="input" maxlength="5000"><?= esc($post->content ?? '') ?></textarea></td>
				</tr>
			</table>

			<div style="margin-top:12px; text-align:right;">
				<button type="submit" class="btn">수정</button>
			</div>
		</form>
	</div>
</body>
</html>

