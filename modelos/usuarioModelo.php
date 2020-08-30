<?php
require_once 'mainModel.php';

class usuarioModelo extends mainModel{

	/*========modelo para agregar usuario=====*/
	protected static function agregar_usuario_modelo($datos){
		$sql = mainModel::conectar()->prepare("INSERT INTO usuario(usuario_dni, usuario_nombre, usuario_apellido, usuario_telefono, usuario_direccion, usuario_email, usuario_usuario, usuario_clave, usuario_estado,	usuario_privilegio)
		 VALUES(:DNI,:Nombre,:Apellido,:Telefono,:Direccion,:Email,:Usuario,:Clave,:Estado,:Privilegio)");
		$sql->bindParam(":DNI",$datos['DNI']);
		$sql->bindParam(":Nombre",$datos['Nombre']);
		$sql->bindParam(":Apellido",$datos['Apellido']);
		$sql->bindParam(":Telefono",$datos['Telefono']);
		$sql->bindParam(":Direccion",$datos['Direccion']);
		$sql->bindParam(":Email",$datos['Email']);
		$sql->bindParam(":Usuario",$datos['Usuario']);
		$sql->bindParam(":Clave",$datos['Clave']);
		$sql->bindParam(":Estado",$datos['Estado']);
		$sql->bindParam(":Privilegio",$datos['Privilegio']);
		$sql->execute();

		return $sql;
	}

	/*========modelo para eliminar usuario=====*/
	protected static function eliminar_usuario_modelo($id){
		$sql = mainModel::conectar()->prepare("DELETE FROM usuario WHERE usuario_id = :ID");
		$sql->bindParam(":ID", $id);
		$sql->execute();
		return $sql;
	}

	/*========modelo datos del usuario y conteo =====*/
	protected static function datos_usuario_modelo($tipo, $id){
		if ($tipo == "Unico") {
			$sql = mainModel::conectar()->prepare("SELECT * FROM usuario WHERE usuario_id = :ID");
			$sql->bindParam(":ID",$id);
		}elseif($tipo == "Conteo"){
			$sql = mainModel::conectar()->prepare("SELECT usuario_id FROM usuario WHERE usuario_id != 1");
		}
		$sql->execute();
		return $sql;
	}

	/*========Modelo actualizar usuario=====*/
	protected static function actualizar_usuario_modelo($datos){
		$sql = mainModel::conectar()->prepare("UPDATE usuario SET usuario_dni = :DNI, usuario_nombre = :NOMBRE, usuario_apellido = :APELLIDO, usuario_telefono = :TELEFONO, usuario_direccion = :DIRECCION, usuario_email = :EMAIL, usuario_usuario = :USUARIO, usuario_clave = :CLAVE, usuario_estado = :ESTADO, usuario_privilegio = :PRIVILEGIO WHERE usuario_id = :ID ");
		$sql->bindParam(":DNI", $datos['DNI']);
		$sql->bindParam(":NOMBRE", $datos['NOMBRE']);
		$sql->bindParam(":APELLIDO", $datos['APELLIDO']);
		$sql->bindParam(":TELEFONO", $datos['TELEFONO']);
		$sql->bindParam(":DIRECCION", $datos['DIRECCION']);
		$sql->bindParam(":EMAIL", $datos['EMAIL']);
		$sql->bindParam(":USUARIO", $datos['USUARIO']);
		$sql->bindParam(":ESTADO", $datos['ESTADO']);
		$sql->bindParam(":CLAVE", $datos['CLAVE']);
		$sql->bindParam(":PRIVILEGIO", $datos['PRIVILEGIO']);
		$sql->bindParam(":ID", $datos['ID']);
		$sql->execute();
		return $sql;
	}

}