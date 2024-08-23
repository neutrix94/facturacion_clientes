<div?php
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
    <div class="row global">
        <div class="row">
            <div class="col-sm-4 text-center"></div>
            <div class="col-sm-4 text-center">
                <img src="./img/Logo.png" width="70%">
            </div>
        </div>
        <div class="row">
            <div class="col-sm-2"></div>
            <div class="col-sm-8">
                <div class="input-group">
                    <input type="text" class="form-control" id="costumer_rfc" 
                    onkeyup="getClientByRfc( event )"
                    placeholder="Digita RFC">
                    <button
                        type="button"
                        class="btn btn-warning"
                        onclick="getClientByRfc( 'intro' )"
                    >
                        <i class="icon-search"></i>
                    </button>
                </div>
                <br>
                <div class="input-group">
                    <input type="text" class="form-control" id="costumer_rfc" 
                    placeholder="Digita Folio Nota" disabled>
                    <button
                        type="button"
                        class="btn btn-primary"
                        disabled
                    >
                        <i class="icon-search"></i>
                    </button>
                </div>
                <br>
                <div class="">
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
                <div>
                    <input type="email" class="form-control" placeholder="Escribe correo destino">
                    <button
                        type="button"
                        class="btn btn-success form-control"
                    >
                        <i class="icon-email">Enviar por Correo</i>
                    </button>
                </div>
                <br>
                <div>
                    <button
                        type="button"
                        class="btn btn-success form-control"
                    >
                        <i class="icon-download-cloud">Descargar archivos</i>
                    </button>
                </div>
            </div>
            <div class="col-sm-2"></div>
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