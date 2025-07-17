<?php
    $uploadDir = '../uploads/';
    $pdfName = basename($_FILES['pdf']['name']);
    $pdfPath = $uploadDir . $pdfName;
    
    if (move_uploaded_file($_FILES['pdf']['tmp_name'], $pdfPath)) {
        $outputBase = $uploadDir . 'page';
        $imagePath = $outputBase . ".jpg";
    
        // Convertir PDF a imagen con buena resolución
        shell_exec("/opt/homebrew/bin/pdftoppm -jpeg -r 100 -f 1 -singlefile \"$pdfPath\" \"$outputBase\"");
    
        // Escanear QR sin usar -q para capturar salida en PHP
        //$qrOutput = shell_exec("zbarimg \"$imagePath\"");
        $qrOutput = shell_exec("/opt/homebrew/bin/zbarimg \"$imagePath\"");
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
        }else{
            echo json_encode( array("status"=>"400", "message"=>"No se detectó ningún código QR en la imagen generada."));
        }
    }else{
        echo json_encode( array("status"=>"400", "message"=>"Error al subir el archivo."));
    }
?>