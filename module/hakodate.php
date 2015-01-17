<?php
$GLOBALS['start_time'] = microtime(true);
date_default_timezone_set('Etc/GMT-9');
mb_regex_encoding('UTF-8');

require(dirname(__FILE__) . '/../config/config_default.php');
if(is_file(dirname(__FILE__) . '/../config/config.php')){
	require(dirname(__FILE__) . '/../config/config.php');
}else{
	die('config.phpを作成してください');
}

require('bell_functions.php');

//session_set_cookie_params(0, '/'.$path.'/');
session_start();

if(isset($_GET['debug']))setDebugMode($_GET['debug'] == 'true');

@mysql_connect($config['db']['host'], $config['db']['user'], $config['db']['password']) OR DIE('メンテナンス中です(501)');
query('USE `'.$config['db']['database'].'`');
query('SET NAMES utf8');
if($config['db']['host'] != '127.0.0.1'){
	query('SET SESSION time_zone="Asia/Tokyo"');
}

function execute(){
	header('Content-type: text/html; charset=utf-8');
	$action = (isset($_GET['action']) ? $_GET['action'] : 'default').'Action';
	if(function_exists($action)){
		$action();
	}else{
		showErrorScreen();
	}
}
function showErrorScreen(){
	if(isset($_SERVER['HTTP_REFERER'])){
		showHeader(array('back' => $_SERVER['HTTP_REFERER']));
	}else{
		showHeader();
	}
	echo '不正な操作です。';
	showFooter();
	exit;
}
function showExecTime(){
	printf('<div>%.2fms</div>', (microtime(true) - $GLOBALS['start_time']) * 1000);
}
function showDebugInfo(){
	if(isDebugMode()){
		showExecTime();
		echo '<h2 style="margin:0;margin-top:10px;margin-left:5px;text-align:left">GET</h2>';
		out($_GET);
		echo '<h2 style="margin:0;margin-top:10px;margin-left:5px;text-align:left">POST</h2>';
		out($_POST);
		echo '<h2 style="margin:0;margin-top:10px;margin-left:5px;text-align:left">SESSION</h2>';
		out($_SESSION);
		echo '<h2 style="margin:0;margin-top:10px;margin-left:5px;text-align:left">QUERY</h2>';
		table(tableRotate(array(
			'query' => $GLOBALS['dblog']['queryArray'], 
			'time' => $GLOBALS['dblog']['timeArray'], 
			'count' => $GLOBALS['dblog']['countArray'], 
			'error' => $GLOBALS['dblog']['errorArray'])));
		echo '<h2>GLOBALS</h2>';
		out($GLOBALS);
	}
}
/**
 * debug
 **/
function isDebugMode(){
	return isset($_SESSION['debug_mode']);
}
function setDebugMode($val){
	if($val){
		$_SESSION['debug_mode'] = true;
	}else{
		unset($_SESSION['debug_mode']);
	}
}

/**
 * Database
 **/
function dbnow(){
	return "[[---dbnow---]]";
}
function dq($str){
	return "'".de($str)."'";
}
function de($str){
	return mysql_real_escape_string($str);
}
function querys($sql){
	processArgs(func_get_args());
	foreach($_GET as $key => $value){
		$sql = str_replace('g:'.$key, dq($value), $sql);
	}
	cleanArgs();
	return query($sql);
}
function query($sql){
	$GLOBALS['dblog']['queryArray'][] = $sql;
	if(isDebugMode()){
		$time = microtime(true);
	}
	$resource = mysql_query($sql);
	if(isDebugMode()){
		$GLOBALS['dblog']['timeArray'][] = sprintf('%.2fms', (microtime(true) - $time) * 1000);
		$GLOBALS['dblog']['countArray'][] = mysql_affected_rows();;
		$GLOBALS['dblog']['errorArray'][] = mysql_error();
	}
	if($resource === false){
		echo '<span style="color:red"><strong>Error: </strong>'.$sql.' -- '.mysql_error().'</span>';
		exit;
	}
	return $resource;
}
function error($str){
	echo '<div style="color:red">'.$str.'</div>';
}
function processArgs($args){
	array_shift($args);
	foreach($args as $key => $value)$_GET['%'.($key + 1)] = $value;
}
function cleanArgs(){
	foreach($_GET as $key => $value){
		if($key[0] == '%'){
			unset($_GET[$key]);
		}
	}	
}
function get($sql){
	processArgs(func_get_args());
	$data = mysql_fetch_assoc(querys($sql));
	cleanArgs();
	return $data;
}
function getAll($sql){
	processArgs(func_get_args());
	for($result = querys($sql), $rowArray = array(); $row = mysql_fetch_assoc($result) ; $rowArray[] = $row);
	mysql_free_result($result);
	cleanArgs();
	return $rowArray;
}
function g($sql){
	processArgs(func_get_args());
	$ret = mysql_fetch_assoc(querys($sql));
	cleanArgs();
	return is_array($ret) ? array_shift($ret) : false;
}
function gAll($sql){
	processArgs(func_get_args());
	for($result = querys($sql), $rowArray = array() ; $row = mysql_fetch_assoc($result) ; $rowArray[] = array_shift($row));
	cleanArgs();
	return $rowArray;
}
function getMap($sql){
	processArgs(func_get_args());
	for($result = querys($sql), $rowArray = array(); $row = mysql_fetch_row($result) ; $rowArray[] = $row);
	cleanArgs();
	$map = array();
	foreach($rowArray as $data){
		if(count($data) != 2)exit;
		$map[$data[0]] = $data[1];
	}
	return $map;
}
function put($tableName, $dataStruct, $keyArray = array()){
	switch(config('csrf')){
		case 1://SAME DOMAIN OR NO REFERER
			if(!isset($_SERVER['HTTP_REFERER']))break;
			if(!preg_match('/^https?:\/\/'.$_SERVER['SERVER_NAME'].'/', $_SERVER['HTTP_REFERER']))die();
			break;
		case 2://SAME DOMAIN ONLY
			if(!preg_match('/^https?:\/\/'.$_SERVER['SERVER_NAME'].'/', $_SERVER['HTTP_REFERER']))die();
			break;
	}
	$fieldNames = array_keys($dataStruct);
	$fieldNames = array_map('backQuote', $fieldNames);
	$dataValues = array_values($dataStruct);
	$dataValues = array_map('quote', $dataValues);		
	if(!is_array($keyArray)){
		$keyArray = explode(',', $keyArray);
	}
	if(count($keyArray) > 0){
		$sql1 = 'SELECT COUNT(*) count FROM '.backQuote($tableName);
		if(count($keyArray)){
			for($i = 0 ; $i < count($keyArray) ; $i++){
				$searchKeyStruct[$keyArray[$i]] = $dataStruct[$keyArray[$i]];
			}
			$searchKeyStruct = array_map('quote', $searchKeyStruct);		
			$dataSearchCondition = array_map('makeEquation', array_keys($searchKeyStruct), array_values($searchKeyStruct));
			$sql1.= ' WHERE '.implode(' AND ', $dataSearchCondition);
		}
		$count = g($sql1);
	}
	if(count($keyArray) == 0 || $count == 0){
		return insert($tableName, $dataStruct);
	}else{
		update($tableName, $dataStruct, $keyArray);
	}
}
function insert($tableName, $dataStruct){
	$fieldNames = array_keys($dataStruct);
	$fieldNames = array_map('backQuote', $fieldNames);
	$dataValues = array_values($dataStruct);
	$dataValues = array_map('quote', $dataValues);
	$sql = 'INSERT INTO `'.$tableName.'`('.implode(',', $fieldNames).') VALUES ('.implode(',', $dataValues).')';
	query($sql);
	return mysql_insert_id();
}
function update($tableName, $dataStruct, $keyArray){
	$fieldNames = array_keys($dataStruct);
	$fieldNames = array_map('backQuote', $fieldNames);
	$dataValues = array_values($dataStruct);
	$dataValues = array_map('quote', $dataValues);		
	if(!is_array($keyArray))$keyArray = explode(',', $keyArray);
	foreach($fieldNames as $i => $name){
		if(in_array($name, $keyArray)){
			unset($fieldNames[$i]);
			unset($dataValues[$i]);
		}
	}
	for($i = 0 ; $i < count($keyArray) ; $i++){
		$searchKeyStruct[$keyArray[$i]] = $dataStruct[$keyArray[$i]];
	}
	$searchKeyStruct = array_map('quote', $searchKeyStruct);		
	$dataSearchCondition = array_map('makeEquation', array_keys($searchKeyStruct), array_values($searchKeyStruct));
	query($sql = 'UPDATE '.backQuote($tableName).' SET '.implode(',', array_map('makeEquation', $fieldNames, $dataValues)).' WHERE '.implode(' AND ',$dataSearchCondition));
}
function quote($value){
	if($value === dbnow()){
		return "NOW()";
	}else if($value === NULL){
		return 'NULL';
	}else{
		return "'".mysql_real_escape_string($value)."'";
	}
}
function backQuote($value){
	return '`'.$value.'`';
}
function makeEquation($key, $value){
	return $key.'='.$value;
}
/* Display multi-dimension array (for debugging) */
function out($data){
	if(is_object($data))$data = get_object_vars($data);
	if(is_array($data)){
		echo '<table border="1" style="border:solid 2px black;border-collapse: collapse;margin-left:5px;" bgcolor="#ffffff">';
		echo '<tr style="background-color:#ffffaa"><th style="background-color:#ffffaa">key</th><th style="background-color:#ffffaa">value</th></tr>';
		foreach($data as $key => $value){
			echo '<tr style="background-color:#ffffff"><td style="background-color:#ffffff">';
			echo $key;
			echo '</td><td>';
			if($key !== 'GLOBALS'){
				out($value);
			}
			echo '</td></tr>';
		}
		echo '</table>';
	}else{
		echo '<div>';
		if(is_string($data)){
			echo h($data);
		}else{
			var_dump($data);
		}
		echo '</div>';
	}
	return $data;
}
/* Display 2-dimension array (for debugging) */
function table($table){
	if(!is_array($table)){
		echo 'Not an array';
		return;
	}
	echo '<table border="1" style="border:solid 2px black;border-collapse: collapse;margin-left:5px;" bgcolor="#ffffff">';
	echo '<tr bgcolor="#ffffaa">';
	echo '<td>-</td>';
	foreach($table as $row){
		foreach($row as $name => $td){
			echo '<td>'.h($name).'</td>';
		}
		break;
	}
	echo '</tr>';
	foreach($table as $name => $tr){
		echo '<tr>';
		echo '<td bgcolor="#ffffaa">'.h($name).'</td>';
		foreach($tr as $td){
			echo '<td>'.h($td).'</td>';
		}
		echo '</tr>';
	}
	echo '</table>';
	return $table;
}
function tableRotate($baseTable){
	foreach($baseTable as $keyForRow => $baseRow){
		foreach($baseRow as $keyForValue => $baseValue){
			$retTable[$keyForValue][$keyForRow] = $baseValue;
		}
	}
	return $retTable;
}
function row($data){
	table(array($data));
}

function h($str){
	return htmlspecialchars($str, ENT_QUOTES, 'utf-8');
}
function uh($str){
	return h(urlencode($str));
}
function doubleQuote($str){
	return '"'.str_replace('"', '""', $str).'"';
}
function location($url){
	header('location:'.$url);
	exit;
}
function gu($list){
	$ret = array();
	foreach(explode(',', $list) as $name){
		if(isset($_GET[$name])){
			$ret[] = $name.'='.urlencode($_GET[$name]);
		}
	}
	return implode('&', $ret);
}
function mh($list = false){
	$ret = array();
	if($list === false){
		$list = array_keys($_POST);
	}elseif(is_array($list)){
	}else{
		$list = explode(',', $list);
	}
	foreach($list as $name){
		if(isset($_POST[$name])){
			if(is_array($_POST[$name])){
				foreach($_POST[$name] as $elem){
					$ret[] = '<input type="hidden" name="'.h($name).'[]" value="'.h($elem).'"/>';
				}
			}else{
				$ret[] = '<input type="hidden" name="'.h($name).'" value="'.h($_POST[$name]).'"/>';
			}
		}
	}
	return implode(chr(10), $ret);
}

/*
 * Validator
 */
function isValid($name=0){
	if($name===0){
		return !isset($GLOBALS['error']);
	}else{
		return !isset($GLOBALS['error'][$name]);
	}
}
function setError($name, $message){
	$GLOBALS['error'][$name] = $message;
}
function showError($name){
	if(isset($GLOBALS['error'][$name])){
		error($GLOBALS['error'][$name]);
	}
}
function validateNecessary($name){
	if($_POST[$name] == '')setError($name, '必ず入力して下さい。');
}
function validateSelection($name){
	if(!isset($_POST[$name]))setError($name, '必ず選択して下さい。');
}
function validateScore($name){
	if($_POST[$name] == ''){
		//空欄の場合はNULLとする。
		$_POST[$name] = null;
	}elseif(preg_match('/^[0-9]{1,2}$/', $_POST[$name]) || $_POST[$name] == 100){
		//2桁以内の数字もしくは100であればOK
	}else{
		setError($name, '0以上100以下の整数もしくは、空文字列を指定して下さい。');
	}
}
function validateQuestionScore($name){
	if(preg_match('/^[0-9]{1,2}$/', $_POST[$name]) || $_POST[$name] == 100){
		//2桁の数字もしくは100であればOK
	}else{
		setError($name, '0以上100以下の整数で指定して下さい。');
	}
}
function validateDateFormat($name,$input=true){
	$keyArray = explode('.',$name);
	if(count($keyArray)==2){
		$val = $_POST[$keyArray[0]][$keyArray[1]];
	}else{
		$val = $_POST[$name];
	}
	if($input == false && !$val){
		return;
	}
	$_POST[$name] = str_replace("/","-",$val);
	if (!preg_match("|^\d{4}\-\d{2}\-\d{2}$|", $_POST[$name]) &&
		!preg_match("|^\d{4}\-\d{2}\-\d{2}\ \d{2}\:\d{2}\:\d{2}$|", $_POST[$name])){
		setError(str_replace('.','',$name), '日付の書式が不正です。');
	}
}
function validateEmail($name){
	if (!strstr($_POST[$name],'@')){
		setError($name, 'メールアドレスの書式が不正です。');
	}
}
function plainField($name){
	echo @h($_POST[$name]);
	hiddenField($name);
}
function hiddenField($name){
	echo '<input type="hidden" name="'.h($name).'" value="'.@h($_POST[$name]).'"/>';
}
function inputField($name, $params = false){
	if(!$params)$params = array();
	if(!isset($params['width'])){
		$params['width'] = '240';
	}
	echo '<input type="text" name="'.h($name).'" value="'.@h($_POST[$name]).'" style="width:'.$params['width'].'px;"/>';
	showError($name);
}
function inputFields($name, $count, $params = false){
	if(!$params)$params = array();
	if(!isset($params['width'])){
		$params['width'] = '120';
	}
	for($i = 0; $i < $count; $i++){
		echo '<input type="text" name="'.h($name).'[]" value="'.@h($_POST[$name][$i]).'" style="width:'.$params['width'].'px;"/>';
	}
	showError($name);
}
function textArea($name, $row = 6, $width = 500){
	echo '<textarea name="'.h($name).'" rows="'.$row.'" style="width:'.$width.'px" >'.@h($_POST[$name]).'</textarea>';
	showError($name);
}
function passwordField($name){
	echo '<input type="password" name="'.h($name).'" value="'.@h($_POST[$name]).'"/>';
	showError($name);
}
function selectField($name,$array,$default){
	echo '<select name="'.$name.'">';
	foreach($array as $key => $val) {
		if((!isset($_POST[$name]) && $key == $default) || $key == @$_POST[$name]){
			echo '<option value="'.h($key).'" selected>'.h($val);
		}else{
			echo '<option value="'.h($key).'">'.h($val);
		}
	}
	echo '</select>';
	showError($name);
}
function rawselectField($name,$array,$default){
	echo '<select name="'.$name.'">';
	foreach($array as $val) {
		if((!isset($_POST[$name]) && $val == $default) || $val == @$_POST[$name]){
			echo '<option value="'.h($val).'" selected>'.h($val);
		}else{
			echo '<option value="'.h($val).'">'.h($val);
		}
	}
	echo '</select>';
	showError($name);
}
function radioButton($name,$array,$default = false){
	foreach($array as $key => $val) {
		if((!isset($_POST[$name]) && $key == $default) || $key == @$_POST[$name]){
			echo '<label>';
			echo '<input type="radio" name="'.h($name).'" value="'.h($key).'" checked>'.h($val);
			echo '</label>';
		}else{
			echo '<label>';
			echo '<input type="radio" name="'.h($name).'" value="'.h($key).'">'.h($val);
			echo '</label>';
		}
	}
	showError($name);
}
/**
 * Mail
 **/
function sendMail($user_id, $title, $body){
	mb_internal_encoding('UTF-8');  
	$title = mb_encode_mimeheader($title);
	$body = mb_convert_encoding($body, 'ISO-2022-JP', 'utf-8');
	$header = 'From: '.mb_encode_mimeheader("ヴェルハウジング").' <dummy@bell>'.chr(10).
			'Content-Type: text/plain; charset=ISO-2022-JP'.chr(10).
			'Content-Transfer-Encoding: 7bit';
	$email = g('SELECT email FROM rac_user WHERE user_id = g:%1', $user_id);
	$result = mail($email, $title, $body, $header/*, '-f'.$returnPath*/);
	return $result;
}
function config($name){
	$nameArray = explode('.', $name);
	if(count($nameArray) == 1){
		return $GLOBALS['config'][$nameArray[0]];
	}else if(count($nameArray) == 2){
		return $GLOBALS['config'][$nameArray[0]][$nameArray[1]];
	}else if(count($nameArray) == 3){
		return $GLOBALS['config'][$nameArray[0]][$nameArray[1]][$nameArray[2]];
	}
}
function quickInsert($tableName,$dataStruct){
	global $tableArray;
	if(!isset($tableArray[$tableName])){
		$tableArray[$tableName] = array();
	}
	$tableArray[$tableName][] = $dataStruct;
	if(count($tableArray[$tableName]) == 1){
		quickInsertCommit($tableName);
	}
}
function quickInsertCommit($tableName){
	global $tableArray;
	if(!isset($tableArray[$tableName]))return;
	if(count($tableArray[$tableName]) == 0)return;
	
	$dataFieldNames = array_keys($tableArray[$tableName][0]);
	$valueListArray = array();
	foreach($tableArray[$tableName] as $dataValues){
		$dataValues = array_map('dq',$dataValues);		
		$valueListArray[] = '('.implode(',', $dataValues).')';
	}
	$sql2 = 'INSERT INTO '.$tableName.'('.implode(',', $dataFieldNames).')VALUES'.implode(',', $valueListArray);
	query($sql2);
	$tableArray[$tableName] = array();
}
