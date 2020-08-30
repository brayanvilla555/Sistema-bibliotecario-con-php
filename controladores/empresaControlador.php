<?php
if($peticionAjax){
	require_once '../modelos/empresaModelo.php';
}else{
	require_once './modelos/empresaModelo.php';
}

class empresaControlador extends empresaModelo{
	public function agregar_empresa_controlador(){
		$nombre = mainModel::lipiar_cadena($_POST['empresa_nombre_reg']);
		$correo = mainModel::lipiar_cadena($_POST['empresa_email_reg']);
		$telefono = mainModel::lipiar_cadena($_POST['empresa_telefono_reg']);
		$direccion = mainModel::lipiar_cadena($_POST['empresa_direccion_reg']);
		/*validar los datos*/
		if($nombre == ""){
			$alerta = [
				"Alerta" => "simpre",
				"Titulo" => "Ocurrio un error inesperado",
				"Texto" => "No has llenado todos los campos necesarios",
				"tipo" => "error"
			];
			echo json_encode($alerta);
			exit();
		}

		if(mainModel::verificar_datos("[a-zA-z0-9áéíóúÁÉÍÓÚñÑ. ]{1,70}", $nombre)){
			$alerta = [
				"Alerta" => "simpre",
				"Titulo" => "Ocurrio un error inesperado",
				"Texto" => "El nombre no coincide con el formato solicitado",
				"tipo" => "error"
			];
			echo json_encode($alerta);
			exit();
		}

		if($correo != ""){
			if(filter_var($correo, FILTER_VALIDATE_EMAIL)){
				$unique_email = mainModel::ejecutar_consulta_simple("SELECT empresa_email FROM empresa WHERE empresa_email = '$email'");
				if ($unique_email->rowCount() > 0) {
					$alerta = [
						"Alerta"=>"simple",
						"Titulo"=>"Ocurrio un error inesperado",
						"Texto"=>"El EMAIL ingresado ya se encuentra registrado en el sistema",
						"Tipo"=>"error"
					];
					echo json_encode($alerta);
					exit();
				}
			}else{
				$alerta = [
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrio un error inesperado",
					"Texto"=>"Ha ingresado un EMAIL no valido",
					"Tipo"=>"error"
				];
				echo json_encode($alerta);
				exit();
			}
		}

		if($telefono != ""){
			if(mainModel::verificar_datos("[0-9()+]{8,20}", $telefono)){
				$alerta = [
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrio un error inesperado",
					"Texto"=>"El TELEFONO no coincide con el formato solicitado",
					"Tipo"=>"error"
				];
				echo json_encode($alerta);
				exit();
			}
		}

		if($direccion != ""){
			if(mainModel::verificar_datos("[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ().,#\- ]{1,190}", $direccion)){
				$alerta = [
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrio un error inesperado",
					"Texto"=>"La direccion no coincide con el formato solicitado",
					"Tipo"=>"error"
				];
				echo json_encode($alerta);
				exit();
			}
		}

		/*recogemos los datos*/
		$datos_empresa = [
			"ID" => '1',
			"NOMBRE" => $nombre,
			"EMAIL" => $correo,
			"TELEFONO" => $telefono,
			"DIRECCION" => $direccion
		];

		$agrgar_empresa = empresaModelo::agregar_empresa_modelo($datos_empresa);

		if($agrgar_empresa->rowCount() == 1){
			$alerta = [
				"Alerta"=>"recargar",
				"Titulo"=>"Usuario registrado",
				"Texto"=>"Datos de la empresa han sido registrado exitosamente",
				"Tipo"=>"success"
			];
		}else{
			$alerta = [
				"Alerta"=>"simple",
				"Titulo"=>"Ocurrio un error inesperado",
				"Texto"=>"No hemos podido guardar los datos de la empresa. Intentalo nuevamente",
				"Tipo"=>"error"
			];
		}
		echo json_encode($alerta);
	}/*fin del controlador*/

	/*mostrar datos de le empresa*/
	public function datos_empresa_controlador(){
		return empresaModelo::datos_empresa_modelo();
	}/*fin del controlador*/

	/*actualizar datos de la empresa*/
	public function actualizar_empresa_controlador(){
		$id = mainModel::decryption($_POST['empresa_id_up']);
		$id = mainModel::lipiar_cadena($id);

		$check_empresa = mainModel::ejecutar_consulta_simple("SELECT * FROM empresa WHERE empresa_id = '$id' ");
		if($check_empresa->rowCount() <= 0 && $id != 1){
			$alerta = [
				"Alerta"=>"simple",
				"Titulo"=>"Ocurrio un error inesperado",
				"Texto"=>"Al parecer usted está manipulando algo inadecuado del sistema",
				"Tipo"=>"error"
			];
			echo json_encode($alerta);
			exit();
		}else{
			$campos = $check_empresa->fetch();
		}

		$nombre = mainModel::lipiar_cadena($_POST['empresa_nombre_up']);
		$correo = mainModel::lipiar_cadena($_POST['empresa_email_up']);
		$telefono = mainModel::lipiar_cadena($_POST['empresa_telefono_up']);
		$direccion = mainModel::lipiar_cadena($_POST['empresa_direccion_up']);

		/*validar campos*/
		if($nombre==""){
			$alerta = [
				"Alerta"=>"simple",
				"Titulo"=>"Ocurrio un error inesperado",
				"Texto"=>"No has llenado todos los campos que son obligatorios",
				"Tipo"=>"error"
			];
			echo json_encode($alerta);
			exit();
		}

		if(mainModel::verificar_datos("[a-zA-z0-9áéíóúÁÉÍÓÚñÑ. ]{1,70}", $nombre)){
			$alerta = [
				"Alerta" => "simpre",
				"Titulo" => "Ocurrio un error inesperado",
				"Texto" => "El nombre no coincide con el formato solicitado",
				"tipo" => "error"
			];
			echo json_encode($alerta);
			exit();
		}

		if($correo != $campos['empresa_email'] && $correo != ""){
			if(filter_var($correo, FILTER_VALIDATE_EMAIL)){
				$unique_email = mainModel::ejecutar_consulta_simple("SELECT empresa_email FROM empresa WHERE empresa_email = '$correo'");
				if ($unique_email->rowCount() > 0) {
					$alerta = [
						"Alerta"=>"simple",
						"Titulo"=>"Ocurrio un error inesperado",
						"Texto"=>"El EMAIL ingresado ya se encuentra registrado en el sistema",
						"Tipo"=>"error"
					];
					echo json_encode($alerta);
					exit();
				}
			}else{
				$alerta = [
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrio un error inesperado",
					"Texto"=>"Ha ingresado un EMAIL no valido",
					"Tipo"=>"error"
				];
				echo json_encode($alerta);
				exit();
			}
		}

		if($telefono != ""){
			if(mainModel::verificar_datos("[0-9()+]{8,20}", $telefono)){
				$alerta = [
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrio un error inesperado",
					"Texto"=>"El TELEFONO no coincide con el formato solicitado",
					"Tipo"=>"error"
				];
				echo json_encode($alerta);
				exit();
			}
		}

		if($direccion != ""){
			if(mainModel::verificar_datos("[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ().,#\- ]{1,190}", $direccion)){
				$alerta = [
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrio un error inesperado",
					"Texto"=>"La direccion no coincide con el formato solicitado",
					"Tipo"=>"error"
				];
				echo json_encode($alerta);
				exit();
			}
		}

		session_start(['name'=>'SPM']);
		if($_SESSION['privilegio_spm'] != 1){
			$alerta = [
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrio un error inesperado",
					"Texto"=>"No tienes los permisos necesarios para desarrollar esta operacion.",
					"Tipo"=>"error"
				];
				echo json_encode($alerta);
				exit();
		}

		/*recogemos los datos*/
		$datos_empresa_up = [
			"NOMBRE" => $nombre,
			"EMAIL" => $correo,
			"TELEFONO" => $telefono,
			"DIRECCION" => $direccion,
			"ID" => $id
		];

		/*actualizar los datos*/
		if(empresaModelo::actualizar_empresa_modelo($datos_empresa_up)){
			$alerta = [
				"Alerta"=>"recargar",
				"Titulo"=>"Datos actualizados",
				"Texto"=>"Los datos se actualizados con exito",
				"Tipo"=>"success"
			];
		}else{
			$alerta = [
				"Alerta"=>"simple",
				"Titulo"=>"Ocurrio un error inesperado",
				"Texto"=>"No hemos podido actualizar los datos, Intente nuevamente.",
				"Tipo"=>"error"
			];
		}
		echo json_encode($alerta);


	}/*fin del controlador*/
}