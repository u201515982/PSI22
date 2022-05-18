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
	<link rel="stylesheet" href="./sis_files/loading.css">
	<script src="./sis_files/loading.js"></script>

	<style>
		* {
			box-sizing: border-box;
			outline: none;
		}
		body {
			margin: 0;
			font-family: 'Roboto', sans-serif;
		}
		.reporte-container {
			margin: 20px auto;
			width: 100%;
			max-width: 1500px;
			padding: 0 20px;
		}
		.reporte-container .titulo {
			font-size: 18px;
			margin: 20px 0;
		}
		.reporte-container .sisged-cud {
			margin: 10px 0;
		}
		.reporte-container .sisged-cud span {
			font-size: 14px;
			font-weight: 600;
		}
		.reporte-container .reporte table {
			border-collapse: collapse;
			width: 100%;
		}
		.reporte-container .reporte th, .reporte-container .reporte td {
			font-size: 13px;
			padding: 3px;
			border: 1px solid black;
		}
		.reporte-container .reporte th {
			font-weight: 100;
			background: #E9EDEE;
		}
		.reporte-container .reporte td {
			padding: 5px;
		}
		.reporte-container .btn-delete {
			color: #D90000;
			cursor: pointer;
			transition: 0.1s;
			user-select: none;
		}
		.reporte-container .btn-delete:hover {
			background: #D90000;
			color: white;
		}
		.options {
			float: right;
		}
		.options button {
			margin: 0 10px 20px 0;
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
		#btn_guardar {
			background: #0075D8;
		}
		#btn_guardar:hover {
			background: #0084F4;
		}
		#btn_guardar:active {
			background: #0058A2;
		}
		#btn_volver {
			background: #F08D00;
		}
		#btn_volver:hover {
			background: #FFA800;
		}
		#btn_volver:active {
			background: #D67E00;
		}
	</style>
</head>
<body>
	<?php include("./sis_files/loading.php"); ?>
	<div class="reporte-container">
		<div class="titulo">
			Reporte
		</div>
		<div class="sisged-cud">
			<span>CUD :&nbsp;</span>
			<input type="text" name="cud" id="cud" autocomplete="off">
			<div class="options">
			<button id="btn_guardar" class="btn-action">Guardar</button>
			<button id="btn_volver" class="btn-action" onclick="location.href = 'http://www.psi.gob.pe/SIPA/list.php'">Volver</button>
		</div>
		</div>
		
		<div class="reporte">
			<table id="my_report">
				<thead>
					<tr>
						<th>N°</th>
						<th>CUD</th>
						<th>CUT</th>
						<th>N° DOCUMENTO</th>
						<th>FECHA DOCUMENTO</th>
						<th>ASUNTO</th>
						<th>CANTIDAD FOLIOS</th>
						<th>OBSERVACIONES</th>
						<th></th>
					</tr>
				</thead>
				<tbody id="my_report_body">
					
				</tbody>
			</table>
		</div>
		
	</div>
	<script>
		var docs = [];
		$(document).ready(function(){
			document.getElementById("cud").focus();
			$(document).on('keypress',function(e) {
				if(e.which == 13) {
					blurAll();
					cud_input = document.getElementById("cud");
					if (!(cud_input.value.trim())){
						cud_input.focus();
						return;
					}
					for (var i = 0; i < docs.length; i++) {
						if (cud_input.value.trim() == docs[i].CUD) {
							cud_input.value = "";
							cud_input.focus();
							return;
						}
					}
					loading(true);
					var formData = new FormData();
					formData.append("cud",cud_input.value.trim());
					formData.append("consulta_cud",true);
					$.ajax({
						url: 'http://www.psi.gob.pe/SIPA/control.php',
						type: 'POST',

						data: formData,
						cache: false,
						contentType: false,
						processData: false,

						error: function(){
							cud_input.focus();
							loading(false);
							return true;
						},
						success: function(rep){
							console.log(rep);
							json_rep = JSON.parse(rep);
							if (json_rep == null) {
								cud_input.value = "";
								loading(false);
								cud_input.focus();
								return true;
							}
							docs.push(json_rep);
							addToTable(docs);
							cud_input.value = "";
							loading(false);
							cud_input.focus();
						}
					});
				}
			});
			$('#btn_guardar').on('click', function () {
				if(docs.length == 0) return;
				save_report();
			});
		});

		function save_report() {
			var formData = new FormData();
			formData.append('docs', JSON.stringify(docs));
			formData.append('reporte_guardar', true);
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
					var json_res = JSON.parse(res);
					openReporte(json_res.LastID);
					location.href = 'http://www.psi.gob.pe/SIPA/list.php';
				}
			});
		}

		function openReporte(rep_id) {
			//location.href = "http://www.psi.gob.pe/SIPA/single.php?cargo_id="+rep_id;
			window.open('http://www.psi.gob.pe/SIPA/single.php?cargo_id='+rep_id,'popup','width=1000,height=800'); return false;
		}

		function removeFromArray(n) {
			this.docs.splice(n,1);
			addToTable(this.docs);
			document.getElementById("cud").focus();
		}

		function blurAll(){
			var tmp = document.createElement("input");
			document.body.appendChild(tmp);
			tmp.focus();
			document.body.removeChild(tmp);
		}

		function addToTable(mydocs) {
			var report_body = document.getElementById("my_report_body");
			report_body.innerHTML = "";
			for (var i = 0; i < mydocs.length; i++) {
				var tr = document.createElement("tr");

				var num = document.createElement("td");
				var cud = document.createElement("td");
				var cut = document.createElement("td");
				var documento = document.createElement("td");
				var fecha = document.createElement("td");
				var asunto = document.createElement("td");
				var folios = document.createElement("td");
				var observaciones = document.createElement("td");
				var btn_delete = document.createElement("td");

				num.innerHTML = i;
				cud.innerHTML = mydocs[i].CUD;
				cut.innerHTML = mydocs[i].CUT;
				documento.innerHTML = mydocs[i].DOCUMENTO;
				fecha.innerHTML = mydocs[i].FECHA_DOCUMENTACION;
				asunto.innerHTML = mydocs[i].ASUNTO;
				folios.innerHTML = mydocs[i].CANT_FOLIOS;
				observaciones.innerHTML = mydocs[i].OBSERVACION==null?"-":mydocs[i].OBSERVACION;
				btn_delete.id = "btn_delete";
				btn_delete.dataset.doc_pos = i;
				btn_delete.innerHTML = "QUITAR";
				btn_delete.classList.add("btn-delete");
				btn_delete.onclick = function(){removeFromArray(this.dataset.doc_pos)};

				tr.appendChild(num);
				tr.appendChild(cud);
				tr.appendChild(cut);
				tr.appendChild(documento);
				tr.appendChild(fecha);
				tr.appendChild(asunto);
				tr.appendChild(folios);
				tr.appendChild(observaciones);
				tr.appendChild(btn_delete);

				report_body.appendChild(tr);
			}
		}
	</script>
</body>
</html>