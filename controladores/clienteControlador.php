<?php
if($peticionAjax){
	require_once '../modelos/clienteModelo.php';
}else{
	require_once './modelos/clienteModelo.php';
}

class clienteControlador extends clienteModelo{
	public function agregar_cliente_controlador(){
		$dni = mainModel::lipiar_cadena($_POST['cliente_dni_reg']);
		$nombre = mainModel::lipiar_cadena($_POST['cliente_nombre_reg']);
		$apellido = mainModel::lipiar_cadena($_POST['cliente_apellido_reg']);
		$telefono = mainModel::lipiar_cadena($_POST['cliente_telefono_reg']);
		$direccion = mainModel::lipiar_cadena($_POST['cliente_direccion_reg']);

		/* validacion del formulario*/
		if($dni=="" || $nombre=="" || $apellido==""){
			$alerta = [
				"Alerta"=>"simple",
				"Titulo"=>"Ocurrio un error inesperado",
				"Texto"=>"No has llenado todos los campos que son obligatorios",
				"Tipo"=>"error"
			];
			echo json_encode($alerta);
			exit();
		}

		if(mainModel::verificar_datos("[0-9-]{1,27}", $dni)){
			$alerta = [
				"Alerta"=>"simple",
				"Titulo"=>"Ocurrio un error inesperado",
				"Texto"=>"El DNI no coincide con el formato solicitado",
				"Tipo"=>"error"
			];
			echo json_encode($alerta);
			exit();
		}

		/*comprobar si el dni es unico*/
		$unique_dni = mainModel::ejecutar_consulta_simple("SELECT cliente_dni FROM cliente WHERE cliente_dni = '$dni' ");
		if($unique_dni->rowCount() > 0){
			$alerta = [
				"Alerta"=>"simple",
				"Titulo"=>"Ocurrio un error inesperado",
				"Texto"=>"El DNI ingresado ya se encuentra registrado al parecer pertenece a otro usuario",
				"Tipo"=>"error"
			];
			echo json_encode($alerta);
			exit();
		}

		if(mainModel::verificar_datos("[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{1,40}", $nombre)){
			$alerta = [
				"Alerta"=>"simple",
				"Titulo"=>"Ocurrio un error inesperado",
				"Texto"=>"El Nombre no coincide con el formato solicitado",
				"Tipo"=>"error"
			];
			echo json_encode($alerta);
			exit();
		}

		if (mainModel::verificar_datos("[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{1,40}",$apellido)) {
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

		if($direccion != ""){
			if(mainModel::verificar_datos("[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ().,#\- ]{1,150}", $direccion)){
				$alerta = [
					"Alerta" => "simple",
					"Titulo" => "Ocurrio un error inesperado",
					"Texto" => "La dirección no coincide con el formato solicitado",
					"Tipo" => "error"
				];
			}
		}

		/*recogemos todos los datos*/
		$datos_cliente_reg = [
			"DNI" => $dni,
			"NOMBRE" => $nombre,
			"APELLIDO" => $apellido,
			"TELEFONO" => $telefono,
			"DIRECCION" => $direccion
		];

		/*obtenemos el modelo para guardar el usuario*/
		$agregar_cliente = clienteModelo::agregar_cliente_modelo($datos_cliente_reg);

		/*validar si se guardo o no*/
		if($agregar_cliente->rowCount() == 1){
			$alerta = [
				"Alerta"=>"limpiar",
				"Titulo"=>"Cliente registrado",
				"Texto"=>"Datos del cliente han sido registrado exitosamente",
				"Tipo"=>"success"
			];
		}else{
			$alerta = [
				"Alerta"=>"simple",
				"Titulo"=>"Ocurrio un error inesperado",
				"Texto"=>"No hemos podido reguistrar el cliente",
				"Tipo"=>"error"
			];
		}
		echo json_encode($alerta);
	}/*fin del controlador*/

	public function paginador_cliente_controlador($pagina, $registros, $privilegio, $id, $url, $busqueda){
		$pagina = mainModel::lipiar_cadena($pagina);
		$registros = mainModel::lipiar_cadena($registros);
		$privilegio = mainModel::lipiar_cadena($privilegio);
		$id = mainModel::lipiar_cadena($id);

		$url = mainModel::lipiar_cadena($url);
		$url = SERVER_URL.$url."/";

		$busqueda = mainModel::lipiar_cadena($busqueda);
		$tabla = "";

		$pagina = (isset($pagina) && $pagina > 0) ? (int)$pagina : 1;
		$inicio =  ($pagina > 0) ? (($pagina * $registros) - $registros) : 0;

		if(isset($busqueda) && $busqueda != ""){
			$consulta = "SELECT SQL_CALC_FOUND_ROWS * FROM cliente WHERE (
			cliente_dni LIKE '%$busqueda%' OR cliente_nombre LIKE '%$busqueda%' OR cliente_apellido LIKE '%$busqueda%' OR cliente_telefono LIKE '%$busqueda%' OR cliente_direccion LIKE '%$busqueda%') ORDER BY cliente_id DESC LIMIT $inicio, $registros";
		}else{
			$consulta = "SELECT SQL_CALC_FOUND_ROWS * FROM cliente ORDER BY cliente_id DESC LIMIT $inicio, $registros ";
		}

		$conexion = mainModel::conectar();
		$datos = $conexion->query($consulta);
		$datos = $datos->fetchAll();

		$total = $conexion->query("SELECT FOUND_ROWS()");
		$total = (int) $total->fetchColumn();

		$numero_paginas = ceil($total/$registros);

		$tabla .= '<div class="table-responsive">
			<table class="table table-dark table-sm">
				<thead>
					<tr class="text-center roboto-medium">
						<th>#</th>
						<th>DNI</th>
						<th>NOMBRE</th>
						<th>APELLIDO</th>
						<th>TELEFONO</th>
						<th>DIRECCIÓN</th>
						<th>ACTUALIZAR</th>
						<th>ELIMINAR</th>
					</tr>
				</thead>
				<tbody>';
		if($total >= 1 && $pagina <= $numero_paginas){
			$contador = $inicio + 1;
			$registro_inicio = $inicio + 1;

			foreach($datos as $rows){
				$tabla .= '
				<tr class="text-center" >
					<td>'.$contador.'</td>
					<td>'.$rows['cliente_dni'].'</td>
					<td>'.$rows['cliente_nombre'].'</td>
					<td>'.$rows['cliente_apellido'].'</td>
					<td>'.$rows['cliente_telefono'].'</td>
					<td>
						<button type="button" class="btn btn-info" data-toggle="popover" data-trigger="hover" title="'.$rows['cliente_nombre'].'" data-content="'.$rows['cliente_direccion'].'">
							<i class="fas fa-info-circle"></i>
						</button>
					</td>
					<td>
						<a href="'.SERVER_URL.'client-update/'.mainModel::encryption($rows['cliente_id']).'/" class="btn btn-success">
							<i class="fas fa-sync-alt"></i>
						</a>
					</td>
					<td>
						<form class="FormularioAjax" action="'.SERVER_URL.'ajax/clienteAjax.php" method="POST" data-form="delete" autocomplete="off">
							<input type="hidden" name="cliente_id_del" value="'.mainModel::encryption($rows['cliente_id']).'"/>
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
				$tabla .= '<tr class="text-center" >
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

		if($total >= 1 && $pagina <= $numero_paginas){
			$tabla .= '<p class="text-right">Mostrar clientes '.$registro_inicio.'- '.$registro_final.' de un total de '.$total.'</p>';
			$tabla .= mainModel::paginador_tablas($pagina,$numero_paginas,$url,7);
		}

		return $tabla;
	}/*fin del controlador*/

	public function eliminar_cliente_controlador(){
		$id = mainModel::decryption($_POST['cliente_id_del']);
		$id = mainModel::lipiar_cadena($id);

		/*comprobar si exsiste en la db*/
		$check_cliente = mainModel::ejecutar_consulta_simple("SELECT cliente_id FROM cliente WHERE cliente_id = '$id'");
		if ($check_cliente->rowCount() <= 0) {
			$alerta = [
				"Alerta"=>"simple",
				"Titulo"=>"Ocurrio un error inesperado",
				"Texto"=>"El cliente que intente eliminar no existe en el sisitema",
				"Tipo"=>"error"
			];
			echo json_encode($alerta);
			exit();
		}

		/*verificar si tiene prestamos*/
		$check_prestamos = mainModel::ejecutar_consulta_simple("SELECT cliente_id FROM prestamo WHERE cliente_id = '$id' LIMIT 1 ");

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

		/*privilegio para poder eliminar clientes*/
		session_start(['name'=>'SPM']);
		if($_SESSION['privilegio_spm'] != 1){
			$alerta = [
				"Alerta"=>"simple",
				"Titulo"=>"Ocurrio un error inesperado",
				"Texto"=>"No tienes los perisos suficientes como para realizar esta operacion",
				"Tipo"=>"error"
			];
			echo json_encode($alerta);
			exit();
		}

		/*pasamo loa valores al modelo para eliminar los datos*/
		$eliminar_cliente = clienteModelo::eliminar_cliente_modelo($id);

		if($eliminar_cliente->rowCount() == 1){
			$alerta = [
				"Alerta"=>"recargar",
				"Titulo"=>"Cliente eliminado",
				"Texto"=>"El cliente a sido eliminado con exito",
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
	}/*fin del controlador*/

	public function datos_cliente_controlador($tipo, $id){
		$tipo = mainModel::lipiar_cadena($tipo);

		$id = mainModel::decryption($id);
		$id = mainModel::lipiar_cadena($id);
		//retornamos el modelo
		return clienteModelo::datos_cliente_modelo($tipo, $id);
	}/*fin del controlador*/

	public function actualizar_cliente_controller(){
		$id = mainModel::decryption($_POST['cliente_id_up']);
		$id = mainModel::lipiar_cadena($id);

		/*comprobar si el usuario esta en la base de datos*/
		$check_cliente = mainModel::ejecutar_consulta_simple("SELECT * FROM cliente WHERE cliente_id = '$id' ");

		if($check_cliente->rowCount() <= 0){
			$alerta = [
				"Alerta"=>"simple",
				"Titulo"=>"Ocurrio un error inesperado",
				"Texto"=>"No hemos encontrado el cliente en el sistema",
				"Tipo"=>"error"
			];
			echo json_encode($alerta);
			exit();
		}else{
			/*datos del cliente*/
			$campos = $check_cliente->fetch();
		}

		$dni = mainModel::lipiar_cadena($_POST['cliente_dni_up']);
		$nombre = mainModel::lipiar_cadena($_POST['cliente_nombre_up']);
		$apellido = mainModel::lipiar_cadena($_POST['cliente_apellido_up']);
		$telefono = mainModel::lipiar_cadena($_POST['cliente_telefono_up']);
		$direccion = mainModel::lipiar_cadena($_POST['cliente_direccion_up']);

		/*validar campos*/
		if($dni=="" || $nombre=="" || $apellido==""){
			$alerta = [
				"Alerta"=>"simple",
				"Titulo"=>"Ocurrio un error inesperado",
				"Texto"=>"No has llenado todos los campos que son obligatorios",
				"Tipo"=>"error"
			];
			echo json_encode($alerta);
			exit();
		}

		if(mainModel::verificar_datos("[0-9-]{1,27}", $dni)){
			$alerta = [
				"Alerta"=>"simple",
				"Titulo"=>"Ocurrio un error inesperado",
				"Texto"=>"El DNI no coincide con el formato solicitado",
				"Tipo"=>"error"
			];
			echo json_encode($alerta);
			exit();
		}

		/*comprobar si el dni es unico*/
		if($dni != $campos['cliente_dni']){
			$unique_dni = mainModel::ejecutar_consulta_simple("SELECT cliente_dni FROM cliente WHERE cliente_dni = '$dni' ");
			if($unique_dni->rowCount() > 0){
				$alerta = [
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrio un error inesperado",
					"Texto"=>"El DNI ingresado ya se encuentra registrado al parecer pertenece a otro usuario",
					"Tipo"=>"error"
				];
				echo json_encode($alerta);
				exit();
			}
		}

		if(mainModel::verificar_datos("[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{1,40}", $nombre)){
			$alerta = [
				"Alerta"=>"simple",
				"Titulo"=>"Ocurrio un error inesperado",
				"Texto"=>"El Nombre no coincide con el formato solicitado",
				"Tipo"=>"error"
			];
			echo json_encode($alerta);
			exit();
		}

		if (mainModel::verificar_datos("[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{1,40}",$apellido)) {
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

		if($direccion != ""){
			if(mainModel::verificar_datos("[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ().,#\- ]{1,150}", $direccion)){
				$alerta = [
					"Alerta" => "simple",
					"Titulo" => "Ocurrio un error inesperado",
					"Texto" => "La dirección no coincide con el formato solicitado",
					"Tipo" => "error"
				];
			}
		}

		/*preparamos datos para mandar al modelo*/
		$datos_cliente_up = [
			"DNI" => $dni,
			"NOMBRE" => $nombre,
			"APELLIDO" => $apellido,
			"TELEFONO" => $telefono,
			"DIRECCION" => $direccion,
			"ID" => $id
		];

		/*guardamos datos*/
		if(clienteModelo::actualizar_cliente_modelo($datos_cliente_up)){
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

}/*fin*/