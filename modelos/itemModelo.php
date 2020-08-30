<?php
require_once 'mainModel.php';
class itemModelo extends mainModel{
	/*agregar item*/
	protected static function agregar_item_modelo($datos){
		$sql = mainModel::conectar()->prepare("INSERT INTO item(item_codigo, item_nombre, item_stock, item_estado, item_detalle, item_imagen, item_fecha_creada_actualizada) VALUES(:CODIGO, :NOMBRE, :STOCK, :ESTADO, :DETALLE, :IMAGEN, :FECHA)");
		$sql->bindParam(":CODIGO", $datos['CODIGO']);
		$sql->bindParam(":NOMBRE", $datos['NOMBRE']);
		$sql->bindParam(":STOCK", $datos['STOCK']);
		$sql->bindParam(":ESTADO", $datos['ESTADO']);
		$sql->bindParam(":DETALLE", $datos['DETALLE']);
		$sql->bindParam(":IMAGEN", $datos['IMAGEN']);
		$sql->bindParam(":FECHA", $datos['FECHA']);
		$sql->execute();
		return $sql;
	}

	/*eliminar item*/
	protected static function eliminar_item_modelo($id){
		$sql = mainModel::conectar()->prepare("DELETE FROM item WHERE item_id = :ID");
		$sql->bindParam(":ID", $id);
		$sql->execute();
		return $sql;
	}

	/*mostrar datos del item*/
	protected static function datos_item_modelo($tipo, $id){
		if($tipo == "Unico"){
			$sql = mainModel::conectar()->prepare("SELECT * FROM item WHERE item_id = :ID");
			$sql->bindParam(":ID", $id);
		}elseif($tipo == "Conteo"){
			$sql = mainModel::conectar()->prepare("SELECT item_id FROM item");
		}
		$sql->execute();
		return $sql;
	}

	/*modelo para actulizar el item*/
	protected static function actualizar_item_modelo($datos){
		$sql = mainModel::conectar()->prepare("UPDATE item SET item_codigo = :CODIGO, item_nombre = :NOMBRE, item_stock = :STOCK, item_estado = :ESTADO, item_detalle = :DETALLE, item_imagen = :IMAGEN, item_fecha_creada_actualizada = :FECHA WHERE item_id = :ID");
		$sql->bindParam(":CODIGO", $datos['CODIGO']);
		$sql->bindParam(":NOMBRE", $datos['NOMBRE']);
		$sql->bindParam(":STOCK", $datos['STOCK']);
		$sql->bindParam(":ESTADO", $datos['ESTADO']);
		$sql->bindParam(":DETALLE", $datos['DETALLE']);
		$sql->bindParam(":IMAGEN", $datos['IMAGEN']);
		$sql->bindParam(":FECHA", $datos['FECHA']);
		$sql->bindParam(":ID", $datos['ID']);
		$sql->execute();
		return $sql;
	}
}