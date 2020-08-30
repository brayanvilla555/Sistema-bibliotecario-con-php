<!-- Page header -->
<div class="full-box page-header">
    <h3 class="text-left">
        <i class="fas fa-sync-alt fa-fw"></i> &nbsp; ACTUALIZAR ITEM
    </h3>
    <p class="text-justify">
        Lorem ipsum dolor sit amet, consectetur adipisicing elit. Eaque laudantium necessitatibus eius iure adipisci modi distinctio. Earum repellat iste et aut, ullam, animi similique sed soluta tempore cum quis corporis!
    </p>
</div>

<div class="container-fluid">
    <ul class="full-box list-unstyled page-nav-tabs">
        <li>
            <a href="<?= SERVER_URL?>item-new/"><i class="fas fa-plus fa-fw"></i> &nbsp; AGREGAR ITEM</a>
        </li>
        <li>
            <a href="<?= SERVER_URL?>item-list/"><i class="fas fa-clipboard-list fa-fw"></i> &nbsp; LISTA DE ITEMS</a>
        </li>
        <li>
            <a href="<?= SERVER_URL?>item-search/"><i class="fas fa-search fa-fw"></i> &nbsp; BUSCAR ITEM</a>
        </li>
    </ul>
</div>

<!--CONTENT-->
<div class="container-fluid">
	<?php
		require_once './controladores/itemControlador.php';
		$inst_item = new itemControlador();
		$datos_item = $inst_item->datos_item_controlador("Unico", $pagina[1]);

		if($datos_item->rowCount() == 1):
			$campos = $datos_item->fetch();
	?>
	<form action="<?= SERVER_URL?>ajax/itemAjax.php" class="form-neon FormularioAjax" method="POST" data-form="update" enctype="multipart/form-data" autocomplete="off">
		<fieldset>
			<legend><i class="far fa-plus-square"></i> &nbsp; Información del item</legend>
			<input type="hidden" name="item_fecha_up" id="item_fecha_up" value="<?=date("Y-m-d")?>">
			<input type="hidden" name="item_id_up" id="item_id_up" value="<?=$pagina[1]?>">
			<div class="container-fluid">
				<div class="row">
					<div class="col-12 col-md-4">
						<div class="form-group">
							<label for="item_codigo" class="bmd-label-floating">Códido</label>
							<input type="text" pattern="[a-zA-Z0-9-]{1,45}" class="form-control" name="item_codigo_up" value="<?= $campos['item_codigo']?>" id="item_codigo" maxlength="45">
						</div>
					</div>

					<div class="col-12 col-md-4">
						<div class="form-group">
							<label for="item_nombre" class="bmd-label-floating">Nombre</label>
							<input type="text" pattern="[a-zA-záéíóúÁÉÍÓÚñÑ0-9 ]{1,140}" class="form-control" name="item_nombre_up" id="item_nombre" value="<?= $campos['item_nombre']?>" maxlength="140">
						</div>
					</div>
					<div class="col-12 col-md-4">
						<div class="form-group">
							<label for="item_stock" class="bmd-label-floating">Stock</label>
							<input type="num" pattern="[0-9]{1,9}" class="form-control" name="item_stock_up" id="item_stock" value="<?= $campos['item_stock']?>"maxlength="9">
						</div>
					</div>
					<div class="col-12 col-md-6">
						<div class="form-group">
							<label for="item_estado" class="bmd-label-floating">Estado</label>
							<select class="form-control" name="item_estado_up" id="item_estado">
								<option value="1"
								<?php if($campos['item_estado'] == 1){
									echo 'selected=""' ;
								}?>>Habilitado
								</option>
								<option value="0"
								<?php if($campos['item_estado'] == 0){
									echo 'selected="" ';
								}?>>Deshabilitado
								</option>
							</select>
						</div>
					</div>
					<div class="col-12 col-md-6">
						<div class="form-group">
							<label for="item_detalle" class="bmd-label-floating">Detalle</label>
							<input type="text" pattern="[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ().,#\- ]{1,200}" class="form-control" name="item_detalle_up" id="item_detalle" value="<?= $campos['item_detalle']?>" maxlength="200">
						</div>
					</div>
					<div class="col-12 col-md-6">
						<div class="form-group">
							<label for="item_detalle" class="bmd-label-floating">Imagen</label>
							<input type="file" class="form-control" name="item_imagen_up" id="item_imagen_up">
							<?php if($campos['item_imagen'] != ""):?>
								<img src="<?=SERVER_URL?>uploads/items/<?=$campos['item_imagen']?>" alt="img" width="150px" height="150px" style=" border: 1px dashed #000;">
							<?php else:?>
								<img src="<?=SERVER_URL?>vistas/assets/img/logo.png" alt="img" width="150px" height="150px" style=" border: 1px dashed #000;">
							<?php endif;?>
							<input type="hidden" class="form-control" name="item_imagen_actual_up" id="item_imagen_actual_up" value="<?=$campos['item_imagen']?>">
						</div>
					</div>
				</div>
			</div>
		</fieldset>
		<br><br><br>
		<p class="text-center" style="margin-top: 40px;">
			<button type="submit" class="btn btn-raised btn-success btn-sm"><i class="fas fa-sync-alt"></i> &nbsp; ACTUALIZAR</button>
		</p>
	</form>
	<?php else:?>
    <div class="alert alert-danger text-center" role="alert">
        <p><i class="fas fa-exclamation-triangle fa-5x"></i></p>
        <h4 class="alert-heading">¡Ocurrió un error inesperado!</h4>
        <p class="mb-0">Lo sentimos, no podemos mostrar la información solicitada debido a un error.</p>
    </div>
<?php endif;?>
</div>