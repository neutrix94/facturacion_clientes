
    function getClientByRfc(){
        var rfc  = $( '#costumer_rfc' ).val().trim();
        if( rfc.length <= 0 ){ 
            show_alert( '<h2 class="text-center text-danger">El RFC no puede ir vacio: </h2>' );
            return false;
        }
        var url = "php/routes.php?&route=getClient";
       // var resp = ajaxR( url );
        var resp_json = '';//JSON.parse( resp );
        if( ! resp_json.rfc ){
            show_alert( '<h2 class="text-center text-danger">El cliente no fue encontrado, se tiene que dar de alta en el siguienete enlace : </h2>' );
        }

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

