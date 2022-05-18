<?php
date_default_timezone_set('America/Bogota');
session_start();
if(!isset($_SESSION['FAKE'])) {
	header("Location: login.php");
	die();
}

if(!isset($_GET['FAKE'])) die();

$cargo_id = $_GET['FAKE'];

$cargo = array();
$connectionInfo = array("Database"=>"FAKE", "UID"=>"FAKE", "PWD"=>"", "CharacterSet" => "UTF-8");
$conn = sqlsrv_connect("FAKE", $connectionInfo);
if ($conn === false) {
	$err_msg = utf8_encode(sqlsrv_errors()[0][2]);
	echo $err_msg;
	die();
}
$query = "SECRET_QUERY";
$result = sqlsrv_query($conn, $query);
if ($result === false) {
	$err_msg = utf8_encode(sqlsrv_errors()[0][2]);
	echo $err_msg;
	die();
}else{
	while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
		array_push($cargo, $row);
	}
}

sqlsrv_free_stmt($result);
sqlsrv_close($conn);

$cargo = json_decode(json_encode($cargo),True);
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="google" content="notranslate">
	<title>Document</title>
	<style>
		* {
			box-sizing: border-box;
			font-family: Arial;
		}
		.container {
		}
		.banner {
			display: flex;
			flex-flow: row;
			height: 40px;
		}
		.banner img {
			width: auto;
			height: 100%;
		}
		.titulo {
			margin: 40px 0 20px 0;
			text-align: center;
		}
		.titulo span {
			font-weight: 600;
			font-size: 14px;
			border-bottom: 1px solid black;
		}
		.data-line {
			font-size: 12px;
			/*margin: 10px 0;*/
			margin-bottom: 10px;
		}
		.mytable table{
			margin: 0 auto;
			width: 100%;
			border-collapse: collapse;
		}
		.mytable th, .mytable td{
			font-weight: 100;
			font-size: 10px;
			padding: 5px;
			border: 1px solid black;
		}
		.mytable td:nth-child(1),.mytable td:nth-child(2),.mytable td:nth-child(3),.mytable td:nth-child(5),.mytable td:nth-child(7) {
			text-align: center;
		}
		.mytable td:nth-child(3) {
			white-space: nowrap;
		}
		.mytable td {
			font-size: 9px;
		}
		/*.footer {
			margin-top: 150px;
			width: 100%;
		}*/
		.footer .username {
			font-size: 11px;
		}
		.footer .signatures {
			display: flex;
			flex-flow: row;
			font-size: 11px;
			margin-bottom: 60px;
		}
		.footer .signatures div {
			width: 200px;
			border-top: 1px solid black;
		}
		.footer .signatures .receive {
			margin-left: auto;
		}

		/****************************************/
		html,
		body {
			height: 100%;
			position: relative;
			margin: 0;
		}
		.container {
			min-height: 100vh; /* will cover the 100% of viewport */
			overflow: hidden;
			display: block;
			position: relative;
			/*padding-bottom: 200px;*/ /* height of your footer */
		}
		.footer {
			position: absolute;
			bottom: 0;
			width: 100%;
		}
		/****************************************/
		.btn-print {
			text-align: center;
			position: fixed;
			bottom: 0;
			width: 100%;
			padding: 20px;
			z-index: 2;
		}
		.btn-print span {
			padding:10px;
			color: white;
			background: green;
			text-align: center;
			width: 200px;
			cursor: pointer;
			border-radius: 2px;
		}
		.btn-print span:hover {
			background: #00A900;
		}
		.btn-print span:active {
			background: darkgreen;
		}
		@media print {
			.btn-print {
				display: none;
			}
		}
		#head-banner {
			padding-bottom: 5px;
			border: none;
		}
		#head-titulo, #head-data {
			padding: 0;
			border: none;
			text-align: left;
		}
	</style>
</head>
<body>
	<div class="btn-print">
		<span onclick="window.print();">
			IMPRIMIR
		</span>
	</div>
	<div class="container">
		
		<div class="mytable" id="main_table">
			<table>
				<thead>
					<tr>
						<th colspan="8" id="head-banner">
							<div class="banner">
								<img src="./sis_files/logo-psi.png">
								<img src="./sis_files/logo-minagri.png" style="margin-left: auto;">
							</div>
						</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td colspan="8" id="head-titulo">
							<div class="titulo">
								<span>CARGO DE DOCUMENTOS</span>
							</div>
						</td>
					</tr>
					<tr>
						<td colspan="8" id="head-data">
							<div class="data-line">
								<span style="font-weight: 600;">FECHA:&nbsp;</span><span><?= (new DateTime("{$cargo[0]['FAKE']['date']}"))->format('d/m/Y') ?></span>
								&nbsp;&nbsp;&nbsp;&nbsp;
								<span style="font-weight: 600;">HORA:&nbsp;</span><span><?= (new DateTime("{$cargo[0]['FAKE']['date']}"))->format('h:i a') ?></span>
							</div>
						</td>
					</tr>
					<tr>
						<td colspan="8" id="head-data">
							<div class="data-line">
								<span style="font-weight: 600;">NRO. DE DOCUMENTOS:&nbsp;</span><span><?= count($cargo) ?></span>
							</div>
						</td>
					</tr>

					<tr>
						<td>N°</td>
						<td>CUD</td>
						<td>CUT</td>
						<td>N° DOCUMENTO</td>
						<td>FECHA</td>
						<td>ASUNTO</td>
						<td>FOLIOS</td>
						<td>OBS.</td>
					</tr>
					<?php $counter = 1; ?>
					<?php foreach ($cargo as $item): ?>
						<tr>
							<td><?= $counter ?></td>
							<?php $counter++ ?>
							<td><?= $item['FAKE'] ?></td>
							<td><?= $item['FAKE'] ?></td>
							<td><?= $item['FAKE'] ?></td>

							<td><?= (new DateTime(json_decode(json_encode($item['FAKE']),True)['date']))->format("d/m/Y"); ?></td>

							<td><?= $item['FAKE'] ?></td>
							<td><?= $item['FAKE'] ?></td>
							<td><?= $item['FAKE'] ?></td>
						</tr>
					<?php endforeach ?>
					<tr>
						<td colspan="8" style="height: 130px; border: none; padding: 0;"></td>
					</tr>
				</tbody>
			</table>
		</div>

		<div class="footer">
			<div class="signatures">
				<div class="send">ENVIA:</div>
				<div class="receive">RECEPCIONA:</div>
			</div>

			<div class="username">
				USUARIO: <?= mb_strtoupper($cargo[0]['FAKE']) ?>
			</div>
		</div>

	</div>
	<script>
		var h = document.getElementById("main_table").clientHeight;
		console.log(h);
		var lim = 1030;
		if(h>lim) {
			var tmp = Math.ceil(h/lim);
			console.log(h/lim);
			console.log(tmp);
			document.getElementById("main_table").style.minHeight = (tmp*100)+"vh";
		}
	</script>

</body>
</html>