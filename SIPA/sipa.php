<?php
date_default_timezone_set('America/Bogota');
session_start();
if(!isset($_SESSION['FAKE'])) {
	header("Location: login.php");
	die();
}
$current_year = (new DateTime())->format('Y');

$auth_query = "SECRET_QUERY";
$auth = SQLStatement($auth_query);
$auth = json_decode(json_encode($auth),true);
$admin_usuarios = true;
foreach ($auth as $item) {
	if ($item['FAKE'] == 0) {
		$admin_usuarios = false;
	}
}
function SQLStatement($sql) {
	$connectionInfo = array("Database"=>"FAKE", "UID"=>"FAKE", "PWD"=>"FAKE", "CharacterSet" => "UTF-8");
	$conn = sqlsrv_connect("FAKE", $connectionInfo);
	if ($conn === false) {
		$err_msg = utf8_encode(sqlsrv_errors()[0][2]);
		echo $err_msg;
		die();
	}
	$res = array();
	$result = sqlsrv_query($conn, $sql);
	if ($result === false) {
		$err_msg = utf8_encode(sqlsrv_errors()[0][2]);
		echo $err_msg;
		die();
	}else{
		while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
			array_push($res, $row);
		}
	}
	sqlsrv_free_stmt($result);
	sqlsrv_close($conn);
	return $res;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="google" content="notranslate">
	<title>SIPA - PSI</title>
	<link href="https://fonts.googleapis.com/css?family=Roboto&display=swap" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css?family=Roboto+Slab&display=swap" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css?family=Roboto+Condensed&display=swap" rel="stylesheet">

	<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

	<script src="./sis_files/jquery.min.js"></script>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.css" />
	<script src="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.js"></script>

	<style>
		* {
			box-sizing: border-box;
		}
		body, html {
			height: calc(100vh - 40px);
		}
		body {
			margin: 0;
			font-family: 'Roboto', sans-serif;
			min-height: 500px;
		}
		.sis-navbar {
			display: flex;
			align-items: center;
			height: 40px;
			width: 100%;
			position: absolute;
			top: 0;
			left: 0;
			background: #009A5E;
			color: white;
			box-shadow: 0 0 3px black;
		}
		.sis-navbar .sis-toggle-sidebar {
			/**/
		}
		.sis-navbar .sis-toggle-sidebar i {
			padding: 5px;
			margin: 3px;
			border-radius: 3px;
			cursor: pointer;
			transition: 0.1s;
			user-select: none;
		}
		.sis-navbar .sis-toggle-sidebar i:hover {
			background: #00C276;
		}
		.sis-navbar .sis-title {
			font-size: 18px;
			margin-left: 10px;
		}
		.sis-navbar .sis-fullname {
			font-size: 18px;
			margin-left: auto;
			margin-right: 20px;
		}
		.sis-main {
			width: 100%;
			height: 100%;
			margin-top: 40px;
			display: flex;
			align-items: stretch;
		}
		.sis-main .sis-sidebar {
			height: 100%;
			width: 100%;
			max-width: 250px;
			background: #01633D;
			transition: 0.2s;
		}
		.sis-menu .sis-home {
			justify-content: center;
			font-size: 20px;
		}
		.sis-menu .sis-option {
			font-size: 16px;
		}
		.sis-menu .sis-home, .sis-menu .sis-option{
			display: flex;
			align-items: center;
			width: 100%;
			color: white;
			cursor: pointer;
		}
		.sis-menu .sis-home {
			padding: 10px;
		}
		.sis-menu .sis-home:hover, .sis-menu .sis-option:hover {
			background: #009A5E;
		}
		.sis-home > i, .sis-home > span{
			padding: 0 3px;
			user-select: none;
		}
		.sis-logout {
			text-align: center;
			padding-top: 40px;
		}
		.sis-logout button {
			background: #009A5E;
			color: white;
			border: none;
			padding: 8px 50px;
			border-radius: 5px;
			font-family: inherit;
			font-size: 16px;
			cursor: pointer;
			transition: 0.1s;
		}
		.sis-logout button:hover {
			background: white;
			color: #01633D;
		}
		.sis-main .sis-content {
			height: 100%;
			width: 100%;
			display: flex;
			flex-flow: column;
		}
		.sis-main .sis-content object {
			height: 100%;
			width: 100%;
			flex-grow: 1;
		}
		.sis-main .sis-content footer {
			flex-grow: 0;
			padding: 5px;
			font-size: 14px;
			border-top: 1px solid #eaeaea;
		}
		.sis-inactive {
			margin-left: -250px;
		}
		.modulo {
			overflow: visible;
		}
		.nombre {
			padding: 10px;
			user-select: none;
			width: 100%;
		}
		.opcion {
			background: green;
			width: 200px;
			position: absolute;
			top:0;
			z-index: -1;
			height: 0;
			transition: 0.1s;
			margin-left: 240px;
		}
		.modulo:hover .opcion {
			margin-left: 250px;
			height: auto;
			z-index: 0;
			transition: 0.1s;
		}
		.opcion div {
			padding: 5px 10px;
		}
		.opcion div:hover {
			background: #00A800;
		}
	</style>
</head>
<body>
	
	<div class="sis-navbar">
		<div class="sis-toggle-sidebar">
			<i class="material-icons">menu</i>
		</div>
		<div class="sis-title">
			SIPA
		</div>
		<div class="sis-fullname">
			<?= mb_strtoupper($_SESSION['FAKE']) ?>
		</div>
	</div>
	<div class="sis-main">
		<div class="sis-sidebar">
			<div class="sis-menu">
				<div class="sis-home" onclick="showContent();">
					<i class="material-icons">home</i><span>Inicio</span>
				</div>

				<?php if ($admin_usuarios): ?>
					<div class="sis-option modulo">
						<div class="nombre" onclick="showContent(2);">Seguridad</div>
						<div class="opcion" style="top: 84px;">
							<div onclick="showContent(2);">Perfiles</div>
							<div onclick="showContent(3);">Usuarios</div>
						</div>
					</div>
					<div class="sis-option modulo">
						<div class="nombre" onclick="showContent(4);">Personal</div>
					</div>
				<?php endif ?>
				<div class="sis-option modulo" onclick="showContent(1);">
					<div class="nombre">Mesa de partes</div>
				</div>
			</div>
			<div class="sis-logout">
				<button id="btn_logout">Cerrar Sesion</button>
			</div>
		</div>
		<div class="sis-content">
			<object data = 'http://www.psi.gob.pe/SIPA/list.php'></object>
			<footer>PSI © <?= $current_year ?> Copyright - Todos los Derechos Reservados</footer>
		</div>
	</div>
	
	<script>
		/* SIDEBAR SLIDE */
		$(document).ready(function () {
			$('.sis-toggle-sidebar').on('click', function () {
				$('.sis-sidebar').toggleClass('sis-inactive');
			});
			sidebarToggle();
			var width = $(window).width();
			$(window).on('resize', function() {
				if ($(this).width() != width) {
					width = $(this).width();
					sidebarToggle();
				}
			});
			$('#btn_logout').on('click', function () {
				logout();
			});
		});

		function logout() {
			var formData = new FormData();
			formData.append('FAKE', true);
			$.ajax({
				url: 'http://www.psi.gob.pe/SIPA/control.php',
				type: 'POST',

				data: formData,
				cache: false,
				contentType: false,
				processData: false,

				error: function(){
					return true;
				},
				success: function(res){
					location.reload();
				}
			});
		}

		function sidebarToggle() {
			if($(window).width() <=768) {
				if(!$(".sis-sidebar").hasClass("sis-inactive"))
					$(".sis-sidebar").addClass("sis-inactive");
			}else{
				$(".sis-sidebar").removeClass("sis-inactive");
			}
		}
		/****************/
		function showContent(option = 0) {
			var content_object = "";
			switch(option) {
				case 1:
					content_object = "<object data = 'http://www.psi.gob.pe/SIPA/list.php'></object>";
					break;
				case 2:
					content_object = "<object data = 'http://www.psi.gob.pe/SIPA/perfiles.php'></object>";
					break;
				case 3:
					content_object = "<object data = 'http://www.psi.gob.pe/SIPA/usuarios.php'></object>";
					break;
				case 4:
					content_object = "<object data = 'http://www.psi.gob.pe/SIPA/personal.php'></object>";
					break;
				case 5:
					content_object = "<object data = 'http://www.psi.gob.pe/SIPA/st_expedientes.php'></object>";
					break;
				default:
					/* CHANGE */
					content_object = "";
					break;
			}
			content_object += "<footer>PSI © <?= $current_year ?> Copyright - Todos los Derechos Reservados</footer>";
			document.querySelector(".sis-content").innerHTML = "";
			document.querySelector(".sis-content").insertAdjacentHTML("afterbegin", content_object);
		}
	</script>
</body>
</html>