<?php
date_default_timezone_set('America/Bogota');
session_start();
if(!isset($_SESSION['FAKE'])) {
	header("Location: login.php");
	die();
}
$auth_query = "SECRET_QUERY";
$auth = SQLStatement($auth_query);
$auth = json_decode(json_encode($auth),true);
foreach ($auth as $item) {
	if ($item['FAKE'] == 0) {
		header("Location: login.php");
		die();
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
	<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css?family=Roboto&display=swap" rel="stylesheet">
	<script src="./sis_files/jquery.min.js"></script>
	<link rel="stylesheet" href="./sis_files/loading.css">
	<script src="./sis_files/loading.js"></script>
	<style>
		* {
			box-sizing: border-box;
		}
		body,html {
			margin: 0;
			height: 100%;
		}
		.container-main {
			display: flex;
			flex-flow: row;
			height: 100%;
		}
		.container-main .tree{
			flex-grow: 1;
			overflow: auto;
		}
		.container-main .container-perfiles {
			height: 100%;
			width: 100%;
			max-width: 300px;
			font-family: 'Roboto', sans-serif;
			border-right: 1px solid #E7E7E7;
			background-color: #F8F8F6;
			font-size: 13px;
		}
		.container-perfiles .perfil {
			border-bottom: 1px solid #E7E7E7;
			cursor: pointer;
			padding: 3px;
		}
		.container-perfiles .perfil:hover {
			background-color: #EBF0F1;
		}
		.container-perfiles .perfil.active {
			background-color: #F7C43A;
			cursor: default;
		}
		ul,li {
			list-style-type: none;
		}
		ul {
			padding-inline-start: 28px;
		}
		li {
			padding-left: 10px;
		}
		.main-list ul li {
			border-left: 1px solid lightgray;
		}
		.main-list ul li:last-child{
			border-width: 1px;
			border-style: solid;
			border-image: 
			linear-gradient(to bottom,lightgray,rgba(0, 0, 0, 0)) 1 100%;
		}
		.opt-container {
			display: flex;
			align-items: center;
		}
		.opt-container i {
			font-size: 20px;
			user-select: none;
			cursor: pointer;
			transition: 0.1s;
			border-radius: 20px;
			margin-left: -20px;
			background: white;
		}
		.opt-container i:hover{
			color: #1C91ED;
			background: #ECEEEF;
		}
		.opt-container input, .opt-container label {
			margin: auto 3px;
		}
		input:checked + label {
			color: black;
		}
		input + label {
			color: red;
			user-select: none;
		}
		.opt-container .opt-label {
			font-family: 'Roboto', sans-serif;
			font-size: 14px;
		}
		.nested {
			transition: 0.2s;
			overflow: hidden;
			height: 0;
		}
		.nested-show {
			height: auto;
		}
		.expand {
			transform: rotate(-90deg);
		}
		#btn_save {
			color: white;
			background-color: #007DE7;
			border: none;
			border-radius: 3px;
			padding: 10px;
			cursor: pointer;
		}
		#btn_save:hover {
			background-color: #3A98E7;
		}
		#btn_save:disabled {
			color: gray;
			background-color: lightgray;
			cursor: default;
		}
	</style>
</head>
<body>
	<?php include("./sis_files/loading.php"); ?>
	<div class="container-main">
		<div class="container-perfiles" id="container_perfiles">
		</div>
		<div class="tree" id="tree">
		</div>
		<div style="position: fixed; top: 0; right: 0; margin: 20px 40px;">
			<button id="btn_save" onclick="saveAccesos();">Aplicar</button>
		</div>
	</div>
	<script>
		var perfil_id = 1;
		var json_perfiles;
		var json_accesos;
		var json_accesos_edit = {};

		$(document).ready(function(){
			getPerfiles();
			getAccesos();
			document.getElementById("btn_save").disabled = true;
		});

		function setExpandable() {
			var toggler = document.getElementsByClassName("opt");
			for (var i = 0; i < toggler.length; i++) {
				toggler[i].addEventListener("click", function() {
					this.parentElement.parentElement.querySelector(".nested").classList.toggle("nested-show");
					this.classList.toggle("expand");
				});
			}
		}

		function inputCheck() {
			var toggler = document.getElementsByClassName("opt-check");
			for (var i = 0; i < toggler.length; i++) {
				toggler[i].addEventListener("change", function() {
					document.getElementById("btn_save").disabled = false;
					var valor;
					var this_id = this.id;
					if(this.checked) valor = 1;
					else valor = 0;
					json_accesos[this_id].Autorizado = valor;
					json_accesos_edit[this_id] = json_accesos[this_id];
					parentCheck(this_id, valor);
					recursiveCheck(this_id, valor);
				});
			}
		}

		function parentCheck(id,valor) {
			var parent = (json_accesos[id].FAKE).trim();
			if(parent == '') return;
			var go = true;
			if (valor == 1) {
				var json_tmp = {};
				for (var k in json_accesos) {
					if (json_accesos[k].FAKE == parent) json_tmp[k] = json_accesos[k];
				}
				for (var k in json_tmp) {
					if (json_tmp[k].FAKE == 0) {
						go = false;
						break;
					}
				}
			}
			if (go || valor == 0) {
				document.getElementById(parent).checked = valor;
				json_accesos[parent].FAKE = valor;
				json_accesos_edit[parent] = json_accesos[parent];
			}
		}

		function recursiveCheck(id, valor) {
			for (var k in json_accesos) {
				if (json_accesos[k].FAKE == id) {
					document.getElementById(k).checked = valor;
					json_accesos[k].FAKE = valor;
					json_accesos_edit[k] = json_accesos[k];
					recursiveCheck(k, valor);
				}
			}
		}

		function buildPerfiles() {
			var container = document.getElementById("container_perfiles");
			container.innerHTML = "";
			for (var k in json_perfiles) {
				var perfil = document.createElement("div");
				perfil.innerHTML = json_perfiles[k];
				perfil.dataset.id = k;
				perfil.classList.add("perfil");
				if(this.perfil_id == k) perfil.classList.add("active");
				else perfil.onclick = function(){setPerfil(this.dataset.id)};
				container.appendChild(perfil);
			}
		}

		function buildAccesos() {
			var container = document.getElementById("tree");
			container.innerHTML = "";
			var grupo = recursive();
			container.appendChild(grupo);
			setExpandable();
			inputCheck();
		}

		function recursive(codigo = "") {
			var json_grupos = {};
			for(var k in json_accesos) {
				if(json_accesos[k].FAKE.trim() == codigo) json_grupos[k] = json_accesos[k];
			}
			var ul = document.createElement("ul");
			if(codigo == "") ul.classList.add("main-list");
			else ul.classList.add("nested");
			var go = false;
			for(var k in json_grupos) {
				var li = document.createElement("li");
				var div = document.createElement("div");
				div.classList.add("opt-container");

				var input = document.createElement("input");
				input.type = "checkbox";
				input.id = k;
				input.classList.add("opt-check");
				if(json_grupos[k].FAKE == 1) input.checked = true;
				var label = document.createElement("label");
				label.htmlFor = k;
				label.innerHTML = json_grupos[k].FAKE;
				label.classList.add("opt-label");

				var subgrupo = recursive(k);

				if (subgrupo != null) {
					var arrow = document.createElement("i");
					arrow.innerHTML = "arrow_drop_down";
					arrow.classList.add("material-icons", "opt", "expand");
					div.appendChild(arrow);
				}
				
				div.appendChild(input);
				div.appendChild(label);

				li.appendChild(div);

				if(subgrupo != null) li.appendChild(subgrupo);
				ul.appendChild(li);
				go = true;
			}
			if(go) return ul;
			else return null;
		}

		function setPerfil(id) {
			this.perfil_id = id;
			buildPerfiles();
			getAccesos();
			json_accesos_edit = {};
			document.getElementById("btn_save").disabled = true;
		}
		function setPerfiles(jsn) {
			this.json_perfiles = jsn;
		}
		function setAccesos(jsn) {
			this.json_accesos = jsn;
		}

		function getPerfiles() {
			var formData = new FormData();
			formData.append('FAKE', true);
			$.ajax({
				url: 'http://www.psi.gob.pe/SIPA/control2.php',
				type: 'POST',

				data: formData,
				cache: false,
				contentType: false,
				processData: false,

				error: function(){
					return true;
				},
				success: function(res){
					setPerfiles(JSON.parse(res));
					buildPerfiles();
					document.getElementById("btn_save").disabled = true;
				}
			});
		}

		function getAccesos() {
			var formData = new FormData();
			formData.append('FAKE', true);
			formData.append('FAKE', this.perfil_id);
			$.ajax({
				url: 'http://www.psi.gob.pe/SIPA/control2.php',
				type: 'POST',

				data: formData,
				cache: false,
				contentType: false,
				processData: false,

				error: function(){
					return true;
				},
				success: function(res){
					setAccesos(JSON.parse(res));
					buildAccesos();
				}
			});
		}

		function saveAccesos() {
			loading(true);
			document.getElementById("btn_save").disabled = true;
			var formData = new FormData();
			formData.append('FAKE', true);
			formData.append('FAKE', JSON.stringify(json_accesos_edit));
			formData.append('FAKE', this.perfil_id);
			$.ajax({
				url: 'http://www.psi.gob.pe/SIPA/control2.php',
				type: 'POST',

				data: formData,
				cache: false,
				contentType: false,
				processData: false,

				error: function(){
					return true;
				},
				success: function(res){
					if(res == 1) {
						json_accesos_edit = {};
					}
					loading(false);
				}
			});
		}
	</script>
</body>
</html>