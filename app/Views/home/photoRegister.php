<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8"/>
	<meta http-equiv="X-UA-Compatible" content="IE=edge"/>
	<title>포토 등록</title>
	<?php $local = rtrim(site_furl(''), '/'); ?>
	<link rel="stylesheet" href="<?php echo $local; ?>/css/common_logged.css?v=<?php echo time(); ?>" type="text/css"/>
	<style>
		body { color:#000; }
		.wrap { padding:12px; }
		.box { border:1px solid #d5d5d5; border-radius:10px; padding:14px; background:#fff; }
		.input { width:100%; border:1px solid #949494; padding:6px; box-sizing:border-box; }
		.row { margin-bottom:10px; }
		.btn { display:inline-block; background:#127CCB; color:#fff; font-weight:bold; border:1px solid #0e609c; padding:8px 14px; cursor:pointer; }
		.note { color:#666; font-size:12px; margin-top:6px; }
		.previewBox {
			width:100px;
			height:100px;
			border:1px solid #d5d5d5;
			background:#f8f8f8;
			display:flex;
			align-items:center;
			justify-content:center;
			overflow:hidden;
			margin-top:10px;
		}
		.previewBox img {
			width:100px;
			height:100px;
			object-fit:cover;
			display:none;
		}
		.previewText {
			color:#999;
			font-size:11px;
			text-align:center;
			line-height:1.4;
		}
		.flashMsg {
			border:1px solid #f1b8ba;
			background:#fff3f4;
			color:#b0111a;
			padding:8px 10px;
			margin-bottom:10px;
			border-radius:6px;
			font-size:12px;
		}
	</style>
</head>
<body>
	<div class="wrap">
		<div class="box">
			<?php $flashMsg = session('message'); ?>
			<?php if (!empty($flashMsg)): ?>
				<div class="flashMsg"><?= esc($flashMsg) ?></div>
			<?php endif; ?>
			<form method="post" action="/?view=photoRegister" enctype="multipart/form-data">
				<div class="row">
					<input type="text" name="title" class="input" maxlength="200" placeholder="제목" />
				</div>
				<div class="row">
					<input type="file" id="photo_file" name="photo_file" class="input" accept="image/png,image/jpeg,image/gif,image/webp" />
					<div class="note">어떤 이미지를 올려도 서버에서 자동으로 200x200(px), 1:1로 변환됩니다.</div>
					<div class="previewBox" id="previewBox">
						<img id="previewImg" alt="미리보기" />
						<div class="previewText" id="previewText">미리보기</div>
					</div>
				</div>
				<div style="text-align:right;">
					<button type="submit" class="btn">등록</button>
				</div>
			</form>
		</div>
	</div>
	<script type="text/javascript">
		(function () {
			var fileInput = document.getElementById('photo_file');
			var previewImg = document.getElementById('previewImg');
			var previewText = document.getElementById('previewText');
			if (!fileInput || !previewImg || !previewText) return;

			fileInput.addEventListener('change', function () {
				var file = fileInput.files && fileInput.files[0];
				if (!file) {
					previewImg.style.display = 'none';
					previewImg.src = '';
					previewText.style.display = 'block';
					previewText.textContent = '미리보기';
					return;
				}

				var reader = new FileReader();
				reader.onload = function (e) {
					previewImg.src = e.target && e.target.result ? e.target.result : '';
					previewImg.style.display = 'block';
					previewText.style.display = 'none';
				};
				reader.readAsDataURL(file);
			});
		})();
	</script>
</body>
</html>

