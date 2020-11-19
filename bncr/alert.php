<?php
session_start();
$retorno = $_SESSION['error'];
?>
<!doctype html>
<html>
<head>
<meta charset="UTF-8">
<title>INNOmuebles</title>
</head>

<body>
<?php
$error_message = $_REQUEST['error_message'];
$codigo = $_REQUEST['errorCode'];

		if($codigo=='41'){
			$Error = "Su tarjeta esta reportada como perdida.";	
		}else{
			if($codigo=='43'){
				$Error = "Su tarjeta esta reportada como robada.";	
			}else{
				if($codigo=='51'){
					$Error = "Su tarjeta no tiene fondos.";	
				}else{
					if($codigo=='82'){
					$Error = "Codigo CVV incorrecto.";	
					}else{
						if($codigo=='54'){
						$Error = "Su tarjeta se encuentra vencida.";
						}else{
							if($codigo=='51'){
							$Error = "Su tarjeta no tiene fondos.";	
							}else{
								
								$Error = "Transaccion no valida Codigo: ".$codigo;	
							}
						}
					}
				}
			}
		}

?>
<form action="<?php echo $_SESSION['error']; ?>" method="POST" name="payform" id="payform">
   
    <input type="hidden" name="status" value="failure">
    <input type="hidden" name="txnid" value="<?php echo $_SESSION['txnid']; ?>">
    <input type="hidden" name="hash" value="<?php echo $_SESSION['hash']; ?>">
    <input type="hidden" name="amount" value="<?php echo $_SESSION['amount']; ?>">
    <input type="hidden" name="trans_ts" value="<?php echo $_SESSION['form_key']; ?>">

    <input type="submit" value="Pago Fallido" style="display: none;">
</form>

<script>
alert('<?=$Error?>\n<?=$error_message?>');
//document.location='<?=$retorno?>&status=failure';
document.payform.submit(); 
</script>
</body>
</html>