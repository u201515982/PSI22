<?php
// TODO Validate user session, authorization
include("personal_util.php");
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
	exit;
}
if (isset($_POST["personal_buscar_dni"])) {
	$per_reg_dni = $_POST["per_reg_dni"];
	if (!validar_input_dni($per_reg_dni)) die();
	$response = search_personal_dni($per_reg_dni);
	echo json_encode($response);
}
else if (isset($_POST["personal_historial"])) {
	$per_current_id = $_POST["per_current_id"];
	$response = get_historial_contractual($per_current_id);
	echo json_encode($response);
}
else if (isset($_POST["personal_registrar"])) {
	$per_reg_dni = $_POST["per_reg_dni"];
	if (!validar_input_dni($per_reg_dni)) die();
	$per_reg_nombres = $_POST["per_reg_nombres"];
	if (!validar_input_string($per_reg_nombres)) die();
	$per_reg_ap = $_POST["per_reg_ap"];
	if (!validar_input_string($per_reg_ap)) die();
	$per_reg_am = $_POST["per_reg_am"];
	if (!validar_input_string($per_reg_am)) die();
	$per_reg_foto = null;
	if (isset($_POST["per_reg_foto"])) $per_reg_foto = $_POST["per_reg_foto"];
	$params = array($per_reg_dni, $per_reg_nombres, $per_reg_ap, $per_reg_am);
	$response = registrar_personal($params, $per_reg_foto);
	echo json_encode($response);
}
else if (isset($_POST["personal_guardar_contrato"])) {
	$persona_id = $_POST["persona_id"];
	if (!validar_input_string($persona_id)) die();
	$cont_reg_tipocontrato = $_POST["cont_reg_tipocontrato"];
	if (!validar_input_string($cont_reg_tipocontrato)) die();
	$cont_reg_area = $_POST["cont_reg_area"];
	if (!validar_input_string($cont_reg_area)) die();
	$cont_reg_inicio = $_POST["cont_reg_inicio"];
	if (!validar_input_string($cont_reg_inicio)) die();
	$cont_reg_plazo = $_POST["cont_reg_plazo"];
	if (!validar_input_string($cont_reg_plazo)) die();
	$cont_reg_documento = mb_strtoupper($_POST["cont_reg_documento"]);
	if (!validar_input_string($cont_reg_documento)) die();

	$params = array($persona_id, $cont_reg_tipocontrato, $cont_reg_area, $cont_reg_inicio, $cont_reg_plazo, $cont_reg_documento);
	$response = guardar_contrato($params);
	echo json_encode($response);
}
else if (isset($_POST["finalizar_contrato"])) {
	$date_today = datetime_today_T();
	$contrato_id = $_POST["contrato_id"];
	$params = array($date_today, $contrato_id);
	$response = finalizar_contrato($params);
	echo json_encode($response);
}
?>