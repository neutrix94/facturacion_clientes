<?php
    if( isset( $_POST['action'] ) || isset( $_GET['action'] ) ){
        //include( 'bd.php' );
        //$db = new db();
       // $link =
        $Routes = new Routes();
        $action = ( isset( $_POST['action'] ) ? $_POST['action'] : $_GET['action'] );
        switch ( $action ) {
            case 'getClient' :
                $rfc = ( isset( $_POST['rfc'] ) ? $_POST['rfc'] : $_GET['rfc'] );
                $post_data = json_encode( array( "rfc"=>$rfc ) );
                $url = $Routes->getPath( 'billing_api' );
                echo $Routes->sendPetition( "{$url}/busca_clientes_por_rfc", $post_data );
            break;

            case 'getSale' :
                $sale_folio = ( isset( $_POST['sale_folio'] ) ? $_POST['sale_folio'] : $_GET['sale_folio'] );
                $post_data = json_encode( array( "folio"=>$sale_folio ) );
                $url = $Routes->getPath( 'billing_api' );
                echo $Routes->sendPetition( "{$url}/busca_ventas_por_folio", $post_data );
            break;

            case 'updatePaymentSubtype' :
                $payment_id = ( isset( $_POST['payment_id'] ) ? $_POST['payment_id'] : $_GET['payment_id'] );
                $payment_subtype = ( isset( $_POST['payment_subtype'] ) ? $_POST['payment_subtype'] : $_GET['payment_subtype'] );
                $post_data = json_encode( array( "payment_id"=>$payment_id, "payment_subtype"=>$payment_subtype ) );
                $url = $Routes->getPath( 'billing_api' );
                echo $Routes->sendPetition( "{$url}/actualiza_subtipo_pago", $post_data );
            break;

            case 'sendBill' :
                $sale_folio = ( isset( $_POST['sale_folio'] ) ? $_POST['sale_folio'] : $_GET['sale_folio'] );
                $sale_costumer = ( isset( $_POST['sale_costumer'] ) ? $_POST['sale_costumer'] : $_GET['sale_costumer'] );
                $cfdi = ( isset( $_POST['cfdi'] ) ? $_POST['cfdi'] : $_GET['cfdi'] );
                $post_data = json_encode( array( "sale_folio"=>$sale_folio, "sale_costumer"=>$sale_costumer, "cfdi_use"=>$cfdi ) );
                $url = $Routes->getPath( 'billing_api' );
                echo $Routes->sendPetition( "{$url}/inserta_venta_sistema_facturacion", $post_data );
            break;

            case 'getBillFiles' :
                
            break;

            case 'sendEmail' : 
                $sale_folio = ( isset( $_POST['sale_folio'] ) ? $_POST['sale_folio'] : $_GET['sale_folio'] );
                $post_data = json_encode( array( "sale_folio"=>$sale_folio ) );
                $url = $Routes->getPath( 'billing_api' );
                echo $Routes->sendPetition( "{$url}/envia_factura_correo", $post_data );
            break;
                
            default :
                echo( "Permission denied on '{$action}'" );
            break;
        }
    }

    final class Routes{
       // private $api_path;
        public function __construct(  ) {
            //$api_url = "{$api_url}/busca_clientes_por_rfc";
        }

        public function getPath( $item_name ){
            $data = file_get_contents( "../config/apis.json");
            $config = json_decode($data, true);
            return $config["{$item_name}"];
        }
        public function sendPetition( $url, $post_data ){
			$resp = "";
            $token = "";
			$crl = curl_init( "{$url}" );
			curl_setopt($crl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($crl, CURLINFO_HEADER_OUT, true);
			curl_setopt($crl, CURLOPT_POST, true);
			curl_setopt($crl, CURLOPT_POSTFIELDS, $post_data);
			//curl_setopt($ch, CURLOPT_NOSIGNAL, 1);
		    curl_setopt($crl, CURLOPT_TIMEOUT, 60000);
			curl_setopt($crl, CURLOPT_HTTPHEADER, array(
			  'Content-Type: application/json',
			  'token: ' . $token)
			);
			$resp = curl_exec($crl);//envia peticion
			curl_close($crl);
            //die('here : ' . " {$url} " . $resp);
			return $resp;
		}

        /*public function updateSalePayments( $costumer_rfc, $sale_folio ){
        //actualiza los subtipos
            
        }*/

        /*public function sendBill( $costumer_rfc, $sale_folio ){
            $resp = array();
            $resp['costumer_rfc'] = $costumer_rfc;
        //consulta datos de venta
			$sql = "SELECT 
						id_pedido, 
						folio_nv, 
						id_estatus, 
						id_moneda, 
						fecha_alta, 
						id_razon_social, 
						subtotal, 
						total, 
						id_sucursal, 
						id_usuario, 
						descuento, 
						tipo_pedido, 
						id_cajero, 
						folio_unico, 
						id_sesion_caja, 
						tipo_sistema, 
						cobro_finalizado,
						id_status_facturacion 
					FROM ec_pedidos 
					WHERE folio_nv = '{$sale_folio}'
					LIMIT 1";
            $stm = $this->link->query( $sql ) or die( "Error al consultar datos de la venta : {$sql}" );
            $sale_header = $stm->fetch();
        //consulta detalle de la venta
            $sql = "SELECT
                        id_producto, 
                        cantidad, 
                        precio, 
                        monto, 
                        iva, 
                        ieps, 
                        cantidad_surtida, 
                        descuento, 
                        modificado, 
                        es_externo, 
                        id_precio, 
                        folio_unico 
					FROM ec_pedidos_detalle 
					WHERE id_pedido = {$sale_header['id_pedido']}";
        }*/
    }
    
?>