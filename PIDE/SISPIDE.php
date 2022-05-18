<!-- Desarrollado por Sergio Manuel Zavaleta Salazar -->
<!DOCTYPE html>
<html class="">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="google" content="notranslate">
	<meta name="robots" content="noindex">
	<title>PSI | PIDE</title>
	<script src="./MINAGRI _ SISPIDE_files/jquery-3.2.1.min.js.download"></script>
	<link href="./MINAGRI _ SISPIDE_files/bootstrap.min.css" rel="stylesheet">
	<link href="./MINAGRI _ SISPIDE_files/style.css" rel="stylesheet">
	<link href="./MINAGRI _ SISPIDE_files/prism.css" rel="stylesheet">
	<link href="./MINAGRI _ SISPIDE_files/chosen.css" rel="stylesheet">
	<link href="./MINAGRI _ SISPIDE_files/estilo.css" rel="stylesheet">
	<link href="./MINAGRI _ SISPIDE_files/estilo(1).css" rel="stylesheet">
	<script src="./MINAGRI _ SISPIDE_files/bootstrap.min.js.download"></script>
	<script src="./MINAGRI _ SISPIDE_files/console_runner.js.download"></script>
	<script src="./MINAGRI _ SISPIDE_files/css_live_reload_init.js.download"></script>
	<script src="./MINAGRI _ SISPIDE_files/jquery.validate.js.download"></script>
	<script src="./MINAGRI _ SISPIDE_files/JQueryRC4.js.download"></script>
</head>
<body style="overflow-y:auto;">
	<?php
	session_start();
	include("authenticate.php");
	if(isset($_GET['out'])) {
		session_unset();
		$_SESSION = array();
		unset($_SESSION['1'],$_SESSION['1'],$_SESSION['1'],$_SESSION['1']);
		session_destroy();
	}
	if(isset($_SESSION['1']) && isset($_SESSION['1']) && isset($_SESSION['1']) && ($_SESSION['1'])){
		header("Location: protected.php");
		die();
	}
	if(isset($_POST['user_login']) && isset($_POST['user_pass'])){
		if(authenticate($_POST['user_login'],$_POST['user_pass'])){
			header("Location: protected.php");
			die();
		}else {
			echo "<br><p style='color:white; font-size:20px;'>Usuario no autorizado.</p>";
		}
	}
	if(isset($_GET['out'])) echo "Logout successful";
	?>
	<div class="" id="mapa"></div>
	<div class="login ">
		<div class="col-xs-11 col-xs-offset-1 wrap">
			<div class="col-xs-12 titulomovil">  SISPIDE <br> <i class="col-xs-12" style="font-size:14px !important"> Sistema Interoperabilidad </i>   </div>
			<div id="toggle-wrap">
				<div id="toggle-terms">
					<div id="cross">
						<span></span>
						<span></span>
					</div>
				</div>
			</div>
			<div class="terms">
				<h2>Términos de referencia</h2>
				<h3>DGSEP - DIA</h3>
				<p> El Área de Estadística Agrícola de la Unidad de Estadística  procesa mensualmente un gran volumen de datos, para lo cual es necesario contar con una herramienta informática (SISTEMA) que permita la automatización del Ingreso, Consistencia, Consolidación y Análisis de datos , con la finalidad de facilitar estos procesos en las Regiones y Agencias Agrarias y garantizar la recepción oportuna de la data en la Dirección General de Evaluación y Seguimiento de Políticas Agrarias,  en donde finalmente se consolidan los resultados a nivel nacional.</p>
			</div>
			<div class="recovery">
				<h3>Recuperación de cuenta de usuario</h3>
				<p>Ingrese su <strong>usuario</strong> de la cuenta a recuperar   <strong>y clic en el boton Enviar</strong></p>
				<p>Nosotros enviaremos a su cuenta de correo institucional las instrucciones de recuperación</p>
				<form class="recovery-form" action="http://winappdes.minagri.gob.pe/SISPIDE" method="post">
					<input type="text" class="input" id="user_recover" placeholder="Ingrese su usuario aqui!">
					<input type="submit" class="button" value="Enviar">
				</form>
				<p class="mssg">An email has been sent to you with further instructions.</p>
			</div>
			<div class="content vistamobil">
				<div class="logo">
				</div>
				<div id="NOTslideshow">
					<div class="one" style="opacity: 1;">
						<h2 style="font-size:24px; margin-top:150px"><span>PLATAFORMA DE INTEROPERABILIDAD </span></h2>
						<p>Permitir el intercambio de datos automatizados entre entidades públicas, basado en la Arquitectura Orientada a Servicio SOA. A través de una plataforma de interoperabilidad flexible para compartir información entre entidades públicas, presentando los datos en formatos legibles para el usuario final apoyando la toma de decisiones rápida y oportuna.</p>
					</div>
				</div>
			</div>
			<div class="user ">
				<img class=" img_pmobil" style="width: 200px; margin-left: 14px;" src="./MINAGRI _ SISPIDE_files/logo.png" alt="">
				<div class="form-wrap bordetab">
					<div class="tabs">
						<h3 class="login-tab"><span class="ftabs">Iniciar Sesión<span></span></span></h3>
					</div>
					<div class="tabs-content">
						<div id="login-tab-content" class="active">
							<form class="login-form" id="login-form" action="SISPIDE.php" method="post">
								<input type="text" class="input" id="user_login" name="user_login" placeholder="Usuario" required>
								<input type="password" class="input" id="user_pass" name="user_pass" placeholder="Clave de acceso" required>
								<input type="submit" class="button" value="Iniciar">
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<script type="text/javascript">
		$(function() {
			var tab = $('.tabs h3 a');
			tab.on('click', function(event) {
				event.preventDefault();
				tab.removeClass('active');
				$(this).addClass('active');
				tab_content = $(this).attr('href');
				$('div[id$="tab-content"]').removeClass('active');
				$(tab_content).addClass('active');
			});
		});
		$(function() {
			$('#slideshow > div:gt(0)').hide();
			setInterval(function() {
				$('#slideshow > div:first')
				.fadeOut(2000)
				.next()
				.fadeIn(2000)
				.end()
				.appendTo('#slideshow');
			}, 4850);
		});
		(function($) {
			'use strict';
			$.fn.swapClass = function(remove, add) {
				this.removeClass(remove).addClass(add);
				return this;
			};
		}(jQuery));
		$(function() {
			$('.agree,.forgot, #toggle-terms, .log-in, .sign-up').on('click', function(event) {
				event.preventDefault();
				var terms = $('.terms'),
				recovery = $('.recovery'),
				close = $('#toggle-terms'),
				arrow = $('.tabs-content .fa');
				if ($(this).hasClass('agree') || $(this).hasClass('log-in') || ($(this).is('#toggle-terms')) && terms.hasClass('open')) {
					if (terms.hasClass('open')) {
						terms.swapClass('open', 'closed');
						close.swapClass('open', 'closed');
						arrow.swapClass('active', 'inactive');
					} else {
						if ($(this).hasClass('log-in')) {
							return;
						}
						terms.swapClass('closed', 'open').scrollTop(0);
						close.swapClass('closed', 'open');
						arrow.swapClass('inactive', 'active');
					}
				}
				else if ($(this).hasClass('forgot') || $(this).hasClass('sign-up') || $(this).is('#toggle-terms')) {
					if (recovery.hasClass('open')) {
						recovery.swapClass('open', 'closed');
						close.swapClass('open', 'closed');
						arrow.swapClass('active', 'inactive');
					} else {
						if ($(this).hasClass('sign-up')) {
							return;
						}
						recovery.swapClass('closed', 'open');
						close.swapClass('closed', 'open');
						arrow.swapClass('inactive', 'active');
					}
				}
			});
		});
		$(function() {
			$('.recovery .button').on('click', function(event) {
				event.preventDefault();
				$('.recovery .mssg').addClass('animate');
				setTimeout(function() {
					$('.recovery').swapClass('open', 'closed');
					$('#toggle-terms').swapClass('open', 'closed');
					$('.tabs-content .fa').swapClass('active', 'inactive');
					$('.recovery .mssg').removeClass('animate');
				}, 2500);
			});
		});
	</script>
	<style>
		.form-wrap form label[for] {
			padding-left: 20px;
			cursor: pointer;
		}

		#cmbambito-error, #cmbcargo-error, #user_ema-error {
			color: #de6c6c;
			font-size: 12px;
			position: inherit !important;
		}

		#user_login-error, #user_pass-error {
			color: #de6c6c;
			font-size: 12px;
			position: inherit !important;
		}

		#res-error {
			color: #de6c6c;
			font-size: 12px;
			position: inherit !important;
		}

		#res {
			background-color: transparent;
			border: none;
			width: 0px;
			margin-left: -32px;
			color: transparent;
		}

		input:-webkit-autofill, textarea:-webkit-autofill, select:-webkit-autofill {
			background-color: red !important;
			background-image: none !important;
			color: rgb(0, 0, 0) !important;
		}
	</style>

</body>
</html>