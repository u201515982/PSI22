<?php
// TODO Validate user session, authorization
include("st_expedientes_util.php");
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
	exit;
}

if (isset($_POST['expediente_buscar'])) {
	$exp_buscar_num = $_POST['exp_buscar_num'];
	$exp_buscar_anno = $_POST['exp_buscar_anno'];
	$exp_buscar_persona = $_POST['exp_buscar_persona'];
	$result = expediente_buscar($exp_buscar_num,$exp_buscar_anno,$exp_buscar_persona);
	echo json_encode($result);
	//echo ($result);
}
else if (isset($_POST['documento_buscar'])) {
	$doc_buscar_num = $_POST['doc_buscar_num'];
	$doc_buscar_cut = $_POST['doc_buscar_cut'];
	$doc_buscar_tipo = $_POST['doc_buscar_tipo'];
	$doc_buscar_fecha = $_POST['doc_buscar_fecha'];
	$result = documento_buscar($doc_buscar_num,$doc_buscar_cut,$doc_buscar_tipo,$doc_buscar_fecha);
	echo json_encode($result);
}
else if (isset($_POST['documento_registrar'])) {
	$doc_nuevo_num = $_POST['doc_nuevo_num'];
	$doc_nuevo_cut = $_POST['doc_nuevo_cut'];
	$doc_nuevo_tipo = $_POST['doc_nuevo_tipo'];
	$doc_nuevo_naturaleza = $_POST['doc_nuevo_naturaleza'];
	$doc_nuevo_fecha = $_POST['doc_nuevo_fecha'];
	$doc_nuevo_asunto = $_POST['doc_nuevo_asunto'];
	$doc_nuevo_proc = $_POST['doc_nuevo_proc'];
	if($doc_nuevo_proc == 0) {
		$doc_nuevo_direccion = $_POST['doc_nuevo_direccion'];
		$doc_nuevo_entidad = null;
	}else if($doc_interno == 1) {
		$doc_nuevo_direccion = null;
		$doc_nuevo_entidad = $_POST['doc_nuevo_entidad'];
	}
	$params = array($doc_nuevo_num,$doc_nuevo_cut,$doc_nuevo_tipo,$doc_nuevo_naturaleza,$doc_nuevo_fecha,$doc_nuevo_asunto,$doc_nuevo_proc,$doc_nuevo_direccion,$doc_nuevo_entidad);
	$result = documento_registrar($params);
	echo json_encode($result);
}
else if (isset($_POST['dni_buscar'])) {
	$dni = $_POST['exp_nuevo_dni'];
	$result = search_pide_reniec($dni);
	echo json_encode($result);
}
else if (isset($_POST['expediente_registrar'])) {
	$IMPLICADOS_LIST = json_decode($_POST['IMPLICADOS_LIST'],true);
	$curr_doc_id = $_POST['curr_doc_id'];
	$exp_nuevo_fecha_st = $_POST['exp_nuevo_fecha_st'];
	$exp_nuevo_fecha_adm = $_POST['exp_nuevo_fecha_adm'];
	$exp_nuevo_fecha_he = $_POST['exp_nuevo_fecha_he'];
	$exp_nuevo_fecha_psi = $_POST['exp_nuevo_fecha_psi'];

	$result = expediente_registrar($curr_doc_id,$exp_nuevo_fecha_st,$exp_nuevo_fecha_adm,$exp_nuevo_fecha_he,$exp_nuevo_fecha_psi);
	$exp_id = $result['message'][0]['FAKE'];

	foreach ($IMPLICADOS_LIST as $key => $value) {
		$result = buscar_persona($key);
		if (count($result['message']) == 1) {
			$result = registrar_pxe($exp_id,$result['message'][0]['FAKE'],$value['FAKE']);
			print_r($result);
		}
		else if (count($result['message']) == 0) {
			$new_persona_id = registrar_persona($key,$value);
			$result = registrar_pxe($exp_id,$new_persona_id,$value['FAKE']);
			print_r($result);
		}
	}
}
?>