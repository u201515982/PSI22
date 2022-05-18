<?php
date_default_timezone_set('America/Bogota');

session_start();

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
	exit;
}

if (isset($_POST['FAKE'])) {
	$sis_username = trim($_POST['FAKE']);
	$sis_password = trim($_POST['FAKE']);
	authenticate($sis_username, $sis_password);
}

else if (isset($_POST['FAKE'])) {
	killSession();
	echo 1;
}

else if (isset($_POST['FAKE'])) {
	$foo = $_POST['SOMETHING'];
	$query = "SELECT THINGS, STUFF FROM MY_TABLE INNER JOIN ANOTHER_TABLE WHERE SOMETHING = SOMETHING_ELSE";
	$res = SQLStatement("SERVER","DATABASE","USER","PASSWORD",$query);
	$out = null;
	foreach ($res['message'] as $item) {
		$date_tmp = json_decode(json_encode($item['MYDATE']),True)['date'];
		$date_f = (new DateTime($date_tmp))->format("d/m/Y");

		$cut_tmp = explode("\\", $item['FAKE']);
		$cut_f = $cut_tmp[2]."-".$cut_tmp[1]."-".$cut_tmp[0];
		$out = array("FAKE" => $item['FAKE'],
					 "FAKE" => $cut_f,
					 "FAKE" => $item['FAKE'],
					 "FAKE" => $date_f,
					 "FAKE" => $item['FAKE'],
					 "FAKE" => $item['FAKE'],
					 "FAKE" => $item['FAKE']
					);
	}
	echo json_encode($out);
}
else if(isset($_POST['FAKE'])) {
	$docs = json_decode($_POST['FAKE'], True);
	$usuario_id = $_SESSION['FAKE'];
	$fecha_cargo = (new DateTime())->format('Y-d-m H:i:s');
	$query = "SOME QUERY";
	$insert_que = "INSERT INTO Table (THINGS,STUFF) VALUES";
	foreach ($docs as $item) {
		$DOCUMENTO = $item['FAKE'];
		$FECHA_DOCUMENTACION = str_replace("'", "''", $item['FAKE']);
		$OBSERVACION = str_replace("'", "''", $item['FAKE']);
		
		$insert_que .= "QUERY EXTENSION";
	}
	$query .= trim($insert_que,",")."SECRET_QUERY";
	$res = SQLStatement("SERVER","DATABASE","USER","PASSWORD",$query);
	/*$res = json_decode(json_encode($res),True);
	print_r($res);*/
	$res2 = SQLStatement("SERVER","DATABASE","USER","PASSWORD","SECRET_QUERY");
	$res2 = json_decode(json_encode($res2),True);
	echo json_encode($res2['message'][0]);

}
else if(isset($_POST['FAKE'])) {
	$cargo_fecha = $_POST['FAKE'];
	$cargo_documento = trim($_POST['FAKE']);
	$cargo_asunto = trim($_POST['FAKE']);
	$query = "SELECT THINGS, STUFF FROM MY_TABLE INNER JOIN ANOTHER_TABLE WHERE SOMETHING = SOMETHING_ELSE";
	$filter_where = "";
	if(!empty($cargo_fecha)) {
		$fecha_tmp = explode("-", $cargo_fecha);
		$fecha_f = $fecha_tmp[2]."-".$fecha_tmp[1]."-".$fecha_tmp[0];
		$filter_where .= "SECRET_QUERY";
	}
	$filter_join_doc = "";
	if(!empty($cargo_documento)) {
		$filter_join_doc .= "SECRET_QUERY";
		$doc_tmp = explode(" ", $cargo_documento);
		$c = 0;
		foreach ($doc_tmp as $item) {
			if($c != 0) $filter_join_doc .= " AND ";
			$c++;
			$filter_join_doc .= "SECRET_QUERY";
		}
		$filter_join_doc .= "SECRET_QUERY";
	}
	$filter_join_as = "";
	if(!empty($cargo_asunto)) {
		$filter_join_as .= "SECRET_QUERY";
		$as_tmp = explode(" ", $cargo_asunto);
		$c = 0;
		foreach ($as_tmp as $item) {
			if($c != 0) $filter_join_as .= " AND ";
			$c++;
			$filter_join_as .= "SECRET_QUERY";
		}
		$filter_join_as .= "SECRET_QUERY";
	}
	$query .= $filter_join_as.$filter_join_doc.$filter_where." GROUP BY THINGS";
	$res = SQLStatement("SERVER","DATABASE","USER","PASSWORD",$query);
	$res = json_decode(json_encode($res),True);
	echo json_encode($res['message']);
}