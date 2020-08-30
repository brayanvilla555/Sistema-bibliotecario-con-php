<?php
	if ($peticionAjax) {
		require_once '../modelos/loginModelo.php';
	}else{
		require_once './modelos/loginModelo.php';
	}

	class loginControlador extends loginModelo{

		/*==========Controlador para iniciar session=======*/
		public function iniciar_sesion_controlador(){
			//resibimos los dos parametros del formulario
			$usuario = mainModel::lipiar_cadena($_POST['usuario_login']);
			$clave = mainModel::lipiar_cadena($_POST['clave_login']);
			#========comprobar campos vacios=========
			if ($usuario == "" || $clave == "") {
				echo '
				<script>
					Swal.fire({
						title: "Ocurio un error inesperado",
						text: "No has llenado todos los campos que son requeridos",
						type: "error",
						confirmButtonText: "Aceptar"
					});
				</script>
				';
				exit();
			}
			#======verificar la integridad de los datos======
			if ($usuario != "") {
				if (mainModel::verificar_datos("[a-zA-Z0-9]{1,35}", $usuario)) {
					echo '
					<script>
						Swal.fire({
							title: "Ocurio un error inesperado",
							text: "El NOMBRE DE USUARIO no coincide con el formato solicitado",
							type: "error",
							confirmButtonText: "Aceptar"
						});
					</script>
					';
					exit();
				}
			}

			if ($clave != "") {
				if (mainModel::verificar_datos("[a-zA-Z0-9$@.-]{7,100}", $clave)) {
					echo '
					<script>
						Swal.fire({
							title: "Ocurio un error inesperado",
							text: "La CONTRASEÑA no coincide con el formato solicitado",
							type: "error",
							confirmButtonText: "Aceptar"
						});
					</script>
					';
					exit();
				}
			}

			#encriptamos la contraseña enviada desde el login para comparar
			$clave = mainModel::encryption($clave);

			$datos_login = [
				"Usuario"=>$usuario,
				"Clave"=>$clave
			];

			//comprobar los datos
			$datos_cuenta = loginModelo::iniciar_sesion_modelo($datos_login);

			if ($datos_cuenta->rowCount() == 1) {
				$row = $datos_cuenta->fetch();

				session_start(['name'=>'SPM']);

				$_SESSION['id_spm'] = $row['usuario_id'];
				$_SESSION['nombre_spm'] = $row['usuario_nombre'];
				$_SESSION['apellido_spm'] = $row['usuario_apellido'];
				$_SESSION['usuario_spm'] = $row['usuario_usuario'];
				$_SESSION['privilegio_spm'] = $row['usuario_privilegio'];
				$_SESSION['token_spm'] = md5(uniqid(mt_rand(),true));
				return header("location: ".SERVER_URL."home/");

			}else{
				echo '
				<script>
					Swal.fire({
						title: "Ocurio un error inesperado",
						text: "EL NOMBRE DE USUARIO O LA CONTRASEÑA SON INCORRECTOS",
						type: "error",
						confirmButtonText: "Aceptar"
					});
				</script>
				';
			}
		}/*------fin controlador------*/

		/*==========Controlador Forzar cerrar la session=======*/
		public function forzar_cierre_sesion_controlador(){
			session_unset();
			session_destroy();
			if (headers_sent()) {
				return "<script> window.location.href='".SERVER_URL."login/'; </script>";
			}else{
				return header("Location: ".SERVER_URL."login/");
			}
		}/*------fin controlador------*/

		/*==========Controlador cerrar la session=======*/
		public function cerrar_sesion_controlador(){
			session_start(['name'=>'SPM']);
			$token = mainModel::decryption($_POST['token']);
			$usuario = mainModel::decryption($_POST['usuario']);

			if ($token == $_SESSION['token_spm'] && $usuario == $_SESSION['usuario_spm']) {
				session_unset();
				session_destroy();
				$alerta = [
					"Alerta"=>"redireccionar",
					"URL"=>SERVER_URL."login/"
				];
			}else{
				$alerta = [
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrio un error inesperado",
					"Texto"=>"No se pudo cerrar la session en el sistema",
					"Tipo"=>"error"
				];
			}
			echo json_encode($alerta);
		}/*------fin controlador------*/

	}
