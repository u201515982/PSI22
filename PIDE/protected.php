<?php
session_start();
if(!isset($_SESSION['1'])) {
	header("Location: SISPIDE.php");
	die();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>SISPIDE</title>
	<link href="https://fonts.googleapis.com/css?family=Roboto&display=swap" rel="stylesheet">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
	<style>
		body{
			font-family: 'Roboto', sans-serif;
			background: #01633D;
			margin: 0px 10px;
			padding: 0;
			position: relative;
			min-height: 100%;
		}
		.flex {
			margin: 0;
			display:flex;
			max-width: 500px;
		}
		.flex div {
			background: white;
			flex:1;
			margin-top: 10px;
			margin-bottom: 10px;
			border-top: 1px solid black;
			border-bottom: 1px solid black;
		}
		.flex img {
			vertical-align: middle;
			width:100%;
		}
		#nav-bar{
			background: white;
			height: 50px;
			max-width: 1500px;
			line-height: 50px;
			position: relative;
			margin: 0 auto;
			border-radius: 6px;
		}
		#nav-bar p {
		    display: block;
		    margin-block-start: 0em;
		    margin-block-end: 0em;
		    margin-inline-start: 0px;
		    margin-inline-end: 0px;
		}
		.wrapper {
			text-align: center;
			position: relative;
			margin: 0 auto;
			padding-top: 15px;
			padding-bottom: 15px;
			max-width: 900px;
		}
		.wrapper img{
			cursor: pointer;
			background: white;
			width:250px;
			height: 100px;
			border: 1px solid black;
			/*padding: 10px;*/
			margin-left: 10px;
			margin-right: 10px;
			margin-top: 2px;
			margin-bottom: 2px;
		}
		.logout {
			font-size: 14px;
			text-decoration: none;
			color: #fff;
			transition: .3s background-color;
			background-color: #494949;
			position: absolute;
			right: 0;
			top: 0;
			bottom: 0;
			padding-left: 20px;
			padding-right: 20px;
			border-top-right-radius: 5px;
			border-bottom-right-radius: 5px;
		}
		.logout:hover {
			background-color: #15BCBC;
		}
		footer {
			position: absolute;
			bottom: 0;
			width: 100%;
			height: 40px;
			padding-bottom: 20px;
			text-align: center;
			color: white;
		}
		html{ height:100%; }
		body::after{ content:''; display:block; height:100px; }
		
	</style>
</head>
<body>
	<script>
		$(document).ready(function () {
			serviceTipoCambio().then(function(jsonRes){
				document.getElementById("UIT").innerHTML = "S/. " + jsonRes.tasa;
				document.getElementById("compra").innerHTML = "S/. " + jsonRes.compra;
				document.getElementById("venta").innerHTML = "S/. " + jsonRes.venta;
			});
		});
		function serviceTipoCambio(){
			var formData = new FormData();
			var srv = "consulta_tipo_cambio.php";
			var xhr = new XMLHttpRequest();
			return new Promise(function (resolve) {
				xhr.open("POST", srv);
				xhr.send(formData);
				xhr.onreadystatechange = function(){
					if (xhr.readyState == 4){
						if (xhr.status == 200){
							var aux = xhr.responseText;
							var jsonRes = JSON.parse(aux);
							if(jsonRes.count==0){
								resolve(null);
							}
							resolve(jsonRes);
						}
					}
				};
			});
		}
	</script>
	<div style="
			position: relative;
			margin: 0 auto;max-width: 1500px;">
	<div class="flex">
		<div style="border-left: 1px solid black;border-right: 1px solid black;">
			<img src="logo-minagri-small.png" title="minagri" alt="minagri">
		</div>
		<div style="border-right: 1px solid black;">
			<img src="logo-psi-small.png" title="psi" alt="psi">
		</div>
	</div>

	<div id="nav-bar">
		<p style="font-size: 16px; padding-left:10px; "><?= $_SESSION['1'] ?></p>
		<div style="">
			<a class="logout" href="SISPIDE.php?out=1">LOGOUT</a>	
		</div>
	</div>
	<div id="tipocambio">
			<table style="background-color: white; color: black; border: 1px solid gray; border-radius: 3px; padding: 5px; font-size: 13px;">
				<tr>
					<th>TIPO DE CAMBIO: &nbsp;&nbsp;</th>
					<th align="left">Compra: </th>
					<td id="compra"></td>
					<td>&nbsp;</td>
					<th align="left">Venta: </th>
					<td id="venta"></td>
					<td>&nbsp;&nbsp;&nbsp;</td>
					<th align="left">UIT:</th>
					<td id="UIT"></td>
				</tr>
			</table>
		</div>
	</div>

	<div class="wrapper">
		
		<form action="redirect.php" method="post" target="_blank" style="">
			<input type="text" name="page_name" value="integral" hidden="true">
			<img src="consulta-integral.png" title="Consulta Integral" alt="consulta-integral" onclick="this.parentNode.submit()">
		</form>
		<form action="redirect.php" method="post" target="_blank" style="display: inline;">
			<input type="text" name="page_name" value="sunat" hidden="true">
			<img src="sunat-small.png" title="SUNAT" alt="sunat" onclick="this.parentNode.submit()">
		</form>
		<form action="redirect.php" method="post" target="_blank" style="display: inline;">
			<input type="text" name="page_name" value="reniec" hidden="true">
			<img src="reniec-small.png" title="RENIEC" alt="reniec" onclick="this.parentNode.submit()">
		</form>
		<form action="redirect.php" method="post" target="_blank" style="display: inline;">
			<input type="text" name="page_name" value="sunarp" hidden="true">
			<img src="sunarp-small.png" title="SUNARP" alt="sunarp" onclick="this.parentNode.submit()">
		</form>
		<form action="redirect.php" method="post" target="_blank" style="display: inline;">
			<input type="text" name="page_name" value="osce" hidden="true">
			<img src="osce-small.png" title="OSCE" alt="osce" onclick="this.parentNode.submit()">
		</form>
		<form action="redirect.php" method="post" target="_blank" style="display: inline;">
			<input type="text" name="page_name" value="sunedu" hidden="true">
			<img src="sunedu-small.png" title="SUNEDU" alt="sunedu" onclick="this.parentNode.submit()">
		</form>
		<form action="redirect.php" method="post" target="_blank" style="display: inline;">
			<input type="text" name="page_name" value="policiales" hidden="true">
			<img src="ant-policiales.png" title="Antecedentes Policiales" alt="ant-policiales" onclick="this.parentNode.submit()">
		</form>
		<form action="redirect.php" method="post" target="_blank" style="display: inline;">
			<input type="text" name="page_name" value="judiciales" hidden="true">
			<img src="ant-judiciales.png" title="Antecedentes Judiciales" alt="ant-judiciales" onclick="this.parentNode.submit()">
		</form>
		<form action="redirect.php" method="post" target="_blank" style="display: inline;">
			<input type="text" name="page_name" value="penales" hidden="true">
			<img src="ant-penales.png" title="Antecedentes Penales" alt="ant-penales" onclick="this.parentNode.submit()">
		</form>
		<!--<form action="redirect.php" method="post" target="_blank" style="display: inline;">
			<input type="text" name="page_name" value="tipocambio" hidden="true">
			<img src="tipo-cambio.png" title="Tipo de Cambio" alt="tipo-cambio" onclick="this.parentNode.submit()">
		</form>-->
	</div>

	<footer>
		<p>PSI © 2022 Copyright - Todos los Derechos Reservados</p>
	</footer>


</body>
</html>