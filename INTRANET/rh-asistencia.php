<?php
/* Template Name: RH Asistencia */
if(!is_user_logged_in()){
	auth_redirect();
}
date_default_timezone_set('America/Bogota');
global $wpdb;
global $current_user; wp_get_current_user();

if(!allowUser($wpdb, $current_user, "asistencia")) die();

$psi_names = array();
$psi_alias = array();

$skip_user = array("foo","foo","foo","foo","foo","foo","foo","foo","foo","foo");

$conn = ldap_connect("ldap://000.000.000.000:123");
$username = "";
$pass = "";
$user = $username."@upc.com";
ldap_set_option($conn,LDAP_OPT_PROTOCOL_VERSION,3);
ldap_set_option($conn, LDAP_OPT_REFERRALS, 0);
if(@ldap_bind($conn,$user,$pass)){
	$filter = "(&(objectCategory=person)(samaccountname=*)(useraccountcontrol=*))";
	$fields = array("cn","dn","samaccountname","useraccountcontrol");
	$sr = ldap_search($conn,"DC=UPC,DC=COM",$filter,$fields);
	$entries = ldap_get_entries($conn, $sr);
	for ($x=0;$x<$entries['count'];$x++) {
		$code=$entries[$x]['useraccountcontrol'][0];
		if($code=="66050" || $code=="66082" || $code=="514") continue;
		if(in_array(strtolower($entries[$x]['samaccountname'][0]), $skip_user)) continue;
		$tmp1 = ucwords(mb_strtolower($entries[$x]['cn'][0]));
		$tmp2 = mb_strtolower($entries[$x]['samaccountname'][0]);
		array_push($psi_names, $tmp1);
		array_push($psi_alias, $tmp2);
	}
}
array_multisort($psi_names, $psi_alias);

function nameOfUser($id,$arr_ids,$arr_names){
	$pos = array_search($id, $arr_ids);
	$name = $arr_names[$pos];
	return $name;
}

function allowUser($wpdb, $current_user, $category = null) {
	$allow = false;
	$roles = $current_user->roles;
	if($roles[0] == "administrator") {
		$allow = true;
	}
	if(!$allow && $category != null){
		$tmp = $wpdb->get_results("SELECT * FROM foo WHERE foo = aux");
		$tmp = json_decode(json_encode($tmp), True);
		foreach ($tmp as $adm) {
			if(mb_strtoupper($adm['foo']) == mb_strtoupper($current_user->user_login)) {
				$allow = true;
				break;
			}
		}
	}
	return $allow;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<link href="https://fonts.googleapis.com/css?family=Roboto&display=swap" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css?family=Roboto+Slab&display=swap" rel="stylesheet">
	<script lang="javascript" src="../../res/xlsx.full.min.js"></script>
	<script lang="javascript" src="../../res/FileSaver.js"></script>
	<script src="../../res/jquery.min.js"></script>

	<link rel="stylesheet" href="../../res/loading.css">
	<script src="../../res/loading.js"></script>

	<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
	<style type="text/css">
		* {
			box-sizing: border-box;
		}
		body {
			margin: 20px 20px 60px 20px;
			font-family: 'Roboto', sans-serif;
		}
		.titulo {
			font-family: 'Roboto Slab', serif;
			font-size: 20px;
			color: #373737;
			/*text-align: center;*/
			margin: 10px 30px;
		}
		
		.autocomplete {
			position: relative;
			display: flex;
			align-items: center;
		}
		.autocomplete-items {
			position: absolute;
			border: 1px solid #d4d4d4;
			z-index: 99;
			top: 100%;
			left: 0;
			right: 0;
			max-height: 400px;
			overflow: auto;
			font-size: 14px;
			width: 100%;
			max-width: 410px;
			margin-left: 100px;
			margin-top: -5px;
		}
		.autocomplete-items div {
			padding: 5px;
			cursor: pointer;
			background-color: #fff; 
			border-bottom: 1px solid #d4d4d4; 
		}
		.autocomplete-items div:hover {
			background-color: #e9e9e9; 
		}
		.autocomplete-active {
			background-color: DodgerBlue !important; 
			color: #ffffff;
		}
		.autocomplete-active * {
			color: #ffffff !important;
		}
		.container {
			margin: 30px;
		}
		.buzon-enviar label {
			display: inline-block;
			min-width: 100px;
			font-size: 14px;
			font-weight: bold;
		}
		.buzon-enviar input[type=text] {
			border: 1px solid lightgray;
			border-radius: 3px;
			padding: 5px;
			width: 410px;
			font-size: 16px;
		}
		.buzon-enviar input[type=date] {
			border: 1px solid lightgray;
			border-radius: 3px;
			padding: 5px;
			font-size: 16px;
			width: 180px;
		}
		.auto-alias {
			color: cornflowerblue;
			font-family: consolas;
		}
		.destino-check {
			font-size: 20px;
			margin-left: 5px;
		}
		.destino-check.gris {
			color: lightgray;
		}
		.destino-check.azul {
			color: cornflowerblue;
		}
		
		#buzon_destino_f {
			font-family: consolas;
			font-size: 14px;
			color: cornflowerblue;
			margin-left: 5px;
		}
		
		#as_fechas, #as_tipo {
			display: flex;
			align-items: center;
			margin-top: 20px;
		}
		
		.bigger-button {
			padding: 10px 20px;
			/*margin: 20px 5px 10px 5px;*/
			font-size: 1.0em;
		}

		button {
			font-size: 14px;
			border: none;
			outline: none;
			padding: 7px 10px;
			margin: 5px;
			border-radius: 3px;
			cursor: pointer;
		}
		button i {
			font-size: 20px !important;
		}
		.btn-blue {
			color: white;
			background: #1a73e8;
		}
		.btn-blue:active {
			background: #3e87e8;
		}
		button:hover {
			box-shadow: 0 1px 2px 0 rgb(86 86 86 / 45%), 0 1px 3px 1px rgb(86 86 86 / 30%);
		}
		
		.btn-black {
			color: white;
			background: #565656;
		}
		.btn-black:active {
			background: #696969;
		}
		
		
		button:disabled,
		button[disabled],
		button:disabled:hover,
		button[disabled]:hover{
			/*border: 1px solid #999999;*/
			background-color: #e4e4e4;
			color: #adadad;
			cursor: default;
			box-shadow: none;
		}
		.border-red {
			outline: 1px solid red;
		}
		#as_resultado {
			margin: 30px;
		}
		#as_head {
			margin: 20px 0px;
			font-size: 18px;
			font-family: 'Roboto Slab', serif;
			padding: 8px 20px;
			background: #424242;
			color: white;
			display: inline-block;
			border-radius: 5px;
		}
		#as_single {
			max-width: 500px;
		}
		#as_multi {
			max-width: 700px;
		}
		#as_multi td:first-child {
			text-align: left;
		}
		table {
			width: 100%;
			/*max-width: 500px;*/
			border-collapse: collapse;
			text-align: center;
			border-radius: 5px;
        	border-style: hidden; /* hide standard table (collapsed) border */
        	box-shadow: 0 0 0 2px #424242;
			display: inline-table;
		}
		td {
			border-bottom: 2px solid #424242;
			padding: 5px;
			background: white;
		}
		th {
			color: white;
			background: #424242;
			padding: 5px;
			font-weight: 100;
		}
		table tr:first-child th:first-child {
			border-top-left-radius: 4px;
		}
		table tr:first-child th:last-child {
			border-top-right-radius: 4px;
		}
		table tr:last-child td:first-child {
			border-bottom-left-radius: 5px;
		}
		table tr:last-child td:last-child {
			border-bottom-right-radius: 5px;
		}
	</style>
</head>
<body>
	
	<?php //echo "<pre>"; print_r($foo); echo "</pre>" ?>
	
	<?php include('./res/loading.php'); ?>
	
	<div class="titulo">
		Reporte de Asistencia de Personal en Trabajo Remoto
	</div>
	<div id="as_form" class="buzon-enviar container">
		<div id="as_usuario" class="autocomplete enviar-field">
			<label for="destino">Nombre&nbsp;</label>
			<input id="destino" type="text" autocomplete="off" autofocus>
			<button class="btn-blue" onclick="search_all();">
				Todos
			</button>
			<input type="text" id="buzon_destino" name="buzon_destino" onpaste="console.log('name change');" hidden>
		</div>
		<div id="as_fechas">
			<label>Fecha inicio</label>
			<input type="date" id="search_fecha_inicio">
			<span>&nbsp;&nbsp;-&nbsp;&nbsp;</span>
			<label>Fecha termino</label>
			<input type="date" id="search_fecha_fin">
			<input type="checkbox" style="margin-right: 0; margin-left: 10px;" id="search_alldates" checked>
			<label for="search_alldates" style="padding-left: 5px;">Todas las fechas</label>
		</div>
	</div>
	<div id="as_resultado">
		<div id="as_head">--</div>
		<div id="as_controles">
			
		</div>
		<div id="as_tabla">
			<table id="as_single">
				<thead>
					<tr>
						<th>Fecha</th>
						<th>Entrada</th>
						<th>Salida</th>
					</tr>
				</thead>
				<tbody id="as_tbody">
					<tr>
						<td>- -</td>
						<td>- -</td>
						<td>- -</td>
					</tr>
				</tbody>
			</table>
			<table id="as_multi" style="display: none;">
				<thead>
					<tr>
						<th>Nombre</th>
						<th>Fecha</th>
						<th>Entrada</th>
						<th>Salida</th>
					</tr>
				</thead>
				<tbody id="as_tbody2">
					<tr>
						<td>- -</td>
						<td>- -</td>
						<td>- -</td>
						<td>- -</td>
					</tr>
				</tbody>
			</table>
			<button id="btn_descargar" class="btn-blue bigger-button" onclick="generar_reporte()" disabled>
				Descargar
			</button>
		</div>
	</div>

	<script>
		var current_name = "";
		
		var as_all = false;
		
		var as_nombre;
		var as_inicio;
		var as_fin;
		var as_alldates;
		/*var as_entrada;
		var as_salida;*/
		var btn_descargar;
		
		var as_result;
		var as_tbody;
		var as_tbody2;
		var as_single;
		var as_multi;
		
		var opdate = {month:'short',day:'2-digit',year:'numeric'};
		var optime = {hour:'2-digit',minute:'2-digit',hour12:'true'};
		
		var opdate_full = {day:'2-digit',month:'2-digit',year:'numeric'};
		
		$(document).ready(function(){
			as_nombre = document.getElementById("buzon_destino");
			as_inicio = document.getElementById("search_fecha_inicio");
			as_fin = document.getElementById("search_fecha_fin");
			as_alldates = document.getElementById("search_alldates");
			btn_descargar = document.getElementById("btn_descargar");
			
			as_tbody = document.getElementById("as_tbody");
			as_single = document.getElementById("as_single");
			as_tbody2 = document.getElementById("as_tbody2");
			as_multi = document.getElementById("as_multi");
			
			as_inicio.onchange = function(){
				as_alldates.checked = false;
				as_fin.setAttribute("min", as_inicio.value);
				if(as_nombre.value == "ALL") val_buscar2();
				else val_buscar();
			};
			as_fin.onchange = function(){
				as_alldates.checked = false;
				as_inicio.setAttribute("max", as_fin.value);
				if(as_nombre.value == "ALL") val_buscar2();
				else val_buscar();
			};
			as_alldates.onchange = function(){
				if(as_alldates.checked){
					as_inicio.value = '';
					as_fin.value = '';
					as_inicio.removeAttribute("max");
					as_fin.removeAttribute("min");
					if(as_nombre.value == "ALL") val_buscar2();
					else val_buscar();
				}
			};
			/*
			window.onclick = function(event) {
				if (event.target == tarea_edit) {
					clean_form();
				}
			}
			*/
		});

		var psi_names, psi_alias;
		var psi_destino_name = null;
		var psi_destino_alias = null;
		function autocomplete(inp, arr) {
			var currentFocus;
			inp.addEventListener("input", function(e) {
				var a, b, i, val = cleanSpaces(this.value);
				closeAllLists();
				if (!val) { return false;}
				currentFocus = -1;
				a = document.createElement("DIV");
				a.setAttribute("id", this.id + "autocomplete-list");
				a.setAttribute("class", "autocomplete-items");
				this.parentNode.appendChild(a);
				for (i = 0; i < arr.length; i++) {
					if (wordsInString(arr[i],val) || wordsInString(psi_alias[i],val)) {
						b = document.createElement("DIV");
						b.innerHTML += setHighlights(arr[i],val.split(" "));
						/*b.innerHTML += "<span class='auto-alias'> : "+psi_alias[i].toLowerCase()+"</span>";*/
						b.innerHTML += "<input type='hidden' value='" + arr[i] + "'>";
						b.innerHTML += "<input type='hidden' value='" + psi_alias[i] + "'>";
						b.addEventListener("click", function(e) {
							inp.value = this.getElementsByTagName("input")[0].value;
							document.getElementById('as_head').innerHTML = this.getElementsByTagName("input")[0].value;
							current_name = this.getElementsByTagName("input")[0].value;
							document.getElementById('buzon_destino').value = this.getElementsByTagName("input")[1].value;
							//document.getElementsByClassName('destino-check')[0].classList.remove("gris");
							//document.getElementsByClassName('destino-check')[0].classList.add("azul");
							closeAllLists();
							val_buscar();
						});
						a.appendChild(b);
					}
				}
			});
			inp.addEventListener("keydown", function(e) {
				var x = document.getElementById(this.id + "autocomplete-list");
				if (x) x = x.getElementsByTagName("div");
				if (e.keyCode == 40) {
					currentFocus++;
					addActive(x);
					document.getElementsByClassName("autocomplete-active")[0].scrollIntoView(false);
				} else if (e.keyCode == 38) {
					currentFocus--;
					addActive(x);
					document.getElementsByClassName("autocomplete-active")[0].scrollIntoView(false);
				} else if (e.keyCode == 13) {
					e.preventDefault();
					if (currentFocus > -1) {
						if (x) x[currentFocus].click();
					}
				}
			});
			function addActive(x) {
				if (!x) return false;
				removeActive(x);
				if (currentFocus >= x.length) currentFocus = 0;
				if (currentFocus < 0) currentFocus = (x.length - 1);
				x[currentFocus].classList.add("autocomplete-active");
			}
			function removeActive(x) {
				for (var i = 0; i < x.length; i++) {
					x[i].classList.remove("autocomplete-active");
				}
			}
			function closeAllLists(elmnt) {
				var x = document.getElementsByClassName("autocomplete-items");
				for (var i = 0; i < x.length; i++) {
					if (elmnt != x[i] && elmnt != inp) {
						x[i].parentNode.removeChild(x[i]);
					}
				}
			}
			
			document.addEventListener("click", function (e) {
				closeAllLists(e.target);
			});
		}
		/* FIND */
		function cleanSpaces(string){
			new_string = string.replace(/\s+/g,' ').trim();
			return new_string
		}

		function rawString(str) {
			var str_norm = str.normalize('NFD').replace(/[\u0300-\u036f]/g, "");
			return str_norm.toUpperCase();
		}

		function wordsInString(string, keywords) {
			var keywords_f = keywords.split(" ");
			for(var i = 0; i < keywords_f.length; i++){
				if(!rawString(string).includes(rawString(keywords_f[i]))) return false;
			}
			return true;
		}
		/* HIGHLIGHT */
		function getIndexOf(str, key) {
			var str_f = rawString(str);
			var key_f = rawString(key);
			return str_f.indexOf(key_f);
		}

		function getSubstring(str, key) {
			var ind = getIndexOf(str, key);
			if(ind == -1) return null;
			var len = key.length;
			var sub = str.substring(ind, ind + len);
			return sub;
		}

		function setHighlights(str, keys) {
			if(keys.length == 0) return str;
			
			var key_origin = getSubstring(str, keys[0]);
			var lines = str.split(key_origin);

			for(var i=0;i<lines.length;i++){
				var keys_alt = keys.slice(1);
				lines[i] = setHighlights(lines[i], keys_alt);
			}

			var key_highlight = "<strong>"+key_origin+"</strong>";
			var res = lines.join(key_highlight);
			return res;
		}

		psi_names = <?= json_encode($psi_names) ?>;
		psi_alias = <?= json_encode($psi_alias) ?>;

		autocomplete(document.getElementById("destino"), psi_names);

		function buscar() {
			loading(true);
			var formData = new FormData();
			formData.append("control_asistencia",true);
			formData.append("as_nombre",as_nombre.value);
			formData.append("as_inicio",as_inicio.value);
			formData.append("as_fin",as_fin.value);
			$.ajax({
				url: 'http://www.psi.gob.pe/intranet/index.php/editor-service',
				type: 'POST',
				data: formData,
				cache: false,
				contentType: false,
				processData: false,
				error: function(){
					return true;
				},
				success: function(e){
					as_result = JSON.parse(e);
					fill_table(as_result);
					loading(false);
				}
			});
			//btn_descargar.disabled = false;
		}
		
		function buscar2() {
			loading(true);
			var formData = new FormData();
			formData.append("control_asistencia_all",true);
			/*formData.append("as_nombre",as_nombre.value);*/
			formData.append("as_inicio",as_inicio.value);
			formData.append("as_fin",as_fin.value);
			$.ajax({
				url: 'http://www.psi.gob.pe/intranet/index.php/editor-service',
				type: 'POST',
				data: formData,
				cache: false,
				contentType: false,
				processData: false,
				error: function(){
					return true;
				},
				success: function(e){
					as_result = JSON.parse(e);
					fill_table2(as_result);
					loading(false);
				}
			});
			//btn_descargar.disabled = false;
		}

		function generar_reporte(){
			var wb = XLSX.utils.book_new();

			wb.Props = {
				Title: "Reporte de Asistencia de Personal en Trabajo Remoto",
				Author: "PSI",
				CreatedDate: new Date()
			};
			wb.SheetNames.push("REPORTE");

			var ws_data = [];
			/*ws_data[0] = ["Reporte de Asistencia"];*/
			/*ws_data[2] = [current_name];*/
			var fila = 0;

			ws_data[fila] = ["Nombre","Fecha","Hora de Entrada","Hora de Salida"];

			fila++;
			for (var i = 0; i < as_result.length; i++) {
				var tkrow = [];
				tkrow[0] = psi_names[psi_alias.indexOf(as_result[i].usuario_id.toLowerCase())];
				var aux = new Date(as_result[i].entrada);
				tkrow[1] = aux.toLocaleDateString("es-PE",opdate_full);
				tkrow[2] = aux.toLocaleTimeString("en-PE",optime);
				if(!as_result[i].salida) tkrow[3] = "SIN MARCAR";
				else{
					var aux2 = new Date(as_result[i].salida);
					tkrow[3] = aux2.toLocaleTimeString("en-PE",optime);
				}
				ws_data[fila] = tkrow;
				fila++;
			}
			var ws = XLSX.utils.aoa_to_sheet(ws_data);
			wb.Sheets["REPORTE"] = ws;
			var wbout = XLSX.write(wb, {bookType:'xlsx',  type: 'binary'});
			var filename = "RA_" + current_name + "_" + (new Date()).getTime() + ".xlsx";
			saveAs(new Blob([s2ab(wbout)],{type:"application/octet-stream"}), filename);
		}

		function s2ab(s) {
			var buf = new ArrayBuffer(s.length);
			var view = new Uint8Array(buf);
			for (var i=0; i<s.length; i++) view[i] = s.charCodeAt(i) & 0xFF;
				return buf;
		}
		
		/*function filter_dates() {
			if(as_alldates.checked) fill_table(as_result);
			else{
				var new_result = [];
				if(as_inicio.value != ''){
					var dini = new Date(as_inicio.value + " 00:00:00");
				}
				if(as_fin.value != '') {
					var dfin = new Date(as_fin.value + " 23:59:59");
				}
			}
		}*/
		
		function fill_table(as_json) {
			as_single.style.display = "inline-table";
			as_multi.style.display = "none";
			if(as_json.length == 0) btn_descargar.disabled = true;
			else btn_descargar.disabled = false;
			as_tbody.innerHTML = '';
			for(var i = 0; i < as_json.length; i++) {
				var tr = document.createElement("tr");
				var td1 = document.createElement("td");
				var td2 = document.createElement("td");
				var td3 = document.createElement("td");
				var entrada = as_json[i].entrada;
				var salida = as_json[i].salida;
				var e_date = new Date(entrada);
				td1.innerHTML = e_date.toLocaleDateString("es-PE",opdate);
				td2.innerHTML = e_date.toLocaleTimeString("en-PE",optime);
				if(salida){
					var s_date = new Date(salida);
					td3.innerHTML = s_date.toLocaleTimeString("en-PE",optime);
				}else{
					td3.innerHTML = '- -';
				}
				tr.appendChild(td1);
				tr.appendChild(td2);
				tr.appendChild(td3);
				as_tbody.appendChild(tr);
			}
		}
		
		function fill_table2(as_json) {
			as_single.style.display = "none";
			as_multi.style.display = "inline-table";
			if(as_json.length == 0) btn_descargar.disabled = true;
			else btn_descargar.disabled = false;
			as_tbody2.innerHTML = '';
			for(var i = 0; i < as_json.length; i++) {
				var tr = document.createElement("tr");
				var td1 = document.createElement("td");
				var td2 = document.createElement("td");
				var td3 = document.createElement("td");
				var td4 = document.createElement("td");
				var entrada = as_json[i].entrada;
				var salida = as_json[i].salida;
				var e_date = new Date(entrada);
				td1.innerHTML = psi_names[psi_alias.indexOf(as_json[i].usuario_id.toLowerCase())];
				td2.innerHTML = e_date.toLocaleDateString("es-PE",opdate);
				td3.innerHTML = e_date.toLocaleTimeString("en-PE",optime);
				if(salida){
					var s_date = new Date(salida);
					td4.innerHTML = s_date.toLocaleTimeString("en-PE",optime);
				}else{
					td4.innerHTML = '- -';
				}
				tr.appendChild(td1);
				tr.appendChild(td2);
				tr.appendChild(td3);
				tr.appendChild(td4);
				as_tbody2.appendChild(tr);
			}
		}
		
		function val_buscar() {
			if (as_nombre.value == '') return;
			if (!as_alldates.checked){
				if(as_inicio.value == '' && as_fin.value == '') return;
			}
			/*if(!as_entrada.checked && !as_salida.checked) return;*/
			buscar();
		}
		
		function val_buscar2() {
			if (as_nombre.value == '') return;
			if (!as_alldates.checked){
				if(as_inicio.value == '' && as_fin.value == '') return;
			}
			/*if(!as_entrada.checked && !as_salida.checked) return;*/
			buscar2();
		}
		
		function search_all() {
			current_name = "Personal PSI";
			document.getElementById('as_head').innerHTML = "Personal PSI";
			document.getElementById('buzon_destino').value = "ALL";
			document.getElementById('destino').value = "";
			val_buscar2();
		}

	</script>
</body>
</html>