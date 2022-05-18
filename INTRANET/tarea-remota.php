<?php
/*
Template Name: tarea-remota
*/
if(!is_user_logged_in()){
	auth_redirect();
}
date_default_timezone_set('America/Bogota');
global $wpdb;
global $current_user; wp_get_current_user();

$id_axu = 0;
$mi_area = 0;
$esjefe = false;
$administrador_dominio = false;

if(!usuario_remoto($wpdb, $current_user,$mi_area,$esjefe,$id_axu,$administrador_dominio)) die();

$gototarea = 0;
if(isset($_GET['gototarea'])) {
	$gototarea = $_GET['gototarea'];
}

$search_responsable = "";
$search_estado = "";
$search_inicio = "";
$search_termino = "";

$mis_tareas_query = "SELECT t.id, t.id, t.n, t.d, t.f, t.p, t.f, t.f, DATE_ADD(f, INTERVAL p DAY) as f, a.id, a.id FROM tarea t";
$mis_actividades_query = "SELECT a.id, a.id, a.d, a.f FROM a INNER JOIN t ON a.id = t.id";
$mis_adjuntos_query = "SELECT a.id, a.id, a.n, a.u FROM a INNER JOIN a ON a.id = a.id INNER JOIN t ON a.id = t.id";
$default_query = " INNER JOIN a ON t.id = a.id WHERE ";
$query_args = array();

if($esjefe) {
	$default_query = $default_query."a.id = %d";
	array_push($query_args,$mi_area);
}
else {
	$default_query = $default_query."t.id = %d";
	array_push($query_args,$id_axu);
}

if(isset($_GET['responsable']) && $esjefe) {
	$search_responsable = $_GET['responsable'];
	$default_query = $default_query." AND t.id = %d";
	array_push($query_args,$search_responsable);
}
if(isset($_GET['estado'])) {
	$search_estado = $_GET['estado'];
}
if(isset($_GET['inicio'])) {
	$search_inicio = $_GET['inicio'];
	$default_query = $default_query." AND fi >= %s";
	array_push($query_args,$search_inicio." 00:00:00");
}
if(isset($_GET['termino'])) {
	$search_termino = $_GET['termino'];
	$default_query = $default_query." AND DATE_ADD(fi, INTERVAL pl DAY) <= %s";
	array_push($query_args,$search_termino." 23:59:59");
}

$default_query = $default_query." ORDER BY t.fi DESC";

$ldap_users = array();

$conn = ldap_connect("ldap://000.000.000.000:123");
$username = "";
$pass = "";
$user = $username."@upc.com";
ldap_set_option($conn,LDAP_OPT_PROTOCOL_VERSION,3);
ldap_set_option($conn,LDAP_OPT_REFERRALS,0);
if(@ldap_bind($conn,$user,$pass)){
	$filter = "(&(objectCategory=person)(samaccountname=*)(useraccountcontrol=*))";
	$fields = array("cn","dn","samaccountname","useraccountcontrol");
	$sr = ldap_search($conn,"DC=UPC,DC=COM",$filter,$fields);
	$entries = ldap_get_entries($conn, $sr);
	for ($x=0;$x<$entries['count'];$x++) {
		$code=$entries[$x]['useraccountcontrol'][0];
		if($code=="66050" || $code=="66082" || $code=="514") continue;
		$ldap_users[mb_strtolower($entries[$x]['samaccountname'][0])] = ucwords(mb_strtolower($entries[$x]['cn'][0]));
	}
}

$mis_trabajadores = array();
$mis_trabajadores_x = array();

$mis_tareas = $wpdb->get_results($wpdb->prepare($mis_tareas_query.$default_query,$query_args));
$mis_tareas = json_decode(json_encode($mis_tareas), True);
$mis_actividades = $wpdb->get_results($wpdb->prepare($mis_actividades_query.$default_query,$query_args));
$mis_actividades = json_decode(json_encode($mis_actividades), True);
$mis_adjuntos = $wpdb->get_results($wpdb->prepare($mis_adjuntos_query.$default_query,$query_args));
$mis_adjuntos = json_decode(json_encode($mis_adjuntos), True);

if($esjefe){
	$tmp = $wpdb->get_results($wpdb->prepare("SELECT * FROM a WHERE i = %d AND e = false AND a = true",$mi_area));
	$mis_trabajadores = json_decode(json_encode($tmp), True);
	$tmpx = $wpdb->get_results($wpdb->prepare("SELECT * FROM a WHERE i = %d AND e = false AND a = false",$mi_area));
	$mis_trabajadores_x = json_decode(json_encode($tmpx), True);
}

foreach($mis_tareas as $key => $item){
	$tmp_estado = fecha_estado($item['foo'],$item['foo'],$item['foo'],$item['foo']);
	if($search_estado != "" && $tmp_estado != $search_estado){
		unset($mis_tareas[$key]);
	}else{
		$mis_tareas[$key]['foo'] = $tmp_estado;
		$mis_tareas[$key]['foo'] = $ldap_users[mb_strtolower($mis_tareas[$key]['foo'])];
	}
}
$mis_tareas = array_values($mis_tareas);

function usuario_remoto($wpdb,$current_user,&$mi_area,&$esjefe,&$id_axu,&$administrador_dominio) {
	$allow = false;
	$cu = $current_user->user_login;
	if(mb_strtoupper($cu) == "ADMINISTRADOR") {
		$administrador_dominio = true;
		/*$allow = true;
		$mi_area = 0;
		$id_axu = 0;
		$esjefe = false;*/
	}
	$tmp = $wpdb->get_results($wpdb->prepare("SELECT * FROM a WHERE i = %s AND a = true",$current_user->user_login));
	$tmp = json_decode(json_encode($tmp), True);
	foreach ($tmp as $adm) {
		$allow = true;
		$mi_area = $adm['foo'];
		$id_axu = $adm['foo'];
		if($adm['foo'] == 1) $esjefe = true;
		break;
	}
	return $allow;
}

function fecha_drop_horas($dt) {
	if ($dt == "") return "--";
	$datetime = new DateTime($dt);
	return $datetime->format('d/m/Y');
}

function fecha_estado($fi,$pl,$ft,$fv){
	
	$origin = new DateTime('now');
	$target = new DateTime($fi);
	$target->modify('+'.$pl.' day');
	$interval = $origin->diff($target);
	$dif = $interval->format('%R%a');

	if ($fv != "") return "vr";
	else if ($ft != "") return "tr";
	else if ($pl == 0) return "ep";
	else if ($dif<0) return "vn";
	else return "ep";
}

$asistencia_res = $wpdb->get_results($wpdb->prepare("SELECT * FROM a WHERE date(e) = CURDATE() AND u = %s", $current_user->user_login));
$asistencia_res = json_decode(json_encode($asistencia_res), True);

$asistencia_in = false;
$asistencia_out = true;
if(!$asistencia_res) {
	$wpdb->query(
		$wpdb->prepare(
			"INSERT INTO a (u) VALUES (%s)", $current_user->user_login
		)
	);
	$asistencia_in = true;
}else if($asistencia_res[0]['foo']) {
	$asistencia_out = false;
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
		.tarea-titulo {
			font-family: 'Roboto Slab', serif;
			font-size: 20px;
			color: #373737;
			text-align: center;
			margin: 10px 0px;
		}
		.cnf-modal,
		.tarea-lista,
		.tarea-edit,
		.tarea-ver,
		.adjunto-edit,
		.actividad-edit {
			display: none;
		}
		.tarea-ver {
			width: 100%;
			max-width: 1000px;
		}
		.tarea-search div:nth-child(1) {
			font-size: 14px;
			margin: 10px 0;
		}
		.tarea-search .responsable,
		.tarea-search .estado,
		.tarea-search .fecha {
			display: flex;
			align-items: center;
			margin: 10px 0;
		}
		.tarea-search option.ex {
			background: #edeee6;
			color: #535353;
		}
		.tarea-edit .tarea-form > div,
		.tarea-datos > div,
		.adjunto-edit .adjunto-form > div,
		.actividad-edit .actividad-form > div {
			display: flex;
			align-items: center;
			margin: 10px 0 15px 0;
		}
		.tarea-search label,
		.tarea-edit label,
		.tarea-datos label,
		.adjunto-edit label,
		.actividad-edit label {
			font-size: 13px;
			font-weight: bold;
		}
		.tarea-search .responsable label,
		.tarea-search .estado label,
		.tarea-edit label,
		.tarea-datos label,
		.adjunto-edit label,
		.actividad-edit label {
			display: inline-block;
			min-width: 80px;
		}
		.tarea-search select,
		.tarea-edit select {
			padding: 5px;
		}
		.tarea-search input	{
			margin: 0 10px;
			padding: 3px 5px;
		}
		.tarea-table {
			margin: 10px 0;
		}
		.tarea-table table {
			font-size: 15px;
		}
		.tarea-table .enproceso,
		.tarea-table .vencido,
		.tarea-table .terminado,
		.tarea-table .verificado {
			padding: 5px;
			border-radius: 3px;
		}
		.enproceso {
			background: #d4d457 !important;
			color: #a06a00;
		}
		.vencido {
			background: #de4b4b !important;
			color: white;
		}
		.terminado {
			background: #2F49A2 !important;
			color: white;
		}
		.verificado {
			background: #478c47 !important;
			color: white;
		}
		.tarea-table table {
			border-collapse: collapse;
			width: 100%;
			max-width: 2500px;
		}
		.tarea-table tbody tr:hover {
			box-shadow: 0 0 2px 1px #88888880;
			border-color:transparent;
		}
		.tarea-table td {
			padding: 10px;
			border: 1px solid lightgray;
		}
		.tarea-table thead tr{
			background: #565656;
			color: white;
			font-size: 14px;
			text-align: left;
		}
		.tarea-table th {
			font-weight: normal;
			padding: 5px 10px;
		}
		.tarea-table th:first-child {
			border-radius: 4px 0 0 0;
		}
		.tarea-table th:last-child {
			border-radius: 0 4px 0 0;
		}
		.tarea-table td,
		.tarea-table th {
			text-align: center;
		}
		.tarea-table td:first-child,
		.tarea-table th:first-child {
			text-align: left;
			max-width: 500px;
		}
		.tarea-table td i {
			font-size: 28px;
			border-radius: 5px;
			transition: 0.1s;
			padding: 5px;
		}
		.tarea-table td:last-child:hover {
			background: #565656;
			color: white;
			cursor: pointer;
		}
		.tarea-info div:first-child {
			font-size: 14px;
			font-weight: bold;
		}
		.tarea-info div:last-child {
			text-align: justify;
		}
		.tarea-result-empty,
		.act-result-empty {
			font-size: 48px;
			color: lightgray;
			margin-top: 100px;
			width: 100%;
			text-align: center;
		}
		.adj-result-empty {
			font-size: 24px;
			color: lightgray;
			width: 100%;
			text-align: center;
		}
		.act-result-empty {
			display: none;
		}
		.bigger-button {
			padding: 10px 20px;
			margin: 20px 5px 10px 5px;
			font-size: 1.0em;
		}

		.tarea-edit,
		.adjunto-edit,
		.actividad-edit,
		.cnf-modal {
			position: fixed; /* Stay in place */
			z-index: 1; /* Sit on top */
			padding-top: 100px; /* Location of the box */
			left: 0;
			top: 0;
			width: 100%; /* Full width */
			height: 100%; /* Full height */
			overflow: auto; /* Enable scroll if needed */
			background-color: rgb(0,0,0); /* Fallback color */
			background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
		}
		.tarea-edit .tarea-form,
		.adjunto-edit .adjunto-form,
		.actividad-edit .actividad-form,
		.cnf-modal .cnf-content {
			background-color: #fefefe;
			margin: auto;
			padding: 10px 20px;
			border: 1px solid #888;
			width: 90%;
			max-width: 1000px;
			border-radius: 5px;
		}
		.form-titulo input,
		.form-detalle textarea {
			flex: 1;
			padding: 5px;
		}
		.tarea-edit .form-detalle textarea {
			resize: none;
		}
		.tarea-edit .form-tiempo input {
			margin-right: 20px;
			padding: 3px 5px;
		}
		.form-archivo input[type="file"] {
			border: 1px solid gray;
			border-radius: 1px;
			padding: 4px;
		}
		.close,
		.adjunto-close,
		.actividad-close {
			color: #aaaaaa;
			float: right;
			font-size: 28px;
			font-weight: bold;
			margin-left: 20px;
		}

		.close:hover,
		.close:focus,
		.adjunto-close:hover,
		.adjunto-close:focus,
		.actividad-close:hover,
		.actividad-close:focus {
			color: #000;
			text-decoration: none;
			cursor: pointer;
		}
		
		.cnf-form {
			display: none;
		}
		.cnf-form div:first-child {
			text-align: center;
			font-size: 1.2em;
			margin: 50px 0 10px 0;
			color: #565656;
			display: flex;
			align-items: center;
			justify-content: center;
		}
		.cnf-form div:nth-child(2) {
			text-align: center;
			margin: 0 auto 10px auto;
			color: slategray;
			max-width: 300px;
			font-size: 0.95em;
		}
		.cnf-form div:last-child {
			display: flex;
			justify-content: center;
			margin-bottom: 30px;
		}
		.cnf-icon {
			margin-right: 10px;
			font-size: 32px;
			color: darkorange;
		}

		.tarea-datos {
			width: 100%;
			max-width: 1000px;
			margin: 30px 0;
		}
		.tarea-datos > div span,
		.tarea-datos > div div {
			background: #f1f1ee;
			border: 1px solid gray;
			border-radius: 3px;
			padding: 5px 7px;
			margin-right: 10px;
			font-size: 14px;
		}
		.tarea-datos > div div {
			flex: 1;
		}
		.tarea-subtitulo {
			font-weight: bold;
		}
		.dotted-line {
			border-top: 5px dotted #eaeaea;
		}
		.tarea-actividades {
			padding: 10px;
			width: 100%;
			max-width: 1000px;
			color: #565656;
		}
		.tarea-actividades .act-single {
			margin-bottom: 15px;
		}
		.tarea-actividades .act-fecha {
			padding: 7px 10px;
			border-radius: 5px 5px 0 0;
			color: white;
			background: #565656;
			font-size: 15px;
			display: inline-block;
			font-family: monospace;
		}
		.tarea-actividades .act-detalle {
			padding: 15px 20px;
			border-radius: 0 5px 5px 5px;
			border: 2px solid #565656;
			font-size: 16px;
		}
		.act-detalle .act-desc {
			text-align: justify;
			margin-right: 100px;
		}
		.act-btn-eliminar {
			float: right;
			margin-top: -8px;
			margin-right: 0px;
			display: flex;
			align-items: center;
		}
		.act-subtitulo {
			font-weight: bold;
			font-size: 14px;
			margin-top: 10px;
			margin-left: 15px;
		}
		.act-separador {
			border-top: 5px double lightgray;
			margin-top: 10px;
		}
		.tabla-adjuntos {
			padding: 10px;
		}
		.tabla-adjuntos table {
			width: 100%;
			border-collapse: collapse;
		}
		.tabla-adjuntos tr {
			border: 3px solid #fafafa;
			background: #dfdfdc;
			border-radius: 8px;
		}
		.tabla-adjuntos td:first-child {
			border-radius: 8px 0 0 8px;
			font-family: 'Roboto Slab', serif;
			padding: 5px 15px;
		}
		.tabla-adjuntos td:last-child {
			text-align: right;
			border-radius: 0 8px 8px 0;
			padding: 7px;
			width: 1%;
			white-space: nowrap;
			border: 1px solid #cacac3;
		}
		.tabla-adjuntos a {
			text-decoration: none;
		}
		.tabla-adjuntos a i {
			padding: 8px;
			color: #0069D4;
			transition: 0.1s;
			border-radius: 50%;
		}
		.tabla-adjuntos a i:hover {
			color: white;
			background: #0069D4;
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
		.btn-green {
			color: white;
			background: #4cb40a;
		}
		.btn-green:active {
			background: #5db426;
		}
		.btn-black {
			color: white;
			background: #565656;
		}
		.btn-black:active {
			background: #696969;
		}
		.btn-gray {
			color: #565656;
			background: #efefef;
			border: 1px solid #e0e0e0;
		}
		.btn-gray:active {
			background: #dddddd;
		}
		.btn-gray:hover {
			box-shadow: 0 1px 2px 0 rgb(127 127 127 / 45%), 0 1px 3px 1px rgb(127 127 127 / 30%);
		}
		.btn-orange {
			color: white;
			background: #e8ac1a;
		}
		.btn-orange:active {
			background: #e8b73e;
		}
		.btn-adjunto {
			display: flex;
			align-items: center;
			margin-left: 13px;
		}
		button:disabled,
		button[disabled],
		button:disabled:hover,
		button[disabled]:hover{
			border: 1px solid #999999;
			background-color: #cccccc;
			color: #666666;
			cursor: default;
			box-shadow: none;
		}
		.border-red {
			outline: 1px solid red;
		}
		#welcome_txt {
			margin: 40px 20px;
			text-align: center;
		}
	</style>
</head>
<body>
	<?php include('./res/loading.php'); ?>
	<div class="tarea-lista">
		<div class="tarea-titulo">Lista de Tareas Trabajo Remoto</div>
		<div class="tarea-search">
			<div>BUSCAR POR:</div>
			<?php if ($esjefe): ?>
				<div class="responsable">
					<label>Responsable</label>
					<select id="search_responsable">
						<option value = 0>TODOS</option>
						<?php foreach ($mis_trabajadores as $item): ?>
							<option value="<?= $item['foo'] ?>" <?= $search_responsable==$item['foo']?'selected':'' ?>><?= $ldap_users[$item['foo']] ?></option>
						<?php endforeach ?>
						<?php foreach ($mis_trabajadores_x as $item): ?>
							<option class="ex" value="<?= $item['foo'] ?>" <?= $search_responsable==$item['foo']?'selected':'' ?>>-&nbsp;<?= $ldap_users[$item['foo']] ?></option>
						<?php endforeach ?>
					</select>
				</div>
			<?php endif ?>
			<div class="estado">
				<label>Estado</label>
				<select id="search_estado">
					<option value = 0>TODOS</option>
					<option value = "ep" <?= $search_estado=='ep'?'selected':'' ?>>En Proceso</option>
					<option value = "vn" <?= $search_estado=='vn'?'selected':'' ?>>Vencido</option>
					<option value = "tr" <?= $search_estado=='tr'?'selected':'' ?>>Terminado</option>
					<option value = "vr" <?= $search_estado=='vr'?'selected':'' ?>>Verificado</option>
				</select>
			</div>
			<div class="fecha">
				<label>Fecha inicio</label>
				<input type="date" id="search_fecha_inicio" value="<?= $search_inicio ?>">
				<span>-&nbsp;&nbsp;</span>
				<label>Fecha termino</label>
				<input type="date" id="search_fecha_fin" value="<?= $search_termino ?>">
				<input type="checkbox" style="margin-right: 0;" id="search_alldates" <?php if($search_inicio == "" && $search_termino == "") echo 'checked'; ?>>
				<label for="search_alldates" style="padding-left: 5px;">Todas las fechas</label>
			</div>
		</div>
		<div style="padding: 10px 0;"></div>
		<div class="dotted-line"></div>
		<div class="tarea-table">
			<button class="bigger-button btn-blue" onclick="edit_tarea()">+ Nueva Tarea</button>
			<?php if(count($mis_tareas) == 0): ?>
				<?php if ($asistencia_out): ?>
					<button class="bigger-button btn-orange" style="float: right;" onclick="confirmation_window(4)">Marcar Salida</button>
				<?php endif ?>
			<div class="tarea-result-empty">NO HAY TAREAS PARA MOSTRAR</div>
			<?php else: ?>
			<button class="bigger-button btn-gray" style="float: right;" onclick="generar_reporte()">Exportar</button>
				<?php if ($asistencia_out): ?>
					<button class="bigger-button btn-orange" style="float: right;" onclick="confirmation_window(4)">Marcar Salida</button>
				<?php endif ?>
			<table>
				<thead>
					<tr>
						<th>Tarea</th>
						<?php if($esjefe): ?><th>Responsable</th><?php endif ?>
						<th>Asignada</th>
						<th>Plazo</th>
						<th>Fin Planeado</th>
						<th>Fin Real</th>
						<th>Estado</th>
						<th>Ver</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach($mis_tareas as $item): ?>
					<?php //if($search_estado != "" && $search_estado != $item['foo']) continue; ?>
						<tr>
							<td>
								<div class="tarea-info">
									<div><?= $item['foo'] ?></div>
									<div><?= $item['foo'] ?></div>
								</div>
							</td>
							<?php if($esjefe): ?><td><?= $item['foo'] ?></td><?php endif ?>
							<td><?= fecha_drop_horas($item['foo']) ?></td>
							<td><?= $item['foo']==0?"--":$item['foo'] ?></td>
							<td><?= $item['foo']==0?"--":fecha_drop_horas($item['foo']) ?></td>
							<td><?= fecha_drop_horas($item['foo']) ?></td>
							<td>
								<?php $res_estado = $item['foo']; ?>
								<span class="<?php switch ($res_estado) {
											case "ep": echo "enproceso"; break;
											case "vn": echo "vencido"; break;
											case "tr": echo "terminado"; break;
											case "vr": echo "verificado"; break;
										} ?>">
									<?php
										switch ($res_estado) {
											case "ep": echo "En&nbsp;Proceso"; break;
											case "vn": echo "Vencido"; break;
											case "tr": echo "Terminado"; break;
											case "vr": echo "Verificado"; break;
										}
									?>
								</span>
							</td>
							<td onclick="ver_tarea(<?= $item['foo'] ?>)" id="<?= $item['foo'] ?>"><i class="material-icons">search</i></td>
						</tr>
					<?php endforeach ?>
				</tbody>
			</table>
			<?php endif; ?>
		</div>
	</div>
	<div class="tarea-edit">
		<div class="tarea-form">
			<span class="close">&times;</span>
			<?php if ($esjefe): ?>
				<div class="form-responsable">
					<label>Responsable</label>
					<select id="tarea_form_responsable">
						<option value = 0>&lt;seleccionar&gt;</option>
						<?php foreach ($mis_trabajadores as $item): ?>
							<option value="<?= $item['foo'] ?>"><?= $ldap_users[$item['foo']] ?></option>
						<?php endforeach ?>
					</select>
				</div>
			<?php endif; ?>
			<div class="form-tiempo">
				<label>Fecha inicio</label>
				<input id="tarea_form_inicio" type="date">
				<label>Plazo (dias)</label>
				<input id="tarea_form_plazo" type="number" step="1" min="0" autocomplete="off">
				<input id="tarea_form_plazo_indef" type="checkbox" style="margin-right: 0;">
				<label for="tarea_form_plazo_indef" style="padding: 0 6px;">Sin Plazo</label>
			</div>
			<div class="form-titulo">
				<label>Tarea</label>
				<input id="tarea_form_nombre" type="text" autocomplete="off">
			</div>
			<div class="form-detalle" style="align-items: start;">
				<label>Detalle</label>
				<textarea id="tarea_form_detalle" rows="8" autocomplete="off"></textarea>
			</div>
			<div id="btn_guardar_tarea">
				<button class="bigger-button btn-blue" onclick="guardar_tarea()">Guardar</button>
			</div>
		</div>
	</div>
	<div class="tarea-ver">
		<div class="tarea-titulo">Tarea de Trabajo Remoto</div>
		<button class="btn-blue" style="float: right; margin: 20px 10px 0 0; display: none;">Modificar Tarea</button>
		<div class="tarea-datos">
			<?php if($esjefe): ?>
				<div class="dato-responsable">
					<label for="">Responsable</label>
					<span id="ver_tarea_responsable"></span>
				</div>
			<?php endif; ?>
			<div class="dato-tiempo">
				<label for="">Fecha inicio</label>
				<span id="ver_tarea_inicio"></span>
				<label>Plazo (dias)</label>
				<span id="ver_tarea_plazo"></span>
				<label style="min-width: 0; margin-right: 10px;">Estado</label>
				<span id="ver_tarea_estado"></span>
			</div>
			<div class="dato-titulo">
				<label for="">Tarea</label>
				<div id="ver_tarea_titulo"></div>
			</div>
			<div class="dato-detalle" style="align-items: start;">
				<label for="">Detalle</label>
				<div id="ver_tarea_detalle"></div>
			</div>
		</div>
		<div style="margin-bottom: 20px;">
			<?php if($esjefe): ?>
			<button class="bigger-button btn-green" id="btn_verificar_tarea" onclick="confirmation_window(2)" disabled>Verificar Tarea</button>
			<?php else: ?>
			<button class="bigger-button btn-green" id="btn_terminar_tarea" onclick="confirmation_window(1)" disabled>Terminar Tarea</button>
			<?php endif; ?>
			<button class="bigger-button btn-black" style="float: right; align-items: center; display: flex;" onclick="go_back()"><i class="material-icons">keyboard_arrow_left</i>Regresar</button>
		</div>
		<div class="dotted-line"></div>
		<div style="padding: 10px 0;"></div>
		<div class="tarea-subtitulo">ACTIVIDADES REGISTRADAS</div>
		<?php if(!$esjefe): ?>
		<div><button class="bigger-button btn-blue" id="btn_nueva_actividad" onclick="edit_actividad()" disabled>+ Nueva Actividad</button></div>
		<?php endif; ?>
		<div class="act-result-empty">NO HAY ACTIVIDADES PARA MOSTRAR</div>
		<div class="tarea-actividades">
			
		</div>
	</div>
	<div class="actividad-edit">
		<div class="actividad-form">
			<span class="actividad-close">&times;</span>
			<div class="form-detalle">
				<label>Detalle de actividad</label>
			</div>
			<div class="form-detalle">
				<textarea id="actividad_form_detalle" rows="8" autocomplete="off"></textarea>
			</div>
			<div>
				<button class="bigger-button btn-blue" onclick="guardar_actividad()">Guardar</button>
			</div>
		</div>
	</div>
	<div class="adjunto-edit">
		<div class="adjunto-form">
			<span class="adjunto-close">&times;</span>
			
			<div class="form-titulo">
				<label>Nombre</label>
				<input type="text" autocomplete="off" id="adjunto_form_nombre">
			</div>
			<div class="form-archivo">
				<label>Archivo</label>
				<input type="file" id="adjunto_form_archivo"><span id="max_30_mb" style="color:gray; font-size: 0.8em; margin: 0 10px;">límite 30 MB</span>
			</div>
			<div>
				<button class="bigger-button btn-blue" onclick="guardar_adjunto()">Guardar</button>
			</div>
		</div>
	</div>
	<div class="cnf-modal">
		<div class="cnf-content" style="max-width:500px;">
			<div id="cnf_terminar" class="cnf-form">
				<div><i class="material-icons cnf-icon">error_outline</i>¿Desea dar por terminada esta tarea?</div>
				<div>Una vez terminada no podra realizar ninguna modificación.</div>
				<div>
					<button class="bigger-button btn-green" onclick="terminar_tarea()">TERMINAR</button>
					<button class="bigger-button btn-gray" onclick="clean_form()">CANCELAR</button>
				</div>
			</div>
			<div id="cnf_verificar" class="cnf-form">
				<div><i class="material-icons cnf-icon">error_outline</i>¿Desea confirmar la verificación de esta tarea?</div>
				<div>Al aceptar se esta dando conformidad de la finalización de esta tarea.</div>
				<div>
					<button class="bigger-button btn-green" onclick="verificar_tarea()">ACEPTAR</button>
					<button class="bigger-button btn-gray" onclick="clean_form()">CANCELAR</button>
				</div>
			</div>
			<div id="cnf_eliminar_actividad" class="cnf-form">
				<div><i class="material-icons cnf-icon">error_outline</i>¿Desea eliminar esta actividad?</div>
				<div>Se eliminará esta actividad y todo su contenido adjunto.</div>
				<div>
					<button class="bigger-button btn-orange" onclick="eliminar_actividad()">ELIMINAR</button>
					<button class="bigger-button btn-gray" onclick="clean_form()">CANCELAR</button>
				</div>
			</div>
			<div id="cnf_marcar_salida" class="cnf-form">
				<div><i class="material-icons cnf-icon">error_outline</i>Registrar Salida</div>
				<div id="clock_out_txt"></div>
				<div>
					<button class="bigger-button btn-orange" onclick="clock_out()">ACEPTAR</button>
					<button class="bigger-button btn-gray" onclick="clean_form()">CANCELAR</button>
				</div>
			</div>
		</div>
	</div>
	<div id="welcome_as" class="cnf-modal">
		<div id="welcome_card" class="cnf-content" style="max-width:500px;">
			<span class="close">&times;</span>
			<div id="welcome_txt"></div>
		</div>
	</div>
	<script>
		var gototarea = <?= $gototarea ?>;
		
		var tarea_form_nombre;
		var tarea_form_detalle;
		var tarea_form_inicio;
		var tarea_form_plazo;
		var tarea_form_plazo_indef;
		<?php if($esjefe): ?>var tarea_form_responsable;<?php endif; ?>
		
		var actividad_form_detalle;
		
		var adjunto_form_nombre;
		var adjunto_form_archivo;
		
		var tarea_lista;
		var tarea_edit;
		var adjunto_edit;
		var actividad_edit;
		var cnf_modal;
		
		var ver_tarea_responsable;
		var ver_tarea_titulo;
		var ver_tarea_detalle;
		var ver_tarea_plazo;
		var ver_tarea_inicio;
		var ver_tarea_estado;
		
		var doc_url = "http://www.psi.gob.pe/intranet/wp-content/uploads/";
		
		var tarea_ver;
		var tarea_actividades;
		var act_result_empty;
		
		var mis_tareas = <?= json_encode($mis_tareas) ?>;
		var mis_actividades = <?= json_encode($mis_actividades) ?>;
		var mis_adjuntos = <?= json_encode($mis_adjuntos) ?>;

		<?php if($esjefe): ?>var btn_verificar_tarea = document.getElementById("btn_verificar_tarea");
		<?php else: ?>var btn_terminar_tarea = document.getElementById("btn_terminar_tarea");
		var btn_nueva_actividad = document.getElementById("btn_nueva_actividad");<?php endif; ?>
		
		var current_idtarea = 0;
		var current_idactividad = 0;
		
		var cnf_terminar;
		var cnf_verificar;
		var cnf_eliminar_actividad;
		var cnf_marcar_salida;
		
		var opciones = { year: '2-digit', month: '2-digit', day: '2-digit' };
		
		var welcome_card;
		var welcome_txt;
		var welcome_as;
		var clock_out_txt;
		var asistencia_in = <?php echo $asistencia_in?"true":"false"; ?>;
		var asistencia_out = <?php echo $asistencia_out?"true":"false"; ?>;
		
		$(document).ready(function(){
			welcome_card = document.getElementById("welcome_card");
			welcome_txt = document.getElementById("welcome_txt");
			welcome_as = document.getElementById("welcome_as");
			clock_out_txt = document.getElementById("clock_out_txt");
			
			if(asistencia_in){
				var today = new Date();
				var welcome_string = "Bienvenido, se ha registrado su ingreso hoy " + today.toLocaleDateString("es-PE") + "<br>a las " + today.toLocaleTimeString("en-PE",{hour12:true, hour:'2-digit', minute:'2-digit'});
				welcome_txt.innerHTML = welcome_string;
				welcome_as.style.display = "block";
			}
			
			tarea_lista = document.getElementsByClassName("tarea-lista")[0];
			tarea_edit = document.getElementsByClassName("tarea-edit")[0];
			adjunto_edit = document.getElementsByClassName("adjunto-edit")[0];
			actividad_edit = document.getElementsByClassName("actividad-edit")[0];
			cnf_modal = document.getElementsByClassName("cnf-modal")[0];
			cnf_terminar = document.getElementById("cnf_terminar");
			cnf_verificar = document.getElementById("cnf_verificar");
			cnf_eliminar_actividad = document.getElementById("cnf_eliminar_actividad");
			cnf_marcar_salida = document.getElementById("cnf_marcar_salida");
			
			ver_tarea_responsable = document.getElementById("ver_tarea_responsable");
			ver_tarea_titulo = document.getElementById("ver_tarea_titulo");
			ver_tarea_detalle = document.getElementById("ver_tarea_detalle");
			ver_tarea_plazo = document.getElementById("ver_tarea_plazo");
			ver_tarea_inicio = document.getElementById("ver_tarea_inicio");
			ver_tarea_estado = document.getElementById("ver_tarea_estado");
			
			tarea_ver = document.getElementsByClassName("tarea-ver")[0];
			tarea_actividades = document.getElementsByClassName("tarea-actividades")[0];
			act_result_empty = document.getElementsByClassName("act-result-empty")[0];
			
			tarea_form_nombre = document.getElementById("tarea_form_nombre");
			tarea_form_detalle = document.getElementById("tarea_form_detalle");
			tarea_form_inicio = document.getElementById("tarea_form_inicio");
			tarea_form_inicio.value = date_today_input();
			tarea_form_plazo = document.getElementById("tarea_form_plazo");
			tarea_form_plazo_indef = document.getElementById("tarea_form_plazo_indef");
			<?php if($esjefe): ?>tarea_form_responsable = document.getElementById("tarea_form_responsable");<?php endif; ?>
			
			actividad_form_detalle = document.getElementById("actividad_form_detalle");
			
			adjunto_form_nombre = document.getElementById("adjunto_form_nombre");
			adjunto_form_archivo = document.getElementById("adjunto_form_archivo");
			
			$("#search_responsable").change(function(){
				buscar_tarea();
			});
			$("#search_estado").change(function(){
				buscar_tarea();
			});
			$("#search_fecha_inicio").change(function(){
				$('#search_alldates').prop('checked', false);
				if(document.getElementById("search_fecha_inicio").value != "") buscar_tarea();
			});
			$("#search_fecha_fin").change(function(){
				$('#search_alldates').prop('checked', false);
				if(document.getElementById("search_fecha_fin").value != "") buscar_tarea();
			});
			$("#search_alldates").click(function(){
				if($(this).is(':checked')){
					document.getElementById("search_fecha_fin").value = "";
					document.getElementById("search_fecha_inicio").value = "";
					buscar_tarea();
				}
			});
			$("#tarea_form_plazo_indef").click(function(){
				if($(this).is(':checked')){
					tarea_form_plazo.value = 0;
					tarea_form_plazo.disabled = true;
				} else {
					tarea_form_plazo.value = "";
					tarea_form_plazo.disabled = false;
				}
			});
			
			window.onclick = function(event) {
				if (event.target == tarea_edit) {
					clean_form();
				}
				if (event.target == adjunto_edit) {
					clean_form();
				}
				if (event.target == actividad_edit) {
					clean_form();
				}
				if (event.target == cnf_modal) {
					clean_form();
				}
				if (event.target == welcome_as) {
					welcome_as.style.display = "none";
				}
			}
			document.getElementsByClassName("close")[0].onclick = function() {
				clean_form();
			}
			document.getElementsByClassName("close")[1].onclick = function() {
				welcome_as.style.display = "none";
			}
			document.getElementsByClassName("actividad-close")[0].onclick = function() {
				clean_form();
			}
			document.getElementsByClassName("adjunto-close")[0].onclick = function() {
				clean_form();
			}
			if(gototarea == 0) tarea_lista.style.display = "block";
			else document.getElementById(gototarea).click();
		});
		
		function buscar_tarea() {
			var s_url = "http://www.psi.gob.pe/intranet/index.php/tarea-remota/?";
			var s_r = "";
			<?php if($esjefe): ?>s_r = document.getElementById("search_responsable").value;<?php endif; ?>
			if(s_r != 0) s_url = s_url + "responsable=" + s_r;
			var s_e = document.getElementById("search_estado").value;
			if(s_e != 0) s_url = s_url + "&estado=" + s_e;
			var s_i = document.getElementById("search_fecha_inicio").value;
			if(s_i != "") s_url = s_url + "&inicio=" + s_i;
			var s_t = document.getElementById("search_fecha_fin").value;
			if(s_t != "") s_url = s_url + "&termino=" + s_t;
			window.location.href = s_url;
		}

		function edit_tarea() {
			tarea_edit.style.display = 'block';
		}
		function edit_actividad() {
			actividad_edit.style.display = 'block';
		}
		function act_adjuntar(e) {
			adjunto_edit.style.display = 'block';
			current_idactividad = e.target.dataset.idactividad;
		}
		function act_eliminar(e) {
			current_idactividad = e.target.dataset.idactividad;
			confirmation_window(3);
		}
		function confirmation_window(cnf) {
			cnf_modal.style.display = 'block';
			switch (cnf) {
				case 1: cnf_terminar.style.display = 'block'; break;
				case 2: cnf_verificar.style.display = 'block'; break;
				case 3: cnf_eliminar_actividad.style.display = 'block'; break;
				case 4: {
							var today = new Date();
							var co_string = "Se registrará su salida hoy " + today.toLocaleDateString("es-PE") + "<br>a las " + today.toLocaleTimeString("en-PE",{hour12:true, hour:'2-digit', minute:'2-digit'});
							clock_out_txt.innerHTML = co_string;
							cnf_marcar_salida.style.display = 'block'; break;
						}
			}
		}

		function ver_tarea(idtarea) {
			current_idtarea = idtarea;
			tarea_actividades.innerHTML = "";
			var tarea_estado = "";
			for (var i = 0; i < mis_tareas.length; i++) {
				if (mis_tareas[i]['foo'] == idtarea) {
					// TAREA DATA
					tarea_estado = mis_tareas[i]['foo'];
					<?php if($esjefe): ?>ver_tarea_responsable.innerHTML = mis_tareas[i]['foo'];<?php endif; ?>
					ver_tarea_titulo.innerHTML = mis_tareas[i]['foo'];
					ver_tarea_detalle.innerHTML = mis_tareas[i]['foo'];
					ver_tarea_plazo.innerHTML = mis_tareas[i]['foo']==0?"--":mis_tareas[i]['foo'];
					ver_tarea_inicio.innerHTML = new Date(mis_tareas[i]['foo']).toLocaleDateString("es-PE",opciones);
					var value_estado = "";
					var class_estado = "";
					switch (mis_tareas[i]['foo']) {
						case "ep": {
							value_estado = "En Proceso";
							class_estado = "enproceso";
							<?php if($esjefe): ?>btn_verificar_tarea.disabled = true;
							<?php else: ?>btn_terminar_tarea.disabled = false;
							btn_nueva_actividad.disabled = false;<?php endif; ?>
							break;
						}
						case "vn": {
							value_estado = "Vencido";
							class_estado = "vencido";
							<?php if($esjefe): ?>btn_verificar_tarea.disabled = true;
							<?php else: ?>btn_terminar_tarea.disabled = false;
							btn_nueva_actividad.disabled = false;<?php endif; ?>
							break;
						}
						case "tr": {
							value_estado = "Terminado";
							class_estado = "terminado";
							<?php if($esjefe): ?>btn_verificar_tarea.disabled = false;
							<?php else: ?>btn_terminar_tarea.disabled = true;
							btn_nueva_actividad.disabled = true;<?php endif; ?>
							break;
						}
						case "vr": {
							value_estado = "Verificado";
							class_estado = "verificado";
							<?php if($esjefe): ?>btn_verificar_tarea.disabled = true;
							<?php else: ?>btn_terminar_tarea.disabled = true;
							btn_nueva_actividad.disabled = true;<?php endif; ?>
							break;
						}
					}
					ver_tarea_estado.innerHTML = value_estado;
					ver_tarea_estado.className = "";
					ver_tarea_estado.classList.add(class_estado);
					break;
				}
			}

			var act_empty = true;
			for (var i = 0; i < mis_actividades.length; i++) {
				if (mis_actividades[i]['foo'] == idtarea) {
					act_empty = false;
					var id_actividad = mis_actividades[i]['foo'];

					var act_single = document.createElement("div");
					act_single.classList.add("act-single");

					var act_fecha = document.createElement("div");
					act_fecha.classList.add("act-fecha");
					var act_detalle = document.createElement("div");
					act_detalle.classList.add("act-detalle");

					var act_desc = document.createElement("div");
					act_desc.classList.add("act-desc");
					
					var btn_eliminar = document.createElement("button");
					btn_eliminar.classList.add("btn-orange");
					btn_eliminar.classList.add("act-btn-eliminar");
					btn_eliminar.dataset.idactividad = mis_actividades[i]['foo'];
					btn_eliminar.onclick = function(e) {act_eliminar(e)};
					var eliminar_icon = document.createElement("i");
					eliminar_icon.classList.add("material-icons");
					var textnode = document.createTextNode("delete");
					eliminar_icon.appendChild(textnode);
					btn_eliminar.appendChild(eliminar_icon);
					textnode = document.createTextNode("Eliminar");
					btn_eliminar.appendChild(textnode);
					
					var act_separador = document.createElement("div");
					act_separador.classList.add("act-separador");
					var tabla_adjuntos = document.createElement("div");
					tabla_adjuntos.classList.add("tabla-adjuntos");

					var btn_adjuntar = document.createElement("button");
					btn_adjuntar.classList.add("btn-blue");
					btn_adjuntar.classList.add("btn-adjunto");
					btn_adjuntar.dataset.idactividad = mis_actividades[i]['foo'];
					btn_adjuntar.onclick = function(e) {act_adjuntar(e)};
					var adjuntar_icon = document.createElement("i");
					adjuntar_icon.classList.add("material-icons");
					textnode = document.createTextNode("attach_file");
					adjuntar_icon.appendChild(textnode);
					btn_adjuntar.appendChild(adjuntar_icon);
					textnode = document.createTextNode("Adjuntar");
					btn_adjuntar.appendChild(textnode);

					var tmp_dt = new Date(mis_actividades[i]['foo']);
					act_fecha.innerHTML = tmp_dt.toLocaleDateString("es-PE",opciones) + "&nbsp;&nbsp;" + tmp_dt.toLocaleTimeString("en-US", { hour12: true, hour: '2-digit', minute: '2-digit' });
					act_desc.innerHTML = mis_actividades[i]['foo'];

					var table = document.createElement("table");

					var adj_empty = true;
					for (var j = 0; j < mis_adjuntos.length; j++) {
						if (mis_adjuntos[j]['foo'] == id_actividad) {
							var adj_empty = false;
							var aux = (mis_adjuntos[j]['foo']).split('.');
							var file_ext = aux[aux.length-1];

							var row = table.insertRow();
							var cell1 = row.insertCell();
							var cell2 = row.insertCell();

							var adjunto_nombre = document.createTextNode(mis_adjuntos[j]['foo']);
							cell1.appendChild(adjunto_nombre);

							var dwn_icon = document.createElement("i");
							dwn_icon.classList.add("material-icons");
							textnode = document.createTextNode("file_download");
							dwn_icon.appendChild(textnode);

							var url = document.createElement("a");
							url.appendChild(dwn_icon);
							url.href = doc_url + mis_adjuntos[j]['foo'];
							url.download = mis_adjuntos[j]['foo'] + "." + file_ext;
							cell2.appendChild(url);
						}
					}
					if(adj_empty) {
						var adj_empty = document.createElement("div");
						adj_empty.innerHTML = "SIN DOCUMENTOS ADJUNTOS";
						adj_empty.classList.add("adj-result-empty");
						tabla_adjuntos.appendChild(adj_empty);
					}else{
						tabla_adjuntos.appendChild(table);
					}
					<?php if(!$esjefe): ?>
					if(tarea_estado != "tr" && tarea_estado != "vr") act_detalle.appendChild(btn_eliminar);
					<?php endif; ?>
					act_detalle.appendChild(act_desc);					
					act_detalle.appendChild(act_separador);
					act_detalle.appendChild(tabla_adjuntos);
					<?php if(!$esjefe): ?>
					if(tarea_estado != "tr" && tarea_estado != "vr") act_detalle.appendChild(btn_adjuntar);
					<?php endif; ?>

					act_single.appendChild(act_fecha);
					act_single.appendChild(act_detalle);

					tarea_actividades.appendChild(act_single);
				}
			}

			if (act_empty) {
				act_result_empty.style.display = 'block';
			}
			else act_result_empty.style.display = 'none';

			tarea_lista.style.display = 'none';
			tarea_ver.style.display = 'block';
		}

		function verificar_tarea() {
			loading(true);
			var formData = new FormData();
			formData.append("tarea_remota_verificar_tarea",true);
			formData.append("verificar_idtarea",current_idtarea);
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
					goto_tarea(current_idtarea);
				}
			});
		}
		
		function modificar_tarea() {
			loading(true);
			var formData = new FormData();
			formData.append("tarea_remota_modificar_tarea",true);
			formData.append("modificar_idtarea",current_idtarea);
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
					console.log(e);
					//goto_tarea(current_idtarea);
				}
			});
		}

		function terminar_tarea() {
			loading(true);
			var formData = new FormData();
			formData.append("tarea_remota_terminar_tarea",true);
			formData.append("terminar_idtarea",current_idtarea);
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
					goto_tarea(current_idtarea);
				}
			});
		}
		
		function guardar_tarea() {
			if (validar_tarea()) {
				loading(true);
				var aux_responsable;
				<?php if($esjefe): ?>
				aux_responsable = tarea_form_responsable.value;
				<?php else: ?>
				aux_responsable = "";
				<?php endif; ?>
				var formData = new FormData();
				formData.append("tarea_remota_guardar_tarea",true);
				formData.append("tarea_nombre",tarea_form_nombre.value.trim());
				formData.append("tarea_descripcion",tarea_form_detalle.value.trim());
				formData.append("tarea_responsable",aux_responsable);
				formData.append("tarea_plazo",tarea_form_plazo.value);
				formData.append("tarea_fechainicio",tarea_form_inicio.value);
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
						goto_tarea();
					}
				});
			}
		}
		
		function guardar_actividad() {
			if (validar_actividad()) {
				loading(true);
				var formData = new FormData();
				formData.append("tarea_remota_guardar_actividad",true);
				formData.append("current_idtarea",current_idtarea);
				formData.append("actividad_form_detalle",actividad_form_detalle.value.trim());
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
						goto_tarea(current_idtarea);
					}
				});
			}
		}
		
		function guardar_adjunto() {
			if (validar_adjunto()) {
				loading(true);
				var formData = new FormData();
				formData.append("tarea_remota_guardar_adjunto",true);
				formData.append("current_idactividad",current_idactividad);
				formData.append("adjunto_form_nombre",adjunto_form_nombre.value.trim());
				formData.append("adjunto_form_archivo",adjunto_form_archivo.files[0]);
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
						goto_tarea(current_idtarea);
					}
				});
			}
		}

		function eliminar_actividad() {
			loading(true);
			var formData = new FormData();
			formData.append("tarea_remota_eliminar_actividad",true);
			formData.append("current_idactividad",current_idactividad);
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
					goto_tarea(current_idtarea);
				}
			});
		}

		function validar_tarea() {
			var go = true;
			<?php if($esjefe): ?>if (tarea_form_responsable.value == 0) {	tarea_form_responsable.classList.add("border-red"); go = false;	}
			else tarea_form_responsable.classList.remove("border-red");<?php endif; ?>
			if (!tarea_form_nombre.value.trim()) {	tarea_form_nombre.classList.add("border-red"); go = false;	}
			else tarea_form_nombre.classList.remove("border-red");
			if (!tarea_form_detalle.value.trim()) {	tarea_form_detalle.classList.add("border-red"); go = false;	}
			else tarea_form_detalle.classList.remove("border-red");
			if (tarea_form_plazo.value == "" || tarea_form_plazo.value < 0) {	tarea_form_plazo.classList.add("border-red"); go = false;	}
			else tarea_form_plazo.classList.remove("border-red");
			if (!tarea_form_inicio.value) {	tarea_form_inicio.classList.add("border-red"); go = false;	}
			else tarea_form_inicio.classList.remove("border-red");
			return go;
		}

		function validar_actividad() {
			var go = true;
			if (!actividad_form_detalle.value.trim()) {	actividad_form_detalle.classList.add("border-red"); go = false;	}
			else actividad_form_detalle.classList.remove("border-red");
			return go;
		}

		function validar_adjunto() {
			var go = true;
			if (!adjunto_form_nombre.value.trim()) {	adjunto_form_nombre.classList.add("border-red"); go = false;	}
			else adjunto_form_nombre.classList.remove("border-red");
			if (!adjunto_form_archivo.value) {
				adjunto_form_archivo.classList.add("border-red"); go = false;
			}else{
				adjunto_form_archivo.classList.remove("border-red");
				var tmp = (adjunto_form_archivo.files[0].size)/1024;
				if(tmp > 30720) {	document.getElementById("max_30_mb").style.color = "red"; go = false;	}
				else document.getElementById("max_30_mb").style.color = "gray";
			}
			return go;
		}

		function go_back() {
			document.getElementsByClassName("tarea-lista")[0].style.display = 'block';
			document.getElementsByClassName("tarea-ver")[0].style.display = 'none';
		}

		function clean_form() {
			<?php if($esjefe): ?>tarea_form_responsable.classList.remove("border-red");
			tarea_form_responsable.value = 0;<?php endif; ?>
			tarea_form_nombre.classList.remove("border-red");
			tarea_form_detalle.classList.remove("border-red");
			tarea_form_plazo.classList.remove("border-red");
			tarea_form_inicio.classList.remove("border-red");
			tarea_form_nombre.value = "";
			tarea_form_detalle.value = "";
			tarea_form_plazo.value = "";
			tarea_form_plazo.disabled = false;
			tarea_form_plazo_indef.checked = false;
			tarea_form_inicio.value = date_today_input();
			tarea_edit.style.display = "none";

			actividad_form_detalle.classList.remove("border-red");
			actividad_form_detalle.value = "";
			actividad_edit.style.display = "none";

			adjunto_form_nombre.classList.remove("border-red");
			adjunto_form_archivo.classList.remove("border-red");
			adjunto_form_nombre.value = "";
			adjunto_form_archivo.value = "";
			adjunto_edit.style.display = "none";

			cnf_modal.style.display = "none";
			
			cnf_terminar.style.display = "none";
			cnf_verificar.style.display = "none";
			cnf_eliminar_actividad.style.display = "none";
			cnf_marcar_salida.style.display = "none";
		}

		function goto_tarea(idtarea = null) {
			var prevget = "";
			var prevget_arr = [];
			var aux = window.location.search.substr(1);
			aux = aux.split('&');
			for(var i = 0; i < aux.length; i++) {
				var tmp = aux[i].split('=');
				if(tmp[0] != "gototarea"){
					var tmp2 = tmp.join('=');
					prevget_arr.push(tmp2);
				}
			}
			if (prevget_arr.length > 0) prevget = prevget_arr.join('&');
			var getparam_tarea = "";
			if(idtarea != null) getparam_tarea = "&gototarea=" + idtarea;
			var url = "http://www.psi.gob.pe/intranet/index.php/tarea-remota/?" + prevget + getparam_tarea;
			window.location.href = url;
		}

		function generar_reporte(){
			if(mis_tareas.length == 0) return;
			var wb = XLSX.utils.book_new();

			wb.Props = {
				Title: "Reporte Trabajo Remoto",
				Author: "PSI",
				CreatedDate: new Date()
			};
			wb.SheetNames.push("REPORTE");

			var ws_data = [];
			ws_data[0] = ["REPORTE DE TAREAS"];
			var fila = 2;

			ws_data[fila] = ["foo","foo","foo","foo","foo","foo","foo","foo"];

			fila++;
			for (var i = 0; i < mis_tareas.length; i++) {
				var tkrow = [];
				tkrow[0] = mis_tareas[i]['foo'];
				tkrow[1] = mis_tareas[i]['foo'];
				tkrow[2] = mis_tareas[i]['foo'];
				tkrow[3] = new Date(mis_tareas[i]['foo']).toLocaleDateString("es-PE",opciones);
				tkrow[4] = mis_tareas[i]['foo'];
				tkrow[5] = new Date(mis_tareas[i]['foo']).toLocaleDateString("es-PE",opciones);
				if(mis_tareas[i]['foo'] == null) tkrow[6] = "";
				else tkrow[6] = new Date(mis_tareas[i]['foo']).toLocaleDateString("es-PE",opciones);
				tkrow[7] = estado_fullname(mis_tareas[i]['foo']);
				ws_data[fila] = tkrow;
				fila++;
			}
			var ws = XLSX.utils.aoa_to_sheet(ws_data);
			wb.Sheets["REPORTE"] = ws;
			var wbout = XLSX.write(wb, {bookType:'xlsx',  type: 'binary'});
			var filename = "REPORTE_" + (new Date()).getTime() + ".xlsx";
			saveAs(new Blob([s2ab(wbout)],{type:"application/octet-stream"}), filename);
		}

		function s2ab(s) {
			var buf = new ArrayBuffer(s.length);
			var view = new Uint8Array(buf);
			for (var i=0; i<s.length; i++) view[i] = s.charCodeAt(i) & 0xFF;
				return buf;
		}

		function estado_fullname(est) {
			var res = "";
			switch (est) {
				case "ep": res = "En Proceso"; break;
				case "vn": res = "Vencido"; break;
				case "tr": res = "Terminado"; break;
				case "vr": res = "Verificado"; break;
			}
			return res;
		}

		function findGetParameter(parameterName) {
			var result = null;
			location.search.substr(1).split("&").forEach(function (item) {
				var tmp = item.split("=");
				if (tmp[0] === parameterName) result = tmp[1];
			});
			return result;
		}
		
		function date_today_input() {
			var d = new Date();
			var datestring = d.getFullYear() + "-" + ("0"+(d.getMonth()+1)).slice(-2) + "-" + ("0" + d.getDate()).slice(-2);
			return datestring;
		}
		function clock_out() {
			loading(true);
			var formData = new FormData();
			formData.append("tarea_remota_clock_out",true);
			$.ajax({
				url: 'http://www.psi.gob.pe/intranet/index.php/editor-service',
				type: 'POST',
				data: formData,
				cache: false,
				contentType: false,
				processData: false,
				error: function(){
					console.log("error");
					loading(false);
					return true;
				},
				success: function(e){
					console.log(e);
					location.reload();
					loading(false);
				}
			});
		}
	</script>
</body>
</html>