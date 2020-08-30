<?php
//incluimos el modelo
require_once "./modelos/vistaModelo.php";
//heredamos lo del modelo para la vista
class vistaControlador extends vistaModelo{
	/*-----------Controlador para optener la plantilla--------------*/
	public function obtener_plantilla_controlador(){
		return require_once "./vistas/plantilla.php";
	}
	/*--------------controlador para  obtener las vistas----------*/
	public function obtener_vistas_controlador(){
		//confirmar si biene difinida la variable get
		if(isset($_GET['views'])){
			$ruta = explode("/", $_GET['views']);
			$respuesta = vistaModelo::obtener_vistas_modelo($ruta[0]);
		}else{
			$respuesta = "login";
		}
		return $respuesta;
	}
}
?>