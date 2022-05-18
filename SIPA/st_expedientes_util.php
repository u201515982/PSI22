<?php
date_default_timezone_set('America/Bogota');

$STNaturaleza_query = "FAKE";
$STTipoDocumento_query = "FAKE";
$PerDireccion_query = "FAKE";

function expediente_buscar($num,$anno,$persona){
	$query = "FAKE";

	$params = array();

	if($persona != ""){
		$personas_m = preg_split ("/\,/", $persona);
		$per_n = 1;
		foreach ($personas_m as $item_p) {
			$persona_arr = preg_split('/\s+/', $item_p, -1, PREG_SPLIT_NO_EMPTY);
			$first_param_in = true;
			$query = $query." FAKE";
			foreach ($persona_arr as $item) {
				if(!$first_param_in) {
					$query = $query." AND ";
				}else{
					$query = $query." WHERE ";
				}
				$query = $query."FAKE";
				array_push($params,$item_f);
				array_push($params,$item_f);
				array_push($params,$item_f);
				$first_param_in = false;
			}
			$query = $query." GROUP BY FAKE";
			$per_n += 1;
		}
	}

	$query = $query." INNER JOIN FAKE ON FAKE = FAKE";

	$first_param = true;
	if($num != ""){
		if(!$first_param) {
			$query = $query." AND ";
		}else{
			$query = $query." WHERE ";
		}
		$query = $query."FAKE = ?";
		array_push($params,$num);
		$first_param = false;
	}
	if($anno != ""){
		if(!$first_param) {
			$query = $query." AND ";
		}else{
			$query = $query." WHERE ";
		}
		$query = $query."FAKE = ?";
		array_push($params,$anno);
		$first_param = false;
	}
	$query = $query." ORDER BY FAKE";
	$result = sql_statement($query,$params);
	return $result;
}

function registrar_pxe($exp_id,$per_id,$area_id) {
	$query = "INSERT INTO STPersonaxExpediente (ExpedienteId,PersonaId,DireccionId) values (?)";
	$params = array($exp_id,$per_id,$area_id);
	$result = sql_statement($query,$params);
	return $result;
}

function registrar_persona($dni,$dat_persona) {
	$query = "INSERT INTO FAKE (FAKE) OUTPUT FAKE VALUES (?)";
	$params = array($dni,$dat_persona['FAKE'],$dat_persona['FAKE'],$dat_persona['FAKE'],$dat_persona['FAKE']);
	$result = sql_statement($query,$params);
	return $result['message'][0]['FAKE'];
}

function buscar_persona($dni) {
	$query = "SELECT FAKE FROM FAKE WHERE FAKE = ?";
	$params = array($dni);
	$result = sql_statement($query,$params);
	return $result;
}

function expediente_registrar($doc_id,$date_st,$date_adm,$date_he,$date_psi) {
	$annio = (new DateTime($date_st))->format('Y');
	$query1 = "SELECT COUNT(*) AS FAKE FROM FAKE WHERE FAKE = ?";
	$params1 = array($annio);
	$result1 = sql_statement($query1,$params1);
	$exp_num = $result1['message'][0]['FAKE'] + 1;

	$exp_st = $date_st==""?null:$date_st;
	$exp_adm = $date_adm==""?null:$date_adm;
	$exp_he = $date_he==""?null:$date_he;
	$exp_psi = $date_psi==""?null:$date_psi;

	$query = "INSERT INTO FAKE (FAKEFAKE) OUTPUT FAKE.FAKE VALUES (?)";
	$params = array($exp_num,$annio,$doc_id,$exp_st,$exp_adm,$exp_he,$exp_psi);
	$result = sql_statement($query,$params);
	return $result;
}

function documento_registrar($params) {
	$query = "INSERT INTO FAKE (FAKE) OUTPUT FAKE VALUES (?)";
	$result = sql_statement($query,$params);
	return $result;
}

function documento_buscar($bus_num,$bus_cut,$bus_tipo,$bus_fecha) {
	$query = "SELECT * FROM FAKE";
	$params = array();
	$first_param = true;
	if($bus_num != ""){
		if(!$first_param) {
			$query = $query." AND ";
		}else{
			$query = $query." WHERE ";
		}
		$query = $query."FAKE = ?";
		array_push($params,$bus_num);
		$first_param = false;
	}
	if($bus_cut != ""){
		if(!$first_param) {
			$query = $query." AND ";
		}else{
			$query = $query." WHERE ";
		}
		$query = $query."FAKE = ?";
		array_push($params,$bus_cut);
		$first_param = false;
	}
	if($bus_tipo != ""){
		if(!$first_param) {
			$query = $query." AND ";
		}else{
			$query = $query." WHERE ";
		}
		$query = $query."FAKE = ?";
		array_push($params,$bus_tipo);
		$first_param = false;
	}
	if($bus_fecha != ""){
		if(!$first_param) {
			$query = $query." AND ";
		}else{
			$query = $query." WHERE ";
		}
		$query = $query."FAKE = ?";
		array_push($params,$bus_fecha);
		$first_param = false;
	}
	$result = sql_statement($query,$params);
	return $result;
}

function search_pide_reniec($dni) {
	
	$pide_raw = @file_get_contents("FAKE");

	if ($pide_raw === false) {
		$pide = array('response' => 2 , 'message' => "PIDE_RENIEC_ERROR");
		return $pide;
	}

	$pide_res = json_decode($pide_raw,true);

	if ($pide_res['return']['FAKE'] == "0000") {
		$datos_persona = $pide_res['return']['FAKE'];
		$NOM = $datos_persona['FAKE'];
		$AP = $datos_persona['FAKE'];
		$AM = $datos_persona['FAKE'];
		$DOM = $datos_persona['FAKE'];
		$pide = array('response' => 4 , 'message' => array('NOMBRES' => $NOM, 'APELLIDO_PATERNO' => $AP, 'APELLIDO_MATERNO' => $AM, 'DOMICILIO' => $DOM));
		return $pide;
	}
	else if ($pide_res['return']['FAKE'] == "1999") {
		$pide = array('response' => 2 , 'message' => "ERROR DE RENIEC. INTENTE DE NUEVO EN UNOS MOMENTOS.");
		return $pide;
	}
	else if ($pide_res['return']['FAKE'] != "0000") {
		$pide = array('response' => 3 , 'message' => "DNI NO VALIDO / NO ENCONTRADO / MENOR DE EDAD");
		return $pide;
	}

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
?>