<?php session_start(); ?>
<?php include("class/vpos_plugin.php");?>
<?php include("class/llaves.php");?>
<?php include("class/3ds.php");?>
<!doctype html>
<html>
<head>
<meta charset="UTF-8">
<title>VPOS</title>
<style>
body {
  background-color: #ffffff;
  margin:0 0 0 0;
  padding:0 0 0 0;
  color:#fff;
}
</style>
</head>
<body>
<?php
/*
 * Created on May 20, 2013
 *
 * Pasarela de pago para Visa y Mastercard
 * Modulo de pago Alignet
 * Creado por: Alfredo Rodriguez jarscr.com  2013 soporte@jarscr.com
 */

	$emailNot = "info@innomuebles.com";
	$arrayIn['IDACQUIRER'] = $_POST['IDACQUIRER'];
	$arrayIn['IDCOMMERCE'] = $_POST['IDCOMMERCE'];
	$arrayIn['XMLRES'] = $_POST['XMLRES'];
	$arrayIn['DIGITALSIGN'] = $_POST['DIGITALSIGN'];
	$arrayIn['SESSIONKEY'] = $_POST['SESSIONKEY'];
	
	$banco = $_POST['IDACQUIRER'];
	$comercio = $_POST['IDCOMMERCE'];

 $arrayOut = '';
 
 $mensajeErrorTra = 'La transaccion presento un problema. Por favor intente de nuevo o contactenos para más información.';
 $mensajeSuccess = 'Successful';

 $DATA = array('retorno'=>$_SESSION['retorno'],'error'=>$_SESSION['error'],'fap'=>$llavePublicaFirma,'cpc'=>$llavePrivadaCifrado);
 

	$VI = "687310091A02206C";
	$arrayOut = '';
	
	if(VPOSResponse($arrayIn,$arrayOut,$llavePublicaFirma,$llavePrivadaCifrado,$VI)){
 	
	 $mensajesBody = "Transacción: ".$arrayOut['purchaseOperationNumber']."\nMonto: ".substr($arrayOut['purchaseAmount'], 0, -2)."\nNombre: ".$arrayOut['billingFirstName']." ".$arrayOut['billingLastName']."\nTelefono: ".$arrayOut['billingPhone']."\nEmail: ".$arrayOut['billingEMail']."\nMensaje de Transaccion: ".$arrayOut['authorizationResult']."\nAutorizacion: ".$arrayOut['authorizationCode']."\nCodigo de Transaccion: ".$arrayOut['errorCode']."\nMensaje de Codigo: ".$arrayOut['errorMessage'];

	
	while(list($key, $val) = each($arrayOut)){
		$mensajeError = $arrayOut['errorMessage'];
		$CodigoError = $arrayOut['authorizationResult'];
		$mensaje = $arrayOut['authorizationResult'];
		$numTrans = $arrayOut['authorizationCode'];
		$factura = $arrayOut['purchaseOperationNumber'];
		$cuenta = $arrayOut['cardNumber'];
		$marca = $arrayOut['cardType'];
		$factura = $arrayOut['purchaseOperationNumber'];

		if($key=="authorizationResult"){
		  if($val=="00"){
		  	$Salida = $_SESSION['retorno'].'&status=success&txnid='.$_SESSION['txnid'].'&hash='.$_SESSION['hash'].'&amount='.$_SESSION['amount'].'&trans_ts='.$_SESSION['trans_ts'].'&mensaje='.$mensajeSuccess;
		  	$statusTrans = 'success';
@mail($emailNot,"Transaccion #".$factura,$mensajesBody,"FROM: info@innomuebles.com");

		  	echo '<form action="'.$_SESSION['retorno'].'" method="POST" name="payform" id="payform">
		    <input type="hidden" name="status" value="success">
		    <input type="hidden" name="txnid" value="'.$_SESSION['txnid'].'">
		    <input type="hidden" name="hash" value="'.$_SESSION['hash'].'">
		    <input type="hidden" name="amount" value="'.$_SESSION['amount'].'">
		    <input type="hidden" name="trans_ts" value="'.$_SESSION['form_key'].'">
			<input type="submit" value="Pago Exitoso" style="display: none;">
			</form>
			<script type="text/javascript">document.payform.submit();</script>';
		  }else{
		    $Salida = 'alert.php?error_message='.$mensajeErrorTra.'&errorCode='.$CodigoError;
		    $statusTrans = 'failure';
		    @mail($emailNot,"Transaccion #".$factura,$mensajesBody,"FROM: info@innomuebles.com");
 			header('location: '.$Salida); 
		  }
		}
}


 }else{
 	$Salida = "alert.php?errorDisplay=Respuesta Invalida";
 	@mail($emailNot,"Transaccion #".$factura,$mensajesBody,"FROM: info@innomuebles.com");
 	header('location: '.$Salida); 
 }	

	

?>
</body>
</html>

