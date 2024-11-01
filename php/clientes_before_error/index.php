<?php
	//include( '../../../../conexionMysqli.php' );
	include( '../../../adminFacturacion/include/db.php' );
	$db = new db();	
	$link = $db->conectDB();
//validacion de token
	/*if( !isset( $_GET['token'] ) ){
		die( "<h3 class=\"text-center\">No hay token, pide ayuda en la tienda donde realizaste la compra!</h3>" );
	}else{
		$sql = "SELECT 
					IF( NOW() < fecha_hora_vencimiento, 'is_valid', 'is_invalid' ) AS token_status
				FROM vf_tokens_alta_clientes
				WHERE token = '{$_GET['token']}'";
		$stm = $link->query( $sql ) or die( "Error al consultar si el token es valido!" );
		if( $stm->num_rows <= 0 ){
			die( "<h3 class=\"text-center\">El token es invalido, pide ayuda en la tienda donde realizaste la compra!</h3>" );
		}else{
			$row = $stm->fetch_assoc();
			if( $row['token_status'] == 'is_invalid' ){
			die( "<h3 class=\"text-center\">El token esta vencido, pide ayuda en la tienda donde realizaste la compra!</h3>" );
			}
		}
		echo "<input type=\"hidden\" id=\"current_token\" value=\"{$_GET['token']}\">";
	}*/
	$sql = "SELECT
				UPPER( nombre ) AS name
			FROM sys_estados";
	$stm = $link->query( $sql ) or die( "Error al consultar los estados : {$sql}" );
	$states = "<option value=\"0\">-- Seleccionar --</option>";
	while( $row = $stm->fetch(PDO::FETCH_ASSOC) ){
		$states .= "<option value=\"{$row['name']}\">{$row['name']}</option>";
	}

	

	$sql = "SELECT
				clave_numerica AS clue,
				nombre_tipo_regimen_fiscal AS name
			FROM vf_tipos_regimenes_fiscales";
	$stm = $link->query( $sql ) or die( "Error al consultar los cfdis : {$sql}" );
	$regimes = "<option value=\"0\">-- Seleccionar --</option>";
	while( $row = $stm->fetch(PDO::FETCH_ASSOC) ){
		$regimes .= "<option value=\"{$row['clue']}\" >{$row['name']}</option>";
	}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Generador de URLs con QR</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
<!--link rel="stylesheet" rel="preload" as="style" onload="this.rel='stylesheet';this.onload=null" href="library/milligram.min.css">
<link rel="stylesheet" href="library/modalStyle.css"-->
	<link rel="stylesheet" type="text/css" href="../../../adminFacturacion/css/icons/css/fontello.css">
	<link rel="stylesheet" type="text/css" href="css/styles.css">
	<script type="text/javascript" src="../../../adminFacturacion/js/jquery-1.10.2.min.js"></script>
	<link rel="stylesheet" type="text/css" href="../../../adminFacturacion/css/bootstrap/css/bootstrap.css">
	<script type="text/javascript" src="../../../adminFacturacion/css/bootstrap/js/bootstrap.bundle.min.js"></script>
	<script type="text/javascript" src="js/contacts.js"></script>
	<script type="text/javascript" src="js/functions.js"></script>
</head>
<body>


	<audio id="ok" controls style="display:none;">
		<source type="audio/wav" src="../../../../files/sounds/ok.mp3">
	</audio>
	<audio id="error" controls style="display:none;">
		<source type="audio/wav" src="../../../../files/sounds/error.mp3">
	</audio>

	<audio id="costumer_exists" controls style="display:none;">
		<source type="audio/wav" src="../../../../files/sounds/costumers/costumer_exists.mp3">
	</audio>
	<audio id="costumer_saved" controls style="display:none;">
		<source type="audio/wav" src="../../../../files/sounds/costumers/costumer_saved.mp3">
	</audio>
	<audio id="new_costumer_with_constance" controls style="display:none;">
		<source type="audio/wav" src="../../../../files/sounds/costumers/new_costumer_with_constance.mp3">
	</audio>
	<audio id="new_costumer_without_constance" controls style="display:none;">
		<source type="audio/wav" src="../../../../files/sounds/costumers/new_costumer_without_constance.mp3">
	</audio>


	
	<div class="emergent" style="display: none;">
		<div class="row">
			<div class="col-12 emergent_content" tabindex="1">
				<h2 class="icon-ok-circled text-success">Token valido</h2>
				<h2 class="text-warning icon-warning text-center"><i></i>Importante</h2>
				<ul>
					<li class="icon-right-big" style="list-style : none; padding : 10px;">Este token caduca en "X" tiempo</li>
					<li class="icon-right-big" style="list-style : none; padding : 10px;">Verifica bien tus datos antes de guardarlo, ya que de lo contrario no podremos facturar tu(s) compra(s)</li>
					<li class="icon-right-big" style="list-style : none; padding : 10px;">Captura un telefono en el que te podamos localizar de manera inmediata en caso de que necesitemos la corrección de algun dato para tu factura</li>
				</ul>
				<br><br>
				<div class="row">
					<div class="column"></div>
					<div class="column text-center">
						<button
							class="btn btn-success form-control"
							onclick="close_emergent();"
						>
							<i class="icon-ok-circle">Aceptar y continuar</i>
						</button>
						<br><br>
					</div>
				</div>
			</div>
		</div>
	</div>

	<h2 class="text-center bg-primary text-light" 
		style="position : sticky; top : 0 !important; padding : 10px; z-index: 100;">Alta de clientes</h2>
	<div class="row text-center" style="padding : 20px;">
		<label class="text-start">Buscador por RFC</label>
		<div class="input-group">
			<input type="text" id="rfc_seeker" onkeyup="check_if_exists_costumer( event );" class="form-control">
			<button
				class="btn btn-primary"
				onclick="check_if_exists_costumer( 'intro' );"
			>
				<i class="icon-search"></i>
			</button>
		</div>
		<div id="social_reason_container" class="row"></div>
		<div class="row">
		<!--accordion-->
			<div id="accordion" id="accordionExample">
			</div>
		<!-- fin de acordion -->
			<div class="row text-center">
				<button
					class="btn btn-success"
					onclick="add_contact_form();"
				>
					<i class="icon-plus">Agregar contacto</i>
				</button>
			</div>
		</div>
		<!--div class="col-sm-6">
			Nombre de Contacto<span class="text-danger">*</span>
			<input type="text" id="costumer_name_input" class="form-control" onblur="changeToUpperCase( this );">
		</div>
		<div class="col-sm-6">
			Telefono <span class="text-danger">*</span>
			<input type="number" id="telephone_input" class="form-control">
		</div>
		<div class="col-sm-6">
			Celular <span class="text-danger">*</span>
			<input type="number" id="cellphone_input" class="form-control">
		</div>
		<div class="col-sm-6">
			Correo <span class="text-danger">*</span>
			<input type="email" id="email_input" class="form-control">
		</div>
		<div class="col-sm-6">
			Uso CFDI <span class="text-danger">*</span>
			<select class="form-select" id="cfdi_input">
			<?php
				//echo $cfdis;
			?>
			</select>
		<br>
		</div-->
		<hr>
		<h2>Razon Social</h2>
		<hr>
		<div class="row">
			<!--button
				class="btn btn-info"
				onclick="enable_scann_camera();"
			>
				<i class="icon-qrcode">Escanear Cedula Fiscal</i>
			</button-->
			<?php
				include( 'reader.php' );
			?>
		</div>	
		<div class="col-sm-6">
			RFC <span class="text-danger">*</span>
			<input type="text" id="rfc_input" class="form-control" onblur="changeToUpperCase( this );">
		</div>
		<div class="col-sm-6">
			Nombre / Razon Social <span class="text-danger">*</span>
			<input type="text" id="name_input" class="form-control" onblur="changeToUpperCase( this );">
		</div>
		<div class="col-sm-6">
			Tipo Persona <span class="text-danger">*</span>
			<select class="form-select" id="person_type_combo">
				<option>--Seleccionar --</option>
				<option label="Sin Tipo Persona" value="1">Sin Tipo Persona</option>
				<option label="Persona Fisica" value="2">Persona Fisica</option>
				<option label="Persona Moral" value="3">Persona Moral</option>
			</select>
		</div>
		<div class="col-sm-6">
			Cedula fiscal :<br>
			<input type="text" id="fiscal_cedule" class="form-control" style="background : transparent;" readonly>
			<!--input type="checkbox">
			<input type="file">
			<button 
				class="btn btn-warning"
			>
				<i class="icon-file-image"></i>
			</button-->
		</div>

		<!--div class="col-sm-6">
			Razon Social<span class="text-danger">*</span>
			<input type="text" class="form-control">
		</div-->
		<div class="col-lg-6">
			Calle <span class="text-danger">*</span>
			<input type="text" id="street_name_input" class="form-control">
		</div>
		<div class="col-lg-3">
			# int : 
			<input type="text" id="internal_number_input" class="form-control">
		</div>
		<div class="col-lg-3">
			# ext : 
			<input type="text" id="external_number_input" class="form-control">
		</div>

		<div class="col-lg-6">
			Colonia <span class="text-danger">*</span>
			<input type="text" id="cologne_input" class="form-control">
		</div>

		<div class="col-lg-6">
			Delegacion / Municipio <span class="text-danger">*</span>
			<input type="text" id="municipality_input" class="form-control">
		</div>

		<div class="col-lg-6">
			C.P. <span class="text-danger">*</span>
			<input type="text" id="postal_code_input" class="form-control">
		</div>
		<!--div class="col-sm-6">
			Localidad
			<input type="text" id="location_input" class="form-control">
		</div>
		<div class="col-sm-6">
			Referencia
			<input type="text" id="reference_input" class="form-control">
		</div-->
		<div class="col-lg-6">
			Pais <span class="text-danger">*</span>
			<select id="country_combo" class="form-select">
				<option value="Mexico">México</option>
			</select>
		</div>
		<div class="col-lg-6">
			Estado <span class="text-danger">*</span>
			<input type="text" id="state_input" class="form-control">
		</div>
		<div class="col-lg-6">
			Regimen Fiscal <span class="text-danger">*</span>
			<select id="regime_input" class="form-select">
				<?php
					echo $regimes;
				?>
			</select>
		</div>
		<div class="col-lg-6">
			Folio Único
				<input type="text" id="costumer_unique_folio" class="form-control" value="" disabled>
			<br><br>
			<br><br>
		</div>
		<div class="col-lg-6">
			Id Cliente : 
				<input type="text" id="costumer_id" class="form-control" value="" disabled>
			<br><br>
			<br><br>
		</div>
	</div>
	<div class="row text-center bg-primary" style="text-align : center;position : fixed; bottom : 0; width : 100%; left : 0; padding : 10px;">
		<div class="col-3">
			<button
				class="btn btn-light"
				type="button"
				onclick="if( confirm( 'Salir al panel?' ) ){ location.href = '../../../../index.php?'}"
			>
				<i class="icon-home-1"></i>
			</button>
		</div>
		<div class="col-6 text-center" style="text-align : center !important;">
			<button
				type="button"
				class="btn btn-success form-control"
				onclick="save_costumer();"
			>
				<i class="icon-floppy">Guardar</i>
			</button>
		</div><div class="col-3">
			<button
				class="btn btn-light"
				type="button"
				onclick="alert( 'Proximamente...' );"
			>
				<i class="icon-help-1"></i>
			</button>
		</div>
	</div>
</body>
</html>