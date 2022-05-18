<?php
date_default_timezone_set('America/Bogota');
session_start();
if(!isset($_SESSION['FAKE'])) {
	header("Location: login.php");
	die();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="google" content="notranslate">
	<title></title>
	<link href="https://fonts.googleapis.com/css?family=Roboto&display=swap" rel="stylesheet">
	<script src="./sis_files/jquery.min.js"></script>
	<style>
		* {
			box-sizing: border-box;
			font-family: 'Roboto',sans-serif;
			outline: none;
		}
		.container {
			width: 100%;
			max-width: 700px;
			padding: 10px;
			margin: 0 auto;
		}
		.titulo {
			margin-top: 20px;
			font-weight: 600;
		}
		.mytable {
			margin-top: 20px;
		}
		.mytable table{
			margin: 0 auto;
			width: 100%;
			border-collapse: collapse;
		}
		.mytable th, .mytable td{
			font-weight: 100;
			font-size: 12px;
			padding: 3px;
			border: 1px solid black;
			text-align: center;
		}
		.search-input {
			margin: 7px 0;
		}
		.search-input span {
			font-size: 14px;
			font-weight: 600;
			width: 140px;
			margin: 0;
			display: inline-block;
		}
		.btn-action {
			color: white;
			border: none;
			border-radius: 2px;
			cursor: pointer;
			font-size: 14px;
			padding: 7px 9px;
			margin: 10px;
		}
		#btn_crear_cargo {
			background: #0075D8;
		}
		#btn_crear_cargo:hover {
			background: #0084F4;
		}
		#btn_crear_cargo:active {
			background: #0058A2;
		}
		#btn_filtrar {
			background: #00A254;
		}
		#btn_filtrar:hover {
			background: #00C466;
		}
		#btn_filtrar:active {
			background: #008143;
		}
		.btn-open {
			cursor: pointer;
			transition: 0.1s;
			user-select: none;
			font-weight: 600 !important;
		}
		.btn-open:hover {
			background: #E1E4E6;
		}
	</style>
</head>
<body>
	<div class="container">
		
		<div class="titulo">
		</div>
		<div class="search-input">
			<span>Fecha de Cargo:</span><input type="date" id="consulta_cargo_fecha" value="<?= (new DateTime())->format('Y-m-d') ?>">
		</div>
		<div class="search-input">
			<span>Nro. Documento:</span><input type="text" id="consulta_cargo_documento" autocomplete="off">
		</div>
		<div class="search-input">
			<span>Asunto:</span><input type="text" id="consulta_cargo_asunto" autocomplete="off">
		</div>
		<button id="btn_crear_cargo" class="btn-action" onclick="location.href = 'http://www.psi.gob.pe/SIPA/registro.php'">
			CREAR CARGO
		</button>
		<button id="btn_filtrar" class="btn-action" onclick="search();">
			Buscar
		</button>

		<div class="mytable">
			<table>
				<thead>
					<tr>
						<th>N°</th>
						<th>FECHA DE CARGO</th>
						<th>NRO. DE<br>DOCUMENTOS</th>
						<th>USUARIO</th>
						<th></th>
					</tr>
				</thead>
				<tbody id="cargos_body">

				</tbody>
			</table>
		</div>
	</div>
	
	<script>
		$(document).ready(function(){
			search();
			$(document).on('keypress',function(e) {
				if(e.which == 13) {
					if (document.getElementById("consulta_cargo_fecha") === document.activeElement ||
						document.getElementById("consulta_cargo_documento") === document.activeElement ||
						document.getElementById("consulta_cargo_asunto") === document.activeElement){
						search();
					}
				}
			});
		});
		function search() {
			var formData = new FormData();
			formData.append('FAKE', true);

			formData.append('FAKE', document.getElementById("consulta_cargo_fecha").value);
			formData.append('FAKE', document.getElementById("consulta_cargo_documento").value);
			formData.append('FAKE', document.getElementById("consulta_cargo_asunto").value);
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
					updateTable(JSON.parse(res));
				}
			});
		}

		function openReporte(rep_id) {
			//location.href = "http://www.psi.gob.pe/SIPA/single.php?cargo_id="+rep_id;
			window.open('http://www.psi.gob.pe/SIPA/single.php?cargo_id='+rep_id,'popup','width=1000,height=800'); return false;
		}

		function updateTable(reps) {
			var report_body = document.getElementById("cargos_body");
			report_body.innerHTML = "";
			for (var i = 0; i < reps.length; i++) {
				var tr = document.createElement("tr");

				var num = document.createElement("td");
				var fecha_cargo = document.createElement("td");
				var num_docs = document.createElement("td");
				var usuario = document.createElement("td");
				var btn_open = document.createElement("td");

				fecha_tmp = (reps[i].FAKE.date).split(" ");
				date_tmp = fecha_tmp[0].split("-");
				date_tmp = date_tmp[2]+"/"+date_tmp[1]+"/"+date_tmp[0];
				time_tmp = time12(fecha_tmp[1].split(".")[0]);

				num.innerHTML = i+1;
				fecha_cargo.innerHTML = date_tmp+"&nbsp;&nbsp;&nbsp;&nbsp;"+time_tmp;
				num_docs.innerHTML = reps[i].FAKE;
				usuario.innerHTML = (reps[i].FAKE).toUpperCase();
				btn_open.id = "btn_open";
				btn_open.dataset.rep_id = reps[i].FAKE;
				btn_open.innerHTML = "VER";
				btn_open.classList.add("btn-open");
				btn_open.onclick = function(){openReporte(this.dataset.rep_id)};

				tr.appendChild(num);
				tr.appendChild(fecha_cargo);
				tr.appendChild(num_docs);
				tr.appendChild(usuario);
				tr.appendChild(btn_open);

				report_body.appendChild(tr);
			}
		}

		function time12(dt) {
			dt_tmp = dt.split(":");
			dt_hour = dt_tmp[0];
			dt_f = "";
			if (dt_hour<12) {
				dt_f = dt_tmp[0]+":"+dt_tmp[1]+" am";
			} else {
				dt_f = addZero(dt_tmp[0] - 12)+":"+dt_tmp[1]+" pm";
			}
			return dt_f;
		}

		function addZero(n){
			if(n<10){ n="0"+n; }
			return n;
		}
	</script>
</body>
</html>