    var global_sale = null, global_costumer = null;
//busqueda de cliente por RFC
    function getClientByRfc( e ){
        if( e.keyCode != 13 && e != 'intro' ){
            return false;
        }
        var rfc  = $( '#costumer_rfc' ).val().trim();
        if( rfc.length <= 0 ){ 
            show_alert( '<h2 class="text-center text-danger">El RFC no puede ir vacio: </h2>' );
            return false;
        }
        var url = "php/routes.php?action=getClient&rfc=" + rfc;
        var resp = ajaxR( url );//alert(resp);
        var costumer_json = JSON.parse( resp );
        if( ! costumer_json.was_found || costumer_json.was_found == 'no' ){
            show_alert( '<h2 class="text-center text-danger">El cliente no fue encontrado, se tiene que dar de alta en el siguienete enlace : </h2>' );
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
        //genera vista de contactos
        var count = 0;
        for (var key in costumer.contacts){
            contacts_html += `<tr>
                <td id="contact_0_${key}">${costumer.contacts[key].contact_name}</td>
                <td id="contact_1_${key}">${costumer.contacts[key].contact_email}</td>
                <td id="contact_2_${key}" value="${costumer.contacts[key].cfdi_use_id}">${costumer.contacts[key].cfdi_use_name}</td>
                <td class="text-center">
                    <input type="radio" name="contact_selected" id="contact_3_${key}" onclick="change_cfdi_use( ${key} );">
                </td>
            </tr>`;
            count ++;
        }
        //muestra contactos
        $( '#contacts_list' ).html( `${contacts_html}` );
        $( '#contacts_container' ).removeClass( 'hidden' );
            /*if( count == 1 ){
            setTimeout( function(){
                $( '#contact_selected_0' ).prop( 'checked', true );
            }, 300 );*/
            //}
        //habilita buscador de nota de venta
        $( '#sale_folio' ).removeAttr( 'disabled' );
        $( "#sale_seeker_btn" ).removeClass( "hidden" );
        $( "#sale_seeker_btn" ).removeAttr( "disabled" );
        $( '#sale_folio' ).focus();
    }
    
    function change_cfdi_use( counter ){//alert( change_cfdi_use );
        if( $( '#contact_3_' + counter ).prop( 'checked' ) == true ){
            //alert();
            var value = $( '#contact_2_' + counter ).attr('value');//alert(value);
            var text = $( '#contact_2_' + counter ).html();
            $( '#cfdi_type' ).append( `<option value="${value}">${text}</option>` );
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
            show_alert( `<h2 class="text-center text-danger">La venta  no fue encontrada, 
                verifica y vuelve a intentar; si el problema continua envia una captura de pantalla</h2>` );
        }else{
            global_sale = sale_json;
            setSale( sale_json );
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
                var selected_2 = ( sale.sale_payments[key].payment_subtype == 1 ? ' selected' : '' );
                var selected_3 = ( sale.sale_payments[key].payment_subtype == 2 ? ' selected' : '' );
                var disabled = ( sale.sale_payments[key].payment_subtype == -1 ? '' : ' disabled' );
                payments_html += `<td>
                    <select class="form-select" id="payment_subtype" 
                        onchange="updateSubtypePayment( this, ${sale.sale_payments[key].payment_id} );"
                        ${disabled}
                    >
                        <option value="-1" ${selected_1}>--Seleccionar--</option>
                        <option value="1" ${selected_2}>Débito</option>
                        <option value="2" ${selected_3}>Crédito</option>
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
        show_alert( `<h3 class="text-center">Generando factura...</h3>
            <div class="text-center">
                <img src="img/load.gif" width="200px">
            </div>`, false );
    //cliente y folio de la nota de venta
        setTimeout( function(){
            var costumer = global_costumer.costumer.costumer_rfc;
            var sale = global_sale.sale.folio;
            var url = `php/routes.php?action=sendBill&sale_folio=${sale}&sale_costumer=${costumer}&cfdi=` + cfdi_use;
            var resp = ajaxR( url );
            var json_resp = JSON.parse(resp);
            var content = `<h2>${json_resp.message}</h2>`;
            if( json_resp.files_url && json_resp.bill_system_id ){
                $( '#files_download' ).attr( "url", `${json_resp.files_url}/code/ajax/fElectronica/zip.php?id_venta=` + json_resp.bill_system_id );
                $( '#download_container' ).removeClass( 'hidden' );//hace visible boton para descargar archivos
                $( '#send_email_btn' ).attr( "sale_folio", `${global_sale.sale.folio}` );
                $( '#email_container' ).removeClass( 'hidden' );//hace visible boton para enviar correo
                if( json_resp.status == 200 ){
                    $( '#bill_container' ).addClass( "hidden" );//oculta boton de facturacion
                    $( '#bill_container' ).css( "display", "none" );//oculta boton de facturacion
                }
            }
            show_alert( content );//+ resp
        }, 1000 );
    }

    function downloadFiles(){
        // Hacer una solicitud fetch para obtener el archivo ZIP
        var url = $('#files_download').attr( "url" );
        if( url == "" ){
            alert( "No hay nota de venta facturada" );
            return false;
        }
        fetch(url)
            .then(response => response.blob())  // Convertir la respuesta en un blob
            .then(blob => {
                // Crear un enlace temporal
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = 'archivo.zip';  // Nombre del archivo ZIP para la descarga
                document.body.appendChild(a);
                a.click();
               // a.remove();  // Eliminar el enlace temporal
                window.URL.revokeObjectURL(url);  // Liberar el objeto URL
                show_alert( `<h3 class="text-center"></h3>
                    <div class="text-center">
                        <h2 class="text-success">Archivos descargados exitosamente.</h2>
                    </div>`, true );
            })
            .catch(() => alert('Error al descargar el archivo.'));
            
    }

    function sendEmail(){
        var sale_folio = $( '#send_email_btn' ).attr( 'sale_folio' );
        var url = `php/routes.php?action=sendEmail&sale_folio=${sale_folio}`;alert(url);
        var resp = ajaxR( url );
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

    function show_alert( message, close_btn = true ){
        var content = message;
        content += ( close_btn ? close_btn_html : `` );
        $( '#alert_content' ).html( content );
        $( '#alert' ).css( 'display', 'block' );
    }

    function close_alert(){
        $( '#alert_content' ).html( '' );
        $( '#alert' ).css( 'display', 'none' );
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
            class="btn btn-success"
            onclick="close_alert();"
        >
            <i class="icon-ok-circled">Aceptar y cerrar</i>
        </button>
    </div>`;

