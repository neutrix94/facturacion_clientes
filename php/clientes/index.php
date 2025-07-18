<?php
	//include( '../../../../conexionMysqli.php' );
	$data = file_get_contents( "../../config/apis.json");
	$config = json_decode($data, true);
	$admin_fact_dir = $config['admin_fact_dir'];
	include( "../../../{$admin_fact_dir}/include/db.php" );
	$db = new db();	
	$link = $db->conectDB();

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
    <title>Alta Clientes</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
<!--link rel="stylesheet" rel="preload" as="style" onload="this.rel='stylesheet';this.onload=null" href="library/milligram.min.css">
<link rel="stylesheet" href="library/modalStyle.css"-->
	<link rel="stylesheet" type="text/css" href="../../../<?php echo "{$admin_fact_dir}";?>/css/icons/css/fontello.css">
	<link rel="stylesheet" type="text/css" href="css/styles.css">
	<script type="text/javascript" src="../../../<?php echo "{$admin_fact_dir}";?>/js/jquery-1.10.2.min.js"></script>
	<link rel="stylesheet" type="text/css" href="../../../<?php echo "{$admin_fact_dir}";?>/css/bootstrap/css/bootstrap.css">
	<script type="text/javascript" src="../../../<?php echo "{$admin_fact_dir}";?>/css/bootstrap/js/bootstrap.bundle.min.js"></script>
	<script type="text/javascript" src="js/contacts.js"></script>
	<script type="text/javascript" src="js/functions.js"></script>
	<script type="text/javascript" src="js/pdf_to_image.js"></script>
</head>
<body>


	<audio id="ok" controls style="display:none;">
		<source type="audio/wav" src="files/sounds/ok.mp3">
	</audio>
	<audio id="error" controls style="display:none;">
		<source type="audio/wav" src="files/sounds/error.mp3">
	</audio>

	<audio id="costumer_exists" controls style="display:none;">
		<source type="audio/wav" src="files/sounds/costumers/costumer_exists.mp3">
	</audio>
	<audio id="costumer_saved" controls style="display:none;">
		<source type="audio/wav" src="files/sounds/costumers/costumer_saved.mp3">
	</audio>
	<audio id="new_costumer_with_constance" controls style="display:none;">
		<source type="audio/wav" src="files/sounds/costumers/new_costumer_with_constance.mp3">
	</audio>
	<audio id="new_costumer_without_constance" controls style="display:none;">
		<source type="audio/wav" src="files/sounds/costumers/new_costumer_without_constance.mp3">
	</audio>


	
	<div class="emergent" style="display: none;">
		<div class="row">
			<div class="col-12 emergent_content" tabindex="1">
				<!--h2 class="icon-ok-circled text-success">Token valido</h2>
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
				</div-->
			</div>
		</div>
	</div>

	<!--h2 class="text-center bg-danger text-light" 
		style="position : sticky; top : 0 !important; padding : 10px; z-index: 100;">Alta de clientes Público (Cliente se da de alta)</h2-->
	<div class="row text-center" style="padding : 10px; margin-left : 12px;">
<!-- Implementacion Oscar 2025-07-15 para subir imagen / pdf de cedula fiscal -->
		<div class="row" id="converter_container">
			<div class="col-4 text-center p-1">
				<button
					type="button"
					class="btn border-info form-control"
					style="font-size : 70%;"
					onclick="show_upload_picture_form();"
				>
					<i class="icon-camera"></i>
					<br>
					<i>Imágen</i>
				</button>
			</div>
			<div class="col-4 text-center p-1">
				<button
					type="button"
					class="btn border-danger text-danger form-control"
					style="font-size : 70%;"
					onclick="show_upload_pdf_form();"
				>
					<i class="icon-file-pdf"></i>
					<br>
					<i>PDF</i>
				</button>
			</div>
			<div class="col-4 text-center p-1">
				<button
					class="btn border-dark form-control"
					onclick="enable_scann_camera();"
					style="font-size : 70%;"
				>
					<i class="icon-qrcode"></i>
					<br>
					<i>Escanear</i>
				</button>
			</div>	
		</div>
<!-- <label class="text-start">Buscador por RFC</label> -->
		
		<div class="row mt-2">
			<div class="input-group">
				<input type="text" id="rfc_seeker" onkeyup="check_if_exists_costumer( event );" class="form-control border-dark" placeholder="Buscador por RFC">
				<button
					class="btn border-dark"
					onclick="check_if_exists_costumer( 'intro' );"
				>
					<i class="icon-search"></i>
				</button>
			</div>
		</div>

		<div id="social_reason_container" class="row"></div>
		<div class="row mb-1">
		<!--accordion-->
			<div id="accordion" id="accordionExample">
			</div>
		<!-- fin de acordion -->
			<div class="text-center mt-1 mb-1">
				<button
					class="btn border-success text-success form-control"
					onclick="add_contact_form();"
				>
					<i class="icon-plus">Agregar contacto</i>
				</button>
			</div>
		</div>
		
		<hr>
		<h2>Razon Social</h2>
		<hr>
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
			<br>
		</div>
		<div class="col-lg-6">
			Id Cliente : 
				<input type="text" id="costumer_id" class="form-control" value="" disabled>
			<br><br>
			<br><br>
		</div>
	</div>
<!--footer-->
	<div class="row text-center bg-white" style="text-align : center;position : sticky; bottom : 2px; width : 100%; left : 0; padding : 10px;left :12px;">
		<div class="col-6">
			<button
				class="btn border-dark form-control"
				type="button"
				onclick="if( confirm( 'Salir a facturación?' ) ){ location.href = '../../index.php?'}"
			>
				<i class="icon-left-open" style="font-size : 70%;">Solicitar Factura</i>
			</button>
		</div>
		<div class="col-6 text-center" style="text-align : center !important;">
			<button
				type="button"
				class="btn border-success form-control text-success"
				onclick="save_costumer();"
			>
				<i class="icon-floppy" style="font-size : 70%;">Guardar</i>
			</button>
		</div>
	</div>
	<div class="hidden">
	<?php
		include('./getTaxDataByQr.php');
	?>
	</div>

</body>
</html>