<?php
if ($peticionAjax) {
	require_once '../modelos/usuarioModelo.php';
}else{
	require_once './modelos/usuarioModelo.php';
}

class usuarioControlador extends usuarioModelo {
	/*====controlador para agregar usuario====*/
	public function agregar_usuario_controlador(){
		$dni = mainModel::lipiar_cadena($_POST['usuario_dni_reg']);
		$nombre = mainModel::lipiar_cadena($_POST['usuario_nombre_reg']);
		$apellido = mainModel::lipiar_cadena($_POST['usuario_apellido_reg']);
		$telefono = mainModel::lipiar_cadena($_POST['usuario_telefono_reg']);
		$direccion = mainModel::lipiar_cadena($_POST['usuario_direccion_reg']);


		$usuario = mainModel::lipiar_cadena($_POST['usuario_usuario_reg']);
		$email = mainModel::lipiar_cadena($_POST['usuario_email_reg']);
		$clave1 = mainModel::lipiar_cadena($_POST['usuario_clave_1_reg']);
		$clave2 = mainModel::lipiar_cadena($_POST['usuario_clave_2_reg']);

		$privilegio = mainModel::lipiar_cadena($_POST['usuario_privilegio_reg']);

		/*============Comprobar si hay camposo vacio====*/
		if ($dni=="" || $nombre=="" || $apellido=="" || $usuario=="" || $clave1=="" || $clave2=="") {
			$alerta = [
				"Alerta"=>"simple",
				"Titulo"=>"Ocurrio un error inesperado",
				"Texto"=>"No has llenado todos los campos que son obligatorios",
				"Tipo"=>"error"
			];
			echo json_encode($alerta);
			exit();
		}

		/*=========Verficar la integridad de los datos======*/
		if (mainModel::verificar_datos("[0-9-]{8,20}",$dni)) {
			$alerta = [
				"Alerta"=>"simple",
				"Titulo"=>"Ocurrio un error inesperado",
				"Texto"=>"El DNI no coincide con el formato solicitado",
				"Tipo"=>"error"
			];
			echo json_encode($alerta);
			exit();
		}

		if (mainModel::verificar_datos("[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,35}",$nombre)) {
			$alerta = [
				"Alerta"=>"simple",
				"Titulo"=>"Ocurrio un error inesperado",
				"Texto"=>"El Nombre no coincide con el formato solicitado",
				"Tipo"=>"error"
			];
			echo json_encode($alerta);
			exit();
		}

		if (mainModel::verificar_datos("[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,35}",$apellido)) {
			$alerta = [
				"Alerta"=>"simple",
				"Titulo"=>"Ocurrio un error inesperado",
				"Texto"=>"El APELLIDO no coincide con el formato solicitado",
				"Tipo"=>"error"
			];
			echo json_encode($alerta);
			exit();
		}

		if ($telefono != "") {
			if (mainModel::verificar_datos("[0-9()+]{8,20}",$telefono)) {
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

		if ($direccion != "") {
			if (mainModel::verificar_datos("[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ().,#\- ]{3,190}",$direccion)) {
				$alerta = [
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrio un error inesperado",
					"Texto"=>"La DIRECCION no coincide con el formato solicitado",
					"Tipo"=>"error"
				];
				echo json_encode($alerta);
				exit();
			}
		}

		if (mainModel::verificar_datos("[a-zA-Z0-9]{1,35}",$usuario)) {
			$alerta = [
				"Alerta"=>"simple",
				"Titulo"=>"Ocurrio un error inesperado",
				"Texto"=>"El NOMDE DE USUARIO no coincide con el formato solicitado",
				"Tipo"=>"error"
			];
			echo json_encode($alerta);
			exit();
		}

		if (mainModel::verificar_datos("[a-zA-Z0-9$@.-]{7,100}",$clave1) || mainModel::verificar_datos("[a-zA-Z0-9$@.-]{7,100}",$clave2)) {
			$alerta = [
				"Alerta"=>"simple",
				"Titulo"=>"Ocurrio un error inesperado",
				"Texto"=>"Las CONTRASEÑAS no coinciden con el formato solicitado",
				"Tipo"=>"error"
			];
			echo json_encode($alerta);
			exit();
		}

		/*==========Comprobar que el DNI sea unicos========*/
		$unique_dni = mainModel::ejecutar_consulta_simple("SELECT usuario_dni FROM usuario WHERE usuario_dni ='$dni'");
		if ($unique_dni->rowCount() > 0) {
			$alerta = [
				"Alerta"=>"simple",
				"Titulo"=>"Ocurrio un error inesperado",
				"Texto"=>"El DNI ingresado ya se encuentra registrado en el sistema",
				"Tipo"=>"error"
			];
			echo json_encode($alerta);
			exit();
		}

		/*==========Comprobar que el NOMBRE DE USUARIO sea unicos========*/
		$unique_usuario = mainModel::ejecutar_consulta_simple("SELECT usuario_usuario FROM usuario WHERE usuario_usuario = '$usuario'");
		if ($unique_usuario->rowCount() > 0) {
			$alerta = [
				"Alerta"=>"simple",
				"Titulo"=>"Ocurrio un error inesperado",
				"Texto"=>"El NOMBRE DE USUARIO ingresado ya se encuentra registrado en el sistema",
				"Tipo"=>"error"
			];
			echo json_encode($alerta);
			exit();
		}

		/*==========VALIDAR EL EMAIL y que sea unico===========*/
		if ($email != "") {
			if (filter_var($email,FILTER_VALIDATE_EMAIL)) {
				$unique_email = mainModel::ejecutar_consulta_simple("SELECT usuario_email FROM usuario WHERE usuario_email = '$email'");
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

		/*====COMPROBAR QUE LA CLAVE1 Y 2 SEAN IGUALES=====*/
		if ($clave1 != $clave2) {
			$alerta = [
				"Alerta"=>"simple",
				"Titulo"=>"Ocurrio un error inesperado",
				"Texto"=>"Las CONTRASEÑAS que ingreso no coinciden ",
				"Tipo"=>"error"
			];
			echo json_encode($alerta);
			exit();
		}else{
			$clave = mainModel::encryption($clave1);
		}
		/*====VALIDAR EL PRIVILEGIO=====*/
		if ($privilegio<1 || $privilegio>3) {
			$alerta = [
				"Alerta"=>"simple",
				"Titulo"=>"Ocurrio un error inesperado",
				"Texto"=>"El PRIVILEGIO selecionado no es valido",
				"Tipo"=>"error"
			];
			echo json_encode($alerta);
			exit();
		}
		/*========recibir datos=========*/

		$datos_usuario_reg = [
			"DNI"=>$dni,
			"Nombre"=>$nombre,
			"Apellido"=>$apellido,
			"Telefono"=>$telefono,
			"Direccion"=>$direccion,

			"Email"=>$email,
			"Usuario"=>$usuario,
			"Clave"=>$clave,
			"Estado"=>"1",
			"Privilegio"=>$privilegio
		];

		//Modelo de guardar usuario
		$agregar_usuario = usuarioModelo::agregar_usuario_modelo($datos_usuario_reg);

		if ($agregar_usuario->rowCount() == 1 ) {
			$alerta = [
				"Alerta"=>"limpiar",
				"Titulo"=>"Usuario registrado",
				"Texto"=>"Datos del usuario han sido registrado exitosamente",
				"Tipo"=>"success"
			];
		}else{
			$alerta = [
				"Alerta"=>"simple",
				"Titulo"=>"Ocurrio un error inesperado",
				"Texto"=>"No hemos podido reguistrar el usuario",
				"Tipo"=>"error"
			];
		}
		echo json_encode($alerta);
	} /*=FIN DEL CONTROLADOR=*/

	/*===paginar los usuario==*/
	public function paginador_usuario_controlador($pagina,$registros,$privilegio,$id,$url,$busqueda){

		$pagina = mainModel::lipiar_cadena($pagina);
		$registros = mainModel::lipiar_cadena($registros);
		$privilegio = mainModel::lipiar_cadena($privilegio);
		$id = mainModel::lipiar_cadena($id);

		$url = mainModel::lipiar_cadena($url);
		$url = SERVER_URL.$url."/";

		$busqueda = mainModel::lipiar_cadena($busqueda);
		$tabla = "";

		$pagina = (isset($pagina) && $pagina > 0) ? (int)$pagina : 1;
		$inicio = ($pagina > 0) ? (($pagina*$registros)- $registros) : 0 ;

		if (isset($busqueda) && $busqueda!="") {
			$consulta = "SELECT SQL_CALC_FOUND_ROWS * FROM usuario WHERE ((usuario_id !='$id' AND usuario_id != '1') AND (usuario_dni LIKE '%$busqueda%' OR usuario_nombre LIKE '%$busqueda%' OR usuario_apellido LIKE '%$busqueda%' OR usuario_usuario LIKE '%$busqueda%' OR usuario_telefono LIKE '%$busqueda%' OR usuario_email LIKE '%$busqueda%')) ORDER BY usuario_id DESC LIMIT $inicio, $registros";
		}else{
			$consulta = "SELECT SQL_CALC_FOUND_ROWS * FROM usuario WHERE usuario_id !='$id' AND usuario_id != '1' ORDER BY usuario_id DESC LIMIT $inicio, $registros";
		}

		$conexion = mainModel::conectar();
		$datos = $conexion->query($consulta);
		$datos = $datos->fetchAll();

		$total = $conexion->query("SELECT FOUND_ROWS()");
		$total = (int) $total->fetchColumn();

		$Numero_paginas = ceil($total/$registros);

		$tabla.= '<div class="table-responsive">
			<table class="table table-dark table-sm">
				<thead>
					<tr class="text-center roboto-medium">
						<th>#</th>
						<th>DNI</th>
						<th>NOMBRE</th>
						<th>APELLIDO</th>
						<th>TELÉFONO</th>
						<th>USUARIO</th>
						<th>EMAIL</th>
						<th>ACTUALIZAR</th>
						<th>ELIMINAR</th>
					</tr>
				</thead>
				<tbody>';
			if ($total >= 1 && $pagina <= $Numero_paginas) {
				$contador = $inicio+1;
				$registro_inicio = $inicio+1;
				foreach ($datos as $rows) {
					$tabla.= '
					<tr class="text-center" >
						<td>'.$contador.'</td>
						<td>'. $rows['usuario_dni'].'</td>
						<td>'. $rows['usuario_nombre'].'</td>
						<td>'. $rows['usuario_apellido'].'</td>
						<td>'. $rows['usuario_telefono'].'</td>
						<td>'. $rows['usuario_usuario'].'</td>
						<td>'. $rows['usuario_email'].'</td>
						<td>
							<a href="'.SERVER_URL.'user-update/'.mainModel::encryption($rows['usuario_id']).'/" class="btn btn-success">
									<i class="fas fa-sync-alt"></i>
							</a>
						</td>
						<td>
							<form class="FormularioAjax" action="'.SERVER_URL.'ajax/usuarioAjax.php" method="POST" data-form="delete" autocomplete="off">
							    <input type="hidden" name="usuario_id_del" value="'.mainModel::encryption($rows['usuario_id']).'"/>
								<button type="submit" class="btn btn-warning">
										<i class="far fa-trash-alt"></i>
								</button>
							</form>
						</td>
					</tr>';
					$contador++;
				}
				$registro_final = $contador-1;
			}else{
				if($total >= 1){
					$tabla.= '<tr class="text-center" >
						          <td colspan="9">
							         <a href="'.$url.'" class="btn btn-raised btn-primary btn-sm">Haga clik aca para recargar el estado
							         <a/>
						          </td>
							</tr';
				}else{
					$tabla.= '<tr class="text-center"><td colspan="9">No hay registros en el sistema</td></tr>';
				}
			}
		$tabla.= '</tbody></table></div>';

		/*---- programamos la botonera con un condicional----*/
		if ($total >= 1 && $pagina <= $Numero_paginas) {
			 $tabla.= '<p class="text-right">Mostrar Usuarios '.$registro_inicio.' - '.$registro_final.' de un total de '.$total.'</p>';
			$tabla.= mainModel::paginador_tablas($pagina,$Numero_paginas,$url,7);
		}
		return $tabla;
	}/*=FIN DEL CONTROLADOR=*/

	/*===controlador para eliminar el usuari====*/
	public function eliminar_usuario_controlado(){
		/* recibiendo ID del usuario*/
		$id = mainModel::decryption($_POST['usuario_id_del']);
		$id = mainModel::lipiar_cadena($id);

		/* comprobando el usuario principal*/
		if ($id == 1) {
			$alerta = [
				"Alerta"=>"simple",
				"Titulo"=>"Ocurrio un error inesperado",
				"Texto"=>"No podemos eliminara al usuario principal del sistema",
				"Tipo"=>"error"
			];
			echo json_encode($alerta);
			exit();
		}

		/*comprobando el usuario en DB*/
		$check_usuario = mainModel::ejecutar_consulta_simple("SELECT usuario_id FROM usuario WHERE usuario_id = '$id' ");

		if ($check_usuario->rowCount() <= 0) {
			$alerta = [
				"Alerta"=>"simple",
				"Titulo"=>"Ocurrio un error inesperado",
				"Texto"=>"El usuario que intente eliminar no existe en el sisitema",
				"Tipo"=>"error"
			];
			echo json_encode($alerta);
			exit();
		}

		/*comprobando los prestamos en DB*/
		$check_prestamos = mainModel::ejecutar_consulta_simple("SELECT usuario_id FROM prestamo WHERE usuario_id = '$id' LIMIT 1");

		if ($check_prestamos->rowCount() > 0) {
			$alerta = [
				"Alerta"=>"simple",
				"Titulo"=>"Ocurrio un error inesperado",
				"Texto"=>"No podemos eliminar ests usuario debido a que tiene prestamos asociados, recomendamos deshabilitar el usuario si ya no sera utilizado",
				"Tipo"=>"error"
			];
			echo json_encode($alerta);
			exit();
		}

		/*comprobando privilegios */
		session_start(['name'=>'SPM']);
		if ($_SESSION['privilegio_spm'] != 1) {
			$alerta = [
				"Alerta"=>"simple",
				"Titulo"=>"Ocurrio un error inesperado",
				"Texto"=>"No tienes los perisos suficientes como para realizar esta operacion",
				"Tipo"=>"error"
			];
			echo json_encode($alerta);
			exit();
		}

		$eliminar_usuario = usuarioModelo::eliminar_usuario_modelo($id);

		if ($eliminar_usuario->rowCount() == 1) {
			$alerta = [
				"Alerta"=>"recargar",
				"Titulo"=>"Usuario eliminado",
				"Texto"=>"El usuario a sido eliminado con exito",
				"Tipo"=>"success"
			];
		}else{
			$alerta = [
				"Alerta"=>"simple",
				"Titulo"=>"Ocurrio un error inesperado",
				"Texto"=>"No hemos podido eliminar el usuario, porfavor intente nuevamente",
				"Tipo"=>"error"
			];
		}
		echo json_encode($alerta);
	}/*==FIN DEL CONTROLADOR==*/

	/*========controlador datos del usuario y conteo =====*/
	public function datos_usuario_controlador($tipo, $id){
		$tipo = mainModel::lipiar_cadena($tipo);

		$id = mainModel::decryption($id);
		$id = mainModel::lipiar_cadena($id);
		/*retornamos el modelo*/
		return usuarioModelo::datos_usuario_modelo($tipo, $id);
	}/*==FIN DEL CONTROLADOR==*/

	/*==controlador actualizar usuario==*/
	public function actualizar_usuario_controller(){
		//Recibiendo el id
		$id = mainModel::decryption($_POST['usuario_id_up']);
		$id = mainModel::lipiar_cadena($id);

		//Comprobar el usuario en la DB
		$check_user = mainModel::ejecutar_consulta_simple("SELECT * FROM usuario WHERE usuario_id = '$id' ");
		if($check_user ->rowCount() <= 0){
			$alerta = [
				"Alerta"=>"simple",
				"Titulo"=>"Ocurrio un error inesperado",
				"Texto"=>"No hemos encontrado el usuario en el sistema",
				"Tipo"=>"error"
			];
			echo json_encode($alerta);
			exit();
		}else{
			//SACAMOS LOS DATOS DEL USUARIO
			$campos = $check_user ->fetch();
		}

		$dni = mainModel::lipiar_cadena($_POST['usuario_dni_up']);
		$nombre = mainModel::lipiar_cadena($_POST['usuario_nombre_up']);
		$apellidos = mainModel::lipiar_cadena($_POST['usuario_apellido_up']);
		$telefono = mainModel::lipiar_cadena($_POST['usuario_telefono_up']);
		$direccion = mainModel::lipiar_cadena($_POST['usuario_direccion_up']);

		$usuario = mainModel::lipiar_cadena($_POST['usuario_usuario_up']);
		$email = mainModel::lipiar_cadena($_POST['usuario_email_up']);
		/*--verificar si el usuario tiene algunos permisos para editar --*/
		if(isset($_POST['usuario_estado_up'])){
			$estado = mainModel::lipiar_cadena($_POST['usuario_estado_up']);
		}else{
			$estado = $campos['usuario_estado'];
		}

		if(isset($_POST['usuario_privilegio_up'])){
			$privilegio = mainModel::lipiar_cadena($_POST['usuario_privilegio_up']);
		}else{
			$privilegio = $campos['usuario_privilegio'];
		}

		/*verificar contraseña y el usuario para guardar los datos*/
		$admin_usuario = mainModel::lipiar_cadena($_POST['usuario_admin']);
		$admin_clave = mainModel::lipiar_cadena($_POST['clave_admin']);

		/*--pasar datopar guardarlo el tipo cd cuneta--*/
		$tipo_de_cuenta = mainModel::lipiar_cadena($_POST['tipo_cuenta']);

		if ($dni=="" || $nombre=="" || $apellidos=="" || $usuario=="" || $admin_usuario=="" || $admin_clave=="") {
			$alerta = [
				"Alerta"=>"simple",
				"Titulo"=>"Ocurrio un error inesperado",
				"Texto"=>"No has llenado todos los campos que son obligatorios",
				"Tipo"=>"error"
			];
			echo json_encode($alerta);
			exit();
		}

		/*=========Verficar la integridad de los datos======*/
		if (mainModel::verificar_datos("[0-9-]{8,20}",$dni)) {
			$alerta = [
				"Alerta"=>"simple",
				"Titulo"=>"Ocurrio un error inesperado",
				"Texto"=>"El DNI no coincide con el formato solicitado",
				"Tipo"=>"error"
			];
			echo json_encode($alerta);
			exit();
		}

		if (mainModel::verificar_datos("[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,35}",$nombre)) {
			$alerta = [
				"Alerta"=>"simple",
				"Titulo"=>"Ocurrio un error inesperado",
				"Texto"=>"El Nombre no coincide con el formato solicitado",
				"Tipo"=>"error"
			];
			echo json_encode($alerta);
			exit();
		}

		if (mainModel::verificar_datos("[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,35}",$apellidos)) {
			$alerta = [
				"Alerta"=>"simple",
				"Titulo"=>"Ocurrio un error inesperado",
				"Texto"=>"El APELLIDO no coincide con el formato solicitado",
				"Tipo"=>"error"
			];
			echo json_encode($alerta);
			exit();
		}

		if ($telefono != "") {
			if (mainModel::verificar_datos("[0-9()+]{8,20}",$telefono)) {
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

		if ($direccion != "") {
			if (mainModel::verificar_datos("[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ().,#\- ]{3,190}",$direccion)) {
				$alerta = [
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrio un error inesperado",
					"Texto"=>"La DIRECCION no coincide con el formato solicitado",
					"Tipo"=>"error"
				];
				echo json_encode($alerta);
				exit();
			}
		}

		if (mainModel::verificar_datos("[a-zA-Z0-9]{1,35}",$usuario)) {
			$alerta = [
				"Alerta"=>"simple",
				"Titulo"=>"Ocurrio un error inesperado",
				"Texto"=>"El NOMDE DE USUARIO no coincide con el formato solicitado",
				"Tipo"=>"error"
			];
			echo json_encode($alerta);
			exit();
		}

		if (mainModel::verificar_datos("[a-zA-Z0-9]{1,35}",$admin_usuario)) {
			$alerta = [
				"Alerta"=>"simple",
				"Titulo"=>"Ocurrio un error inesperado",
				"Texto"=>"TU NOMDE DE USUARIO no coincide con el formato solicitado",
				"Tipo"=>"error"
			];
			echo json_encode($alerta);
			exit();
		}

		if (mainModel::verificar_datos("[a-zA-Z0-9$@.-]{7,100}",$admin_clave)) {
			$alerta = [
				"Alerta"=>"simple",
				"Titulo"=>"Ocurrio un error inesperado",
				"Texto"=>"TU CLAVE no coincide con el formato solicitado",
				"Tipo"=>"error"
			];
			echo json_encode($alerta);
			exit();
		}

		$admin_clave = mainModel::encryption($admin_clave);

		if ($privilegio < 1 || $privilegio >3){
			$alerta = [
				"Alerta"=>"simple",
				"Titulo"=>"Ocurrio un error inesperado",
				"Texto"=>"El privilegio no tiene un valor valido",
				"Tipo"=>"error"
			];
			echo json_encode($alerta);
			exit();
		}

		if($estado != 1 && $estado != 0){
			$alerta = [
				"Alerta"=>"simple",
				"Titulo"=>"Ocurrio un error inesperado",
				"Texto"=>"El estado de la cuenta no coincide con el formato solicitado",
				"Tipo"=>"error"
			];
			echo json_encode($alerta);
			exit();
		}

		/*==Comprobar que el DNI sea unicos==*/
		if($dni != $campos['usuario_dni']){
			$unique_dni = mainModel::ejecutar_consulta_simple("SELECT usuario_dni FROM usuario WHERE usuario_dni ='$dni'");
			if ($unique_dni->rowCount() > 0) {
				$alerta = [
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrio un error inesperado",
					"Texto"=>"El DNI ingresado ya se encuentra registrado en el sistema",
					"Tipo"=>"error"
				];
				echo json_encode($alerta);
				exit();
			}
		}

		/*==Comprobar que el NOMBRE DE USUARIO sea unicos==*/
		if($usuario!= $campos['usuario_usuario']){
			$unique_usuario = mainModel::ejecutar_consulta_simple("SELECT usuario_usuario FROM usuario WHERE usuario_usuario = '$usuario'");
			if ($unique_usuario->rowCount() > 0) {
				$alerta = [
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrio un error inesperado",
					"Texto"=>"El NOMBRE DE USUARIO ingresado ya se encuentra registrado en el sistema",
					"Tipo"=>"error"
				];
				echo json_encode($alerta);
				exit();
			}
		}

		/*==Comprobar el email==*/
		if($email != $campos['usuario_email'] && $email != ""){
			if(filter_var($email,FILTER_VALIDATE_EMAIL)){
				$check_email = mainModel::ejecutar_consulta_simple("SELECT usuario_email FROM usuario WHERE usuario_email = '$email'");
				if ($check_email->rowCount() > 0) {
					$alerta = [
						"Alerta"=>"simple",
						"Titulo"=>"Ocurrio un error inesperado",
						"Texto"=>"El nuevo email ingresado ya se encuetra registrado en el sistema!!!",
						"Tipo"=>"error"
					];
					echo json_encode($alerta);
					exit();
				}
			}else{
				$alerta = [
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrio un error inesperado",
					"Texto"=>"Ha ingresado un correo no valido!!!!",
					"Tipo"=>"error"
				];
				echo json_encode($alerta);
				exit();
			}
		}

		/*==Comprobar la claveo o contraseña==*/
		$pass1 = $_POST['usuario_clave_nueva_1'];
		$pass2 = $_POST['usuario_clave_nueva_2'];
		if($pass1!="" || $pass2!= ""){
			if($pass1 != $pass2){
				$alerta = [
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrio un error inesperado",
					"Texto"=>"Las nuevas claves o contraseñas no coinciden",
					"Tipo"=>"error"
				];
				echo json_encode($alerta);
				exit();
			}else{
				if(mainModel::verificar_datos("[a-zA-Z0-9$@.-]{7,100}",$pass1) || mainModel::verificar_datos("[a-zA-Z0-9$@.-]{7,100}",$pass2 )){
					$alerta = [
						"Alerta"=>"simple",
						"Titulo"=>"Ocurrio un error inesperado",
						"Texto"=>"Las nuevas claves o contraseñas no coinciden con el formato solicitado",
						"Tipo"=>"error"
					];
					echo json_encode($alerta);
					exit();
				}
				$clave = mainModel::encryption($pass1);
			}
		}else{
			$clave = $campos['usuario_clave'];
		}

		/*==Comprobar las credenciales para guardar los datos==*/
		if($tipo_de_cuenta == "Propia"){
			$check_cuenta = mainModel::ejecutar_consulta_simple("SELECT usuario_id FROM usuario WHERE usuario_usuario = '$admin_usuario' AND usuario_clave ='$admin_clave' AND usuario_id = '$id'");
		}else{
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
			$check_cuenta = mainModel::ejecutar_consulta_simple("SELECT usuario_id FROM usuario WHERE usuario_usuario = '$admin_usuario' AND usuario_clave ='$admin_clave'");
		}

		/*Contar cuantas veces fue guardados los datos*/
		if($check_cuenta->rowCount() <= 0){
			$alerta = [
				"Alerta"=>"simple",
				"Titulo"=>"Ocurrio un error inesperado",
				"Texto"=>"Nombre y clave del administrador no validos",
				"Tipo"=>"error"
			];
			echo json_encode($alerta);
			exit();
		}

		/*=============PREPARANDODATOS PARA MANDARLOS AL MODELO==========*/
		$datos_usuario_up = [
			"DNI" => $dni,
			"NOMBRE" => $nombre,
			"APELLIDO" => $apellidos,
			"TELEFONO" => $telefono,
			"DIRECCION" => $direccion,
			"EMAIL" => $email,
			"USUARIO" => $usuario,
			"ESTADO" => $estado,
			"CLAVE" => $clave,
			"PRIVILEGIO" => $privilegio,
			"ID" => $id
		];

		if(usuarioModelo::actualizar_usuario_modelo($datos_usuario_up)){
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

	}/*==FIN DEL CONTROLADOR==*/

}