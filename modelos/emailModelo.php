<?php
require_once 'mainModel.php';
class email extends mainModel{
	protected static function agregar_email_modelo(){
		$sql = mainModel::conectar()->prepare("INSERT INTO email() VALUES()");
	}
}