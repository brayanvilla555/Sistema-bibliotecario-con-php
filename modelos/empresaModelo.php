<?php
require_once 'mainModel.php';
class empresaModelo extends mainModel{
	/*agregar empresa*/
	protected static function agregar_empresa_modelo($datos){
		$sql = mainModel::conectar()->prepare("INSERT INTO empresa(empresa_id, empresa_nombre, empresa_email, empresa_telefono, empresa_direccion) VALUES(:ID, :NOMBRE, :EMAIL, :TELEFONO, :DIRECCION)");
		$sql->bindParam(":ID", $datos['ID']);
		$sql->bindParam(":NOMBRE", $datos['NOMBRE']);
		$sql->bindParam(":EMAIL", $datos['EMAIL']);
		$sql->bindParam(":TELEFONO", $datos['TELEFONO']);
		$sql->bindParam(":DIRECCION", $datos['DIRECCION']);
		$sql->execute();

		return $sql;
	}

	/*contar y mostrar datos de la empresa*/
	protected static function datos_empresa_modelo(){
		$sql = mainModel::conectar()->prepare("SELECT * FROM empresa WHERE empresa_id = 1");
		$sql->execute();
		return $sql;
	}

	/*actualizar datos de la empresa*/
	protected static function actualizar_empresa_modelo($datos){
		$sql = mainModel::conectar()->prepare("UPDATE empresa SET empresa_nombre = :NOMBRE, empresa_email = :EMAIL, empresa_telefono = :TELEFONO, empresa_direccion = :DIRECCION WHERE empresa_id = :ID");
		$sql->bindParam(":NOMBRE", $datos['NOMBRE']);
		$sql->bindParam(":EMAIL", $datos['EMAIL']);
		$sql->bindParam(":TELEFONO", $datos['TELEFONO']);
		$sql->bindParam(":DIRECCION", $datos['DIRECCION']);
		$sql->bindParam(":ID", $datos['ID']);
		$sql->execute();
		return $sql;
	}

}