<?php
date_default_timezone_set('America/Bogota');

$PerTipoContrato_query = "SECRET_QUERY";
$PerDireccion_query = "SECRET_QUERY";
$PerArea_query = "SECRET_QUERY";
$PerPersonaXContrato_query = "SECRET_QUERY";

function finalizar_contrato($params) {
	$query = "SECRET_QUERY";
	$result = sql_statement($query, $params);
	return $result;
}
function guardar_contrato($params) {
	$query = "SECRET_QUERY";
	$result = sql_statement($query, $params);
	return $result;
}
function registrar_personal($params,$foto = null) {
	if ($foto != null) {
		$dni = $params[0];
		$data = base64_decode(preg_replace('#^data:image/\w+;base64,#i','',$foto));
		file_put_contents("img/dni/".$dni.".jpg", $data);
	}
	$query = "SECRET_QUERY";
	$result = sql_statement($query, $params);
	return $result;
}

function get_historial_contractual($persona_id) {
	$query = "SECRET_QUERY";
	$params = array($persona_id);
	$result = sql_statement($query, $params);
	return $result;
}

function search_personal_dni($dni) {
	
	$query = "SECRET_QUERY";
	$params = array($dni);
	$result = sql_statement($query, $params);
	if ($result['response'] == 0) {
		return $result;
	} else if($result['response'] == 1) {
		$per_data = $result['message'];
		if (count($per_data) == 1) {
			return $result;
		} else {
			return search_pide_reniec($dni);
		}
	}
}

function search_pide_reniec($dni) {
	
	/* DNI AUTH TEMPORAL */$dni_auth = "12345678";
	
	$ws_reniec = "SECRET_URL";
	
	$pide_raw = @file_get_contents($ws_reniec);
	if ($pide_raw === false) {
		$pide = array('response' => 2 , 'message' => "PIDE_RENIEC_ERROR");
		return $pide;
	}
	$pide_res = pide_to_array($pide_raw);
	if ($pide_res['return']['FAKE'] == "1999") {
		$pide = array('response' => 2 , 'message' => "PIDE_RENIEC_ERROR");
		return $pide;
	} else if ($pide_res['return']['FAKE'] != "0000") {
		$pide = array('response' => 3 , 'message' => "DNI_NO_VALIDO / DNI_NO_ENCONTRADO / DNI_MENOR_DE_EDAD");
		return $pide;
	} else if ($pide_res['return']['FAKE'] == "0000") {
		$datos_persona = $pide_res['return']['FAKE'];
		$NOM = $datos_persona['FAKE'];
		$AP = $datos_persona['FAKE'];
		$AM = $datos_persona['FAKE'];
		$FOTO = $datos_persona["FAKE"];
		$pide = array('response' => 4 , 'message' => array('NOMBRES' => $NOM, 'APELLIDO_PATERNO' => $AP, 'APELLIDO_MATERNO' => $AM, "FOTO" => $FOTO));
		return $pide;
	}

}

function validar_input_dni($dni) {
	if ($dni == null || $dni == "" || strlen($dni) != 8) {
		return false;
	}
	return true;
}

function validar_input_string($str) {
	if ($str == null || $str == "") {
		return false;
	}
	return true;
}

function sql_statement($query, $params = null) {
	$serverName = "";
	$Database = "";
	$UID = "";
	$PWD = "";
	/* CONNECTION */
	$connectionInfo = array("Database"=>$Database, "UID"=>$UID, "PWD"=>$PWD, "CharacterSet" => "UTF-8");

	$RES = array('response' => null , 'message' => null);

	$conn = sqlsrv_connect($serverName, $connectionInfo);
	/* ERROR HANDLE */
	if ($conn === false) {
		if( ($errors = sqlsrv_errors() ) != null) {
			$error_message = format_sql_error($errors);
			return $error_message;
		}
	}
	$result = null;

	if ($params == null) $result = sqlsrv_query($conn, $query);
	else $result = sqlsrv_query($conn, $query, $params);

	if ($result === false) {
		if( ($errors = sqlsrv_errors() ) != null) {
			$error_message = format_sql_error($errors);
			return $error_message;
		}
	}else{
		$tmp = array();
		while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
			array_push($tmp, $row);
		}
		$RES['response'] = 1;
		$RES['message'] = $tmp;
	}
	sqlsrv_free_stmt($result);
	sqlsrv_close($conn);
	return $RES;
}

function format_sql_error($errors) {
	$msg = "";
	foreach( $errors as $error ) {
		// maybe utf8_encode()
		$msg = $msg."[[ SQLSTATE: ".$error['SQLSTATE']." ] [ code: ".$error['code']." ][ message: ".$error['message']." ]]";
	}
	$out = array('response' => 0 , 'message' => $msg);
	return $out;
}

function pide_to_array($content){
	$response = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $content);
	$xml = new SimpleXMLElement($response);
	$array = json_decode(json_encode((array)$xml),TRUE);
	return $array;
}

function get_situacion($fi,$ft,$ff) {
	/* VG 1, SO 2, NL 3 */
	if ($fi == null || $ff != null) return 3;
	$origin = new DateTime('now');
	$target = new DateTime($ft->format('Y-m-d'));
	$interval = $origin->diff($target);
	$dif = $interval->format('%R%a');
	if ($dif<0) return 2;
	else return 1;
}

function datetime_format($dto) {
	return $dto->format('d/m/y');
}
function datetime_today() {
	return (new DateTime())->format('Y-m-d h:i:s');
}
function datetime_today_T() {
	return (new DateTime())->format('Y-m-d\Th:i:s');
}
?>