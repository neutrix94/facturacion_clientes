<?php
    /*include( './php/classes/config.php' );
    $cnfg = new Config();
    //$api_url = $cnfg->getApiPath();
    $data = file_get_contents( "./config/apis.json");
    $config = json_decode($data, true);
    $api_url = $config['billing_api'];*/
?>
<!DOCTYPE html>
<html lang="en">
    <script src="./js/jquery-1.10.2.min.js"></script>
    <script src="./js/billingFunctions.js"></script>
    <link rel="stylesheet" href="./css/bootstrap/css/bootstrap.css">
    <link rel="stylesheet" href="./css/icons/css/fontello.css">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facturación CDLL</title>
</head>
<body>
    <div id="alert">
        <div id="alert_content">

        </div>
    </div>
    <?php
    //    echo "<input type=>";
    ?>
    <div class="row global">
        <div class="row">
            <div class="col-sm-4 text-center">
                <img src="./img/Logo.png" width="20%">
                Facturación de compras
            <div class="col-sm-4 text-center"></div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-1"></div>
            <div class="col-sm-10">
                <div class="input-group">
                    <input type="text" class="form-control" id="costumer_rfc" 
                    onkeyup="getClientByRfc( event )"
                    placeholder="Digita RFC">
                    <button
                        type="button"
                        id="rfc_seeker_btn"
                        class="btn btn-warning"
                        onclick="getClientByRfc( 'intro' )"
                    >
                        <i class="icon-search"></i>
                    </button>
                    <button
                        type="button"
                        id="rfc_seeker_reset_btn"
                        class="btn btn-danger hidden"
                        onclick="resetBillingForm()"
                    >
                        <i class="icon-spin3"></i>
                    </button>
                </div>
                <div>
                    <button
                        class="btn btn-info form-control"
                        onclick="location.href='php/clientes/index.php?'"
                    >
                        <i class="icon-user">Dar de alta nuevo cliente</i>
                    </button>
                </div>
                <!--div class="text-info">
                   <p>FUNK671228PH6</p> 
                   <p>TPM140304253</p>
                </div-->
                <br>
                <div class="hidden" id="contacts_container">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th class="text-center">Nombre</th>
                                <th class="text-center">Correo</th>
                                <th class="text-center">Uso CFDI</th>
                                <th class="text-center">Seleccionar</th>
                            </tr>
                        </thead>
                        <tbody id="contacts_list"></tbody>
                    </table>
                </div>
                <br>
                <div class="input-group">
                    <input type="text" class="form-control" id="sale_folio" 
                    placeholder="Digita Folio Nota" onkeyup="getSaleByFolio( event );" disabled>
                    <button
                        type="button"
                        class="btn btn-primary"
                        id="sale_seeker_btn"
                        onclick="getSaleByFolio( 'intro' );"
                        disabled
                    >
                        <i class="icon-search"></i>
                    </button>
                    <button
                        type="button"
                        id="sale_seeker_reset_btn"
                        class="btn btn-danger hidden"
                        onclick="resetBillingForm()"
                    >
                        <i class="icon-spin3"></i>
                    </button>
                </div>
                <div>
                    <p>24MAT7</p>
                </div>
                <br>
                <div class="hidden" id="payments_container">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Forma de Pago</th>
                                <th>Monto</th>
                                <th>Tipo</th>
                            </tr>
                        </thead>
                        <tbody id="payments_list"></tbody>
                    </table>
                </div>
                <div class="row" id="payment_type_container">
                    <div class="col-sm-6">
                        <label for="payment_type">Tipo de pago</label>
                        <select id="payment_type" class="form-select">
                            <option value="-1">--Sin tipo de pago--</option>
                        </select>
                    </div>
                    <div class="col-sm-6">
                        <label for="payment_type">Uso de CFDI</label>
                        <select id="cfdi_type" class="form-select">
                            <!--option value="-1">--Sin uso de CFDI--</option-->
                        </select>
                    </div>
                </div>
                <div id="bill_container" class="hidden">
                    <button
                        type="button"
                        class="btn btn-success form-control"
                        onclick="Bill();"
                    >
                        <i class="icon-ok-circle">Facturar</i>
                    </button>
                </div>
                <div id="download_container" class="hidden">
                    <button
                        type="button"
                        class="btn btn-success form-control"
                        id="files_download"
                        onclick="downloadFiles();"
                        
                    ><!-- url="http://localhost/clucesFact2023/c2lzdGVtYXM=/casaLucesBazar/code/ajax/fElectronica/zip.php?id_venta=64277"-->
                        <i class="icon-download-cloud">Descargar archivos</i>
                    </button>
                </div>
                <div id="email_container" class="hidden">
                    <input type="email" class="form-control" placeholder="Escribe correo destino">
                    <button
                        type="button"
                        class="btn btn-success form-control"
                        id="send_email_btn"
                        onclick="sendEmail();"
                    ><!--sale_folio=""-->
                        <i class="icon-email">Enviar por Correo</i>
                    </button>
                </div>
            </div>
            <div class="col-sm-1"></div>
        </div>
    </div>
</body>
</html>

<style>
    .global{
        position: absolute;
        height: 100%;
        top : 0;
        width: 100%;
        right : -5%;
       /* background-image: url("https://centroplenum.es/wp-content/uploads/2020/04/Fondo-rejilla-panal.jpg");
    */}
    .hidden{
        display : none;
    }
    #alert{
        position : fixed;
        top : 0;
        height: 100%;
        left : 0;
        width: 100%;
        background : rgba( 0, 0, 0, .5 );
        z-index: 100;
        display : none;
    }
    #alert_content{
        position: relative;
        width : 80%;
        left : 10%;
        min-height: 30%;
        max-height: 80%;
        top : 10%;
        background : white;
        box-shadow: 3px 3px 15px rgba( 0, 0, 0, .5 );
        padding: 20px;
    }
</style>