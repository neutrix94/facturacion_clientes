var global_sale = null, global_costumer = null;
//busqueda de cliente por RFC
    function getClientByRfc( e ){
        if( e.keyCode != 13 && e != 'intro' ){
            return false;
        }
        var rfc  = $( '#costumer_rfc' ).val().trim();
        if( rfc.length <= 0 ){ 
            show_alert( '<h2 class="text-center text-danger">El RFC no puede ir vacio.</h2>' );
            return false;
        }
        var url = "php/routes.php?action=getClient&rfc=" + rfc;
        var resp = ajaxR( url );//alert(resp);
        var costumer_json = JSON.parse( resp );
        if( ! costumer_json.was_found || costumer_json.was_found == 'no' ){
            show_alert( `<div class="text-center">
                <h2 class="text-center text-danger icon-warning">El cliente '<b>${rfc}</b>' no fue encontrado,<br>¿Desea darlo de alta? </h2>
                <button
                    type="button"
                    class="btn btn-success"
                    onclick="location.href='php/clientes/index.php?'"
                >
                    <i class="icon-ok-circled">Dar de alta</i>
                </button>
                <br><br>
                <button
                    type="button"
                    class="btn btn-danger"
                    onclick="if(close_alert()){document.getElementById('costumer_rfc').select();}"
                >
                    <i class="icon-cancel-circled">Regresar</i>
                </button>
            </div>`, false );
        }else{
            global_costumer = costumer_json;
            setCostumer( costumer_json );
        }
    }
//setea cliente
    function setCostumer( costumer ){
        var contacts_html = ``;
        $( "#costumer_rfc" ).val( costumer.costumer.costumer_rfc );
        $( "#costumer_rfc" ).attr( 'disabled', true );
        $( "#rfc_seeker_reset_btn" ).removeClass( "hidden" );
        $( "#rfc_seeker_btn" ).addClass( "hidden" );

        $('#customer_seeker_container').addClass('hidden');
        $('#customer_name_container').removeClass('hidden');
        $('#customer_name').val(`${costumer.costumer.costumer_name} - ${costumer.costumer.costumer_id}`);
    //genera vista de contactos
        var count = 0;
        for (var key in costumer.contacts){
            contacts_html += `<tr>
                <td id="contact_0_${key}">${costumer.contacts[key].contact_name}</td>
                <td id="contact_1_${key}">${costumer.contacts[key].contact_email}</td>
                <td id="contact_2_${key}" value="${costumer.contacts[key].cfdi_use_id}">${costumer.contacts[key].cfdi_use_name}</td>
                <td class="text-center">
                    <input type="radio" name="contact_selected" id="contact_3_${key}" onclick="change_cfdi_use( ${key} );" contact_id="${costumer.contacts[key].contact_costumer_id}">
                </td>
            </tr>`;
            count ++;
        }
        contacts_html += `<tr>
            <td colspan="4">
                <button
                    class="btn btn-warning form-control"
                    onclick="edit_customer();"
                >
                    <i class="icon-plus">Actualizar o agregar contacto</i>
                </button>
            </td>
        </tr>`;
        //muestra contactos
        $( '#contacts_list' ).html( `${contacts_html}` );
        $( '#contacts_global_container' ).removeClass( 'hidden' );
        $( '#contacts_accordion_button').click();
        //$( '#contacts_container' ).removeClass( 'hidden' );
            /*if( count == 1 ){
            setTimeout( function(){
                $( '#contact_selected_0' ).prop( 'checked', true );
            }, 300 );*/
            //}
        //habilita buscador de nota de venta
        //$( '#sale_folio' ).removeAttr( 'disabled' );
        //$( "#sale_seeker_btn" ).removeClass( "hidden" );
        //$( "#sale_seeker_btn" ).removeAttr( "disabled" );
    }

    function edit_customer(){
        var url = "php/clientes/index.php?customerRfc=" + $('#costumer_rfc').val().trim();
        location.href = url;
    }
    
    function show_and_hidde_contacts_container(obj){
        var visibility = $(obj).attr("visibility");
        if( visibility == "false" ){
            $('#contacts_container').removeClass('hidden');
            $(obj).attr("visibility", "true");
            $('#contacts_accordion_icon').removeClass("icon-down-open");
            $('#contacts_accordion_icon').addClass("icon-up-open");
        }else{
            $('#contacts_container').addClass('hidden');
            $(obj).attr("visibility", "false");
            $('#contacts_accordion_icon').removeClass("icon-up-open");
            $('#contacts_accordion_icon').addClass("icon-down-open");
        }
    }
/*    function validate_if_contact_is_selected(){
        alert();
    }
*/    
    function change_cfdi_use( counter ){//alert( change_cfdi_use );
        if( $( '#contact_3_' + counter ).prop( 'checked' ) == true ){
            //alert();
            var value = $( '#contact_2_' + counter ).attr('value');//alert(value);
            var text = $( '#contact_2_' + counter ).html();
            $( '#cfdi_type' ).append( `<option value="${value}">${text}</option>` );
        //se habilitan 
            $( '#sale_container' ).removeClass( 'hidden' );
            $( '#sale_folio' ).removeAttr( 'disabled' );
            $( "#sale_seeker_btn" ).removeClass( "hidden" );
            $( "#sale_seeker_btn" ).removeAttr( "disabled" );
            $( '#sale_folio' ).focus();
            if( $("#contacts_accordion_button").attr("visibility") == "true" ){
                $("#contacts_accordion_button").click();
            }
        }
    }
//busqueda de venta por folio
    function getSaleByFolio( e ){
        if( e.keyCode != 13 && e != 'intro' ){
            return false;
        }
        var folio = $( '#sale_folio' ).val().trim();
        var url = "php/routes.php?action=getSale&sale_folio=" + folio;
        var resp = ajaxR( url );//alert(resp);
        var sale_json = JSON.parse( resp );
        if( ! sale_json.was_found || sale_json.was_found == 'no' ){
            show_alert( `<div class="text-center">
                <h2 class="text-center text-danger">La venta '${folio}' no fue encontrada, 
                verifica y vuelve a intentar; si el problema continua envia una captura de pantalla</h2>
            </div>` );
            //return false;
        }else if( sale_json.was_found && sale_json.was_found == 'invalid_month' ){
            var content = `<div class="text-center">
                <h3 class=\"text-center\"><b>Lo sentimos</b></h3>
                <h5>Su solicitud ha sido rechazada ya que la venta '${folio}' no corresponde al mes de la solicitud.<h5>
            </div>`;
            show_alert(content);
            //return false;
        }else if(sale_json.was_found && sale_json.was_found == 'yes'){
            global_sale = sale_json;
            setSale( sale_json );
            setTimeout( function(){
                setFinalPaymentType();
            }, 400);    
        }
    }

//setea venta
    function setSale( sale ){
        //console.log();
        var payments_html = ``;
        $( "#sale_folio" ).val( sale.sale.folio );
        $( "#sale_folio" ).attr( 'disabled', true );
        $( "#sale_seeker_reset_btn" ).removeClass( "hidden" );
        $( "#sale_seeker_btn" ).addClass( "hidden" );//return false;
        //genera vista de pagos
        for (var key in sale.sale_payments){
            payments_html += `<tr>
                <td value="${sale.sale_payments[key].payment_type_id}">${sale.sale_payments[key].payment_type_name}</td>
                <td class="text-end">${sale.sale_payments[key].ammount}</td>`;
            if( sale.sale_payments[key].payment_type_id == 7 ){
                var selected_1 = ( sale.sale_payments[key].payment_subtype == -1 ? ' selected' : '' );
                var selected_2 = ( sale.sale_payments[key].payment_subtype == 14 ? ' selected' : '' );
                var selected_3 = ( sale.sale_payments[key].payment_subtype == 11 ? ' selected' : '' );
                var disabled = ( sale.sale_payments[key].payment_subtype == -1 ? '' : '' );// disabled
                payments_html += `<td>
                    <select class="form-select" id="payment_subtype" 
                        onchange="updateSubtypePayment( this, ${sale.sale_payments[key].payment_id} );"
                        ${disabled}
                    >
                        <option value="-1" ${selected_1}>--Seleccionar--</option>
                        <option value="14" ${selected_2}>Débito</option>
                        <option value="11" ${selected_3}>Crédito</option>
                    </select>
                </td>`;
            }else{
                payments_html += `<td>n/a</td>`;
            }
            payments_html += `</tr>`;
        }
        //muestra contactos
        $( '#payments_list' ).html( `${payments_html}` );
        $( '#payments_container' ).removeClass( 'hidden' );
        $( '#bill_container' ).css( "display", "block" );

        $('#payments_container').removeClass('hidden');
        $('#payment_type_container').removeClass('hidden');
        //valida si la venta fue facturada
        if(sale.sale.id_status_facturacion == 8){
            download_and_send(`${sale.sale.url_descarga_archivos_facturacion}`);
            //$( '#files_download' ).attr( "url", `${sale.sale.url_descarga_archivos_facturacion}` );//code/ajax/fElectronica/zip.php?id_venta= + json_resp.bill_system_id
            //$( '#download_container' ).removeClass( 'hidden' );//hace visible boton para descargar archivos
            //$( '#send_email_btn' ).attr( "sale_folio", `${global_sale.sale.folio}` );
            //$( '#email_container' ).removeClass( 'hidden' );//hace visible boton para enviar correo
            //$( '#bill_container' ).addClass( "hidden" );//oculta boton de facturacion
            $( '#bill_container' ).css( "display", "none" );//oculta boton de facturacion
            $( '#payment_type_container' ).css( "display", "none" );//oculta boton de facturacion
            $( '#payments_container' ).css( "display", "none" );//oculta boton de facturacion
            $( '#contacts_container' ).css("display", "none");
            $( '#special_messages' ).html("La nota de venta ya habia sido facturada anteriormente.");
        }
    }

    function setFinalPaymentType(){
    //recorre los tipos de pagos
        var payments = new Array();
        var payments_types = new Array();
    //valida tipos de pago
        $( '#payments_list tr' ).each( function ( index ){
            $( this ).children('td').each( function ( index2 ){
                if( index2 == 0 ){
                    //alert( $(this).attr("value") );
                    if( ! payments_types.includes( $(this).attr("value") ) ){
                        payments_types.push( $(this).attr("value") );
                    }
                }
            });
        });
        //alert(payments_types);
        if( payments_types.length > 1 ){
            $( '#payment_type' ).empty();
            $( '#payment_type' ).html( '<option value="17">OTROS</option>' );
        }else{
            if( payments_types.length == 1 && payments_types[0] == 1 ){
                $( '#payment_type' ).html( '<option value="1">EFECTIVO</option>' );
            }else if( payments_types.length == 1 && payments_types[0] == 2 ){
                $( '#payment_type' ).html( '<option value="17">OTROS</option>' );
            }else if( payments_types.length == 1 && payments_types[0] == 8 ){
                                    $( '#payment_type' ).html( '<option value="9">TRANSFERENCIA</option>' );
            }else if( payments_types.length == 1 ){//&& payments_types[0] == 1 
            //recorre tipos de pagos
                payments_types = new Array();
                $( '#payments_list tr' ).each( function ( index ){
                    $( this ).children('td').each( function ( index2 ){
                        if( index2 == 2 ){
                            $( this ).children('select').each( function( index3 ){
                                if( $( this ).val() == 11 ){
                                    $( '#payment_type' ).html( '<option value="11">TARJETA DE CRÉDITO</option>' );
                                }else if(  $( this ).val() == 14 ){
                                    $( '#payment_type' ).html( '<option value="14">TARJETA DE DÉBITO</option>' );
                                }
                            });
                        }
                    });
                });
            }
        }
    }

    function updateSubtypePayment( obj, payment_id ){
        var payment_subtype =  $( obj ).val();
        if( payment_subtype == -1 ){
            return false;
        }else{
            var url = `php/routes.php?action=updatePaymentSubtype&payment_id=${payment_id}&payment_subtype=${payment_subtype}`;
            var resp = ajaxR( url );
            alert( resp );
        }

    }

    function Bill(){
        var cfdi_use = $( '#cfdi_type' ).val();
        if( cfdi_use == '' || cfdi_use == 0 || cfdi_use == null ){
            alert( "Debes de elegir un contacto para continuar." );
            return false;
        }
        var contact_id = null;
        $('#contacts_list tr').each(function(index1){
            if($('#contact_3_' + index1).prop('checked') == true){
                contact_id = $('#contact_3_' + index1).attr('contact_id');
            }
        });
        if(contact_id == null){
            alert( "Debes de elegir un contacto para continuar." );
            return false;
        }
        show_alert( `<h3 class="text-center">Generando factura...</h3>
            <div class="text-center">
                <img src="img/load.gif" width="200px">
            </div>`, false );
    //cliente y folio de la nota de venta
        setTimeout( function(){
            var costumer = global_costumer.costumer.costumer_rfc;
            var sale = global_sale.sale.folio;
            var payment_type = $( '#payment_type' ).val();
            if( payment_type == '' || payment_type == -1 || payment_type == null ){
                alert( "El tipo de pago es requerido." );
                return false;
            }
            var url = `php/routes.php?action=sendBill&sale_folio=${sale}&sale_costumer=${costumer}&cfdi=${cfdi_use}&payment_type=${payment_type}&contact_id=${contact_id}`;
//alert(url);
            var resp = ajaxR( url );
//alert( resp );
            var text_color = "text-success";
            resp = resp.replaceAll(/\\'/g, "'");//resp.replaceAll("\'", "'");
            var json_resp = JSON.parse(resp);
            if(json_resp.sub_status){
                text_color = "text-danger";
            }
            var content = `<h2 class="text-center ${text_color}">${json_resp.message}</h2>`;
            if(json_resp.sub_status){
                content += `<h4 class="text-center text-danger">Error : ${json_resp.sub_status}</h4>`;
            }
            if( json_resp.files_url && json_resp.bill_system_id ){
                download_and_send(`${json_resp.files_url}/code/ajax/fElectronica/zip.php?id_venta=${json_resp.bill_system_id}`);
                //$( '#files_download' ).attr( "url", `${json_resp.files_url}/code/ajax/fElectronica/zip.php?id_venta=` + json_resp.bill_system_id );
                //$( '#download_container' ).removeClass( 'hidden' );//hace visible boton para descargar archivos
                //$( '#send_email_btn' ).attr( "sale_folio", `${global_sale.sale.folio}` );
                //$( '#email_container' ).removeClass( 'hidden' );//hace visible boton para enviar correo
                if( json_resp.status == 200 ){
                    $( '#bill_container' ).addClass( "hidden" );//oculta boton de facturacion
                    $( '#bill_container' ).css( "display", "none" );//oculta boton de facturacion
                }
            }else{
                show_alert( content );//+ resp
            }
        }, 1000 );
    }

    /*function downloadFiles(){
        var url = $('#files_download').attr( "url" );
        var ventana = window.open(url, '_blank');
        setTimeout(function(){
            ventana.close();
            location.reload();
        },2000);
    }*/

    function downloadFiles(type) {
        //const url = 'ruta/a/tu/archivo.ext'; // Aquí pones la URL del archivo que deseas descargar
        var url = $('#files_download').attr( "url" );
        const nombreArchivo = 'factura_comprimida.zip'; // Nombre con el que deseas guardar el archivo
        // Crea un enlace temporal
        const enlace = document.createElement('a');
        enlace.href = url;
        if(type == 'pdf'){
            url = url.replace('zip.php?', 'pdf_download.php?');
            nombreArchivo = "factura.pdf";
        }else if(type == 'xml'){
            url = url.replace('zip.php?', 'xml_download.php?');
            nombreArchivo = "factura.xml";
        }
        enlace.download = nombreArchivo; // El atributo 'download' sugiere el nombre para guardar el archivo

        // Agrega el enlace al DOM y simula un clic
        document.body.appendChild(enlace);
        enlace.click();

        // Elimina el enlace después de la descarga
        document.body.removeChild(enlace);
    }

    function sendEmail(){
        var sale_folio = $( '#send_email_btn' ).attr( 'sale_folio' );
        var custom_email = $('#custom_email').val().trim();
        var url = `php/routes.php?action=sendEmail&sale_folio=${sale_folio}`;//alert(url);
        if(custom_email.length > 0){
            url += `&custom_email=${custom_email}`;
        }
        var resp = ajaxR( url );//alert(resp);
        var email_json = JSON.parse( resp );
        var color_class = ( email_json.status == 400 ? "text-danger" : "text-success" );
        show_alert( `<h3 class="text-center"></h3>
            <div class="text-center">
                <h2 class="${color_class}">${email_json.message}</h2>
                <div class="text-center">
                    <button
                        type="button"
                        class="btn btn-success"
                        onclick="location.reload();"
                    >
                        <i class="icon-ok-cirlced">Aceptar</i>
                    </button>
                </div>
            </div>`, false );
    }

//$( "#sale_seeker_reset_btn" ).removeClass( "hidden" );

    function resetBillingForm(){
        $( "#costumer_rfc" ).val( '' );
        $( "#costumer_rfc" ).removeAttr( 'disabled' );
        $( "#rfc_seeker_reset_btn" ).addClass( "hidden" );
        $( "#rfc_seeker_btn" ).removeClass( "hidden" );
    //limpia tabla de contactos
        $( '#contacts_list' ).empty();
        $( "#sale_folio" ).val( '' );
        //$( "#sale_folio" ).removeAttr( 'disabled' );
        $( "#sale_seeker_reset_btn" ).addClass( "hidden" );
        $( "#sale_seeker_btn" ).removeClass( "hidden" );
        $( "#sale_seeker_btn" ).prop( "disabled", true );
    //limpia tabla de pagos
        $( '#payments_list' ).empty();
        $( "#costumer_rfc" ).focus();
    }

    function download_and_send(files_url){
        var content = `<div class="text-center">
            <i class="icon-ok-circled text-success" style="font-size : 300%;"></i>
            <h2 class="text-success">La factura esta lista</h2>
            <p>
                Los documentos de facturación electrónica están listos para su descarga y/o envió a correo electrónico.
            </p>
        </div>
        <div class="row">
            <div id="download_container" class="col-6 p-3 text-center">
                <button
                    type="button"
                    class="btn btn-success form-control"
                    id="files_download_xml_btn"
                    onclick="downloadFiles('xml');"
                    url="${files_url}"
                >
                    <i class="icon-download-cloud">Descargar XML</i>
                </button>
            </div>
            <div id="download_container" class="col-6 p-3 text-center">
                <button
                    type="button"
                    class="btn btn-success form-control"
                    id="files_download_pdf_btn"
                    onclick="downloadFiles('pdf');"
                    url="${files_url}"
                >
                    <i class="icon-download-cloud">Descargar PDF</i>
                </button>
            </div>
            <div id="download_container" class="col-6 p-3 text-center">
                <button
                    type="button"
                    class="btn btn-success form-control"
                    id="files_download"
                    onclick="downloadFiles('zip');"
                    url="${files_url}"
                >
                    <i class="icon-download-cloud">Descargar archivos (comprimido)</i>
                </button>
            </div>
            <div id="email_container" class="col-6 p-3 text-center">
                <button
                    type="button"
                    class="btn btn-success form-control"
                    id="send_email_btn"
                    onclick="sendEmail();"
                    sale_folio="${global_sale.sale.folio}"
                >
                    <i class="icon-email">Enviar a Correo de Contacto</i>
                </button>
            </div>
            <div class="col-12">
                <p class="text-center text-red">NOTA : POR EL MOMENTO NO SE PUEDEN ENVIAR CORREOS A GMAIL</p>
                <h4>Escribe aqui el correo destino (opcional)</h4>
                <input type="email" id="custom_email" class="form-control" placeholder="Escribe aqui el correo destino (opcional)">
                <br>
                <button
                    type="btn btn-success form-control"
                    onclick="sendEmail();"
                    sale_folio="${global_sale.sale.folio}"
                >
                    Enviar a este correo
                </button>
            </div>
        </div>`;
        show_alert(content, false);
        //$( '#files_download' ).attr( "url", `${files_url}` );
        //$( '#download_container' ).removeClass( 'hidden' );//hace visible boton para descargar archivos
        //$( '#send_email_btn' ).attr( "sale_folio", `${global_sale.sale.folio}` );
        //$( '#email_container' ).removeClass( 'hidden' );//hace visible boton para enviar correo
    }

    function show_alert( message, close_btn = true ){
        var content = message;
        content += ( close_btn ? close_btn_html : `` );
        $( '#alert_content' ).html( content );
        $( '#alert' ).css( 'display', 'block' );
    }

    function close_alert(){
        $( '#alert_content' ).html( '' );
        $( '#alert' ).css( 'display', 'none' );
        return true;
    }

    function ajaxR( url ){
        if(window.ActiveXObject){       
            var httpObj = new ActiveXObject("Microsoft.XMLHTTP");
        }
        else if (window.XMLHttpRequest)
        {       
            var httpObj = new XMLHttpRequest(); 
        }
        httpObj.open("POST", url , false, "", "");
        httpObj.send(null);
        return httpObj.responseText;
    }

    var close_btn_html = `<br><br>
    <div class="row etxt-center">
       
        <button
            type="button"
            class="btn border-success text-success"
            onclick="close_alert();"
        >
            <i class="icon-ok-circled">Aceptar y cerrar</i>
        </button>
    </div>`;

