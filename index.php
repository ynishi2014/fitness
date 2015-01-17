<?php
include("module/hakodate.php");

execute();

function defaultAction(){
	logout();
	if(isset($_POST['login'])){
	}elseif(isset($_COOKIE['login'])){
		$_POST['login'] = $_COOKIE['login'];
		$_POST['password'] = base64_decode($_COOKIE['password']);
	}else{
		$_POST['login'] = '';
		$_POST['password'] = '';
	}
	if(isset($_COOKIE['login'])){
		$checked = ' checked="checked"';
	}else{
		$checked = '';
	}
	include("inc_index.php");
}

function loginAction(){
	$admin_id = g("SELECT staff_id FROM fit_staff WHERE login = g:%1 AND password = sha1(g:%2)", @$_POST['login'], @$_POST['password']);
	if($admin_id === false){
		setError('password','IDとパスワードの組合せが正しくありません。');
	}
	if(isset($_POST['save_id_password'])){
		$timeout = time() + 14 * 86400;
	}else{
		$timeout = time() - 14 * 86400;
	}	
	setCookie('login', $_POST['login'], $timeout);
	setCookie('password', base64_encode($_POST['password']), $timeout);
	if(isValid()){
		setUserID($admin_id);
		location('sys/index.php');
	}else{
		include('inc_index.php');
	}
}

function logoutAction(){
	logout();
	location('index.php?logout=true');
}