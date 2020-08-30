<?php
if($peticionAjax){
	require_once '../modelos/itemModelo.php';
}else{
	require_once './modelos/itemModelo.php';
}

class itemControlador extends itemModelo{
	/*agregar items*/
	public function agregar_item_controlador(){
		$codigo = mainModel::lipiar_cadena($_POST['item_codigo_reg']);
		$nombre = mainModel::lipiar_cadena($_POST['item_nombre_reg']);
		$stock = mainModel::lipiar_cadena($_POST['item_stock_reg']);
		$estado = mainModel::lipiar_cadena($_POST['item_estado_reg']);
		$detalle = mainModel::lipiar_cadena($_POST['item_detalle_reg']);
		$fecha = mainModel::lipiar_cadena($_POST['item_fecha_reg']);

		/*validaciones*/
		if($codigo==""||$nombre==""||$stock==""||$estado==""){
			$alerta = [
				"Alerta"=>"simple",
				"Titulo"=>"Ocurrio un error inesperado",
				"Texto"=>"Todos los campos necesarios no h¿an sido llenados",
				"Tipo"=>"error"
			];
			echo json_encode($alerta);
			exit();
		}

		$check_codigo = mainModel::ejecutar_consulta_simple("SELECT * from item WHERE item_codigo = '$codigo'");
		if($check_codigo->rowCount() == 1){
			$alerta = [
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrio un error inesperado",
					"Texto"=>"El codigo pertenece a otro item",
					"Tipo"=>"error"
			];
			echo json_encode($alerta);
			exit();
		}

		if(mainModel::verificar_datos("[a-zA-Z0-9-]{1,45}",$codigo)){
			$alerta = [
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrio un error inesperado",
					"Texto"=>"El codigo no coincide con el formato solicitado",
					"Tipo"=>"error"
			];
			echo json_encode($alerta);
			exit();
		}

		if(mainModel::verificar_datos("[a-zA-záéíóúÁÉÍÓÚñÑ0-9 ]{1,140}",$nombre)){
			$alerta = [
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrio un error inesperado",
					"Texto"=>"El nombre no coincide con el formato solicitado",
					"Tipo"=>"error"
			];
			echo json_encode($alerta);
			exit();
		}

		if(mainModel::verificar_datos("[0-9]{1,9}",$stock)){
			$alerta = [
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrio un error inesperado",
					"Texto"=>"El stock no coincide con el formato solicitado",
					"Tipo"=>"error"
			];
			echo json_encode($alerta);
			exit();
		}

		if($estado < 0 || $estado > 1){
			$alerta = [
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrio un error inesperado",
					"Texto"=>"El estado no coincide con el formato solicitado no coincide con el formato solicitado",
					"Tipo"=>"error"
			];
			echo json_encode($alerta);
			exit();
		}

		if(file_exists($_FILES['item_imagen_reg']['tmp_name']) || is_uploaded_file($_FILES['item_imagen_reg']['tmp_name'])){
			if(isset($_FILES['item_imagen_reg'])){
				$file = $_FILES['item_imagen_reg'];
				$filename = $file['name'];
				$nimetype = $file['type'];

				if($nimetype == "image/jpg"||$nimetype == "image/png"||$nimetype == "image/jpeg"||$nimetype == "image/gif"){
					if(!is_dir('../uploads/items')){
						mkdir('../uploads/items',077, true);
					}
					$imagen = $filename;
					move_uploaded_file($file['tmp_name'], '../uploads/items/'.$filename);
				}else{
					$alerta = [
						"Alerta"=>"simple",
						"Titulo"=>"Ocurrio un error inesperado",
						"Texto"=>"El formato de la imagen no coincide con lo solicitado",
						"Tipo"=>"error"
					];
					echo json_encode($alerta);
					exit();
				}
			}
		}else{
			$imagen = "";
		}

		if($detalle != ""){
			if(mainModel::verificar_datos("[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ().,#\- ]{1,200}",$detalle)){
				$alerta = [
						"Alerta"=>"simple",
						"Titulo"=>"Ocurrio un error inesperado",
						"Texto"=>"El detalle no coincide con el formato solicitado",
						"Tipo"=>"error"
				];
				echo json_encode($alerta);
				exit();
			}
		}

		/*recibimos los valore*/
		$datos_item = [
			"CODIGO" => $codigo,
			"NOMBRE" => $nombre,
			"STOCK" => $stock,
			"ESTADO"=> $estado,
			"DETALLE" => $detalle,
			"IMAGEN" => $imagen,
			"FECHA" => $fecha
		];

		/*guardamos los dato*/
		$agregar_item = itemModelo::agregar_item_modelo($datos_item);
		if($agregar_item->rowCount() == 1){
			$alerta = [
				"Alerta"=>"limpiar",
				"Titulo"=>"Item registrado",
				"Texto"=>"Datos del item han sido registrado exitosamente",
				"Tipo"=>"success"
			];
		}else{
			$alerta = [
				"Alerta"=>"simple",
				"Titulo"=>"Ocurrio un error inesperado",
				"Texto"=>"No hemos podido guardar los datos de este item. Intentalo nuevamente",
				"Tipo"=>"error"
			];
		}
		echo json_encode($alerta);
	}/*fin del controlado*/

	/*listar items*/
	public function listar_item_controlador($pagina, $registros, $privilegio, $id, $url, $busqueda){
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
			$consulta = "SELECT SQL_CALC_FOUND_ROWS * FROM item WHERE(item_codigo LIKE'%$busqueda%' OR item_nombre LIKE'%$busqueda%' OR item_detalle LIKE'%$busqueda%' OR item_fecha_creada_actualizada LIKE'%$busqueda%') ORDER BY item_id DESC LIMIT $inicio, $registros";
		}else{
			$consulta = "SELECT SQL_CALC_FOUND_ROWS * FROM item ORDER BY item_id DESC LIMIT $inicio, $registros";
		}

		$conexion = mainModel::conectar();
		$datos = $conexion->query($consulta);
		$datos = $datos->fetchAll();

		$total = $conexion->query("SELECT FOUND_ROWS()");
		$total = (int) $total->fetchColumn();

		$numero_paginas = ceil($total/$registros);

		$tabla.='<div class="table-responsive">
			<table class="table table-dark table-sm">
				<thead>
					<tr class="text-center roboto-medium">
						<th>#</th>
						<th>CÓDIGO</th>
						<th>NOMBRE</th>
						<th>STOCK</th>
	                    <th>DETALLE</th>
	                    <th>IMAGEN</th>
						<th>ACTUALIZAR</th>
						<th>ELIMINAR</th>
					</tr>
				</thead>
				<tbody>
		';
		if($total >= 1 && $pagina <= $numero_paginas){
			$contador = $inicio + 1;
			$registro_inicio = $inicio + 1;

			foreach($datos as $rows){
				$tabla.='<tr class="text-center" >
					<td>'.$contador.'</td>
					<td>'.$rows['item_codigo'].'</td>
					<td>'.$rows['item_nombre'].'</td>
					<td>'.$rows['item_stock'].'</td>
                    <td>
                        <button type="button" class="btn btn-info" data-toggle="popover" data-trigger="hover" title="'.$rows['item_nombre'].'" data-content="'.$rows['item_detalle'].'">
                            <i class="fas fa-info-circle"></i>
                        </button>
                    </td>
                    <td><img src="'.SERVER_URL.'uploads/items/'.$rows['item_imagen'].'" height="50px" width="50px"></td>
					<td>
                        <a href="'.SERVER_URL.'item-update/'.mainModel::encryption($rows['item_id']).'/" class="btn btn-success">
                            <i class="fas fa-sync-alt"></i>
                        </a>
                    </td>
                    <td>
                        <form class="FormularioAjax" action="'.SERVER_URL.'ajax/itemAjax.php" method="POST" data-form="delete" autocomplete="off">
	                        <input type="hidden" id="id_item_del" name="id_item_del" value="'.mainModel::encryption($rows['item_id']).'">
                            <button type="submit" class="btn btn-warning">
                                <i class="far fa-trash-alt"></i>
                            </button>
                        </form>
                    </td>
				</tr>';
				$contador ++;
			}
			$registro_final = $contador-1;
		}else{
			if($total >= 0){
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
			if($total >= 1 && $pagina <= $numero_paginas){
				$tabla .= '<p class="text-right">Mostrar clientes '.$registro_inicio.'- '.$registro_final.' de un total de '.$total.'</p>';
				$tabla .= mainModel::paginador_tablas($pagina,$numero_paginas,$url,7);
			}
		}
		return $tabla;
	}/*fin del controlador*/

	/*eliminar unitem*/
	public function eliminar_item_controlador(){
		$id = mainModel::decryption($_POST['id_item_del']);
		$id = mainModel::lipiar_cadena($id);

		/*comprobar si exsiste en la DB*/
		$check_item = mainModel::ejecutar_consulta_simple("SELECT item_id FROM item WHERE item_id = '$id'");
		if($check_item->rowCount() <= 0){
			$alerta = [
				"Alerta"=>"simple",
				"Titulo"=>"Ocurrio un error inesperado",
				"Texto"=>"El item que intente eliminar no existe en el sisitema",
				"Tipo"=>"error"
			];
			echo json_encode($alerta);
			exit();
		}

		/*comprobar stock en 0 para eliminar*/
		$chek_stock = mainModel::ejecutar_consulta_simple("SELECT * FROM item WHERE item_id = '$id' AND item_stock > 0");

		if($chek_stock->rowCount() >0){
			$alerta = [
				"Alerta"=>"simple",
				"Titulo"=>"Ocurrio un error inesperado",
				"Texto"=>"El item que intente eliminar aun tiene un stock mayor a 0",
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

		/*eliminar*/
		$eliminar_item = itemModelo::eliminar_item_modelo($id);
		if($eliminar_item->rowCount() == 1){
			$alerta = [
				"Alerta"=>"recargar",
				"Titulo"=>"Item eliminado",
				"Texto"=>"El item a sido eliminado con exitosamente",
				"Tipo"=>"success"
			];
		}else{
			$alerta = [
				"Alerta"=>"simple",
				"Titulo"=>"Ocurrio un error inesperado",
				"Texto"=>"No hemos podido eliminar el item, porfavor intente nuevamente",
				"Tipo"=>"error"
			];
		}
		echo json_encode($alerta);
	}/*fin del controlador*/

	/*mostrar datosdel item*/
	public function datos_item_controlador($tipo, $id){
		$tipo = mainModel::lipiar_cadena($tipo);

		$id = mainModel::decryption($id);
		$id = mainModel::lipiar_cadena($id);

		/*retornamos el modelo*/
		return itemModelo::datos_item_modelo($tipo, $id);
	}
	/*actualizaritem*/
	public function actualizar_item_controlador(){
		$id = mainModel::decryption($_POST['item_id_up']);
		$id = mainModel::lipiar_cadena($id);

		/*comprobar si el item existe*/
		$check_item = mainModel::ejecutar_consulta_simple("SELECT * FROM item WHERE item_id = '$id'");
		if($check_item->rowCount() <= 0){
			$alerta = [
				"Alerta"=>"simple",
				"Titulo"=>"Ocurrio un error inesperado",
				"Texto"=>"No hemos encontrado el item en el sistema",
				"Tipo"=>"error"
			];
			echo json_encode($alerta);
			exit();
		}else{
			/*datos del cliente*/
			$campos = $check_item->fetch();
		}

		$codigo = mainModel::lipiar_cadena($_POST['item_codigo_up']);
		$nombre = mainModel::lipiar_cadena($_POST['item_nombre_up']);
		$stock = mainModel::lipiar_cadena($_POST['item_stock_up']);
		$estado = mainModel::lipiar_cadena($_POST['item_estado_up']);
		$detalle = mainModel::lipiar_cadena($_POST['item_detalle_up']);
		$fecha = mainModel::lipiar_cadena($_POST['item_fecha_up']);
		$imagen_actual = mainModel::lipiar_cadena($_POST['item_imagen_actual_up']);

		/*validaciones*/
		if($id=="" || $codigo==""||$nombre==""||$stock==""||$estado==""){
			$alerta = [
				"Alerta"=>"simple",
				"Titulo"=>"Ocurrio un error inesperado",
				"Texto"=>"Todos los campos necesarios no h¿an sido llenados",
				"Tipo"=>"error"
			];
			echo json_encode($alerta);
			exit();
		}

		if($codigo != $campos['item_codigo'] ){
			$check_codigo = mainModel::ejecutar_consulta_simple("SELECT * from item WHERE item_codigo = '$codigo'");
			if($check_codigo->rowCount() == 1){
				$alerta = [
						"Alerta"=>"simple",
						"Titulo"=>"Ocurrio un error inesperado",
						"Texto"=>"El codigo pertenece a otro item",
						"Tipo"=>"error"
				];
				echo json_encode($alerta);
				exit();
			}
		}

		if(mainModel::verificar_datos("[a-zA-Z0-9-]{1,45}",$codigo)){
			$alerta = [
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrio un error inesperado",
					"Texto"=>"El codigo no coincide con el formato solicitado",
					"Tipo"=>"error"
			];
			echo json_encode($alerta);
			exit();
		}

		if($nombre != $campos['item_nombre']){
			$check_nombre = mainModel::ejecutar_consulta_simple("SELECT item_nombre FROM item WHERE item_nombre = '$nombre'");
			if($check_nombre->rowCount() == 1){
				$alerta = [
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrio un error inesperado",
					"Texto"=>"El nombre de este item ya pertenece a otro",
					"Tipo"=>"error"
				];
				echo json_encode($alerta);
				exit();
			}
		}

		if(mainModel::verificar_datos("[a-zA-záéíóúÁÉÍÓÚñÑ0-9 ]{1,140}",$nombre)){
			$alerta = [
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrio un error inesperado",
					"Texto"=>"El nombre no coincide con el formato solicitado",
					"Tipo"=>"error"
			];
			echo json_encode($alerta);
			exit();
		}

		if(mainModel::verificar_datos("[0-9]{1,9}",$stock)){
			$alerta = [
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrio un error inesperado",
					"Texto"=>"El stock no coincide con el formato solicitado",
					"Tipo"=>"error"
			];
			echo json_encode($alerta);
			exit();
		}

		if($estado < 0 || $estado > 1){
			$alerta = [
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrio un error inesperado",
					"Texto"=>"El estado no coincide con el formato solicitado no coincide con el formato solicitado",
					"Tipo"=>"error"
			];
			echo json_encode($alerta);
			exit();
		}

		if(file_exists($_FILES['item_imagen_up']['tmp_name']) || is_uploaded_file($_FILES['item_imagen_up']['tmp_name'])){
			if(isset($_FILES['item_imagen_up'])){
				$file = $_FILES['item_imagen_up'];
				$filename = $file['name'];
				$nimetype = $file['type'];

				if($nimetype == "image/jpg"||$nimetype == "image/png"||$nimetype == "image/jpeg"||$nimetype == "image/gif"){
					if(!is_dir('../uploads/items')){
						mkdir('../uploads/items',077, true);
					}
					$imagen = $filename;
					move_uploaded_file($file['tmp_name'], '../uploads/items/'.$filename);
				}else{
					$alerta = [
						"Alerta"=>"simple",
						"Titulo"=>"Ocurrio un error inesperado",
						"Texto"=>"El formato de la imagen no coincide con lo solicitado",
						"Tipo"=>"error"
					];
					echo json_encode($alerta);
					exit();
				}
			}
		}else{
			$imagen = $imagen_actual;
		}

		if($detalle != ""){
			if(mainModel::verificar_datos("[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ().,#\- ]{1,200}",$detalle)){
				$alerta = [
						"Alerta"=>"simple",
						"Titulo"=>"Ocurrio un error inesperado",
						"Texto"=>"El detalle no coincide con el formato solicitado",
						"Tipo"=>"error"
				];
				echo json_encode($alerta);
				exit();
			}
		}

		$datos_item_up = [
			"CODIGO" => $codigo,
			"NOMBRE" => $nombre,
			"STOCK" => $stock,
			"ESTADO" => $estado,
			"DETALLE" => $detalle,
			"IMAGEN" => $imagen,
			"FECHA" => $fecha,
			"ID" => $id
		];
		$datos_actualizaros = itemModelo::actualizar_item_modelo($datos_item_up);
		if($datos_actualizaros){
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