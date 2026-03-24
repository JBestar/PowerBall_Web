<?php
$site_title = $site_title ?? ($site_name ?? '파워볼게임') . ' : 로그인';
$return_url = $return_url ?? site_furl('/');
$login_error = $login_error ?? '';
$css_login = site_furl('css/login.css');
$img_logo = site_furl('images/logo.png');
$img_sp_login = site_furl('images/sp_login.png');
$js_jquery = site_furl('js/jquery-1.11.2.min.js');
$js_login = site_furl('js/login.js');
?>
<!DOCTYPE html>
<html lang="ko">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
	<meta http-equiv="cache-control" content="no-cache">
	<title><?= esc($site_title) ?></title>
	<link rel="stylesheet" type="text/css" href="<?= $css_login ?>?v=<?= time() ?>">
	<link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Nanum+Gothic">
	<link rel="shortcut icon" href="<?= site_furl('favicon.ico') ?>">
</head>
<body>
	<div id="wrap">
		<div id="header">
			<h1>
				<a href="<?= site_furl('/') ?>" class="logo"><img src="<?= $img_logo ?>" alt="POWERBALLGAME"></a>
			</h1>
		</div>
		<div id="container">
			<div class="login_page">
				<h2>로그인</h2>
				<div class="login_box">
					<form id="loginForm" name="loginForm" autocomplete="off" action="<?= site_furl('/') ?>" method="POST">
						<input type="hidden" name="view" value="action">
						<input type="hidden" name="action" value="login">
						<input type="hidden" name="url" value="<?= esc($return_url) ?>">
						<input type="hidden" name="enctp" id="enctp" value="1">
						<input type="hidden" name="enckey" id="enckey" value="">
						<input type="hidden" name="enctk" id="enctk" value="">
						<input type="hidden" name="encpw" id="encpw" value="">
						<input type="hidden" name="gtcaptchaYN" id="gtcaptchaYN" value="N">

						<fieldset>
							<dl>
								<dt class="blind">아이디</dt>
								<dd>
									<span class="input_box">
										<input type="text" id="id" name="id" placeholder="아이디" maxlength="20" value="">
										<button type="button" class="btn_clear" tabindex="-1">삭제</button>
									</span>
								</dd>
								<dt class="blind">비밀번호</dt>
								<dd>
									<span class="input_box">
										<input type="password" id="pw" name="pw" placeholder="비밀번호" maxlength="20">
										<button type="button" class="btn_clear" tabindex="-1">삭제</button>
									</span>
								</dd>
							</dl>
							<?php if ($login_error !== '') : ?>
							<div class="text_alert">
								<p class="error"><?= esc($login_error) ?></p>
							</div>
							<?php endif; ?>
							<div class="btn_login">
								<button type="submit">로그인</button>
							</div>
						</fieldset>
					</form>
				</div>
				<div class="login_menu">
					<ul>
						<li><a href="<?= site_furl('/') ?>">아이디 찾기</a></li>
						<li><a href="<?= site_furl('/') ?>">비밀번호 찾기</a></li>
						<li><a href="<?= site_furl('/') ?>">회원가입</a></li>
					</ul>
				</div>
			</div>
		</div>
		<div id="footer">
			<ul class="link">
				<li><a href="<?= site_furl('/') ?>">이용약관</a></li>
				<li><a href="<?= site_furl('/') ?>"><strong>개인정보처리방침</strong></a></li>
				<li><a href="<?= site_furl('/') ?>"><strong>청소년보호정책</strong></a></li>
				<li><a href="<?= site_furl('/') ?>">고객센터</a></li>
			</ul>
		</div>
	</div>
	<script type="text/javascript" src="<?= $js_jquery ?>"></script>
	<script type="text/javascript" src="<?= $js_login ?>?v=<?= time() ?>"></script>
</body>
</html>
