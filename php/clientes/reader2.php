
  <?php
   // include( 'getTaxDataByQr.php' );
  ?>
  <main class="wrapper" style="padding-top:2em">
    <section class="container" id="demo-content" style="display:;">
      <!--modal-->
      <div id="myNav" class="overlay">
        <a id="resetButton" class="closebtn">&times;</a>
        <div class="overlay-content">
          <div>
            <center>
              <video id="video" width="80%" height="50%" style="border: 1px solid gray"></video>
            <center>
          </div>
        </div>
      </div>
      <!-- Selección de cámara-->
      <div id="sourceSelectPanel" style="display:;"><!--style="display:none;"-->
        <label for="sourceSelect">Cámara</label>
        <select id="sourceSelect" class="form-select" style="max-width:200px">
        </select>
      <!--/div>
      < Acciones: Encender, Apagar >
      <div class="row"-->
        <button class="btn btn-success" id="startButton">Escanear
          <!-- <img src="assets/barcode.png" alt="Girl in a jacket" width="50" height="30" button> -->
        </button>
        <!-- <a class="button" id="resetButton">Detener</a> -->
      </div>
      <!-- Espacio de video-
      <div>
        <center>
          <video id="video" width="50%" height="30%" style="border: 1px solid gray"></video>
        <center>
      </div>-->
      <!-- Resultado-->
      <div style="display : ;">
        <label>Resultado:</label>
        <pre><code id="result"></code></pre>
      </div>
      <!-- <p>See the <a href="https://github.com/zxing-js/library/tree/master/docs/examples/multi-camera/">source code</a> for this example.</p> -->
    </section>
  </main>
  <!-- Modal Scanner -->
  <!--<div class="">
    <div id="id01" class="w3-modal">
      <div class="w3-modal-content">
        <div class="w3-container">
          <span id="resetButton" class="w3-button w3-display-topright">X</span>
          !-- Espacio de video-
          <div>
            <center>
              <video id="video" width="100%" height="100%" style="border: 1px solid gray"></video>
            <center>
          </div>
        </div>
      </div>
    </div>
  </div>-->

  <!-- <script type="text/javascript" src="https://unpkg.com/@zxing/library@latest/umd/index.min.js"></script> -->
  <script type="text/javascript" src="library/zxing.min.js"></script>
  <script type="text/javascript">
    escaner();
    setTimeout(function(){
        $('#startButton').click();
    }, 1000);
    //window.addEventListener('load', 
    function escaner() {
        //alert();
      var listaCodigos = [];
      let selectedDeviceId;
      const codeReader = new ZXing.BrowserMultiFormatReader()
      console.log('ZXing code reader initialized')
      codeReader.listVideoInputDevices()
        .then((videoInputDevices) => {//console.log(videoInputDevices);alert(videoInputDevices);
          const sourceSelect = document.getElementById('sourceSelect')
          selectedDeviceId = videoInputDevices[0].deviceId
          if (videoInputDevices.length >= 1) {
            videoInputDevices.forEach((element) => {
              const sourceOption = document.createElement('option')
              sourceOption.text = element.label
              sourceOption.value = element.deviceId
          /*local Storage*/
            if( localStorage.getItem( "bill_camera_selected" ) == element.deviceId ){
              //alert( localStorage.getItem( "bill_camera_selected" ) );
              document.getElementById('sourceSelectPanel').value = localStorage.getItem( "bill_camera_selected" );
              
              sourceOption.selected = true;
              //$( '#sourceSelectPanel' ).val( localStorage.getItem( "bill_camera_selected" ) );
            }
          /**/
              sourceSelect.appendChild(sourceOption)
            })

            sourceSelect.onchange = () => {
              selectedDeviceId = sourceSelect.value;
              /*local Storage*/
              //alert( $( '#sourceSelectPanel' ).val() );
              localStorage.setItem( "bill_camera_selected", sourceSelect.value );
          /**/
            };

            const sourceSelectPanel = document.getElementById('sourceSelectPanel')
            sourceSelectPanel.style.display = 'block'
          
          }

          //Función para leer código de barras
          document.getElementById('startButton').addEventListener('click', () => {
            //Limpia y abre modal
            document.getElementById('result').textContent = '';
            document.getElementById('myNav').style.display='block';
            //Listener para lectura de código de barras
            codeReader.decodeFromVideoDevice(selectedDeviceId, 'video', (result, err) => {
              if (result) {
                console.log(result);
                //alert("here : " + result.text);
                listaCodigos.push("Código: " + result.text + " --  Tipo: " + ZXing.BarcodeFormat[result.format]);
                //document.getElementById('result').textContent = "Código: " + result.text + " --  Tipo: " + ZXing.BarcodeFormat[result.format];
                document.getElementById('result').textContent = listaCodigos.toString();
                //document.getElementById('url_value').value = result.text;
                $( '#rfc_seeker' ).val(result.text);
                //document.getElementById('get_data_btn').click();
                var audio = new Audio('library/scanner.mp3');
                audio.play();
                check_if_exists_costumer( 'intro' );
                //document.getElementById('myNav').style.display='none';
                //codeReader.reset()
                close_emergent();
                return true;
              }
              if (err && !(err instanceof ZXing.NotFoundException)) {
                console.log(err);
                document.getElementById('result').textContent = err ;
              }
            })
            console.log(`Started continous decode from camera with id ${selectedDeviceId}`)
          })
          //Función para detener lectura
          document.getElementById('resetButton').addEventListener('click', () => {
            //Cierra modal y restablece lector
            document.getElementById('myNav').style.display='none';
            codeReader.reset()
            //document.getElementById('result').textContent = '';
            console.log('Reset.')
          })

        })
        .catch((err) => {
          console.log(err);
        })

    }//)
  </script>