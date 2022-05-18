<?php
date_default_timezone_set('America/Bogota');
session_start();
if(isset($_SESSION['FAKE'])) {
	header("Location: sipa.php");
	die();
}
?>
<!DOCTYPE html>
<html class="">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="google" content="notranslate">
	<meta name="robots" content="noindex">
	<title>SIPA - PSI</title>
	<script src="./sis_files/jquery-3.2.1.min.js"></script>
	<link href="./sis_files/bootstrap.min.css" rel="stylesheet">
	<link href="./sis_files/style.css" rel="stylesheet">
	<link href="./sis_files/prism.css" rel="stylesheet">
	<link href="./sis_files/chosen.css" rel="stylesheet">
	<link href="./sis_files/estilo.css" rel="stylesheet">
	<link href="./sis_files/estilo(1).css" rel="stylesheet">
	<script src="./sis_files/bootstrap.min.js"></script>
	<script src="./sis_files/console_runner.js"></script>
	<script src="./sis_files/css_live_reload_init.js"></script>
	<script src="./sis_files/jquery.validate.js"></script>
	<script src="./sis_files/JQueryRC4.js"></script>
</head>
<body style="overflow-y:auto;">
	<div class="" id="mapa"></div>
	<div class="login ">
		<div class="col-xs-11 col-xs-offset-1 wrap">
			<div class="col-xs-12 titulomovil">Sistema Integrado de Procesos Administrativos<br><i class="col-xs-12" style="font-size:14px !important"> SIPA </i></div>
			<div class="content vistamobil">
				<div id="NOTslideshow">
					<div class="one" style="opacity: 1;">
						<h2 style="font-size:24px; margin-top:150px"><span>Sistema Integrado de Procesos Administrativos - SIPA</span></h2>
						<p>Permitir el intercambio de datos en tiempo real y de forma fidedigna entre el supervisor de obra y el PSI a través de la plataforma de monitoreo, permite una mejor comunicación y el logro de los objetivos para el progreso de nuestro País.</p>
					</div>
				</div>
			</div>
			<div class="user ">
				<div class="form-wrap bordetab">
					<div class="tabs">
						<h3 class="login-tab"><span class="ftabs">Iniciar Sesión</span></h3>
					</div>
					<div class="tabs-content">
						<div id="login-tab-content" class="active">
							<form class="login-form" id="login_form">
								<input type="checkbox" name="sistema_login" hidden>
								<input type="text" class="input" id="sistema_username" name="sistema_username" placeholder="Usuario" spellcheck="false" required>
								<input type="password" class="input" id="sistema_password" name="sistema_password" placeholder="Clave de acceso" spellcheck="false" required>
								<input type="button" class="button" value="Iniciar" id="btn_login">
								<div id="error-msg"></div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<script type="text/javascript">
		$(document).ready(function(){
			$(document).on('keypress',function(e) {
				if(e.which == 13) {
					authenticate();
				}
			});
			$('#btn_login').on('click', function () {
				authenticate();
			});
			$('#sistema_username').keyup(function() {
				document.getElementById('error-msg').innerHTML = "";
			});
			$('#sistema_password').keyup(function() {
				document.getElementById('error-msg').innerHTML = "";
			});
		});
		function authenticate() {
			var sistema_username = document.getElementById('sistema_username').value;
			var sistema_password = document.getElementById('sistema_password').value;
			if (!sistema_username.trim() || !sistema_password.trim()) {
				document.getElementById('error-msg').innerHTML = "Ingresar datos";
				return;
			}
			var formData = new FormData($('#login_form')[0]);
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
				success: function(eid){
					if (eid == 1) {
						location.href = "http://www.psi.gob.pe/SIPA/sipa.php";
					} else {
						document.getElementById('error-msg').innerHTML = eid;
					}
				}
			});
		}
	</script>
	<style>
		.form-wrap form label[for] {
			padding-left: 20px;
			cursor: pointer;
		}
		input:-webkit-autofill, textarea:-webkit-autofill, select:-webkit-autofill {
			background-color: red !important;
			background-image: none !important;
			color: rgb(0, 0, 0) !important;
		}
		#error-msg {
			color: #DC0101;
		}
	</style>
</body>
</html>