<?php
	 //sonstantes para la base de datos
	 const SERVER = "localhost";
	 const DB = "sisten_bibliotec";
	 const USER = "root";
	 const PASSWORD = "";

	 //coneccion con PDO crearmos una constante para trbajar con ese en losmodelos
	 const SGBD = "mysql:host=".SERVER.
	              ";dbname=".DB;

	 //constantes para encriptacion
	 const METHOD = "AES-256-CBC";
	 //crear una llave secreta
	 const SECRET_KEY = '$SISTEN_BIBLIOTEC@2020';
	 const SECRET_IV = '037970';
?>