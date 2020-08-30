<script>
	let btn_salir = document.querySelector(".btn-exit-system");
	btn_salir.addEventListener('click', function(e){
		e.preventDefault();
		Swal.fire({
			title: 'Seguro que quieres salir del sistema?',
			text: "La sesion actual se eliminara y saldras del sistema",
			type: 'question',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			confirmButtonText: 'Si salir',
			cancelButtonText: 'No, Canselar'
		}).then((result) => {
			if (result.value) {

				let url = '<?php echo SERVER_URL;?>ajax/loginAjax.php';
				let token = '<?php echo $loginControlador->encryption( $_SESSION['token_spm']);?>';
				let usuario = '<?php echo$loginControlador->encryption($_SESSION['usuario_spm']);?>';

				let datos = new FormData();
				datos.append("token",token);
				datos.append("usuario",usuario);

				fetch(url,{
					method: 'POST',
					body: datos
				})
				.then(respuesta => respuesta.json())
				.then(respuesta => {
					return alertas_ajax(respuesta);
				});
			}
		});
	});
</script>