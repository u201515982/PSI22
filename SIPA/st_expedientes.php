<?php
// TODO validate user session, authorization
include("st_expedientes_util.php");

$STNaturaleza_raw = sql_statement($STNaturaleza_query);
if ($STNaturaleza_raw['response'] == 0) {
	echo "HA OCURRIDO UN ERROR.<br>".$STNaturaleza_raw['message'];
	die();
}
$STTipoDocumento_raw = sql_statement($STTipoDocumento_query);
if ($STTipoDocumento_raw['response'] == 0) {
	echo "HA OCURRIDO UN ERROR.<br>".$STTipoDocumento_raw['message'];
	die();
}
$PerDireccion_raw = sql_statement($PerDireccion_query);
if ($PerDireccion_raw['response'] == 0) {
	echo "HA OCURRIDO UN ERROR.<br>".$PerDireccion_raw['message'];
	die();
}

$STNaturaleza = $STNaturaleza_raw['message'];
$STTipoDocumento = $STTipoDocumento_raw['message'];
$PerDireccion = $PerDireccion_raw['message'];

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
	<link href="https://fonts.googleapis.com/css?family=Roboto&display=swap" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css?family=Roboto+Slab&display=swap" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css2?family=Montserrat&display=swap" rel="stylesheet">
	<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
	<link rel="stylesheet" href="st_expedientes.css">
	<link rel="stylesheet" href="style_rd.css">
	<link rel="stylesheet" href="loading.css">
	<script src="loading.js"></script>
</head>
<body>
	<?php include('loading.php'); ?>

	<div id="server_error" style="display: none;"></div>

	<select id="res_select_area" style="display: none;">
		<option value="" disabled selected hidden>&nbsp;</option>
		<?php foreach ($PerDireccion as $item): ?>
			<option value="<?= $item['FAKE'] ?>"><?= $item['FAKE'] ?></option>
		<?php endforeach ?>
	</select>

	<div id="exp_buscar">
		<div class="form-title">Listado de <strong>Expedientes PAD</strong></div>
		<div style="display: inline-block;">
			<fieldset>
				<legend>BUSQUEDA</legend>
				<div>
					<input id="exp_buscar_num" type="text" placeholder="NRO. EXPEDIENTE" autocomplete="off" required>
					&nbsp;&nbsp;&nbsp;
					<input id="exp_buscar_anno" type="text" placeholder="AÑO EXPEDIENTE" autocomplete="off" required>
				</div>
				<div>
					<input id="exp_buscar_persona" type="text" placeholder="NOMBRE DE LA PERSONA" autocomplete="off" required>
				</div>
				<div>
					<button class="btn-black bigger-button" onclick="expediente_buscar_val();" style="flex-grow: 1;">Buscar</button>
				</div>
			</fieldset>
			<br>
			<button id="exp_buscar_btn_nuevo" class="icon blue" onclick="doc_buscar_show();"><i class="material-icons">add</i>Nuevo Expediente</button>
		</div>
		<div style="margin: 10px 0 0 2px; max-width: 1200px;">
			<table class="generic">
				<thead>
					<tr>
						<th>Nro. Expediente</th>
						<th>Nro. Documento</th>
						<th>Tipo Documento</th>
						<th>Naturaleza</th>
						<th>Asunto</th>
						<th>Personas</th>
						<th>Estado</th>
						<th>Ver</th>
						<!--<th>Modificar</th>-->
					</tr>
				</thead>
				<tbody id="exp_buscar_tbody_expedientes">
				</tbody>
			</table>
		</div>
	</div>

	<div id="exp_nuevo" style="display: none;">
		<div class="form-title">Registro de <strong>Expediente PAD</strong></div>
		<div style="margin-bottom: 20px;">
			<div style="display: flex; max-width: 1100px;">
				<div class="form-unit" style="margin-right: 30px;">
					<div class="mini-title">Registro de Fechas</div>
					<div class="mini-title-under"></div>
					<div class="reg-fechas">
						<div>
							<label class="obl">Recepción Sec. Tec.</label>
							<input id="exp_nuevo_fecha_st" type="date" required>
						</div>
						<div>
							<label>Recepción Adm.</label>
							<input id="exp_nuevo_fecha_adm" type="date" required>
							<div class="btn-input-delete">
								<i class="material-icons" onclick="reset_date(0);">close</i>
							</div>
						</div>
						<div>
							<label>Hechos</label>
							<input id="exp_nuevo_fecha_he" type="date" required>
							<div class="btn-input-delete">
								<i class="material-icons" onclick="reset_date(1);">close</i>
							</div>
						</div>
						<div>
							<label>Recepción PSI</label>
							<input id="exp_nuevo_fecha_psi" type="date" required>
							<div class="btn-input-delete">
								<i class="material-icons" onclick="reset_date(2);">close</i>
							</div>
						</div>
					</div>
				</div>
				<div id="exp_nuevo_doc" class="form-unit" style="flex: 1;">
					<div class="mini-title">Documento Origen</div>
					<div class="mini-title-under"></div>
					<div class="doc-origen">
						<div><div>Nro. DOCUMENTO</div><div id="doc_1"></div></div>
						<div><div>Nro. CUT</div><div id="doc_2"></div></div>
						<div><div>TIPO</div><div id="doc_3"></div></div>
						<div><div>FECHA</div><div id="doc_5"></div></div>
						<div><div>NATURALEZA</div><div id="doc_4"></div></div>
						<div><div>ASUNTO</div><div id="doc_6"></div></div>
						<div><div>PROCEDENCIA</div><div id="doc_7"></div></div>
						<div><div id="doc_8_name"></div><div id="doc_8"></div></div>
					</div>
				</div>
			</div>
		</div>
		<div class="form-unit" style="max-width: 1100px;">
			<div class="mini-title">Implicados</div>
			<div class="mini-title-under"></div>
			<div>
				<div style="margin-bottom: 10px; display: flex; align-items: center;">
					<input id="exp_nuevo_dni" type="text" placeholder="DNI" style="font-size: 15px; margin-right: 10px;" pattern="^[0-9]*$" maxlength="8" autocomplete="off" required>
					<button class="btn-blue bigger-button" onclick="dni_buscar_val();" style="margin: 0;">Agregar</button>
					<div id="exp_nuevo_dni_error"></div>
				</div>
				<div>
					<table class="generic">
						<thead>
							<tr>
								<th>Apellidos</th>
								<th>Nombres</th>
								<th>Domicilio</th>
								<th>Área</th>
								<th>Eliminar</th>
							</tr>
						</thead>
						<tbody id="exp_nuevo_tbody_implicados" class="tbody-implicados">
							
						</tbody>
					</table>
				</div>
			</div>
			
		</div>
		<div style="margin: 30px 0 30px 20px; display: inline-grid;">
			<button style="grid-row: 1; margin-right: 5px;" class="icon green" onclick="expediente_registrar_val();"><i class="material-icons">check</i>Registrar Expediente</button>
			<button style="grid-row: 1;" class="btn-black bigger-button" onclick="exp_nuevo_hide();">Cancelar</button>
		</div>
	</div>

	<div id="exp_ver" style="display: none;">
		<div class="form-title"><strong>Expediente PAD</strong></div>
		<div style="margin-bottom: 20px;">
			<div style="display: flex; max-width: 1100px;">
				<div class="form-unit" style="margin-right: 30px;">
					<div class="mini-title">Registro de Fechas</div>
					<div class="mini-title-under"></div>
					<div class="reg-fechas">
						<div>
							<label class="obl">Recepción Sec. Tec.</label>
							<input id="exp_ver_fecha_st" type="date" style="font-weight: bold;" disabled>
						</div>
						<div>
							<label>Recepción Adm.</label>
							<input id="exp_ver_fecha_adm" type="date" disabled>
						</div>
						<div>
							<label>Hechos</label>
							<input id="exp_ver_fecha_he" type="date" disabled>
						</div>
						<div>
							<label>Recepción PSI</label>
							<input id="exp_ver_fecha_psi" type="date" disabled>
						</div>
					</div>
				</div>
				<div id="exp_ver_doc" class="form-unit" style="flex: 1;">
					<div class="mini-title">Documento Origen</div>
					<div class="mini-title-under"></div>
					<div class="doc-origen">
						<div><div>Nro. DOCUMENTO</div><div id="v_doc_1"></div></div>
						<div><div>Nro. CUT</div><div id="v_doc_2"></div></div>
						<div><div>TIPO</div><div id="v_doc_3"></div></div>
						<div><div>FECHA</div><div id="v_doc_5"></div></div>
						<div><div>NATURALEZA</div><div id="v_doc_4"></div></div>
						<div><div>ASUNTO</div><div id="v_doc_6"></div></div>
						<div><div>PROCEDENCIA</div><div id="v_doc_7"></div></div>
						<div><div id="v_doc_8_name"></div><div id="v_doc_8"></div></div>
					</div>
				</div>
			</div>
		</div>
		<div class="form-unit" style="max-width: 1100px;">
			<div class="mini-title">Implicados</div>
			<div class="mini-title-under"></div>
			<div>
				<div>
					<table class="generic">
						<thead>
							<tr>
								<th>DNI</th>
								<th>Apellidos</th>
								<th>Nombres</th>
								<th>Domicilio</th>
								<th>Área</th>
							</tr>
						</thead>
						<tbody id="exp_ver_tbody_implicados" class="tbody-implicados">
							
						</tbody>
					</table>
				</div>
			</div>
			
		</div>
		<div style="margin: 30px 0 30px 20px;">
			<button class="btn-black bigger-button" onclick="exp_ver_hide();">Regresar</button>
		</div>
	</div>

	<div id="exp_mod" style="display: none;">
		<div class="form-title">Registro de <strong>Expediente PAD</strong></div>
		<div id="exp_mod_num"></div>
		<div style="margin-bottom: 20px;">
			<div style="display: flex; max-width: 1100px;">
				<div class="form-unit" style="margin-right: 30px;">
					<div class="mini-title">Registro de Fechas</div>
					<div class="mini-title-under"></div>
					<div class="reg-fechas">
						<div>
							<label class="obl">Recepción Sec. Tec.</label>
							<input id="exp_mod_fecha_st" type="date" required>
							<div class="btn-input-reset">
								<i class="material-icons" onclick="reset_date_mod(0);">replay</i>
							</div>
							<div id="exp_mod_fecha_st_orig" class="date-orig"></div>
						</div>
						<div>
							<label>Recepción Adm.</label>
							<input id="exp_mod_fecha_adm" type="date" required>
							<div class="btn-input-reset">
								<i class="material-icons" onclick="reset_date_mod(1);">replay</i>
							</div>
							<div id="exp_mod_fecha_adm_orig" class="date-orig"></div>
						</div>
						<div>
							<label>Hechos</label>
							<input id="exp_mod_fecha_he" type="date" required>
							<div class="btn-input-reset">
								<i class="material-icons" onclick="reset_date_mod(2);">replay</i>
							</div>
							<div id="exp_mod_fecha_he_orig" class="date-orig"></div>
						</div>
						<div>
							<label>Recepción PSI</label>
							<input id="exp_mod_fecha_psi" type="date" required>
							<div class="btn-input-reset">
								<i class="material-icons" onclick="reset_date_mod(3);">replay</i>
							</div>
							<div id="exp_mod_fecha_psi_orig" class="date-orig"></div>
						</div>
					</div>
				</div>
				<div id="exp_mod_doc" class="form-unit" style="flex: 1;">
					<div class="mini-title">Documento Origen</div>
					<div class="mini-title-under"></div>
					<div class="doc-origen">
						<div><div>Nro. DOCUMENTO</div><div id="m_doc_1"></div></div>
						<div><div>Nro. CUT</div><div id="m_doc_2"></div></div>
						<div><div>TIPO</div><div id="m_doc_3"></div></div>
						<div><div>FECHA</div><div id="m_doc_5"></div></div>
						<div><div>NATURALEZA</div><div id="m_doc_4"></div></div>
						<div><div>ASUNTO</div><div id="m_doc_6"></div></div>
						<div><div>PROCEDENCIA</div><div id="m_doc_7"></div></div>
						<div><div id="m_doc_8_name"></div><div id="m_doc_8"></div></div>
					</div>
				</div>
			</div>
		</div>
		<div class="form-unit" style="max-width: 1100px;">
			<div class="mini-title">Implicados</div>
			<div class="mini-title-under"></div>
			<div>
				<div style="margin-bottom: 10px; display: flex; align-items: center;">
					<input id="exp_mod_dni" type="text" placeholder="DNI" style="font-size: 15px; margin-right: 10px;" pattern="^[0-9]*$" maxlength="8" autocomplete="off" required>
					<button class="btn-blue bigger-button" onclick="dni_buscar_val();" style="margin: 0;">Agregar</button>
					<div id="exp_mod_dni_error"></div>
				</div>
				<div>
					<table class="generic">
						<thead>
							<tr>
								<th>DNI</th>
								<th>Apellidos</th>
								<th>Nombres</th>
								<th>Domicilio</th>
								<th>Área</th>
								<th>Eliminar</th>
							</tr>
						</thead>
						<tbody id="exp_mod_tbody_implicados" class="tbody-implicados">
							
						</tbody>
					</table>
				</div>
			</div>
			
		</div>
		<div style="margin: 30px 0 30px 20px;">
			<button class="btn-black bigger-button" onclick="exp_mod_hide();">Cancelar</button>
		</div>
	</div>

	<div id="doc_buscar" class="modal" style="display: none;">
		<div class="modal-content">
			<div class="form-title">Documento Origen</div>

			<div class="form-container">
				<div class="input-half">
					<label>Nro. Documento</label>
					<label>Nro. CUT</label>
				</div>
				<div class="input-half">
					<input id="doc_buscar_num" type="text" autocomplete="off" required>
					<input id="doc_buscar_cut" type="text" autocomplete="off" required>
				</div>
				<div class="input-half">
					<label>Tipo</label>
					<label>Fecha</label>
				</div>
				<div class="input-half">
					<select id="doc_buscar_tipo" required>
						<option value="" disabled selected hidden>&nbsp;</option>
						<?php foreach ($STTipoDocumento as $item): ?>
							<option value="<?= $item['FAKE'] ?>"><?= $item['FAKE'] ?></option>
						<?php endforeach ?>
					</select>
					<input id="doc_buscar_fecha" type="date" required>
				</div>
				<div style="display: flex; margin: 14px 0; width: 49%;">
					<button class="btn-blue bigger-button" onclick="documento_buscar_val();" style="margin-right: 10px; flex: 1;">Buscar</button>
					<button class="btn-black bigger-button" onclick="doc_buscar_hide();" style="flex: 1;">Cancelar</button>
				</div>
			</div>

			<div id="doc_buscar_no_result" style="display: none;">
				<div class="doc-result-alert">
					No se encontró el documento. ¿Desea registrarlo?
				</div>
				<div style="display: flex; width: 49%;">
					<button onclick="doc_nuevo_show();" class="icon blue" style="flex: 1;"><i class="material-icons">add</i>Registrar Documento</button>
				</div>
			</div>

			<div id="doc_buscar_multi_result" style="display: none;">
				<div class="doc-result-alert">
					Se encontró más de un documento.
				</div>
				<div id="doc_buscar_multi_result_op" class="wrap-content"></div>
				
			</div>
		</div>
	</div>

	<div id="doc_nuevo" class="modal" style="display: none;">
		<div class="modal-content">
			<div class="form-title">Nuevo Documento</div>
			<div class="form-container">
				<div class="input-half">
					<label>Nro. Documento</label>
					<label>Nro. CUT</label>
				</div>
				<div class="input-half">
					<input id="doc_nuevo_num" type="text" autocomplete="off" required>
					<input id="doc_nuevo_cut" type="text" autocomplete="off" required>
				</div>
				<div class="input-half">
					<label>Tipo</label>
					<label>Fecha</label>
				</div>
				<div class="input-half">
					<select id="doc_nuevo_tipo" required>
						<option value="" disabled selected hidden>&nbsp;</option>
						<?php foreach ($STTipoDocumento as $item): ?>
							<option value="<?= $item['FAKE'] ?>"><?= $item['FAKE'] ?></option>
						<?php endforeach ?>
					</select>
					<input id="doc_nuevo_fecha" type="date" required>
				</div>

				<div class="input-long">
					<label>Asunto</label>
					<textarea id="doc_nuevo_asunto" autocomplete="off" style="max-width: 99%;" required></textarea>
				</div>

				<div class="input-half">
					<label>Naturaleza</label>
					<label>Procedencia</label>
				</div>

				<div style="display: flex; align-items: end; margin-bottom: 10px;">
					<div class="input-mid">
						<select id="doc_nuevo_naturaleza" style="margin-bottom: 1px; padding: 10px;" required>
							<option value="" disabled selected hidden>&nbsp;</option>
							<?php foreach ($STNaturaleza as $item): ?>
								<option value="<?= $item['FAKE'] ?>"><?= $item['FAKE'] ?></option>
							<?php endforeach ?>
						</select>
					</div>
					<div class="toggle" style="/* margin-right: 5px; */">
						<input type="radio" name="radio_tipo" value="0" id="r_interno" checked="checked" hidden>
						<label for="r_interno">Interno</label>
						<input type="radio" name="radio_tipo" value="1" id="r_externo" hidden>
						<label for="r_externo">Externo</label>
						<input id="doc_nuevo_proc" type="text" value="0" hidden>
					</div>
				</div>

				<div class="input-long" style="height: 63px;">
					<label id="doc_nuevo_proc_label">Dirección</label>
					<select id="doc_nuevo_direccion" required>
						<option value="" disabled selected hidden>&nbsp;</option>
						<?php foreach ($PerDireccion as $item): ?>
							<option value="<?= $item['FAKE'] ?>"><?= $item['FAKE'] ?></option>
						<?php endforeach ?>
					</select>
					<input id="doc_nuevo_entidad" type="text" autocomplete="off" style="display: none;" required>
				</div>
			</div>
			
			<div style="margin-top: 30px; margin-bottom: 10px; display: inline-grid;">
				<button class="icon green" style="grid-row: 1; margin-right: 5px;" onclick="documento_registrar_val();">
					<i class="material-icons">check</i>Registrar Documento
				</button>
				<button class="btn-black bigger-button" style="grid-row: 1;" onclick="doc_nuevo_hide();">Cancelar</button>
			</div>
		</div>
	</div>

	<script>
		const STTipoDocumento = <?= json_encode($STTipoDocumento) ?>;
		const STNaturaleza = <?= json_encode($STNaturaleza) ?>;
		const PerDireccion = <?= json_encode($PerDireccion) ?>;

		var EXPEDIENTES_LIST = null;
		var IMPLICADOS_LIST = {};
		var res_select_area;
		var curr_doc_id = null;

		//EXP_BUSCAR
		var exp_buscar;
		var exp_buscar_num;
		var exp_buscar_anno;
		var exp_buscar_persona;
		var exp_buscar_tbody_expedientes;

		//EXP_NUEVO
		var exp_nuevo;
		var exp_nuevo_fecha_st;
		var exp_nuevo_fecha_adm;
		var exp_nuevo_fecha_he;
		var exp_nuevo_fecha_psi;
		var exp_nuevo_dni;
		var exp_nuevo_dni_error;
		var exp_nuevo_tbody_implicados;

		//DOC_BUSCAR
		var doc_buscar;
		var doc_buscar_num;
		var doc_buscar_cut;
		var doc_buscar_tipo;
		var doc_buscar_fecha;
		var doc_buscar_no_result;
		var doc_buscar_multi_result;
		var doc_buscar_multi_result_op;

		//DOC_NUEVO
		var doc_nuevo;
		var doc_nuevo_num;
		var doc_nuevo_cut;
		var doc_nuevo_tipo;
		var doc_nuevo_naturaleza;
		var doc_nuevo_fecha;
		var doc_nuevo_asunto;
		var doc_nuevo_proc;
		var doc_nuevo_proc_label;
		var doc_nuevo_direccion;
		var doc_nuevo_entidad;

		jQuery(document).ready(function(){
			res_select_area = document.getElementById("res_select_area");

			//EXP_BUSCAR
			exp_buscar = document.getElementById("exp_buscar");
			exp_buscar_num = document.getElementById("exp_buscar_num");
			exp_buscar_anno = document.getElementById("exp_buscar_anno");
			exp_buscar_persona = document.getElementById("exp_buscar_persona");
			exp_buscar_tbody_expedientes = document.getElementById("exp_buscar_tbody_expedientes");

			//EXP_NUEVO
			exp_nuevo = document.getElementById("exp_nuevo");
			exp_nuevo_fecha_st = document.getElementById("exp_nuevo_fecha_st");
			exp_nuevo_fecha_adm = document.getElementById("exp_nuevo_fecha_adm");
			exp_nuevo_fecha_he = document.getElementById("exp_nuevo_fecha_he");
			exp_nuevo_fecha_psi = document.getElementById("exp_nuevo_fecha_psi");
			exp_nuevo_dni = document.getElementById("exp_nuevo_dni");
			exp_nuevo_dni_error = document.getElementById("exp_nuevo_dni_error");
			exp_nuevo_tbody_implicados = document.getElementById("exp_nuevo_tbody_implicados");

			//DOC_BUSCAR
			doc_buscar = document.getElementById("doc_buscar");
			doc_buscar_num = document.getElementById("doc_buscar_num");
			doc_buscar_cut = document.getElementById("doc_buscar_cut");
			doc_buscar_tipo = document.getElementById("doc_buscar_tipo");
			doc_buscar_fecha = document.getElementById("doc_buscar_fecha");
			doc_buscar_no_result = document.getElementById("doc_buscar_no_result");
			doc_buscar_multi_result = document.getElementById("doc_buscar_multi_result");
			doc_buscar_multi_result_op = document.getElementById("doc_buscar_multi_result_op");

			//DOC_NUEVO
			doc_nuevo = document.getElementById("doc_nuevo");
			doc_nuevo_num = document.getElementById("doc_nuevo_num");
			doc_nuevo_cut = document.getElementById("doc_nuevo_cut");
			doc_nuevo_tipo = document.getElementById("doc_nuevo_tipo");
			doc_nuevo_naturaleza = document.getElementById("doc_nuevo_naturaleza");
			doc_nuevo_fecha = document.getElementById("doc_nuevo_fecha");
			doc_nuevo_asunto = document.getElementById("doc_nuevo_asunto");
			doc_nuevo_proc = document.getElementById("doc_nuevo_proc");
			doc_nuevo_proc_label = document.getElementById("doc_nuevo_proc_label");
			doc_nuevo_direccion = document.getElementById("doc_nuevo_direccion");
			doc_nuevo_entidad = document.getElementById("doc_nuevo_entidad");

			//--------
			exp_buscar_num.onkeypress = function(e) {if(e.keyCode == 13) expediente_buscar_val();}
			exp_buscar_anno.onkeypress = function(e) {if(e.keyCode == 13) expediente_buscar_val();}
			exp_buscar_persona.onkeypress = function(e) {if(e.keyCode == 13) expediente_buscar_val();}

			exp_nuevo_dni.onkeypress = function(e) {if(e.keyCode == 13) dni_buscar_val();}

			doc_buscar_num.onkeypress = function(e) {if(e.keyCode == 13) documento_buscar_val();}
			doc_buscar_cut.onkeypress = function(e) {if(e.keyCode == 13) documento_buscar_val();}
			doc_buscar_tipo.onkeypress = function(e) {if(e.keyCode == 13) documento_buscar_val();}
			doc_buscar_fecha.onkeypress = function(e) {if(e.keyCode == 13) documento_buscar_val();}

			/*window.onclick = function(event) {
				if (event.target == doc_buscar) {
					hide_doc_buscar();
				}
				else if (event.target == doc_nuevo) {
					hide_doc_nuevo();
				}
			}*/

			const radioButtons = document.querySelectorAll('input[name="radio_tipo"]');
			for(const radioButton of radioButtons){
				radioButton.addEventListener('change', doc_procedencia);
			}
		});

		function doc_procedencia(e) {proc_campo(e.target.value);}
		function proc_campo(i) {
			if(i == 0) {
				doc_nuevo_proc.value = "0";
				doc_nuevo_proc_label.innerHTML = "Dirección";
				doc_nuevo_entidad.value = "";
				doc_nuevo_entidad.style.display = "none";
				doc_nuevo_direccion.style.display = "block";
			}
			else if(i == 1) {
				doc_nuevo_proc.value = "1";
				doc_nuevo_proc_label.innerHTML = "Entidad";
				doc_nuevo_direccion.value = 0;
				doc_nuevo_direccion.style.display = "none";
				doc_nuevo_entidad.style.display = "block";
			}
		}

		function doc_buscar_show() {doc_buscar.style.display = "block"; document.getElementsByTagName("body")[0].style.overflow = "hidden";}
		function doc_buscar_hide() {
			document.getElementsByTagName("body")[0].style.overflow = "auto";
			doc_buscar_multi_result_hide();
			doc_buscar_no_result_hide();
			doc_buscar.style.display = "none";
			doc_buscar_num.value = "";
			doc_buscar_cut.value = "";
			doc_buscar_tipo.value = "";
			doc_buscar_fecha.value = "";
		}

		function doc_buscar_no_result_show() {doc_buscar_no_result.style.display = "block";}
		function doc_buscar_no_result_hide() {doc_buscar_no_result.style.display = "none";}

		function doc_buscar_multi_result_show() {doc_buscar_multi_result.style.display = "block";}
		function doc_buscar_multi_result_hide() {
			doc_buscar_multi_result_op.scrollTop = 0;
			doc_buscar_multi_result.style.display = "none";
			doc_buscar_multi_result_op.innerHTML = "";
		}

		function documento_buscar_val() {
			if (!doc_buscar_num.validity.valid &&
				!doc_buscar_cut.validity.valid &&
				!doc_buscar_tipo.validity.valid &&
				!doc_buscar_fecha.validity.valid)
				return;
			if (doc_buscar_num.value.trim() == "" &&
				doc_buscar_cut.value.trim() == "" &&
				doc_buscar_tipo.value.trim() == "" &&
				doc_buscar_fecha.value.trim() == "")
				return;
			documento_buscar();
		}
		function documento_buscar() {
			doc_buscar_no_result_hide();
			doc_buscar_multi_result_hide();
			loading(true);
			var formData = new FormData();
			formData.append("documento_buscar",true);
			formData.append("doc_buscar_num",doc_buscar_num.value.trim());
			formData.append("doc_buscar_cut",doc_buscar_cut.value.trim());
			formData.append("doc_buscar_tipo",doc_buscar_tipo.value);
			formData.append("doc_buscar_fecha",doc_buscar_fecha.value);
			$.ajax({
				url: 'http://localhost/sipa/st_expedientes_control.php',
				type: 'POST',
				data: formData,
				cache: false,
				contentType: false,
				processData: false,
				error: function(e){
					console.log(e);
					loading(false);
					return true;
				},
				success: function(e){
					documento_buscar_result(e);
					loading(false);
				}
			});
		}
		function documento_buscar_result(e) {
			var result = JSON.parse(e);
			if (!result.response) {
				document.getElementById("server_error").innerHTML = result.message;
				document.getElementById("server_error").style.display = "block";
			}
			else if (result.response) {
				if (result.message.length == 0) {
					doc_buscar_no_result_show();
				}
				else if (result.message.length == 1) {
					exp_nuevo_show(result.message[0]);
				}
				else if (result.message.length > 1) {
					doc_buscar_multi_result_show();
					var ops = result.message;
					for (var i = 0; i < ops.length; i++) {
						var doc_previz = document.createElement("div");
						var dpv_info = document.createElement("div");
						var table = document.createElement("table");
						
						var tr1 = document.createElement("tr");
						var th1 = document.createElement("th");
						var td1 = document.createElement("td");
						
						var tr2 = document.createElement("tr");
						var th2 = document.createElement("th");
						var td2 = document.createElement("td");
						
						var tr3 = document.createElement("tr");
						var th3 = document.createElement("th");
						var td3 = document.createElement("td");
						
						var tr4 = document.createElement("tr");
						var th4 = document.createElement("th");
						var td4 = document.createElement("td");

						var dpv_btn = document.createElement("div");
						var icon = document.createElement("i");

						th1.innerHTML = "Nro. Documento";
						th2.innerHTML = "Nro. CUT";
						th3.innerHTML = "Tipo";
						th4.innerHTML = "Fecha";

						td1.innerHTML = ops[i].FAKE;
						td2.innerHTML = ops[i].FAKE;
						var aux = STTipoDocumento.findIndex(function(item,j) {return item.TipoDocumentoId === ops[i].FAKE;});
						td3.innerHTML = STTipoDocumento[aux].FAKE;
						td4.innerHTML = new Date(ops[i].FAKE.date).toLocaleDateString("es-PE",{day:'2-digit',month:'2-digit',year:'numeric'});;

						tr1.appendChild(th1);
						tr1.appendChild(td1);
						tr2.appendChild(th2);
						tr2.appendChild(td2);
						tr3.appendChild(th3);
						tr3.appendChild(td3);
						tr4.appendChild(th4);
						tr4.appendChild(td4);

						table.appendChild(tr1);
						table.appendChild(tr2);
						table.appendChild(tr3);
						table.appendChild(tr4);

						dpv_info.classList.add("dpv-info");
						dpv_info.appendChild(table);
						doc_previz.classList.add("doc-previz");
						doc_previz.appendChild(dpv_info);

						icon.classList.add("material-icons");
						icon.innerHTML = "forward";
						dpv_btn.classList.add("dpv-btn");
						dpv_btn.appendChild(icon);
						dpv_btn.dataset.doc_id = i;
						dpv_btn.onclick = function(e) {
							exp_nuevo_show(ops[this.dataset.doc_id]);
						}
						doc_previz.appendChild(dpv_btn);

						doc_buscar_multi_result_op.appendChild(doc_previz);
					}
				}
			}
		}

		function doc_nuevo_show() {
			doc_nuevo_num.value = doc_buscar_num.value;
			doc_nuevo_cut.value = doc_buscar_cut.value;
			doc_nuevo_tipo.value = doc_buscar_tipo.value;
			doc_nuevo_fecha.value = doc_buscar_fecha.value;

			doc_nuevo.style.display = "block";

			doc_buscar_hide();
		}
		function doc_nuevo_hide() {
			doc_nuevo.style.display = "none";
			doc_nuevo_num.value = "";
			doc_nuevo_cut.value = "";
			doc_nuevo_tipo.value = "";
			doc_nuevo_naturaleza.value = "";
			doc_nuevo_fecha.value = "";
			doc_nuevo_asunto.value = "";
			doc_nuevo_direccion.value = "";
			doc_nuevo_entidad.value = "";
			doc_nuevo_proc.value = "0";
			proc_campo(0);
			document.getElementById("r_interno").checked = true;
		}

		function exp_nuevo_show(doc) {
			console.log(doc);
			curr_doc_id = doc.FAKE;
			document.getElementById('doc_1').innerHTML = doc.FAKE;
			document.getElementById('doc_2').innerHTML = doc.FAKE;

			var d_3 = STTipoDocumento.findIndex(function(item,i){return item.FAKE === doc.FAKE;});
			document.getElementById('doc_3').innerHTML = STTipoDocumento[d_3].FAKE;
			
			var d_4 = STNaturaleza.findIndex(function(item,i){return item.FAKE === doc.FAKE;});
			document.getElementById('doc_4').innerHTML = STNaturaleza[d_4].FAKE;
			
			document.getElementById('doc_5').innerHTML = new Date(doc.FAKE.date).toLocaleDateString("es-PE",{day:'2-digit',month:'2-digit',year:'numeric'});
			document.getElementById('doc_6').innerHTML = doc.FAKE;
			document.getElementById('doc_7').innerHTML = doc.FAKE == 0?"Interno":"Externo";
			var d_8 = PerDireccion.findIndex(function(item,i){return item.FAKE === doc.FAKE;});
			console.log(d_8);
			if(doc.Interno == 0) {
				document.getElementById('doc_8_name').innerHTML = "DIRECCIÓN";
				document.getElementById('doc_8').innerHTML = PerDireccion[d_8].FAKE.toLowerCase();
			}
			else {
				document.getElementById('doc_8_name').innerHTML = "ENTIDAD";
				document.getElementById('doc_8').innerHTML = doc.FAKE.toLowerCase();
			}
			exp_buscar.style.display = "none";
			doc_buscar_hide();
			doc_nuevo_hide();
			exp_nuevo.style.display = "block";
		}
		function exp_nuevo_hide() {
			exp_nuevo_fecha_st.value = "";
			exp_nuevo_fecha_adm.value = "";
			exp_nuevo_fecha_he.value = "";
			exp_nuevo_fecha_psi.value = "";

			exp_nuevo_dni.value = "";
			exp_nuevo_dni_error.innerHTML = "";

			curr_doc_id = null;

			exp_nuevo_tbody_implicados.innerHTML = "";
			IMPLICADOS_LIST = {};

			exp_buscar.style.display = "block";
			exp_nuevo.style.display = "none";
		}

		function documento_registrar_val() {
			if(!doc_nuevo_num.validity.valid) return;
			if(!doc_nuevo_cut.validity.valid) return;
			if(!doc_nuevo_tipo.validity.valid) return;
			if(!doc_nuevo_fecha.validity.valid) return;
			if(!doc_nuevo_asunto.validity.valid) return;
			if(!doc_nuevo_naturaleza.validity.valid) return;
			if(doc_nuevo_proc.value == '0' && !doc_nuevo_direccion.validity.valid) return;
			if(doc_nuevo_proc.value == '1' && !doc_nuevo_entidad.validity.valid) return;
			documento_registrar();
		}
		function documento_registrar() {
			loading(true);
			var formData = new FormData();
			formData.append("documento_registrar",true);
			formData.append("doc_nuevo_num",doc_nuevo_num.value.trim());
			formData.append("doc_nuevo_cut",doc_nuevo_cut.value.trim().toUpperCase());
			formData.append("doc_nuevo_tipo",doc_nuevo_tipo.value);
			formData.append("doc_nuevo_naturaleza",doc_nuevo_naturaleza.value);
			formData.append("doc_nuevo_fecha",doc_nuevo_fecha.value);
			formData.append("doc_nuevo_asunto",doc_nuevo_asunto.value.trim());
			formData.append("doc_nuevo_proc",parseInt(doc_nuevo_proc.value));
			formData.append("doc_nuevo_direccion",doc_nuevo_direccion.value);
			formData.append("doc_nuevo_entidad",doc_nuevo_entidad.value.trim());
			$.ajax({
				url: 'http://localhost/sipa/st_expedientes_control.php',
				type: 'POST',
				data: formData,
				cache: false,
				contentType: false,
				processData: false,
				error: function(e){
					console.log(e);
					loading(false);
					return true;
				},
				success: function(e){
					documento_registrar_result(e);
					loading(false);
				}
			});
		}
		function documento_registrar_result(e) {
			var result = JSON.parse(e);
			if (!result.response) {
				document.getElementById("server_error").innerHTML = result.message;
				document.getElementById("server_error").style.display = "block";
			}
			else if (result.response) {
				exp_nuevo_show(result.message[0]);
			}
		}

		function dni_buscar_val() {
			exp_nuevo_dni_error.innerHTML = "";
			if (!exp_nuevo_dni.validity.valid) return;
			if (exp_nuevo_dni.value.length != 8) return;
			if (Object.keys(IMPLICADOS_LIST).includes(exp_nuevo_dni.value)) {
				exp_nuevo_dni_error.innerHTML = "YA SE ENCUENTRA EN LA LISTA.";
				return;
			}
			dni_buscar();
		}
		function dni_buscar() {
			exp_nuevo_dni.blur();
			exp_nuevo_dni_error.innerHTML = "";
			loading(true);
			var formData = new FormData();
			formData.append("dni_buscar",true);
			formData.append("exp_nuevo_dni",exp_nuevo_dni.value);
			$.ajax({
				url: 'http://localhost/sipa/st_expedientes_control.php',
				type: 'POST',
				data: formData,
				cache: false,
				contentType: false,
				processData: false,
				error: function(e){
					console.log(e);
					loading(false);
					return true;
				},
				success: function(e){
					dni_buscar_result(e);
					loading(false);
					exp_nuevo_dni.focus();
				}
			});
		}
		function dni_buscar_result(pide) {
			var curr_dni = exp_nuevo_dni.value;
			var pide = JSON.parse(pide);
			if(pide.response == 2 || pide.response == 3) {
				exp_nuevo_dni_error.innerHTML = pide.message;
			}
			else if (pide.response == 4) {
				var implicado = pide.message;
				if(Object.keys(IMPLICADOS_LIST).includes(curr_dni)) {
					exp_nuevo_dni_error.innerHTML = "YA SE ENCUENTRA EN LA LISTA.";
				} else {
					IMPLICADOS_LIST[curr_dni] = implicado;
					implicados_fill(curr_dni);
				}
			}
		}
		function implicados_fill(dni) {
			var tr = document.createElement("tr");
			var td1 = document.createElement("td");
			var td2 = document.createElement("td");
			var td3 = document.createElement("td");
			var td4 = document.createElement("td");
			var select = document.createElement("select");
			var td5 = document.createElement("td");
			var div = document.createElement("div");
			var icon = document.createElement("i");

			td1.innerHTML = IMPLICADOS_LIST[dni].FAKE.toLowerCase() + " " + IMPLICADOS_LIST[dni].FAKE.toLowerCase();
			td2.innerHTML = IMPLICADOS_LIST[dni].FAKE.toLowerCase();
			td3.innerHTML = IMPLICADOS_LIST[dni].FAKE.toLowerCase();
			select.innerHTML = res_select_area.innerHTML;
			select.required = true;
			select.onchange = function(e) {
				IMPLICADOS_LIST[dni]['AREA'] = e.target.value;
			}
			td4.appendChild(select);

			div.classList.add("btn-input-delete");
			icon.classList.add("material-icons");
			icon.innerHTML = "close";
			icon.onclick = function(e) {
				var row_index = this.parentNode.parentNode.parentNode,rowIndex;
				exp_nuevo_tbody_implicados.deleteRow(row_index);
				delete IMPLICADOS_LIST[dni];
			}

			div.appendChild(icon);
			td5.appendChild(div);

			tr.appendChild(td1);
			tr.appendChild(td2);
			tr.appendChild(td3);
			tr.appendChild(td4);
			tr.appendChild(td5);

			exp_nuevo_tbody_implicados.appendChild(tr);
		}

		function reset_date(n) {
			switch (n) {
				case 0:
					exp_nuevo_fecha_adm.value = "";
					break;
				case 1:
					exp_nuevo_fecha_he.value = "";
					break;
				case 2:
					exp_nuevo_fecha_psi.value = "";
					break;
			}
		}

		function reset_date_mod(n) {
			switch (n) {
				case 0:
					exp_mod_fecha_st.value = exp_mod_fecha_st.dataset.orig;
					break;
				case 1:
					exp_mod_fecha_adm.value = exp_mod_fecha_adm.dataset.orig;
					break;
				case 2:
					exp_mod_fecha_he.value = exp_mod_fecha_he.dataset.orig;
					break;
				case 3:
					exp_mod_fecha_psi.value = exp_mod_fecha_psi.dataset.orig;
					break;
			}
		}

		function expediente_registrar_val() {
			//TODO validar
			if(!exp_nuevo_fecha_st.validity.valid) return;
			if(jQuery.isEmptyObject(IMPLICADOS_LIST)) return;
			for(var key in IMPLICADOS_LIST) {if(!IMPLICADOS_LIST[key].hasOwnProperty('FAKE')) return;}
			expediente_registrar();
		}
		function expediente_registrar() {
			loading(true);
			var formData = new FormData();
			formData.append("expediente_registrar",true);
			formData.append("IMPLICADOS_LIST",JSON.stringify(IMPLICADOS_LIST));
			formData.append("curr_doc_id",curr_doc_id);
			formData.append("exp_nuevo_fecha_st",exp_nuevo_fecha_st.value);
			formData.append("exp_nuevo_fecha_adm",exp_nuevo_fecha_adm.value);
			formData.append("exp_nuevo_fecha_he",exp_nuevo_fecha_he.value);
			formData.append("exp_nuevo_fecha_psi",exp_nuevo_fecha_psi.value);
			$.ajax({
				url: 'http://localhost/sipa/st_expedientes_control.php',
				type: 'POST',
				data: formData,
				cache: false,
				contentType: false,
				processData: false,
				error: function(e){
					console.log(e);
					loading(false);
					return true;
				},
				success: function(e){
					location.reload();
					loading(false);
				}
			});
		}
		function expediente_buscar_val() {
			expediente_buscar();
		}
		function expediente_buscar() {
			loading(true);
			var formData = new FormData();
			formData.append("expediente_buscar",true);
			formData.append("exp_buscar_num",exp_buscar_num.value.trim());
			formData.append("exp_buscar_anno",exp_buscar_anno.value.trim());
			formData.append("exp_buscar_persona",exp_buscar_persona.value.trim());
			$.ajax({
				url: 'http://localhost/sipa/st_expedientes_control.php',
				type: 'POST',
				data: formData,
				cache: false,
				contentType: false,
				processData: false,
				error: function(e){
					console.log(e);
					loading(false);
					return true;
				},
				success: function(e){
					expediente_buscar_result(e);
					loading(false);
				}
			});
		}
		function expediente_buscar_result(e) {
			var result = JSON.parse(e);
			if (!result.response) {
				document.getElementById("server_error").innerHTML = result.message;
				document.getElementById("server_error").style.display = "block";
			}
			else if (result.response) {
				expedientes_fill(result.message);
			}
		}
		function expedientes_fill(list_exp){
			EXPEDIENTES_LIST = list_exp.slice();
			exp_buscar_tbody_expedientes.innerHTML = "";
			act_exp_nro = null;
			act_exp_anno = null;
			for (var i = 0; i < list_exp.length; i++) {
				if(list_exp[i].FAKE == act_exp_nro && list_exp[i].FAKE == act_exp_anno) continue;
				act_exp_nro = list_exp[i].FAKE;
				act_exp_anno = list_exp[i].FAKE;

				var tr = document.createElement("tr");
				var td1 = document.createElement("td");
				var td2 = document.createElement("td");
				var td3 = document.createElement("td");
				var td4 = document.createElement("td");
				var td5 = document.createElement("td");
				var td6 = document.createElement("td");
				var td7 = document.createElement("td");
				var td8 = document.createElement("td");
				var td9 = document.createElement("td");

				td1.innerHTML = list_exp[i].FAKE + "-" + list_exp[i].FAKE;
				td2.innerHTML = list_exp[i].FAKE;
				td3.innerHTML = list_exp[i].FAKE;
				td4.innerHTML = list_exp[i].FAKE;
				td5.innerHTML = list_exp[i].FAKE;

				/*ESTADO PENDIENTE*/
				td7.innerHTML = "- -";
				/*----------------*/

				td8.dataset.exid = list_exp[i].Expedienteid;
				td8.onclick = function() {expediente_ver(this.dataset.exid);};
				var icon1 = document.createElement("i");
				icon1.innerHTML = "open_in_new";
				icon1.classList.add("material-icons");
				td8.appendChild(icon1);


				var btn_mod = document.createElement("button");
				btn_mod.innerHTML = "Mod";
				btn_mod.dataset.exid = list_exp[i].FAKE;
				btn_mod.onclick = function() {expediente_mod(this.dataset.exid);};
				td9.appendChild(btn_mod);

				for (var j = 0; j < list_exp.length; j++) {
					if(list_exp[j].FAKE != act_exp_nro || list_exp[j].FAKE != act_exp_anno) continue;
					var div = document.createElement("div");
					var icon = document.createElement("i");
					icon.classList.add("material-icons");
					icon.innerHTML = "person";
					div.appendChild(icon);
					var nombre_txt = list_exp[j].FAKE + " " + list_exp[j]["FAKE"] + " " + list_exp[j]["FAKE"];
					var nombre_node = document.createTextNode(nombre_txt);
					div.appendChild(nombre_node);
					td6.appendChild(div);
				}

				tr.appendChild(td1);
				tr.appendChild(td2);
				tr.appendChild(td3);
				tr.appendChild(td4);
				tr.appendChild(td5);
				tr.appendChild(td6);
				tr.appendChild(td7);
				tr.appendChild(td8);
				//tr.appendChild(td9);

				exp_buscar_tbody_expedientes.appendChild(tr);
			}
		}
		function expediente_ver(e) {
			var exp_single_index = EXPEDIENTES_LIST.findIndex(function(item,i){return item.FAKE === parseInt(e);});
			var exp_single = EXPEDIENTES_LIST[exp_single_index];
			//DOCUMENTO ORIGEN
			document.getElementById('v_doc_1').innerHTML = exp_single.FAKE;
			document.getElementById('v_doc_2').innerHTML = exp_single.FAKE;
			document.getElementById('v_doc_3').innerHTML = exp_single.FAKE.toLowerCase();
			document.getElementById('v_doc_4').innerHTML = exp_single.FAKE.toLowerCase();
			document.getElementById('v_doc_5').innerHTML = new Date(exp_single.FAKE.date).toLocaleDateString("es-PE",{day:'2-digit',month:'2-digit',year:'numeric'});
			document.getElementById('v_doc_6').innerHTML = exp_single.FAKE;
			document.getElementById('v_doc_7').innerHTML = exp_single.FAKE == 0?"Interno":"Externo";
			var d_8 = PerDireccion.findIndex(function(item,i){return item.FAKE === exp_single.FAKE;});
			if(exp_single.FAKE == 0) {
				document.getElementById('v_doc_8_name').innerHTML = "DIRECCIÓN";
				document.getElementById('v_doc_8').innerHTML = PerDireccion[d_8].FAKE.toLowerCase();
			}
			else {
				document.getElementById('v_doc_8_name').innerHTML = "ENTIDAD";
				document.getElementById('v_doc_8').innerHTML = exp_single.FAKE.toLowerCase();
			}
			//FECHAS
			document.getElementById("exp_ver_fecha_st").value = exp_single.FAKE.date.split(" ")[0];
			if(exp_single.FAKE != null) {
				document.getElementById("exp_ver_fecha_adm").value = exp_single.FAKE.date.split(" ")[0];
				document.getElementById("exp_ver_fecha_adm").style.fontWeight = "bold";
			}
			if(exp_single.FAKE != null) {
				document.getElementById("exp_ver_fecha_he").value = exp_single.FAKE.date.split(" ")[0];
				document.getElementById("exp_ver_fecha_he").style.fontWeight = "bold";
			}
			if(exp_single.FAKE != null) {
				document.getElementById("exp_ver_fecha_psi").value = exp_single.FAKE.date.split(" ")[0];
				document.getElementById("exp_ver_fecha_psi").style.fontWeight = "bold";
			}
			//IMPLICADOS
			for (var i = 0; i < EXPEDIENTES_LIST.length; i++) {
				if(EXPEDIENTES_LIST[i].FAKE != e) continue;
				var tr = document.createElement("tr");
				var td1 = document.createElement("td");
				var td2 = document.createElement("td");
				var td3 = document.createElement("td");
				var td4 = document.createElement("td");
				var td5 = document.createElement("td");
				var div = document.createElement("div");
				var span = document.createElement("span");

				td1.innerHTML = EXPEDIENTES_LIST[i].FAKE
				td2.innerHTML = EXPEDIENTES_LIST[i]["FAKE"].toLowerCase() + " " + EXPEDIENTES_LIST[i]["APELLIDO MATERNO"].toLowerCase();
				td3.innerHTML = EXPEDIENTES_LIST[i].FAKE.toLowerCase();
				td4.innerHTML = EXPEDIENTES_LIST[i].FAKE.toLowerCase();
				div.classList.add("tooltip");
				span.classList.add("tooltiptext");
				var aux = EXPEDIENTES_LIST[i].FAKE;
				var p_d = PerDireccion.findIndex(function(item,i){return item.FAKE === aux;});
				span.innerHTML = PerDireccion[p_d].FAKE;
				div.appendChild(document.createTextNode(PerDireccion[p_d].FAKE));
				div.appendChild(span);
				td5.appendChild(div);

				tr.appendChild(td1);
				tr.appendChild(td2);
				tr.appendChild(td3);
				tr.appendChild(td4);
				tr.appendChild(td5);

				document.getElementById("exp_ver_tbody_implicados").appendChild(tr);
			}

			document.getElementById("exp_buscar").style.display = "none";
			document.getElementById("exp_ver").style.display = "block";
		}
		function exp_ver_hide() {
			document.getElementById("exp_ver_fecha_adm").value = "";
			document.getElementById("exp_ver_fecha_he").value = ""
			document.getElementById("exp_ver_fecha_psi").value = "";
			document.getElementById("exp_ver_fecha_adm").style.fontWeight = "normal";
			document.getElementById("exp_ver_fecha_he").style.fontWeight = "normal";
			document.getElementById("exp_ver_fecha_psi").style.fontWeight = "normal";
			document.getElementById("exp_ver_tbody_implicados").innerHTML = "";

			document.getElementById("exp_ver").style.display = "none";
			document.getElementById("exp_buscar").style.display = "block";
		}
		function expediente_mod(e){
			var exp_single_index = EXPEDIENTES_LIST.findIndex(function(item,i){return item.FAKE === parseInt(e);});
			var exp_single = EXPEDIENTES_LIST[exp_single_index];
			//DOCUMENTO ORIGEN
			document.getElementById('m_doc_1').innerHTML = exp_single.FAKE;
			document.getElementById('m_doc_2').innerHTML = exp_single.FAKE;
			document.getElementById('m_doc_3').innerHTML = exp_single.FAKE.toLowerCase();
			document.getElementById('m_doc_4').innerHTML = exp_single.FAKE.toLowerCase();
			document.getElementById('m_doc_5').innerHTML = new Date(exp_single.FAKE.date).toLocaleDateString("es-PE",{day:'2-digit',month:'2-digit',year:'numeric'});
			document.getElementById('m_doc_6').innerHTML = exp_single.FAKE;
			document.getElementById('m_doc_7').innerHTML = exp_single.FAKE == 0?"Interno":"Externo";
			var d_8 = PerDireccion.findIndex(function(item,i){return item.DIRECCIONID === exp_single.FAKE;});
			if(exp_single.FAKE == 0) {
				document.getElementById('m_doc_8_name').innerHTML = "DIRECCIÓN";
				document.getElementById('m_doc_8').innerHTML = PerDireccion[d_8].FAKE.toLowerCase();
			}
			else {
				document.getElementById('m_doc_8_name').innerHTML = "ENTIDAD";
				document.getElementById('m_doc_8').innerHTML = exp_single.FAKE.toLowerCase();
			}
			//FECHAS
			document.getElementById("exp_mod_fecha_st").dataset.orig = exp_single.FAKE.date.split(" ")[0];
			document.getElementById("exp_mod_fecha_adm").dataset.orig = exp_single.FAKE == null?"":exp_single.FAKE.date.split(" ")[0];
			document.getElementById("exp_mod_fecha_he").dataset.orig = exp_single.FAKE == null?"":exp_single.FAKE.date.split(" ")[0];
			document.getElementById("exp_mod_fecha_psi").dataset.orig = exp_single.FAKE == null?"":exp_single.FAKE.date.split(" ")[0];

			document.getElementById("exp_mod_fecha_st_orig").innerHTML = new Date(exp_single.FAKE.date).toLocaleDateString("es-PE",{day:'2-digit',month:'2-digit',year:'numeric'});
			document.getElementById("exp_mod_fecha_adm_orig").innerHTML = exp_single.FAKE == null?"--":new Date(exp_single.FAKE.date).toLocaleDateString("es-PE",{day:'2-digit',month:'2-digit',year:'numeric'});
			document.getElementById("exp_mod_fecha_he_orig").innerHTML = exp_single.FAKE == null?"--":new Date(exp_single.FAKE.date).toLocaleDateString("es-PE",{day:'2-digit',month:'2-digit',year:'numeric'});
			document.getElementById("exp_mod_fecha_psi_orig").innerHTML = exp_single.FAKE == null?"--":new Date(exp_single.FAKE.date).toLocaleDateString("es-PE",{day:'2-digit',month:'2-digit',year:'numeric'});

			document.getElementById("exp_mod_fecha_st").value = exp_single.FAKE.date.split(" ")[0];
			if(exp_single.FAKE != null) document.getElementById("exp_mod_fecha_adm").value = exp_single.FAKE.date.split(" ")[0];
			if(exp_single.FAKE != null) document.getElementById("exp_mod_fecha_he").value = exp_single.FAKE.date.split(" ")[0];
			if(exp_single.FAKE != null) document.getElementById("exp_mod_fecha_psi").value = exp_single.FAKE.date.split(" ")[0];
			//IMPLICADOS


			document.getElementById("exp_buscar").style.display = "none";
			document.getElementById("exp_mod").style.display = "block";
		}
		function exp_mod_hide() {
			document.getElementById("exp_mod_fecha_adm").value = "";
			document.getElementById("exp_mod_fecha_he").value = ""
			document.getElementById("exp_mod_fecha_psi").value = "";
			document.getElementById("exp_mod_tbody_implicados").innerHTML = "";

			document.getElementById("exp_mod").style.display = "none";
			document.getElementById("exp_buscar").style.display = "block";
		}
	</script>
</body>
</html>