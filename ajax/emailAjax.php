<?php
$peticionAjax = true;
require_once '../config/App.php';
if(true){
	echo "hola mundo desde ajax";
}else{
	session_start(['name'=>'SPM']);
	session_unset();
	session_destroy();
	header("locatio: ".SERVER_URL."login/");
	exit();
}