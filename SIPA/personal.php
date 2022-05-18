<?php
// TODO validate user session, authorization
include("personal_util.php");
$PerTipoContrato_raw = sql_statement($PerTipoContrato_query);
if ($PerTipoContrato_raw['response'] == 0) {
	echo "HA OCURRIDO UN ERROR. INTENTE MÁS TARDE.<br>".$PerTipoContrato_raw['message'];
	die();
}
$PerDireccion_raw = sql_statement($PerDireccion_query);
if ($PerDireccion_raw['response'] == 0) {
	echo "HA OCURRIDO UN ERROR. INTENTE MÁS TARDE.<br>".$PerDireccion_raw['message'];
	die();
}
$PerArea_raw = sql_statement($PerArea_query);
if ($PerArea_raw['response'] == 0) {
	echo "HA OCURRIDO UN ERROR. INTENTE MÁS TARDE.<br>".$PerArea_raw['message'];
	die();
}
$PerPersonaXContrato_raw = sql_statement($PerPersonaXContrato_query);
if ($PerPersonaXContrato_raw['response'] == 0) {
	echo "HA OCURRIDO UN ERROR. INTENTE MÁS TARDE.<br>".$PerPersonaXContrato_raw['message'];
	die();
}

$PerTipoContrato = $PerTipoContrato_raw['message'];
$PerDireccion = $PerDireccion_raw['message'];
$PerArea = $PerArea_raw['message'];
$PerPersonaXContrato = $PerPersonaXContrato_raw['message'];

$PerPersona_list = array();

$newdni = 0;
if (isset($_GET['FAKE'])) {
	$newdni = $_GET['FAKE'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Personal PSI</title>
	<link rel="preconnect" href="https://fonts.gstatic.com">
	<link href="https://fonts.googleapis.com/css?family=Roboto&display=swap" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css2?family=Roboto+Slab&display=swap" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css2?family=Roboto+Mono&display=swap" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css2?family=Ubuntu&display=swap" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css2?family=Montserrat&display=swap" rel="stylesheet">
	<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
	<!-- jQuery -->
	<!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script> -->
	<script src="jquery.min.js"></script>
	<!-- my loading  CSS -->
	<link rel="stylesheet" href="loading.css">
	<!-- my loading JS -->
	<script src="loading.js"></script>
	<!-- -->
	<link rel="stylesheet" href="personal.css">
	<link rel="stylesheet" href="loading-mini.css">
</head>
<body>
	<?php include('loading.php'); ?>
	<div id="screen_block"></div>
	<!-- my loading PHP -->
	<div class="personal-listado">
		<div class="big-titulo">Listado Único de Personal</div>
		<fieldset class="personal-filtro">
			<legend>BUSQUEDA</legend>
			<div>
				<div class="select-wrap">
					<label for="per_search_tipo_contrato">TIPO DE CONTRATO</label>
					<select id="per_search_tipo_contrato" onchange="this.dataset.chosen = this.value; personal_filtrar();" data-chosen="0">
						<option value="0">TODO</option>
						<?php foreach ($PerTipoContrato as $item): ?>
							<option value="<?= $item['FAKE'] ?>"><?= $item['FAKE'] ?></option>
						<?php endforeach ?>
					</select>
				</div>
				<div class="select-wrap">
					<label for="per_search_estado_contrato">SITUACIÓN</label>
					<select id="per_search_estado_contrato" onchange="this.dataset.chosen = this.value; personal_filtrar();" data-chosen="0">
						<option value="0">TODO</option>
						<option value="1">VIGENTE</option>
						<option value="2">SIN CONTRATO VIGENTE</option>
						<option value="3">NO LABORA</option>
					</select>
				</div>
			</div>
			<div style="display: flex;">
				<div class="select-wrap">
					<label for="per_search_direccion">DIRECCIÓN</label>
					<select id="per_search_direccion" onchange="filter_change_area(); personal_filtrar();" data-chosen="0">
						<option value="0">TODO</option>
						<?php foreach ($PerDireccion as $item): ?>
							<option value="<?= $item['FAKE'] ?>"><?= $item['FAKE'] ?>&nbsp;(<?= $item['FAKE'] ?>)</option>
						<?php endforeach ?>
					</select>
				</div>
			</div>
			<div style="display: flex;">
				<div class="select-wrap">
					<label for="per_search_area">ÁREA</label>
					<select id="per_search_area" onchange="this.dataset.chosen = this.value; personal_filtrar();" data-chosen="0">
						<option value="0">TODO</option>
					</select>
				</div>
			</div>
			<div style="display: flex;">
				<input id="per_search_nombre" onkeyup="personal_filtrar();" type="text" placeholder="NOMBRE(S) Y/O APELLIDO(S)" autocomplete="off" style="flex: 1;">
				<input id="per_search_dni" onkeyup="personal_filtrar();" type="text" placeholder="DNI" maxlength="8" autocomplete="off" style="width: 150px;">
			</div>
		</fieldset>
		<div class="separador"></div>
		<div><button class="btn-blue bigger-button" id="btn_nuevo_personal">+ Nuevo Personal</button></div>
		<div class="tabla-listado tabla-normal">
			<div class="result-empty">NO HAY PERSONAL PARA MOSTRAR</div>
			<table id="per_listado">
				<thead>
					<tr>
						<th>Dirección</th>
						<th>Área</th>
						<th>DNI</th>
						<th>Nombre</th>
						<th>Tipo de<br>Contrato</th>
						<th>Fecha<br>Inicio</th>
						<th>Fecha<br>Término</th>
						<th>Ver</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($PerPersonaXContrato as $item): ?>
						<?php
							$PerPersona_list[$item['FAKE']] = array('FAKE' => $item['FAKE'], 'FAKE' => $item['FAKE'], 'FAKE' => $item['FAKE'], 'FAKE' => $item['FAKE']);
						?>
						<tr data-situacion=<?= get_situacion($item['FAKE'],$item['FAKE'],$item['FAKE']); ?>>
							<td data-direccion_id="<?= $item['FAKE']==null?"0":$item['FAKE'] ?>">
								<div class="tooltip">
									<?= $item['FAKE']==null?"--":$item['FAKE'] ?>
									<?php if ($item['FAKE']!=null): ?>
										<div class="tooltiptext"><?= ucwords(mb_strtolower($item['FAKE'])) ?></div>
									<?php endif ?>
								</div>
							</td>
							<td data-area_id="<?= $item['FAKE']==null?"0":$item['FAKE'] ?>">
								<div class="tooltip">
									<?= $item['FAKE']==null?"--":$item['FAKE'] ?>
									<?php if ($item['FAKE']!=null): ?>
										<div class="tooltiptext"><?= ucwords(mb_strtolower($item['FAKE'])) ?></div>
									<?php endif ?>
								</div>
							</td>
							<td ><?= $item['FAKE'] ?></td>
							<td><?= mb_strtoupper($item['FAKE']." ".$item['FAKE'].", ".$item['FAKE']) ?></td>
							<td data-tipocontrato_id="<?= $item['FAKE']==null?"0":$item['FAKE'] ?>">
								<?= $item['FAKE']==null?"--":$item['FAKE'] ?>
							</td>
							<td><?= $item['FAKE']==null?"--":datetime_format($item['FAKE']) ?></td>
							<td><?= $item['FAKE']==null?"--":datetime_format($item['FAKE']) ?></td>
							<td data-persona_id="<?= $item['FAKE'] ?>" id="<?= $item['FAKE'] ?>" onclick="show_ficha_unica(this.dataset.persona_id);">
								<i class="material-icons">search</i>
							</td>
						</tr>
					<?php endforeach ?>
				</tbody>
			</table>
		</div>
	</div>
	<div id="my_modal" class="modal">

		<div id="modal_personal_registro" class="modal-content personal-registro">
			<span class="close">&times;</span>

			<div class="dni-search">
				<input type="text" placeholder="DNI" maxlength="8" id="per_reg_dni_search" autocomplete="off">
				<button class="btn-black btn-icon" style="margin-left: 10px;" onclick="buscar_dni();">
					<i class="material-icons" style="font-size: 24px;">search</i>
				</button>
			</div>

			<div class="server-error">HA OCURRIDO UN ERROR. INTENTE OTRA VEZ.</div>
			<div class="ya-existe">YA EXISTE PERSONAL CON ESTE DNI.</div>
			<div class="no-encontro">DNI NO ES VÁLIDO.</div>
			<div class="pide-error">
				<div>
					BUSQUEDA NO DISPONIBLE.<br>INTENTAR NUEVAMENTE O INGRESAR INFORMACIÓN DE FORMA MANUAL.
				</div>
				<div style="padding: 5px 0;"><button class="btn-orange" onclick="show_registro_manual()">REGISTRO MANUAL</button></div>
			</div>

			<div class="registro-data">
				<div id="loading_pide">
					<div class="mini-spinner"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
				</div>
				<div class="foto-input">
					<img src="http://www.psi.gob.pe/sipa/img/foto-empty.png" id="per_reg_foto" data-empty="1">
				</div>
				<div class="data-input dni-input">
					<div>DNI</div>
					<input class="per-reg-input" id="per_reg_dni" type="text" maxlength="8" autocomplete="off">
				</div>
				<div class="data-input">
					<div>Primer Apellido</div>
					<div class="per-reg-show" id="per_reg_ap_show"></div>
					<input class="per-reg-input"  id="per_reg_ap" type="text" autocomplete="off">
				</div>
				<div class="data-input">
					<div>Segundo Apellido</div>
					<div class="per-reg-show" id="per_reg_am_show"></div>
					<input class="per-reg-input" id="per_reg_am" type="text" autocomplete="off">
				</div>
				<div class="data-input">
					<div>Nombres</div>
					<div class="per-reg-show" id="per_reg_nombres_show"></div>
					<input class="per-reg-input" id="per_reg_nombres" type="text" autocomplete="off">
				</div>
			</div>
			<div><button class="btn-blue bigger-button" id="btn_registrar_personal" onclick="registrar_personal();" disabled>Registrar</button></div>
			<div class="server-error">HA OCURRIDO UN ERROR. INTENTE OTRA VEZ.</div>
		</div>

		<div id="modal_contrato_registro" class="modal-content contrato-registro">
			<span class="close">&times;</span>
			<div class="contrato-form">
				<div style="padding: 0 0 10px 10px; font-weight: bold;">Nuevo Contrato</div>
				<div class="contrato-input">
					<select id="cont_reg_direccion" style="flex: 1;" data-chosen="0">
						<option value="0" hidden disabled selected>DIRECCION</option>
						<?php foreach ($PerDireccion as $item): ?>
							<option value="<?= $item['FAKE'] ?>"><?= $item['FAKE'] ?>&nbsp;(<?= $item['FAKE'] ?>)</option>
						<?php endforeach ?>
					</select>
				</div>
				<div class="contrato-input">
					<select id="cont_reg_area" style="flex: 1;" data-chosen="0" disabled>
						<option value="0" hidden disabled selected>AREA</option>
					</select>
				</div>
				<div class="contrato-input">
					<select id="cont_reg_tipocontrato" data-chosen="0">
						<option value="0" hidden disabled selected>TIPO DE CONTRATO</option>
						<?php foreach ($PerTipoContrato as $item): ?>
							<option value="<?= $item['FAKE'] ?>"><?= $item['FAKE'] ?></option>
						<?php endforeach ?>
					</select>	
					<input id="cont_reg_documento" type="text" placeholder="DOCUMENTO" autocomplete="off">
				</div>
				<div class="contrato-input">
					<input id="cont_reg_inicio" type="date">
					<input id="cont_reg_plazo" type="number" placeholder="PLAZO (DIAS)" min=0 autocomplete="off">
					<div class="calculo-fecha">
						<i class="material-icons" style="font-size: 20px;">play_arrow</i>
						<div id="cont_reg_calculo_fecha">--</div>
					</div>
				</div>
				<button class="btn-blue bigger-button" onclick="guardar_contrato()">Guardar</button>
				<div class="server-error">HA OCURRIDO UN ERROR. INTENTE OTRA VEZ.</div>
			</div>
		</div>
		<div id="modal_contrato_confirm" class="modal-content contrato-confirm">
			<div><i class="material-icons cnf-icon">error_outline</i>¿Desea dar por terminado el servicio?</div>
			<div>Confirmar que el trabajador ya no labora en el PSI.</div>
			<div>
				<button class="bigger-button btn-orange" onclick="finalizar_contrato();">FINALIZAR</button>
				<button id="btn_cancelar" class="bigger-button btn-gray">CANCELAR</button>
			</div>
			<div class="server-error" style="text-align: center; margin-bottom: 20px; color: red;">HA OCURRIDO UN ERROR. INTENTE OTRA VEZ.</div>
		</div>

	</div>
	<div class="personal-historia">
		<div class="big-titulo">Ficha Única de Personal</div>
		<div style="display: inline-block;">
			<div class="persona-card">
				<div class="foto-marco">
					<img id="hist_foto" src="" alt="foto">
				</div>
				<div class="datos-marco">
					<div>
						<div>DNI</div>
						<div id="hist_dni"></div>
					</div>
					<div>
						<div>Primer Apellido</div>
						<div id="hist_ap"></div>
					</div>
					<div>
						<div>Segundo Apellido</div>
						<div id="hist_am"></div>
					</div>
					<div>
						<div>Nombres</div>
						<div id="hist_nom"></div>
					</div>
				</div>
			</div>
		</div>
		<div></div>
		<div style="display: inline-block; min-width: 800px;">
			<div class="separador"></div>
			<!-- if LIST_LENGTH == empty || FECHAFIN != null || FECHATERMINO > hoy -->
			<button id="btn_hist_contratar" class="btn-blue bigger-button">+ Nuevo Contrato</button>
			<button id="btn_hist_finalizar" class="btn-orange bigger-button">Finalizar Contrato</button>
			<button class="btn-black bigger-button" style="float: right; display: flex; align-items: center; padding: 8px 20px;" onclick="hide_ficha_unica();">
				<i class="material-icons" style="font-size:24px">keyboard_arrow_left</i>
				Regresar
			</button>
			<div class="result-empty">NO HAY CONTRATOS PARA MOSTRAR</div>
			<div class="result-error">HA OCURRIDO UN ERROR</div>
			<div class="tabla-normal">
				<table id="tabla_contratos">
					<thead>
						<tr>
							<th>Tipo de<br>Contrato</th>
							<th>Dirección</th>
							<th>Área</th>
							<th>Documento</th>
							<th>Inicio</th>
							<th>Plazo</th>
							<th>Término</th>
							<th>Finalizado</th>
						</tr>
					</thead>
				</table>
			</div>
		</div>
	</div>

	<script>
		String.prototype.ucwords = function() {
			str = this.toLowerCase();
			return str.replace(/(^([a-zA-Z\p{M}]))|([ -][a-zA-Z\p{M}])/g,
				function($1){
					return $1.toUpperCase();
				});
		}
		Date.prototype.addDays = function(days) {
			var date = new Date(this.valueOf());
			date.setDate(date.getDate() + days);
			return date;
		}
		var opciones = { year: '2-digit', month: '2-digit', day: '2-digit' };
		var per_area = <?= json_encode($PerArea) ?>;
		var lista_personas = <?= json_encode($PerPersona_list) ?>;
		var newdni = <?= json_encode($newdni) ?>;
		var current_id = 0;
		var current_dni = 0;
		var current_direccion = 0;
		var current_area = 0;
		var current_tipocontrato = 0;
		var current_termino = 0;
		var current_fin = 0;

		var screen_block;
		var container_personal_listado;
		var container_personal_historia;

		var per_search_tipo_contrato;
		var per_search_estado_contrato;
		var per_search_direccion;
		var per_search_area;
		var per_search_nombre;
		var per_search_dni;
		var btn_nuevo_personal;

		var personas_result_empty;
		var per_listado;
		var tabla_contratos;

		var modal;
		var modal_personal_registro;
		var modal_contrato_registro;
		var modal_contrato_confirm;
		var span;
		var span2;
		var btn_cancelar;
		
		var dni_search;
		var per_reg_dni_search;
		
		var loading_pide;
		var server_error;
		var server_error_reg;
		var server_error_contrato;
		var server_error_fin;
		var ya_existe;
		var no_encontro;
		var pide_error;

		var dni_input;
		var per_reg_dni;
		var per_reg_nombres_show;
		var per_reg_nombres;
		var per_reg_ap_show;
		var per_reg_ap;
		var per_reg_am_show;
		var per_reg_am;
		var per_reg_foto;
		var foto_container;

		var btn_registrar_personal;

		var hist_foto;
		var hist_dni;
		var hist_nom;
		var hist_ap;
		var hist_am;

		var btn_hist_contratar;
		var btn_hist_finalizar;

		var contratos_result_empty;
		var contratos_result_error;

		var cont_reg_direccion;
		var cont_reg_area;
		var cont_reg_tipocontrato;
		var cont_reg_documento;
		var cont_reg_inicio;
		var cont_reg_plazo;
		var cont_reg_calculo_fecha;
		var container_calculo_fecha;

		$(document).ready(function(){
			screen_block = document.getElementById("screen_block");
			container_personal_listado = document.getElementsByClassName("personal-listado")[0];
			container_personal_historia = document.getElementsByClassName("personal-historia")[0];
			
			per_search_tipo_contrato = document.getElementById("per_search_tipo_contrato");
			per_search_estado_contrato = document.getElementById("per_search_estado_contrato");
			per_search_direccion = document.getElementById("per_search_direccion");
			per_search_area = document.getElementById("per_search_area");
			per_search_nombre = document.getElementById("per_search_nombre");
			per_search_dni = document.getElementById("per_search_dni");
			btn_nuevo_personal = document.getElementById("btn_nuevo_personal");

			personas_result_empty = document.getElementsByClassName("result-empty")[0];
			per_listado = document.getElementById("per_listado");
			tabla_contratos = document.getElementById("tabla_contratos");
			
			modal = document.getElementById("my_modal");
			modal_personal_registro = document.getElementById("modal_personal_registro");
			modal_contrato_registro = document.getElementById("modal_contrato_registro");
			modal_contrato_confirm = document.getElementById("modal_contrato_confirm");
			span = document.getElementsByClassName("close")[0];
			span2 = document.getElementsByClassName("close")[1];
			btn_cancelar = document.getElementById("btn_cancelar");

			dni_search = document.getElementsByClassName("dni-search")[0];
			per_reg_dni_search = document.getElementById("per_reg_dni_search");

			loading_pide = document.getElementById("loading_pide");
			server_error = document.getElementsByClassName("server-error")[0];
			server_error_reg = document.getElementsByClassName("server-error")[1];
			server_error_contrato = document.getElementsByClassName("server-error")[2];
			server_error_fin = document.getElementsByClassName("server-error")[3];
			ya_existe = document.getElementsByClassName("ya-existe")[0];
			no_encontro = document.getElementsByClassName("no-encontro")[0];
			pide_error = document.getElementsByClassName("pide-error")[0];
			
			dni_input = document.getElementsByClassName("dni-input")[0];
			per_reg_dni = document.getElementById("per_reg_dni");
			per_reg_nombres_show = document.getElementById("per_reg_nombres_show");
			per_reg_nombres = document.getElementById("per_reg_nombres");
			per_reg_ap_show = document.getElementById("per_reg_ap_show");
			per_reg_ap = document.getElementById("per_reg_ap");
			per_reg_am_show = document.getElementById("per_reg_am_show");
			per_reg_am = document.getElementById("per_reg_am");
			per_reg_foto = document.getElementById("per_reg_foto");
			foto_container = document.getElementsByClassName("foto-input")[0];

			btn_registrar_personal = document.getElementById("btn_registrar_personal");

			hist_foto = document.getElementById("hist_foto");
			hist_dni = document.getElementById("hist_dni");
			hist_nom = document.getElementById("hist_nom");
			hist_ap = document.getElementById("hist_ap");
			hist_am = document.getElementById("hist_am");

			btn_hist_contratar = document.getElementById("btn_hist_contratar");
			btn_hist_finalizar = document.getElementById("btn_hist_finalizar");

			contratos_result_empty = document.getElementsByClassName("result-empty")[1];
			contratos_result_error = document.getElementsByClassName("result-error")[0];

			cont_reg_direccion = document.getElementById("cont_reg_direccion");
			cont_reg_area = document.getElementById("cont_reg_area");
			cont_reg_tipocontrato = document.getElementById("cont_reg_tipocontrato");
			cont_reg_documento = document.getElementById("cont_reg_documento");
			cont_reg_inicio = document.getElementById("cont_reg_inicio");
			cont_reg_plazo = document.getElementById("cont_reg_plazo");
			cont_reg_calculo_fecha = document.getElementById("cont_reg_calculo_fecha");
			container_calculo_fecha = document.getElementsByClassName("calculo-fecha")[0];

			cont_reg_direccion.onchange = function() {
				this.dataset.chosen = this.value;
				filter_change_area(false);
				cont_reg_direccion.classList.remove("input-invalido");
			}
			cont_reg_area.onchange = function() {
				this.dataset.chosen = this.value;
				cont_reg_area.classList.remove("input-invalido");
			}
			cont_reg_tipocontrato.onchange = function() {
				this.dataset.chosen = this.value;
				cont_reg_documento.focus();
				cont_reg_tipocontrato.classList.remove("input-invalido");
			}
			cont_reg_documento.onkeyup = function() {
				cont_reg_documento.classList.remove("input-invalido");
			}
			cont_reg_inicio.onchange = function() {
				cont_reg_inicio.classList.remove("input-invalido");
				calcular_fecha_fin();
			}
			cont_reg_plazo.onkeyup = function() {
				cont_reg_plazo.classList.remove("input-invalido");
				calcular_fecha_fin();
			}
			cont_reg_plazo.onchange = function() {
				cont_reg_plazo.classList.remove("input-invalido");
				calcular_fecha_fin();
			}

			// modal
			btn_nuevo_personal.onclick = function() {
				show_simple(modal);
				show_simple(modal_personal_registro);
				per_reg_dni_search.focus();
			}
			btn_hist_contratar.onclick = function() {
				show_simple(modal);
				show_simple(modal_contrato_registro);
				default_nuevo_contrato();
			}
			btn_hist_finalizar.onclick = function() {
				show_simple(modal);
				show_simple(modal_contrato_confirm);
			}
			span.onclick = function() {
				show_simple(modal,false);
				personal_registro_clear(true);
			}
			span2.onclick = function() {
				show_simple(modal,false);
				clear_nuevo_contrato();
			}
			btn_cancelar.onclick = function() {
				show_simple(modal,false);
				show_simple(modal_contrato_confirm,false);
				show_simple(server_error_fin,false);
			}
			window.onclick = function(event) {
				if (event.target == modal) {
					show_simple(modal,false);
					personal_registro_clear(true);
					clear_nuevo_contrato();
					show_simple(modal_contrato_confirm,false);
					show_simple(server_error_fin,false);
				}
			}
			// DNI input valido
			set_input_filter (per_reg_dni_search, function(value) {
				return /^\d*$/.test(value);
			});
			set_input_filter (per_reg_dni, function(value) {
				return /^\d*$/.test(value);
			});
			set_input_filter (per_search_dni, function(value) {
				return /^\d*$/.test(value);
			});
			set_input_filter (per_search_nombre, function(value) {
				return /^[a-z ]*$/i.test(value);
			});
			set_input_filter (per_reg_nombres, function(value) {
				return /^[a-z ]*$/i.test(value);
			});
			set_input_filter (per_reg_ap, function(value) {
				return /^[a-z ]*$/i.test(value);
			});
			set_input_filter (per_reg_am, function(value) {
				return /^[a-z ]*$/i.test(value);
			});
			// DNI enter
			dni_search.addEventListener("keyup", function(event) {
				if (event.keyCode === 13) {
					event.preventDefault();
					buscar_dni();
				}
			});
			if (newdni != 0) {
				document.getElementById(newdni).click();
			}
			personal_filtrar();
		});

		function filter_change_area(busqueda = true) {
			cont_reg_area.classList.remove("input-invalido");
			select_area = busqueda?per_search_area:cont_reg_area;
			select_direccion = busqueda?per_search_direccion:cont_reg_direccion;
			select_area.disabled = false;
			select_direccion.dataset.chosen = select_direccion.value;
			select_area.innerHTML = "";
			var dir_val = select_direccion.value;
			if (dir_val == 0) {
				var opt = document.createElement("option");
				opt.value = "0";
				if (busqueda) { opt.text = "TODO"; }
				else {
					opt.text = "AREA";
					opt.hidden = true;
					opt.disabled = true;
					opt.selected = true;
					select_area.disabled = true;
				}
				select_area.appendChild(opt);
				select_area.dataset.chosen = select_area.value;
			}else{
				var c_a = 0;
				for (var i = 0; i < per_area.length; i++) {
					if (per_area[i]['FAKE'] == dir_val) {
						var opt = document.createElement("option");
						opt.value = per_area[i]['FAKE'];
						opt.text = per_area[i]['FAKE'] + " (" + per_area[i]['FAKE'] + ")";
						select_area.appendChild(opt);
						c_a++;
					}
				}
				if (c_a == 1) {
					select_area.disabled = true;
				} else {
					var opt = document.createElement("option");
					opt.value = "0";
					if (busqueda) { opt.text = "TODO"; }
					else {
						opt.text = "AREA";
						opt.hidden = true;
						opt.disabled = true;
						opt.selected = true;
					}
					select_area.prepend(opt);
					select_area.value = 0;
					select_area.dataset.chosen = select_area.value;
				}
			}
		}

		function validar_input_dni() {
			personal_registro_clear();
			per_reg_dni_search.classList.remove("input-invalido");
			var dni_val = per_reg_dni_search.value.trim();
			if(dni_val == null || dni_val=="" || dni_val.length!=8) {
				per_reg_dni_search.classList.add("input-invalido");
				per_reg_dni_search.focus();
				return false;
			}
			return true;
		}

		function buscar_dni() {
			if (!validar_input_dni()) return;
			show_load_pide();
			per_reg_dni.value = per_reg_dni_search.value;

			var formData = new FormData();
			formData.append("personal_buscar_dni",true);
			formData.append("per_reg_dni",per_reg_dni.value);
			$.ajax({
				url: 'http://www.psi.gob.pe/sipa/personal_control.php',
				type: 'POST',
				data: formData,
				cache: false,
				contentType: false,
				processData: false,
				error: function(e){
					console.log(e);
					show_load_pide(false);
					show_simple(server_error);
					return true;
				},
				success: function(e){
					show_load_pide(false);
					buscar_dni_response(e);
				}
			});
		}

		function buscar_dni_response(e) {
			var result = JSON.parse(e);
			var response = result.response;
			var message = result.message;
			switch (response) {
				case 0: {/* SERVER ERROR */
					show_simple(server_error);
					break;
				}
				case 1: {/* YA EXISTE */
					show_simple(ya_existe);
					break;
				}
				case 2: {/* PIDE ERROR */
					show_simple(pide_error);
					break;
				}
				case 3: {/* NO ENCONTRO */
					show_simple(no_encontro);
					break;
				}
				case 4: {/* PIDE FILL */
					btn_registrar_personal.disabled = false;
					personal_registro_pide_fill(message);
					break;
				}
			}
		}

		function personal_registro_pide_fill(data) {
			per_reg_nombres_show.innerHTML = data.NOMBRES;
			per_reg_nombres_show.classList.add("filled");
			per_reg_nombres.value = data.NOMBRES;
			per_reg_ap_show.innerHTML = data.APELLIDO_PATERNO;
			per_reg_ap_show.classList.add("filled");
			per_reg_ap.value = data.APELLIDO_PATERNO;
			per_reg_am_show.innerHTML = data.APELLIDO_MATERNO;
			per_reg_am_show.classList.add("filled");
			per_reg_am.value = data.APELLIDO_MATERNO;
			per_reg_foto.src = "data:image/jpeg;base64," + data.FOTO;
			per_reg_foto.dataset.empty = "0";
		}

		function personal_registro_clear(full = false) {
			if (full) {
				per_reg_dni_search.value = "";
				per_reg_dni.value = "";
				show_registro_manual(false);
				modal_personal_registro.style.display = "";
			}
			show_simple(server_error, false);
			show_simple(server_error_reg, false);
			show_simple(ya_existe, false);
			show_simple(no_encontro, false);
			show_simple(pide_error, false);
			per_reg_nombres_show.innerHTML = "";
			per_reg_nombres_show.classList.remove("filled");
			per_reg_nombres.value = "";
			per_reg_ap_show.innerHTML = "";
			per_reg_ap_show.classList.remove("filled");
			per_reg_ap.value = "";
			per_reg_am_show.innerHTML = "";
			per_reg_am_show.classList.remove("filled");
			per_reg_am.value = "";
			per_reg_foto.src = "http://www.psi.gob.pe/sipa/img/foto-empty.png";
			per_reg_foto.dataset.empty = "1";
			btn_registrar_personal.disabled = true;
			per_reg_dni_search.classList.remove("input-invalido");
		}

		function registrar_personal() {
			if (!validar_input_registro()) return;
			loading();
			var formData = new FormData();
			formData.append("personal_registrar",true);
			formData.append("per_reg_dni",per_reg_dni.value);
			formData.append("per_reg_nombres",per_reg_nombres.value);
			formData.append("per_reg_ap",per_reg_ap.value);
			formData.append("per_reg_am",per_reg_am.value);
			if (per_reg_foto.dataset.empty == 0) formData.append("per_reg_foto",per_reg_foto.src);
			$.ajax({
				url: 'http://www.psi.gob.pe/sipa/personal_control.php',
				type: 'POST',
				data: formData,
				cache: false,
				contentType: false,
				processData: false,
				error: function(e){
					console.log(e);
					loading(false);
					show_simple(server_error_reg);
					return true;
				},
				success: function(e){
					window.location.href = "http://www.psi.gob.pe/sipa/personal.php?newdni=" + per_reg_dni.value;
				}
			});
		}

		function validar_input_registro() {
			go = true;
			show_simple(server_error_reg,false);
			per_reg_dni.classList.remove("input-invalido");
			per_reg_nombres.classList.remove("input-invalido");
			per_reg_ap.classList.remove("input-invalido");
			per_reg_am.classList.remove("input-invalido");
			var dni_val = per_reg_dni.value.trim();
			var nombres_val = per_reg_nombres.value.trim();
			var ap_val = per_reg_ap.value.trim();
			var am_val = per_reg_am.value.trim();
			if(nombres_val == null || nombres_val=="") {
				per_reg_nombres.classList.add("input-invalido");
				per_reg_nombres.focus();
				go = false;
			}
			if(am_val == null || am_val=="") {
				per_reg_am.classList.add("input-invalido");
				per_reg_am.focus();
				go = false;
			}
			if(ap_val == null || ap_val=="") {
				per_reg_ap.classList.add("input-invalido");
				per_reg_ap.focus();
				go = false;
			}
			if(dni_val == null || dni_val=="" || dni_val.length!=8) {
				per_reg_dni.classList.add("input-invalido");
				per_reg_dni.focus();
				go = false;
			}
			return go;
		}

		function show_simple(element, init = true) {
			if (init) {
				element.style.display = "block";
			} else {
				element.style.display = "none";
			}
		}

		function show_load_pide(init = true) {
			if (init) {
				loading_pide.style.display = "flex";
				screen_block.style.display = "block";
			} else {
				loading_pide.style.display = "none";
				screen_block.style.display = "none";
			}
		}

		function show_registro_manual(init = true) {
			if (init) {
				show_simple(pide_error,false);
				btn_registrar_personal.disabled = false;
				dni_search.style.display = "none";
				per_reg_nombres_show.style.display = "none";
				per_reg_ap_show.style.display = "none";
				per_reg_am_show.style.display = "none";
				foto_container.style.display = "none";
				per_reg_foto.dataset.empty = "1";

				dni_input.style.display = "flex";
				per_reg_nombres.style.display = "block";
				per_reg_ap.style.display = "block";
				per_reg_am.style.display = "block";
			} else {
				dni_search.style.display = "";
				per_reg_nombres_show.style.display = "";
				per_reg_ap_show.style.display = "";
				per_reg_am_show.style.display = "";
				foto_container.style.display = "";
				per_reg_foto.dataset.empty = "0";

				dni_input.style.display = "";
				per_reg_nombres.style.display = "";
				per_reg_ap.style.display = "";
				per_reg_am.style.display = "";
			}
		}

		function fill_ficha_unica() {
			var persona_data = lista_personas[current_id];
			current_dni = persona_data['FAKE'];
			hist_foto.src = "img/dni/" + persona_data['FAKE'] + ".jpg";
			hist_dni.innerHTML = persona_data['FAKE'];
			hist_nom.innerHTML = persona_data['FAKE'].toUpperCase();
			hist_ap.innerHTML = persona_data['FAKE'].toUpperCase();
			hist_am.innerHTML = persona_data['FAKE'].toUpperCase();
		}

		function show_ficha_unica(per_id) {
			container_personal_listado.style.display = "none";
			current_id = per_id;
			fill_ficha_unica();
			container_personal_historia.style.display = "block";
			loading();
			historial_get();
			loading(false);
		}

		function hide_ficha_unica() {
			container_personal_listado.style.display = "";
			current_id = 0;
			current_dni = 0;
			current_contrato = 0;
			current_direccion = 0;
			current_area = 0;
			current_tipocontrato = 0;
			current_termino = 0;
			current_fin = 0;
			hist_foto.src = "";
			hist_dni.innerHTML = "";
			hist_nom.innerHTML = "";
			hist_ap.innerHTML = "";
			hist_am.innerHTML = "";

			btn_hist_contratar.style.display = "";
			btn_hist_finalizar.style.display = "";
			contratos_result_empty.style.display = "";
			contratos_result_error.style.display = "";
			try{ tabla_contratos.removeChild(tabla_contratos.getElementsByTagName("tbody")[0]) }
			catch (err) {}
			tabla_contratos.style.display = "";
			container_personal_historia.style.display = "";
		}

		function historial_get() {
			var formData = new FormData();
			formData.append("personal_historial",true);
			formData.append("per_current_id",current_id);
			$.ajax({
				url: 'http://www.psi.gob.pe/sipa/personal_control.php',
				type: 'POST',
				data: formData,
				cache: false,
				contentType: false,
				processData: false,
				error: function(e){
					console.log(e);
					contratos_result_error.style.display = "block";
					return true;
				},
				success: function(e){
					historial_fill(e);
				}
			});
		}

		function historial_fill(e) {
			var tmp = JSON.parse(e);
			var response = tmp.response;
			if (response == 0) {
				contratos_result_error.style.display = "block";
			} else if (response == 1) {
				var hc = tmp.message;
				var hc_len = hc.length;
				if (hc_len == 0) {
					btn_hist_contratar.style.display = "inline-block";
					contratos_result_empty.style.display = "block";
				} else {
					current_contrato = hc[0].FAKE;
					current_direccion = hc[0].FAKE;
					current_area = hc[0].FAKE;
					current_tipocontrato = hc[0].FAKE;
					current_termino = hc[0].FAKE.date;
					current_fin = hc[0].FAKE==null?null:hc[0].FAKE.date;
					//if (HOY < TERMINO || FIN != null) btn_hist_contratar.style.display = "inline-block";
					btn_hist_contratar.style.display = "inline-block";
					if (current_fin == null) btn_hist_finalizar.style.display = "inline-block";
					tabla_contratos.style.display = "table";
					var tbody = document.createElement("tbody");
					for (var i = 0; i < hc_len; i++) {
						var tr = tbody.insertRow();
						var hc_tc = tr.insertCell();
						var hc_d = tr.insertCell();
						var hc_a = tr.insertCell();
						var hc_doc = tr.insertCell();
						var hc_fi = tr.insertCell();
						var hc_pl = tr.insertCell();
						var hc_ft = tr.insertCell();
						var hc_ff = tr.insertCell();

						hc_tc.appendChild(document.createTextNode(hc[i].FAKE));
						var d_tt = document.createElement("div");
						var d_ttx = document.createElement("div");
						d_tt.classList.add("tooltip");
						d_ttx.classList.add("tooltiptext");
						d_tt.appendChild(document.createTextNode(hc[i].FAKE));
						d_ttx.appendChild(document.createTextNode(hc[i].FAKE.ucwords()));
						d_tt.appendChild(d_ttx);
						hc_d.appendChild(d_tt);
						var a_tt = document.createElement("div");
						var a_ttx = document.createElement("div");
						a_tt.classList.add("tooltip");
						a_ttx.classList.add("tooltiptext");
						a_tt.appendChild(document.createTextNode(hc[i].FAKE));
						a_ttx.appendChild(document.createTextNode(hc[i].FAKE.ucwords()));
						a_tt.appendChild(a_ttx);
						hc_a.appendChild(a_tt);
						hc_doc.appendChild(document.createTextNode(hc[i].FAKE));
						hc_fi.appendChild(document.createTextNode(object_date_format(hc[i].FAKE.date)));
						hc_pl.appendChild(document.createTextNode(hc[i].FAKE));
						hc_ft.appendChild(document.createTextNode(object_date_format(hc[i].FAKE.date)));
						hc_ff.appendChild(document.createTextNode(hc[i].FAKE==null?"--":object_date_format(hc[i].FAKE.date)));
					}
					tabla_contratos.appendChild(tbody);
				}
			}
		}

		function default_nuevo_contrato() {
			if (current_direccion != 0) {
				cont_reg_direccion.value = current_direccion;
				cont_reg_direccion.dataset.chosen = current_direccion;
			}
			filter_change_area(false);
			if (current_area != 0) {
				cont_reg_area.value = current_area;
				cont_reg_area.dataset.chosen = current_area;
			}
			if (current_tipocontrato != 0) {
				cont_reg_tipocontrato.value = current_tipocontrato;
				cont_reg_tipocontrato.dataset.chosen = current_tipocontrato;
			}
		}

		function clear_nuevo_contrato() {
			show_simple(server_error_contrato,false);
			cont_reg_direccion.classList.remove("input-invalido");
			cont_reg_area.classList.remove("input-invalido");
			cont_reg_tipocontrato.classList.remove("input-invalido");
			cont_reg_documento.classList.remove("input-invalido");
			cont_reg_inicio.classList.remove("input-invalido");
			cont_reg_plazo.classList.remove("input-invalido");
			cont_reg_direccion.value = 0;
			filter_change_area(false);
			cont_reg_tipocontrato.value = 0;
			cont_reg_tipocontrato.dataset.chosen = 0;
			cont_reg_documento.value = "";
			cont_reg_inicio.value = "";
			cont_reg_plazo.value = "";
			modal_contrato_registro.style.display = "";
		}

		function validar_nuevo_contrato() {
			go = true;
			show_simple(server_error_contrato,false);
			cont_reg_direccion.classList.remove("input-invalido");
			cont_reg_area.classList.remove("input-invalido");
			cont_reg_tipocontrato.classList.remove("input-invalido");
			cont_reg_documento.classList.remove("input-invalido");
			cont_reg_inicio.classList.remove("input-invalido");
			cont_reg_plazo.classList.remove("input-invalido");
			var direccion_val = cont_reg_direccion.value;
			var area_val = cont_reg_area.value;
			var tc_val = cont_reg_tipocontrato.value;
			var doc_val = cont_reg_documento.value.trim();
			var inicio_val = cont_reg_inicio.value;
			var plazo_val = cont_reg_plazo.value;
			if(plazo_val == null || plazo_val=="" || plazo_val < 0) {
				cont_reg_plazo.classList.add("input-invalido");
				cont_reg_plazo.focus();
				go = false;
			}
			if(inicio_val == null || inicio_val=="") {
				cont_reg_inicio.classList.add("input-invalido");
				cont_reg_inicio.focus();
				go = false;
			}
			if(doc_val == null || doc_val=="") {
				cont_reg_documento.classList.add("input-invalido");
				cont_reg_documento.focus();
				go = false;
			}
			if(tc_val == null || tc_val=="" || tc_val==0) {
				cont_reg_tipocontrato.classList.add("input-invalido");
				cont_reg_tipocontrato.focus();
				go = false;
			}
			if(area_val == null || area_val=="" || area_val==0) {
				cont_reg_area.classList.add("input-invalido");
				cont_reg_area.focus();
				go = false;
			}
			if(direccion_val == null || direccion_val=="" || direccion_val==0) {
				cont_reg_direccion.classList.add("input-invalido");
				cont_reg_direccion.focus();
				go = false;
			}
			return go;
		}

		function guardar_contrato() {
			if (!validar_nuevo_contrato()) return;
			loading();
			var formData = new FormData();
			formData.append("personal_guardar_contrato",true);
			formData.append("persona_id",current_id);
			formData.append("cont_reg_area",cont_reg_area.value);
			formData.append("cont_reg_tipocontrato",cont_reg_tipocontrato.value);
			formData.append("cont_reg_documento",cont_reg_documento.value);
			formData.append("cont_reg_inicio",cont_reg_inicio.value + "T00:00:00");
			formData.append("cont_reg_plazo",cont_reg_plazo.value);
			$.ajax({
				url: 'http://www.psi.gob.pe/sipa/personal_control.php',
				type: 'POST',
				data: formData,
				cache: false,
				contentType: false,
				processData: false,
				error: function(e){
					console.log(e);
					loading(false);
					show_simple(server_error_contrato);
					return true;
				},
				success: function(e){
					var result = JSON.parse(e);
					if (result.response == 0) {
						loading(false);
						show_simple(server_error_contrato);
					}
					window.location.href = "http://www.psi.gob.pe/sipa/personal.php?newdni=" + current_dni;
				}
			});
		}

		function finalizar_contrato() {
			loading();
			var formData = new FormData();
			formData.append("finalizar_contrato",true);
			formData.append("contrato_id",current_contrato);
			$.ajax({
				url: 'http://www.psi.gob.pe/sipa/personal_control.php',
				type: 'POST',
				data: formData,
				cache: false,
				contentType: false,
				processData: false,
				error: function(e){
					console.log(e);
					loading(false);
					show_simple(server_error_fin);
					return true;
				},
				success: function(e){
					var result = JSON.parse(e);
					if (result.response == 0) {
						console.log(result);
						loading(false);
						show_simple(server_error_fin);
					}else{
						window.location.href = "http://www.psi.gob.pe/sipa/personal.php?newdni=" + current_dni;
					}
				}
			});
		}

		function personal_filtrar() {
			var cc = 0;
			var tr = per_listado.getElementsByTagName("tr");
			input_tc = per_search_tipo_contrato.value;
			input_ec = per_search_estado_contrato.value;
			input_d = per_search_direccion.value;
			input_a = per_search_area.value;
			input_dni = per_search_dni.value.trim().split(" ");
			input_nom = per_search_nombre.value.trim().split(" ");
			for(var i = 1; i < tr.length; i++){
				var td = tr[i].getElementsByTagName("td");
				if (input_d != 0 && td[0].dataset.direccion_id != input_d) {
					tr[i].style.display = "none";
					continue;
				}
				if (input_a != 0 && td[1].dataset.area_id != input_a) {
					tr[i].style.display = "none";
					continue;
				}
				if (!words_in_string(td[2].innerHTML, input_dni)) {
					tr[i].style.display = "none";
					continue;
				}
				if (!words_in_string(td[3].innerHTML, input_nom)) {
					tr[i].style.display = "none";
					continue;
				}
				if (input_tc != 0 && td[4].dataset.tipocontrato_id != input_tc) {
					tr[i].style.display = "none";
					continue;
				}
				if (input_ec != 0 && tr[i].dataset.situacion != input_ec) {
					tr[i].style.display = "none";
					continue;
				}
				tr[i].style.display = "";
				cc++;
			}
			if (cc == 0){
				per_listado.style.display = "none";
				personas_result_empty.style.display = "block";
			} else {
				per_listado.style.display = "";
				personas_result_empty.style.display = "";
			}
		}
		function calcular_fecha_fin() {
			var inicio_val = cont_reg_inicio.value;
			var plazo_val = cont_reg_plazo.value;
			if (inicio_val == "" || plazo_val == "" || plazo_val < 0) {
				container_calculo_fecha.style.color = "";
				cont_reg_calculo_fecha.innerHTML = "--";
			} else {
				var tmpdate = new Date(inicio_val + " 00:00:00");
				tmpdate = tmpdate.addDays(parseInt(plazo_val,10));
				container_calculo_fecha.style.color = "darkslategray";
				cont_reg_calculo_fecha.innerHTML = "Termina el " + object_date_format(tmpdate);
			}
		}
		function object_date_format(mydate) {
			return new Date(mydate).toLocaleDateString("es-PE",opciones);
		}
		function words_in_string(string, keywords) {
			for(var i = 0; i < keywords.length; i++){
				if(!raw_string(string).includes(raw_string(keywords[i]))) return false;
			}
			return true;
		}
		function raw_string(str) {
			var str_norm = str.normalize('NFD').replace(/[\u0300-\u036f]/g, "");
			return str_norm.toUpperCase();
		}

		function set_input_filter(textbox, inputFilter) {
			["input", "keydown", "keyup", "mousedown", "mouseup", "select", "contextmenu", "drop"].forEach(function(event) {
				textbox.addEventListener(event, function() {
					if (inputFilter(this.value)) {
						this.oldValue = this.value;
						this.oldSelectionStart = this.selectionStart;
						this.oldSelectionEnd = this.selectionEnd;
					} else if (this.hasOwnProperty("oldValue")) {
						this.value = this.oldValue;
						this.setSelectionRange(this.oldSelectionStart, this.oldSelectionEnd);
					} else {
						this.value = "";
					}
				});
			});
		}
	</script>
</body>
</html>