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

    <div class="global p-1">
<!--Encabezado con imagen-->
        <div class="text-center">
            <img src="./img/Logo.png" width="40%" class="">
            <br>
            <h3 class="fs-1">Facturación de compras</h3>
        </div>

        <div class="row">
            <div class="col-sm-1"></div>
            <div class="col-sm-10">
                <div class="input-group" id="customer_seeker_container">
                    <input type="text" class="form-control" id="costumer_rfc" 
                    onkeyup="getClientByRfc( event )"
                    placeholder="Digite RFC">
                    <button
                        type="button"
                        id="rfc_seeker_btn"
                        class="btn btn-dark"
                        onclick="getClientByRfc( 'intro' )"
                    >
                        <i class="icon-search"></i>
                    </button>
                </div>
                <div class="input-group hidden" id="customer_name_container">
                    <input type="text" class="form-control" id="customer_name" 
                    onkeyup="getClientByRfc( event )"
                    disabled>
                    <button
                        type="button"
                        id="rfc_seeker_reset_btn"
                        class="btn btn-danger"
                        onclick="location.reload();"
                    >
                        <i class="icon-spin3"></i>
                    </button>
                </div>
                <div id="contacts_global_container" class="hidden">
                    <div class="text-center p-0 m-0">
                        <button
                            id="contacts_accordion_button"
                            class="btn btn-info form-control text-center text-light"
                            onclick="show_and_hidde_contacts_container(this);"
                            visibility="false"
                        >
                            Seleccionar contacto <i id="contacts_accordion_icon" class="icon-down-open"></i>
                        </button>
                    </div>
                    <div class="hidden" id="contacts_container">
                        <table class="table table-striped table-bordered" style="font-size : 60% !important;">
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
                </div>
                <br>
                <div class="input-group hidden" id="sale_container">
                    <input type="text" class="form-control border-dark" id="sale_folio" 
                    placeholder="Digite Folio Nota" onkeyup="getSaleByFolio( event );" disabled>
                    <button
                        type="button"
                        class="btn btn-dark"
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
                   <h3 id="special_messages" class="text-center"></h3> 
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
                <div class="row hidden" id="payment_type_container">
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
                    <br>
                    <button
                        type="button"
                        class="btn btn-success form-control"
                        onclick="Bill();"
                    >
                        <i class="icon-ok-circle">Solicitar Factura</i>
                    </button>
                    <br><br>
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
        margin-left : 2px;
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