<?php
	 class vistaModelo{
	 	//creamos el Metodo que permirita ver las vistas en el index(MODELO PARA OBTENER LAS VISTAS)
	 	protected static function obtener_vistas_modelo($vista){
	 		//lista de palabras que se usaran en la URL
	 		$listaBlanca = ["home", "client-list", "client-new", "client-search", "client-update", "company", "reservation-new", "item-list", "item-new", "item-search", "item-update", "reservation-list", "reservation-new", "reservation-pending", "reservation-reservation", "reservation-reservation", "reservation-search", "reservation-search", "reservation-update", "user-list" ,"user-new", "user-search", "user-update", "email-new"];
	 		//comprobar si la vista existe
	 		if (in_array($vista, $listaBlanca)) {
	 			if (is_file("./vistas/contenidos/".$vista."-view.php")) {
	 				$contenido = "./vistas/contenidos/".$vista."-view.php";
	 			}else{
	 				$contenido = "404";
	 			}
	 		}elseif($vista == "login" || $vista == "index"){
	 			$contenido = "login";
	 		}else{
	 			$contenido = "404";
	 		}
	 		//devolvemos el valor de contenido
	 		return $contenido;
	 	}
	 }
?>