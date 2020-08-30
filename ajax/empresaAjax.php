<?php
$peticionAjax = true;
require_once '../config/APP.php';

if(isset($_POST['empresa_nombre_reg']) || isset($_POST['empresa_id_up'])){

	require_once '../controladores/empresaControlador.php';
	$inst_empresa = new empresaControlador();

	/*agregar empresa*/
	if(isset($_POST['empresa_nombre_reg'])){
		echo $inst_empresa->agregar_empresa_controlador();
	}

	/*actualizar empresa*/
	if(isset($_POST['empresa_id_up'])){
		echo $inst_empresa->actualizar_empresa_controlador();
	}
}else{
	session_stard(['name' => 'SPM']);
	session_unset();
	session_destroy();
	header("location".SERVER_URL."login/");
	exit();
}