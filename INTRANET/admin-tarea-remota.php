<?php
/*
Template Name: admin-tarea-remota
*/
if(!is_user_logged_in()){
	auth_redirect();
}
global $wpdb;
global $current_user; wp_get_current_user();

if (!allowUser($wpdb, $current_user, "foo")) die();

$ldap_users = array();

$conn = ldap_connect("ldap://000.000.000.000:123");
$username = "";
$pass = "";
$user = $username."@com.com";
ldap_set_option($conn,LDAP_OPT_PROTOCOL_VERSION,3);
ldap_set_option($conn,LDAP_OPT_REFERRALS,0);
if(@ldap_bind($conn,$user,$pass)){
	$filter = "(&(objectCategory=person)(samaccountname=*)(useraccountcontrol=*))";
	$fields = array("cn","dn","samaccountname","useraccountcontrol");
	$sr = ldap_search($conn,"DC=com,DC=com",$filter,$fields);
	$entries = ldap_get_entries($conn, $sr);
	for ($x=0;$x<$entries['count'];$x++) {
		$code=$entries[$x]['useraccountcontrol'][0];
		if($code=="66050" || $code=="66082" || $code=="514") continue;
		$ldap_users[mb_strtolower($entries[$x]['samaccountname'][0])] = ucwords(mb_strtolower($entries[$x]['cn'][0]));
	}
}

function allowUser($wpdb, $current_user, $category = null) {
	$allow = false;
	$roles = $current_user->roles;
	if($roles[0] == "administrator") {
		$allow = true;
	}
	if(!$allow && $category != null){
		$tmp = $wpdb->get_results($wpdb->prepare("SELECT * FROM control WHERE encargo = %s", $category));
		$tmp = json_decode(json_encode($tmp), True);
		foreach ($tmp as $adm) {
			if(mb_strtoupper($adm['alias']) == mb_strtoupper($current_user->user_login)) {
				$allow = true;
				break;
			}
		}
	}
	return $allow;
}

/**/$list_jefes = $wpdb->get_results($wpdb->prepare("SELECT * FROM t WHERE c = true AND c = true"));
$list_jefes = json_decode(json_encode($list_jefes),true);
/**/$list_trabajadores = $wpdb->get_results($wpdb->prepare("SELECT * FROM t WHERE c = false AND c = true"));
$list_trabajadores = json_decode(json_encode($list_trabajadores),true);

$ldap_users_free = $ldap_users;
foreach ($list_jefes as $item) {
	unset($ldap_users_free[$item['foo']]);
}
foreach ($list_trabajadores as $item) {
	unset($ldap_users_free[$item['foo']]);
}

$get_idarea = 0;
if(isset($_GET['idarea'])){
	$get_idarea = $_GET['idarea'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<link href="https://fonts.googleapis.com/css?family=Roboto&display=swap" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css?family=Roboto+Slab&display=swap" rel="stylesheet">

	<script src="../../res/jquery.min.js"></script>

	<link rel="stylesheet" href="../../res/loading.css">
	<script src="../../res/loading.js"></script>

	<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
	<style>
		* {
			box-sizing: border-box;
		}
		body {
			margin: 20px;
			font-family: 'Roboto', sans-serif;
		}
		.rem-title {
			margin: 25px 0 0 40px;
			font-size: 18px;
			font-weight: bold;
		}
		.rem-areas, .area-edit, .ldap-usuarios {
			padding: 15px 30px;
			display: none;
		}
		.rem-areas .area-single {
			padding: 15px;
			margin: 5px;
			border: 1px solid #A8A8A8FF;
			border-radius: 3px;
			font-size: 14px;
			max-width: 1000px;
		}
		.rem-areas .area-single:hover {
			background: linear-gradient(0deg, rgba(214,214,214,1) 0%, rgba(244,244,244,1) 28%, rgba(255,255,255,1) 100%);
			cursor: pointer;
		}
		.area-edit .area-title {
			padding: 5px 10px;
			margin: 5px;
			border-radius: 5px;
			background: #2D2D2DFF;
			color: white;
			font-size: 14px;
			display: inline-block;
		}
		.arrow-back {
			padding-bottom: 15px;
			margin: 5px;
		}
		.arrow-back i {
			background: #ff954a;
			color: white;
			font-size: 30px;
			cursor: pointer;
			padding: 5px;
			border-radius: 3px;
		}
		.area-jefe table, .area-trabajador table {
			max-width: 800px;
			width: 100%;
			border-collapse: collapse;
			margin: 20px 0 0 5px;
		}
		.area-jefe th, .area-trabajador th {
			text-align: left;
			background: lightgray;
			font-size: 14px;
			padding: 5px;
		}
		.area-jefe tr, .area-trabajador tr {
			border: 1px solid lightgray;
		}
		.area-jefe td:last-child, .area-trabajador td:last-child {
			text-align: right;
		}
		.area-jefe td, .area-trabajador td {
			padding: 5px;
		}
		button {
			cursor: pointer;
		}
		.big-button {
			padding: 5px 10px;
			margin: 0 5px;
		}
		.ldap-usuarios {
			max-width: 800px;
			width: 100%;
		}
		.ldap-usuarios div:first-child {
			display: flex;
		}
		.ldap-usuarios input {
			flex: 1;
		}
		.ldap-usuarios table {
			width: 100%;
			border-collapse: collapse;
			margin: 20px 0;
		}
		.ldap-usuarios tr {
			border: 1px solid lightgray;
		}
		.ldap-usuarios tbody tr:hover {
			box-shadow: 0 0 3px lightgray;
		}
		.ldap-usuarios th {
			padding: 5px;
			font-size: 14px;
			font-weight: normal;
			text-align: left;
			background: #4c4c4c;
			color: white;
		}
		.ldap-usuarios td {
			padding: 5px;
			font-size: 15px;
		}
		.ldap-usuarios td:last-child {
			text-align: right;
		}
	</style>
</head>
<body>
	<?php include('./res/loading.php'); ?>
	<div class="rem-title">Administrar Sistema de Tareas</div>
	<div class="rem-areas">
		<?php
		$list_areas = $wpdb->get_results($wpdb->prepare("SELECT * FROM t"));
		$list_areas = json_decode(json_encode($list_areas), True);
		?>
		<?php foreach ($list_areas as $area): ?>
			<div class="area-single" onclick="administrar_area(this)" id="<?= $area['foo'] ?>">
				<?= $area['foo'] ?>&nbsp;&nbsp;&#40;<?= $area['foo'] ?>&#41;
			</div>
		<?php endforeach ?>
	</div>
	<div class="area-edit">
		<div class="arrow-back"><i class="material-icons" onclick="go_back(true)">arrow_back</i></div>
		<div class="area-title"></div>
		<div class="area-usuarios">
			<div class="area-jefe">
				<table id="tb_jefe">
					<thead>
						<tr>
							<th>Jefe</th>
							<th></th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>
							</td>
							<td><button class="big-button" onclick="cambiar_jefe()">CAMBIAR JEFE</button></td>
						</tr>
					</tbody>
				</table>	
			</div>
			<div class="area-trabajador">
				<table id="tb_trabajadores">
					<thead>
						<tr>
							<th>Trabajadores</th>
							<th></th>
						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
				<div style="padding: 10px;"><button class="big-button" onclick="agregar_trabajador()">+ AGREGAR TRABAJADOR</button></div>
			</div>
		</div>
	</div>
	<div class="ldap-usuarios">
		<div>
			<input type="text" placeholder="Buscar..." id="ldap_buscar" onkeyup="search_table('ldap_buscar','ldap_tabla')">
			<button class="big-button" onclick="go_back(false)">CANCELAR</button>
		</div>
		<table id="ldap_tabla">
			<thead>
				<tr>
					<th>Nombre</th>
					<th>Usuario</th>
					<th></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($ldap_users_free as $key => $value): ?>
					<tr>
						<td><?= $value ?></td>
						<td><?= $key ?></td>
						<td><button onclick="seleccionar_usuario('<?= $key ?>')">+</button></td>
					</tr>
				<?php endforeach ?>
			</tbody>
		</table>
	</div>
	<script>
		var current_area_id = 0;
		var ldap_users = <?= json_encode($ldap_users) ?>;
		var list_jefes = <?= json_encode($list_jefes) ?>;
		var list_trabajadores = <?= json_encode($list_trabajadores) ?>;
		var current_function = 0;
		var get_idarea = <?= $get_idarea ?>;

		$(document).ready(function(){
			if(get_idarea != 0){
				document.getElementById(get_idarea).click();
			}else{
				document.getElementsByClassName("rem-areas")[0].style.display = 'block';
			}
		});

		function administrar_area(e) {
			document.getElementsByClassName("area-title")[0].innerHTML = e.innerHTML;
			document.getElementsByClassName("rem-areas")[0].style.display = 'none';
			document.getElementsByClassName("area-edit")[0].style.display = 'block';
			current_area_id = e.id;
			fill_usuarios(current_area_id);
		}

		function fill_usuarios(idarea) {
			var area_jefe = null;
			for(var i = 0; i < list_jefes.length; i++){
				if (list_jefes[i].foo == idarea) {
					area_jefe = list_jefes[i].foo;
				}
			}
			if(area_jefe == null) document.getElementById("tb_jefe").tBodies[0].rows[0].cells[0].innerHTML = "VACIO";
			else document.getElementById("tb_jefe").tBodies[0].rows[0].cells[0].innerHTML = ldap_users[area_jefe];

			var area_trabajadores = [];
			var tc = 0;
			for(var i = 0; i < list_trabajadores.length; i++){
				if (list_trabajadores[i].foo == idarea) {
					area_trabajadores[tc] = list_trabajadores[i];
					tc++;
				}
			}
			if(area_trabajadores.length != 0) {
				for (var i = 0; i < area_trabajadores.length; i++) {
					var table = document.getElementById("tb_trabajadores").tBodies[0];
					var row = table.insertRow();
					var cell1 = row.insertCell();
					var cell2 = row.insertCell();
					var trabajador = document.createTextNode(ldap_users[area_trabajadores[i].foo]);
					cell1.appendChild(trabajador);
					var button = document.createElement("button");
					button.innerHTML = "Eliminar";
					button.dataset.id_areaxusuario = area_trabajadores[i].foo;
					button.setAttribute("onclick", "eliminar_trabajador(this)");
					cell2.appendChild(button);
				}
			}
		}

		function seleccionar_usuario(usr) {
			loading(true);
			if (current_function == 1) {
				var formData = new FormData();
				formData.append("area_cambiar_jefe",true);
				formData.append("id_area",current_area_id);
				formData.append("id_user",usr);
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
						window.location.href = "http://www.psi.gob.pe/intranet/index.php/administrador-tarea-remota/?idarea="+current_area_id;
					}
				});
			}else if (current_function == 2) {
				var formData = new FormData();
				formData.append("area_agregar_trabajador",true);
				formData.append("id_area",current_area_id);
				formData.append("id_user",usr);
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
						window.location.href = "http://www.psi.gob.pe/intranet/index.php/administrador-tarea-remota/?idarea="+current_area_id;
					}
				});
			}
		}

		function cambiar_jefe() {
			document.getElementsByClassName("area-edit")[0].style.display = 'none';
			document.getElementsByClassName("ldap-usuarios")[0].style.display = 'block';
			current_function = 1;
		}

		function agregar_trabajador() {
			document.getElementsByClassName("area-edit")[0].style.display = 'none';
			document.getElementsByClassName("ldap-usuarios")[0].style.display = 'block';
			current_function = 2;
		}

		function eliminar_trabajador(e) {
			var id_axu = e.dataset.id_areaxusuario;
			loading(true);
			var formData = new FormData();
			formData.append("area_eliminar_trabajador",true);
			formData.append("id_axu",id_axu);
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
					window.location.href = "http://www.psi.gob.pe/intranet/index.php/administrador-tarea-remota/?idarea="+current_area_id;
				}
			});
		}

		function search_table(myinput,mytable){
			var str_i = myinput;
			var str_t = mytable;
			var input, filter, found, table, tr, td, i, j;
			input = document.getElementById(str_i);
			aux = input.value;
			filter = aux.trim().split(" ");
			table = document.getElementById(str_t);
			tr = table.getElementsByTagName("tr");
			for(i = 1; i < tr.length; i++){
				td = tr[i].getElementsByTagName("td");
				for(j = 0; j < td.length-1; j++){
					found = words_in_string(td[j].innerHTML, filter);
					if(found) break;
				}
				if(found){
					tr[i].style.display = "";
					found = false;
				}else{
					tr[i].style.display = "none";
				}
			}
		}
		function raw_string(str) {
			var str_norm = str.normalize('NFD').replace(/[\u0300-\u036f]/g, "");
			return str_norm.toUpperCase();
		}
		function words_in_string(string, keywords) {
			for(var i = 0; i < keywords.length; i++){
				if(!raw_string(string).includes(raw_string(keywords[i]))) return false;
			}
			return true;
		}

		function go_back(w) {
			if (w) {
				document.getElementsByClassName("rem-areas")[0].style.display = 'block';
				document.getElementsByClassName("area-edit")[0].style.display = 'none';
				document.getElementById("tb_trabajadores").tBodies[0].innerHTML = "";
			}
			else {
				document.getElementsByClassName("area-edit")[0].style.display = 'block';
				document.getElementsByClassName("ldap-usuarios")[0].style.display = 'none';
			}
		}
	</script>
</body>
</html>