<?php
	session_start(['name'=>'SPM']);
	require_once "../config/APP.php";
	if(isset($_POST['busqueda_inicial']) || isset($_POST['eliminar_busqueda']) || isset($_POST['busqueda_inicio_prestamo']) || isset($_POST['busqueda_final_prestamo'])){

		$data_url = [
			"usuario" => "user-search",
			"cliente" => "client-search",
			"item" => "item-search",
			"prestamo" => "reservation-search"
		];

		if(isset($_POST['modulo'])){
			$modulo = $_POST['modulo'];
				if(!isset($data_url[$modulo])){
					$alerta = [
						"Alerta"=>"simple",
						"Titulo"=>"Ocurrio un error inesperado",
						"Texto"=>"No podemos continuar con la busqueda devido a un error.	",
						"Tipo"=>"error"
						];
					echo json_encode($alerta);
					exit();
			}
		}else{
			$alerta = [
				"Alerta"=>"simple",
				"Titulo"=>"Ocurrio un error inesperado",
				"Texto"=>"No podemos continuar con la busqueda debido a un error de configuración",
				"Tipo"=>"error"
			];
			echo json_encode($alerta);
			exit();
		}

		/*== crear las variables de session de la busqueda y eliminarlas==*/
		if($modulo == "prestamo"){
			$fecha_inicio = "fecha_inicio_".$modulo;
			$fecha_final = "fecha_final_".$modulo;

			/*iniciar la busqueda con las barriables de session*/
			if(isset($_POST['busqueda_inicio_prestamo']) || isset($_POST['busqueda_final_prestamo'])){

				if($_POST['busqueda_inicio_prestamo'] == "" || $_POST['busqueda_final_prestamo'] == ""){
					$alerta = [
						"Alerta"=>"simple",
						"Titulo"=>"Ocurrio un error inesperado",
						"Texto"=>"Por favor introduce una fecha de inicio y una fecha final.",
						"Tipo"=>"error"
					];
					echo json_encode($alerta);
					exit();
				}
				/*si contiene ambas fechas*/
				$_SESSION[$fecha_inicio] = $_POST['busqueda_inicio_prestamo'];
				$_SESSION[$fecha_final] = $_POST['busqueda_final_prestamo'] ;
			}

			/*eliminar la busqueda*/
			if(isset($_POST['eliminar_busqueda'])){
				unset($_SESSION[$fecha_inicio]);
				unset($_SESSION[$fecha_final]);
			}

		}else{
			$name_var = "busqueda_".$modulo;

			/*iniciar la busqueda*/
			if(isset($_POST['busqueda_inicial'])){
				if($_POST['busqueda_inicial'] ==""){
					$alerta = [
						"Alerta"=>"simple",
						"Titulo"=>"Ocurrio un error inesperado",
						"Texto"=>"Porfavor introducir un termino para buscar.",
						"Tipo"=>"error"
					];
					echo json_encode($alerta);
					exit();
				}

				$_SESSION[$name_var] = $_POST['busqueda_inicial'];
			}

			/*eliminar busqueda*/
			if(isset($_POST['eliminar_busqueda'])){
				unset($_SESSION[$name_var]);
			}
		}

		/* redireccionar*/
		$url = $data_url[$modulo];

		$alerta = [
			"Alerta" => "redireccionar",
			"URL" => SERVER_URL.$url."/"
		];
		echo json_encode($alerta);

	}else{
		session_start(['name'=>'SPM']);
		session_unset();
		session_destroy();
		header("Location: ".SERVER_URL."login/");
		exit();
	}