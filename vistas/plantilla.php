<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
	<title><?= COMPANY ?></title>
	<!----links-->
	<?php include_once './vistas/includes/Link.php';?>
</head>
<body>
	<?php
		 $peticionAjax = false;
		//incluimos el controlador
		require_once './controladores/vistaControlador.php';
		//IV
		$instaciaVistas = new vistaControlador();

		$vistas = $instaciaVistas->obtener_vistas_controlador();
		if ($vistas == "login" || $vistas == "404") {
			require_once "./vistas/contenidos/".$vistas."-view.php";
		}else{
			session_start(['name'=>'SPM']);

			/*---- url para los paginadores----*/
			$pagina = explode("/", $_GET['views']);

			//plantilla para cerrar la sesion
			require_once './controladores/loginControlador.php';
			$loginControlador = new loginControlador();

			if (!isset($_SESSION['token_spm']) || !isset($_SESSION['usuario_spm']) || !isset($_SESSION['privilegio_spm']) || !isset($_SESSION['id_spm'])) {
				echo $loginControlador->forzar_cierre_sesion_controlador();
				exit();
			}
	?>
	<!-- Main container -->
	<main class="full-box main-container">
		<!-- Nav lateral -->
		<?php include_once './vistas/includes/NavLatera.php';?>
		<!-- Page content -->
		<section class="full-box page-content">
			<?php
				include_once './vistas/includes/Navar.php';
				include_once $vistas;
			?>
		</section>
	</main>
<!--=============================================
=            Include JavaScript files           =
==============================================-->
	<?php
		include_once './vistas/includes/logOut.php';
		 }
		include_once './vistas/includes/Script.php';
	?>

</body>
</html>