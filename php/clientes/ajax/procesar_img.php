<?php
echo shell_exec("which zbarimg");
$uploadDir = '../uploads/';
$imgName = basename($_FILES['pdf']['name']);
//echo "NOMBRE : {$pdfName}";
$imgPath = "{$uploadDir}{$imgName}";
//die("name : " . $_FILES['pdf']['tmp_name']);
if (move_uploaded_file($_FILES['pdf']['tmp_name'], $imgPath)) {
    $extension = pathinfo($imgName, PATHINFO_EXTENSION);
    $fileName = pathinfo($imgName, PATHINFO_FILENAME);

    $tamanoArchivo = round(filesize("{$uploadDir}{$imgName}")/1024);
//echo round($tamanoArchivo);
    //die("extension = {$extension}");
    if(strtoupper($extension) == "HEIC" || $tamanoArchivo >= 1500){//|| strtoupper($extension) == "JPEG"|| strtoupper($extension) == "PNG"
        $imgPath = "{$uploadDir}{$fileName}.jpg";
        $qrOutput = shell_exec("/opt/homebrew/bin/magick {$uploadDir}{$imgName} -resize 30% {$imgPath}");
//echo "salida : {$qrOutput}";
    }/*else if(){

    }*/
    /*$outputBase = $uploadDir . 'page';
    $imagePath = $outputBase . ".jpg";
    // Convertir PDF a imagen con buena resolución
    shell_exec("pdftoppm -jpeg -r 400 -f 1 -singlefile \"$pdfPath\" \"$outputBase\"");*/

    // Escanear QR sin usar -q para capturar salida en PHP
    //$qrOutput = shell_exec("zbarimg \"$imagePath\"");
    $qrOutput = shell_exec("/opt/homebrew/bin/zbarimg \"$imgPath\"");
    if ($qrOutput) {
        $lines = explode("\n", trim($qrOutput));
        foreach ($lines as $line) {
            if (strpos($line, 'QR-Code:') === 0) {
                $qrData = substr($line, strlen('QR-Code:'));
                //echo "QR detectado: <a href=\"$qrData\" target=\"_blank\">$qrData</a>";
                echo json_encode( array("status"=>"200", "message"=>"{$qrData}"));
                break;
            }
        }
        //echo "<strong>Resultado:</strong><br>";
        //echo nl2br(htmlentities($qrOutput));
    } else {
        echo json_encode( array("status"=>"400", "message"=>"No se detectó ningún código QR en la imagen generada."));
    }
} else {
    echo json_encode( array("status"=>"400", "message"=>"Error al subir el archivo."));
}
?>