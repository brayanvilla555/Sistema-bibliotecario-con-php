<?php
$peticionAjax = true;
require_once '../config/App.php';

if(isset($_POST['cliente_dni_reg']) || isset($_POST['cliente_id_del']) || isset($_POST['cliente_id_up'])){

	require_once '../controladores/clienteControlador.php';
	/*instanciar controlador*/
	$ins_cliente = new clienteControlador();

	/*agregar usuario*/
	if(isset($_POST['cliente_dni_reg']) && isset($_POST['cliente_nombre_reg']) && isset($_POST['cliente_apellido_reg'])){
		echo $ins_cliente->agregar_cliente_controlador();
	}

	/*eliminar cliente*/
	if(isset($_POST['cliente_id_del'])){
		echo $ins_cliente->eliminar_cliente_controlador();
	}

	/*actualñizar usuario*/
	if(isset($_POST['cliente_id_up'])){
		echo $ins_cliente->actualizar_cliente_controller();
	}
}else{
	session_start(['name'=>'SPM']);
	session_unset();
	session_destroy();
	header("locatio: ".SERVER_URL."login/");
	exit();
}