
    function show_upload_pdf_form(){
        var content = `<div class="text-center">
            <form enctype="multipart/form-data">
                <div class="text-center p-3">
                    <input type="file" id="form_pdf_input" name="pdf" accept="application/pdf" onchange="validarTamano(this)" style="display:none;" required>
                    <button
                        type="button"
                        id="upload_selection_pdf_button"
                        class="btn btn-danger form-control"
                        onclick="pdf_selection();"
                    >
                        <i class="icon-file-pdf">Seleccionar PDF</i>
                    </button>
                </div>
                <div id="pdf_name_container" class="text-center p-3 input-group" style="display: none;">
                    <input id="pdf_name_input" class="form-control bg-light" readonly>
                    <button
                        type="button"
                        class="btn btn-danger icon-cancel-circled"
                    >
                    </button>
                </div>
                <div class="m-2">
                    <button 
                        type="button"
                        id="upload_pdf_button"
                        class="btn btn-success form-control"
                        onclick="uploadPDF();"
                        style="display:none;"
                    >
                        <i class="icon-upload">Subir PDF y procesar</i>
                    </button>
                </div>
            </form>
        </div>`;
        $('.emergent_content').html(content);
        $('.emergent').css('display', 'block');
    }

    function show_upload_picture_form(){
        var content = `<div class="text-center">
            <form enctype="multipart/form-data">
                <div class="text-center p-3">
                    <input type="file" id="form_img_input" name="pdf" accept="image/*" required class="btn btn-warning" onchange="mostrarImagen(event)" style="display:none;" required>
                    <button
                        type="button"
                        id="upload_selection_picture_button"
                        class="btn btn-primary form-control"
                        onclick="image_selection();"
                    >
                        <i class="icon-picture">Seleccionar imágen</i>
                    </button>
                </div>

                <div id="image_name_container" class="text-center p-3 input-group" style="display: none;">
                    <input id="image_name_input" class="form-control bg-light" readonly>
                    <button
                        type="button"
                        class="btn btn-danger icon-cancel-circled"
                    >
                    </button>
                </div>
                <div class="text-center p-3">
                    <button 
                        type="button"
                        id="upload_picture_button"
                        class="btn btn-success form-control"
                        style="display:none;"
                        onclick="uploadImage();"
                    >
                        <i class="icon-upload">Subir Imágen y procesar</i>
                    </button>
                </div>
            </form>
        </div>`;
        $('.emergent_content').html(content);
        $('.emergent').css('display', 'block');
    }

    function image_selection(){
        $('#form_img_input').click();
    }
    function pdf_selection(){
        $('#form_pdf_input').click();
    }
    
    function validarTamano(input) {
        const archivo = input.files[0];
        const tamanoMaximo = 1024*200; // 1MB en bytes * 1024

        if (archivo && archivo.size > tamanoMaximo) {
            var size_kb = Math.round(archivo.size/ 1024);
            alert(`El archivo excede el tamaño máximo permitido (200 KB) (${size_kb} KB).`);
            input.value = ''; // Limpiar el input
            return false;
        }

        $('#upload_pdf_button').css("display", "block");
        $('#pdf_name_container').css("display", "");
        var file_name = $('#form_pdf_input').val();
        file_name = file_name.replace("C:\\fakepath\\", '');
        $('#pdf_name_input').val(file_name);
    }
    
    function mostrarImagen(event) {
        $('#upload_picture_button').css("display", "block");
        $('#image_name_container').css("display", "");
        var file_name = $('#form_img_input').val();
        file_name = file_name.replace("C:\\fakepath\\", '');
        $('#image_name_input').val(file_name);
    }

    function uploadImage() {
        const formData = new FormData();
        const fileInput = $('#form_img_input')[0];
        const file = fileInput.files[0];
        if (!file) {
            alert('Por favor seleccione una imágen de su cédula fiscal para continuar.');
            return;
        }
        var content = `<div class="text-center">
            <br>
            <h3 class="text-center">Procesando Imágen</h3>
            <img src="../../img/load.gif" width="30%">
        </div>`;
        $('.emergent_content').html(content);
        
        formData.append('pdf', file);

        $.ajax({
            url: 'ajax/procesar_img.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                var json_resp = JSON.parse(response);
                if(json_resp.status && json_resp.status == 200){
                    $('#rfc_seeker').val(json_resp.message);
                    setTimeout( function(){
                        check_if_exists_costumer( 'intro' );
                        close_alert();
                    }, 500);
                }else{
                    alert(json_resp.message);
                    location.reload();
                }
            }, 
            error: function(xhr, status, error) {
                alert('Error al subir imagen:\n' + error);
            }
        });
    }

    function uploadPDF() {
        const formData = new FormData();
        const fileInput = $('#form_pdf_input')[0];
        const file = fileInput.files[0];
        if (!file) {
            alert('Por favor seleccione un PDF de su cédula fiscal para continuar.');
            return;
        }
        var content = `<div class="text-center">
            <br>
            <h3 class="text-center">Procesando PDF</h3>
            <img src="../../img/load.gif" width="30%">
        </div>`;
        $('.emergent_content').html(content);
        
        formData.append('pdf', file);

        $.ajax({
            url: 'ajax/procesar_pdf.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                var json_resp = JSON.parse(response);
                if(json_resp.status && json_resp.status == 200){
                    $('#rfc_seeker').val(json_resp.message);
                    setTimeout( function(){
                        check_if_exists_costumer( 'intro' );
                        close_alert();
                    }, 500);
                }else{
                    alert(json_resp.message);
                    location.reload();
                }
            }, 
            error: function(xhr, status, error) {
                alert('Error al subir imagen:\n' + error);
            }
        });
    }