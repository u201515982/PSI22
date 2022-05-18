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
	<title>En construcción</title>
	<link href="https://fonts.googleapis.com/css?family=Roboto&display=swap" rel="stylesheet">
	<link href="//fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet" type="text/css">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<style>
		body {
			background: url('blurred.jpg');
			background-repeat: no-repeat;
			background-size: 100% 100vh;
			font-family: 'Roboto', sans-serif;
			margin: 0;
			padding: 0;
		}
		.wrapper {
			text-align: center;
			position: relative;
			margin: 0 auto;
			padding-top: 15px;
			padding-bottom: 15px;
			max-width: 900px;
		}
		.wrapper p {
			font-family: 'Montserrat', sans-serif;
			color: #fff;
			font-size: 40px;
			font-weight: 900;
			text-transform: uppercase;
		}
		.foot {
			position: fixed;
			left: 0;
			bottom: 0;
			width: 100%;
			padding-bottom: 20px;
			color: black;
			text-align: center;
		}
	</style>
</head>
<body>
	<div class="wrapper">
		<p>PÁGINA EN CONSTRUCCIÓN</p>
		<img style="width: 50%; max-width: 250px;" src="work.png" alt="work">
	</div>
	<div class="foot">
		<img style="width: 80%; max-width: 400px; margin-bottom: 15px" src="logo-psi-small.png" alt="logo-psi">
		<p><a href="http://www.psi.gob.pe">Sitio Web</a> - <a href="http://www.psi.gob.pe/PIDE/SISPIDE.php">PIDE</a></p>
		<p>PSI © 2022 Copyright - Todos los Derechos Reservados</p>
	</div>
</body>
</html>