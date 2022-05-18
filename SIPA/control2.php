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
else if(isset($_POST['FAKE'])) {
	$perfiles = SQLStatement("SERVER","DATABASE","USER","PASSWORD","SECRET_QUERY");
	$perfiles = json_decode(json_encode($perfiles['message']),True);
	$res = array();
	foreach ($perfiles as $item) {
		$res[$item['FAKE']] = $item['FAKE'];
	}
	echo json_encode($res);
}
else if(isset($_POST['FAKE'])) {
	$perfil_id = $_POST['FAKE'];
	$qry = "SECRET_QUERY";
	$accesos = SQLStatement("SERVER","DATABASE","USER","PASSWORD",$qry);
	$accesos = json_decode(json_encode($accesos['message']),True);
	$res = array();
	foreach ($accesos as $item) {
		$res[$item['FAKE']] = array('FAKE' => $item['FAKE'],
						'FAKE' => $item['FAKE'],
						'FAKE' => $item['FAKE'],
						'FAKE' => $item['FAKE'],
						'FAKE' => $item['FAKE']);
	}
	echo json_encode($res);
}
else if(isset($_POST['FAKE'])) {
	$auth_query = "SECRET_QUERY";
	$auth = SQLStatement("SERVER","DATABASE","USER","PASSWORD",$auth_query);
	$auth = json_decode(json_encode($auth),true);
	$GO = true;
	foreach ($auth as $item) {
		if ($item['FAKE'] == 0) {
			$GO = false;
		}
	}
	if($GO) {
		$accesos = json_decode($_POST['FAKE'], true);
		if(!empty($accesos)) {
			$perfil_id = $_POST['FAKE'];
			$insert = "INSERT INTO Table (THINGS, STUFF) VALUES ";
			$insert_go = false;
			$update = "";
			foreach ($accesos as $item) {
				if(empty($item['FAKE'])) {
					$insert_go = true;
					$insert .= "SECRET_QUERY";
				}else{
					$update .= "SECRET_QUERY";
				}
			}
			if(!$insert_go) {
				$insert = "";
			}
			else {
				$insert = trim($insert, ",");
				$insert .= ";";
			}
			$qry = $insert.$update;
			$res = SQLStatement("SERVER","DATABASE","USER","PASSWORD",$qry);
			echo $res['response'];
		}
	}
}
else if(isset($_POST['FAKE'])) {
	$usuarios = SQLStatement("SERVER","DATABASE","USER","PASSWORD","SECRET_QUERY");
	$usuarios = json_decode(json_encode($usuarios['message']),True);
	$res = array();
	foreach ($usuarios as $item) {
		$res[$item['FAKE']] = array('FAKE' => $item['FAKE'],
										'FAKE' => $item['FAKE'],
										'FAKE' => $item['FAKE'],
										'FAKE' => $item['FAKE'],
										'FAKE' => $item['FAKE'],
										'FAKE' => $item['FAKE']);
	}
	echo json_encode($res);
}
else if(isset($_POST['FAKE'])) {
	$usuario_data = json_decode($_POST['FAKE'],true);
	$params = array($usuario_data['FAKE'], $usuario_data['FAKE'], $usuario_data['FAKE'], $usuario_data['FAKE'], $usuario_data['FAKE']);
	$query = "INSERT INTO Table (THINGS,STUFF) VALUES (?,?)";
	$res = SQLStatement("SERVER","DATABASE","USER","PASSWORD",$query,$params);
}
else if(isset($_POST['FAKE'])) {
	$usuario_data = json_decode($_POST['FAKE'],true);
	$params = array($usuario_data['FAKE'], $usuario_data['FAKE'], $usuario_data['FAKE'], $usuario_data['FAKE'], $usuario_data['FAKE']);
	$query = "UPDATE Table SET THINGS = ?, STUFF = ? WHERE SOMETHING = SOMETHING_ELSE";
	$res = SQLStatement("SERVER","DATABASE","USER","PASSWORD",$query,$params);
}

function killSession() {
	session_unset();
	$_SESSION = array();
	unset(
		$_SESSION['FAKE'],
		$_SESSION['FAKE'],
		$_SESSION['FAKE'],
		$_SESSION['FAKE']
	);
	session_destroy();
}

function authenticate($user, $password) {
	if (empty($user) || empty($password)) {
		return false;
	} else {
		$query = "SELECT * FROM GrlUsuarios WHERE Codigo = '{$user}' AND Clave = '{$password}' AND inactivo = 0";
		$res = SQLStatement("SERVER","DATABASE","USER","PASSWORD",$query);
		if ($res['response'] == 0) {
			echo $res['message'];// ERROR CODE
		} else {
			if (empty($res['message'])) {
				echo 'Usuario y/o contraseña incorrecto(s).';
			}else{
				setSession($res['FAKE'][0]['FAKE'],$res['message'][0]['FAKE'],$res['message'][0]['FAKE']);
				echo true;
			}
		}
	}
}

function setSession($usuario, $perfilid, $usuarioid) {
	$_SESSION['FAKE'] = true;
	$_SESSION['FAKE'] = $usuario;
	$_SESSION['FAKE'] = $usuarioid;
	$_SESSION['FAKE'] = $perfilid;
}

function SQLStatement($serverName, $Database, $UID, $PWD, $sql, $params = null) {
	/* CONNECTION */
	$connectionInfo = array("Database"=>$Database, "UID"=>$UID, "PWD"=>$PWD, "CharacterSet" => "UTF-8");
	$conn = sqlsrv_connect($serverName, $connectionInfo);
	if ($conn === false) {
		$err_msg = utf8_encode(sqlsrv_errors()[0][2]);
		echo $err_msg;
		die();
	}

	$res = array('response' => null , 'message' => null);

	if ($params == null) {/* SELECT */
		$result = sqlsrv_query($conn, $sql);
		if ($result === false) {
			$err_msg = utf8_encode(sqlsrv_errors()[0][2]);
			$res = array('response' => 0 , 'message' => $err_msg);
		}else{
			$tmp = array();
			while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
				array_push($tmp, $row);
			}
			$res['response'] = 1;
			$res['message'] = $tmp;
		}
	} else {/* INSERT */
		$result = sqlsrv_query($conn, $sql, $params);
		if($result === false) {
			$err_msg = utf8_encode(sqlsrv_errors()[0][2]);
			$res = array('response' => 0 , 'message' => $err_msg);
		}else{
			$res['response'] = 1;
			$res['message'] = 'OK';
		}
	}
	sqlsrv_free_stmt($result);
	sqlsrv_close($conn);
	return $res;
}
?>