<?php
	 /*==============detectar si es peticion AJAX o no===========*/
	 //definimos la vavuable $peticionAjax en todos los que usa ajax
	 if ($peticionAjax) {
	 	require_once "../config/SERVER.php";
	 }else{
	 	require_once "./config/SERVER.php";
	 }


class mainModel{
	/*==========Modelo para conectar a la DB=====*/
	protected static function conectar(){
		$conexion = new PDO(SGBD, USER, PASSWORD);
		$conexion->exec("SET CHARACTER SET utf-8");
		return $conexion;
	}

	/*==========Modelo ejecutar consultas simples=====*/
	protected static function ejecutar_consulta_simple($consulta){
		$sql = self::conectar()->prepare($consulta);
		$sql->execute();
		return $sql;
	}

	/*==========Modelo para ENCRIPTAR el id cuando se muestr e la url=====*/
	public  function encryption($string){
		$output=FALSE;
		$key=hash('sha256', SECRET_KEY);
		$iv=substr(hash('sha256', SECRET_IV), 0, 16);
		$output=openssl_encrypt($string, METHOD, $key, 0, $iv);
		$output=base64_encode($output);
		return $output;
	}
	/*==========Modelo para DESENCRIPTAR cualquier has o strinq que estee encriptado=====*/
	protected static function decryption($string){
		$key=hash('sha256', SECRET_KEY);
		$iv=substr(hash('sha256', SECRET_IV), 0, 16);
		$output=openssl_decrypt(base64_decode($string), METHOD, $key, 0, $iv);
		return $output;
	}

	/*============Mpdelo paragenerar codigos aleatorios===============*/
	public static function generar_codigo_aleatorio($letra,$logitud,$numero){
		for($i = 1; $i <= $logitud; $i++){
			$aleatorio = rand(0,9);
			$letra = $letra.$aleatorio; /*puedes cambiar el pundo de concatenacion por un +*/
		}
		return $letra."-".$numero;
	}

	/*============modeloLimpiar insercion SQL===============*/
	protected static function lipiar_cadena($cadena){
		//limpiar espacios
		$cadena = trim($cadena);
		//limpiar barras invertida
		$cadena = stripcslashes($cadena);
		//eliminar < que o > que
		$cadena = str_replace("<script>", "", $cadena);
		$cadena = str_replace("</script>", "", $cadena);
		$cadena = str_replace("<script src", "", $cadena);
		$cadena = str_replace("<script type=", "", $cadena);
		$cadena = str_replace("SELECT * FROM", "", $cadena);
		$cadena = str_replace("DELETE FROM", "", $cadena);
		$cadena = str_replace("INSERT INTO", "", $cadena);
		$cadena = str_replace("DROP TABLE", "", $cadena);
		$cadena = str_replace("DROP DATABASE", "", $cadena);
		$cadena = str_replace("TRUNCATE TABLE", "", $cadena);
		$cadena = str_replace("SHOW TABLES", "", $cadena);
		$cadena = str_replace("SHOW DATBASES", "", $cadena);
		$cadena = str_replace("<?php", "", $cadena);
		$cadena = str_replace("?>", "", $cadena);
		$cadena = str_replace("--", "", $cadena);
		$cadena = str_replace("<", "", $cadena);
		$cadena = str_replace(">", "", $cadena);
		$cadena = str_replace("[", "", $cadena);
		$cadena = str_replace("]", "", $cadena);
		$cadena = str_replace("^", "", $cadena);
		$cadena = str_replace("==", "", $cadena);
		$cadena = str_replace(";", "", $cadena);
		$cadena = str_replace("::", "", $cadena);
		$cadena = stripcslashes($cadena);
		$cadena = trim($cadena);
		return $cadena;
	}

	/*=========comprobar si el tipo de dato es valido=======*/
	protected static function verificar_datos($filtro, $cadena){
		if (preg_match("/^".$filtro."$/", $cadena)) {
			return false;
		}else{
			return true;
		}
	}

	/*=========modelo verificar fechas=======*/
	protected static function verificar_fecha($fecha){
		$valores = explode('-', $fecha);
		if (count($valores) == 3 && checkdate($valores[1],$valores[0],$valores[2])) {
			return false;
		}else{
			return true;
		}
	}

	/*=============modelo de paginacion de tablas=============== capitulo13 /botones cap 14*/
	protected static function paginador_tablas($pagina,$Npaginas,$url,$botones){
		$tabla = '<nav aria-label="Page navigation example">
			<ul class="pagination justify-content-center">';//inicio contenedor de la paginacion
		if ($pagina == 1) {
			$tabla .= '<li class="page-item disabled">
						<a class="page-link"><i class="fas fa-angle-double-left"></i></a>
					</li>';
		}else{
			$tabla .= '<li class="page-item">
						<a class="page-link" href="'.$url.'1/"><i class="fas fa-angle-double-left"></i></a>
					</li>
					<li class="page-item">
						<a class="page-link" href="'.$url.($pagina-1).'/">Anterior</a>
					</li>';
		}

		$contadorIteracion = 0;
		for($i = $pagina; $i <= $Npaginas; $i++){
			if ($contadorIteracion >= $botones) {
				break;
			}

			if($pagina == $i){
				$tabla .= '<li class="page-item">
								<a class="page-link" active href="'.$url.$i.'/">'.$i.'</a>
							</li>';
			}else{
				$tabla .= '<li class="page-item">
								<a class="page-link" href="'.$url.$i.'/">'.$i.'</a>
							</li>';
			}

			$contadorIteracion++;
		}

		if ($pagina == $Npaginas) {
			$tabla .= '<li class="page-item disabled">
						<a class="page-link"><i class="fas fa-angle-double-right"></i></a>
					</li>';
		}else{
			$tabla .= '<li class="page-item">
						<a class="page-link" href="'.$url.($pagina+1).'/">Sigiente</a>
					</li>
					<li class="page-item">
						<a class="page-link" href="'.$url.$Npaginas.'/"><i class="fas fa-angle-double-right"></i></a>
					</li>';
		}
		$tabla.= '</ul></nav>';//fin del contenedor de la paginacion
		return $tabla;
	}

}
?>