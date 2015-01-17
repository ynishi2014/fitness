<?php

function showHeader(){
$path = config("path");

$logoutButton = getUserID() ? '<button class="btn" data-toggle="button" style="float:right;" onclick="location.href=\''.config('path').'index.php?action=logout\'">ログアウト</button>' : '';

echo <<<HTML
<!DOCTYPE html>
<html lang="ja">
	<head>
		<meta charset="utf-8">
		<title>Fitness</title>
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1">
		<meta name="description" content="">
		<meta name="author" content="">
		<!-- Le styles -->
		<link rel="stylesheet"  href="{$path}css/themes/ui-lightness/jquery.ui.all.css" />
		<link rel="shortcut icon" href="favicon.ico">
		<link href="{$path}css/bootstrap.css" rel="stylesheet">
		<link rel="stylesheet" href="{$path}css/additional.css" />
		<script src="{$path}js/jquery.js"></script>
		<script src="{$path}js/jquery.ui.core.js"></script>
		<script src="{$path}js/jquery.ui.widget.js"></script>
		<script src="{$path}js/jquery.ui.mouse.js"></script>
		<script src="{$path}js/jquery.ui.sortable.js"></script>
		<script src="{$path}js/jquery.ui.datepicker.js"></script>
		<script src="{$path}js/jquery.ui.datepicker-ja.js"></script>
		<style type="text/css">
			body {
				padding-top: 60px;
				padding-bottom: 0px;
				min-width: 400px;
			}
		</style>
		<!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
		<!--[if lt IE 9]>
			<script src="https://html5shim.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->
	</head>
	<body>
		<div class="navbar navbar-fixed-top">
			<div class="navbar-inner" style="padding:2px;height:40px;">
				<div class="container">
					<a class="brand" style="padding-left:0px;margin-left:10px;top:3px;left:50px;z-index:0;margin-right:-20px;overflow:hidden;max-height:8px;" target="_top">Fitness</a>
					$logoutButton
				</div>
			</div>
		</div>
		<div class="container" align="right"></div>
		<div class="container">
HTML;
}


function showFooter(){
	echo <<<HTML
			<hr>
			<footer>
				<p>&copy; 2015 Fitness</p>
							</footer>
		</div> <!-- /container -->
	</body>
</html>
HTML;
}

function setUserID($user_id){
	$_SESSION['user_id'] = $user_id;
}
function getUserID(){
	return isset($_SESSION['user_id']) ? $_SESSION['user_id'] : false;
}
function logout(){
	unset($_SESSION['user_id']);
}
function loginCheck(){
	if(getUserID() === false){
		location(config("path"));
	}
}

function getPrefectureArray(){
	return explode(",", "北海道,青森県,岩手県,宮城県,秋田県,山形県,福島県,茨城県,栃木県,群馬県,埼玉県,千葉県,東京都,神奈川県,新潟県,富山県,石川県,福井県,山梨県,長野県,岐阜県,静岡県,愛知県,三重県,滋賀県,京都府,大阪府,兵庫県,奈良県,和歌山県,鳥取県,島根県,岡山県,広島県,山口県,徳島県,香川県,愛媛県,高知県,福岡県,佐賀県,長崎県,熊本県,大分県,宮崎県,鹿児島県,沖縄県");
}