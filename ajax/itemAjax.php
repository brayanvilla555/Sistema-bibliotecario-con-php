<?php
$peticionAjax = true;
if(isset($_POST['item_codigo_reg']) || isset($_POST['id_item_del']) || isset($_POST['item_id_up'])){

	require_once '../controladores/itemControlador.php';
	$inst_item = new itemControlador();

	/*agregar item*/
	if(isset($_POST['item_codigo_reg'])){
		echo $inst_item->agregar_item_controlador();
	}

	/*eliminar item*/
	if(isset($_POST['id_item_del'])){
		echo $inst_item->eliminar_item_controlador();
	}

	/*actualizar item*/
	if(isset($_POST['item_id_up'])){
		echo $inst_item->actualizar_item_controlador();
	}

}else{
	/*
	session_start(['name'=>'SPM']);
	session_unset();
	session_destroy();
	header("location :".SERVER_URL."login/");
	exit();
	*/
}