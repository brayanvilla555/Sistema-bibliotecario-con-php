<?php
$peticionAjax = true;
require_once '../config/APP.php';

if (isset($_POST['usuario_dni_reg']) || isset($_POST['usuario_id_del']) || isset($_POST['usuario_id_up'])) {
	/*==============instancia al controlador==========*/
	require_once '../controladores/usuarioControlador.php';
	$ins_usuario = new usuarioControlador();

	/*=======Agregar un Usuario=======*/
	if (isset($_POST['usuario_dni_reg']) && isset($_POST['usuario_nombre_reg'])) {
		echo $ins_usuario->agregar_usuario_controlador();
	}

	/*====Eliminar un Usuario====*/
	if (isset($_POST['usuario_id_del'])) {
		echo $ins_usuario->eliminar_usuario_controlado();
	}

	/*===Actualizar usuario===*/
	if(isset($_POST['usuario_id_up'])){
		echo $ins_usuario->actualizar_usuario_controller();
	}
}else{
	session_start(['name'=>'SPM']);
	session_unset();
	session_destroy();
	header("location: ".SERVER_URL."login/");
	exit();
}