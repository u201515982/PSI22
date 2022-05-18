<?php
/*
Template Name: editor-service
*/
if(!is_user_logged_in()){
	auth_redirect();
}
date_default_timezone_set('America/Bogota');
global $wpdb;
global $current_user; wp_get_current_user();

if(isset($_POST['area_cambiar_jefe'])) {
	if(!allowUser($wpdb, $current_user, "tareas")) die();
	
	$id_area = $_POST['id_area'];
	$id_user = $_POST['id_user'];
	
	$wpdb->query(
		$wpdb->prepare('UPDATE t SET c = false WHERE c = %d AND c = true AND c = true', $id_area)
	);
	
	$tmp = $wpdb->get_results(
		$wpdb->prepare('SELECT * FROM t WHERE c = %s AND c = true AND c = %d', $id_user, $id_area)
	);
	if (count($tmp) == 1) {
		$wpdb->query(
			$wpdb->prepare('UPDATE t SET c = true WHERE c = %s AND c = true AND c = %d',$id_user,$id_area)
		);
	}
	else {
		$wpdb->query(
			$wpdb->prepare('INSERT INTO t (c,c,c,c) VALUES (%d,%s,true,true)',$id_area,$id_user)
		);
	}
	
}
else if(isset($_POST['area_agregar_trabajador'])) {
	if(!allowUser($wpdb, $current_user, "tareas")) die();
	
	$id_area = $_POST['id_area'];
	$id_user = $_POST['id_user'];
	
	$tmp = $wpdb->get_results(
		$wpdb->prepare('SELECT * FROM t WHERE c = %s AND c = false AND c = %d', $id_user, $id_area)
	);
	if (count($tmp) == 1) {
		$wpdb->query(
			$wpdb->prepare('UPDATE t SET c = true WHERE c = %s AND c = false AND c = %d',$id_user,$id_area)
		);
	}
	else {
		$wpdb->query(
			$wpdb->prepare('INSERT INTO t (c,c,c,c) VALUES (%d,%s,false,true)',$id_area,$id_user)
		);
	}
	
}
else if(isset($_POST['area_eliminar_trabajador'])) {
	if(!allowUser($wpdb, $current_user, "tareas")) die();
	
	$id_axu = $_POST['id_axu'];
	
	$wpdb->query(
		$wpdb->prepare('UPDATE t SET c = false WHERE c = %d', $id_axu)
	);
}
else if(isset($_POST['tarea_remota_guardar_tarea'])) {
	$tarea_nombre = $_POST['tarea_nombre'];
	$tarea_descripcion = $_POST['tarea_descripcion'];
	$tarea_responsable = $_POST['tarea_responsable'];
	$tarea_fechainicio_aux = $_POST['tarea_fechainicio'];
	$tarea_fechainicio = $tarea_fechainicio_aux." ".date('H:i:s');
	$tarea_plazo = $_POST['tarea_plazo'];
	$tmp = $wpdb->get_results($wpdb->prepare("SELECT * FROM t WHERE c = %s AND c = true",$current_user->user_login));
	$tmp = json_decode(json_encode($tmp), True);
	if (count($tmp) == 1) {
		if ($tmp[0]['esjefe'] == 1 && trim($tarea_responsable) != "") {
			$jefe_area = $tmp[0]['idarea'];
			$tmp2 = $wpdb->get_results($wpdb->prepare("SELECT * FROM t WHERE c = %d AND c = false AND c = %d AND c = true",$tarea_responsable,$jefe_area));
			$tmp2 = json_decode(json_encode($tmp2), True);
			if (count($tmp2) == 1) {
				$wpdb->query(
					$wpdb->prepare("INSERT INTO t (c,c,c,c,c) VALUES (%d,%s,%s,%s,%d)",
								   $tarea_responsable,
								   $tarea_nombre,
								   $tarea_descripcion,
								   $tarea_fechainicio,
								   $tarea_plazo)
				);
			}
		}else if($tmp[0]['esjefe'] != 1){
			$aux = $wpdb->get_results($wpdb->prepare("SELECT * FROM t WHERE c = %s AND c = true",$current_user->user_login));
			$aux = json_decode(json_encode($aux), True);
			$wpdb->query(
				$wpdb->prepare("INSERT INTO t (c,c,c,c,c) VALUES (%d,%s,%s,%s,%d)",
							   $aux[0]['idareaxusuario'],
							   $tarea_nombre,
							   $tarea_descripcion,
							   $tarea_fechainicio,
							   $tarea_plazo)
			);
		}
	}
}
else if(isset($_POST['tarea_remota_verificar_tarea'])) {
	$verificar_idtarea = $_POST['verificar_idtarea'];
	$tmp = $wpdb->get_results($wpdb->prepare("SELECT * FROM t WHERE c = %s AND c = true AND c = true",$current_user->user_login));
	$tmp = json_decode(json_encode($tmp), True);
	if (count($tmp) == 1) {
		$jefe_area = $tmp[0]['idarea'];
		$tmp2 = $wpdb->get_results($wpdb->prepare("SELECT * FROM t INNER JOIN t ON t.c = t.c WHERE t.c = %d AND t.c = %d" ,$verificar_idtarea,$jefe_area));
		$tmp2 = json_decode(json_encode($tmp2), True);
		if (count($tmp2) == 1) {
			$wpdb->query(
				$wpdb->prepare("UPDATE t SET c = NOW() WHERE c = %d",
					$verificar_idtarea)
			);
		}
	}
}
else if(isset($_POST['tarea_remota_terminar_tarea'])) {
	$terminar_idtarea = $_POST['terminar_idtarea'];
	$tmp = $wpdb->get_results($wpdb->prepare("SELECT * FROM t WHERE c = %s AND c = false AND c = true",$current_user->user_login));
	$tmp = json_decode(json_encode($tmp), True);
	if (count($tmp) == 1) {
		$tmp2 = $wpdb->get_results($wpdb->prepare("SELECT * FROM t INNER JOIN t ON t.c = t.c WHERE t.c = %d AND t.c = %s" ,$terminar_idtarea,$current_user->user_login));
		$tmp2 = json_decode(json_encode($tmp2), True);
		if (count($tmp2) == 1) {
			$wpdb->query(
				$wpdb->prepare("UPDATE t SET c = NOW() WHERE c = %d",
					$terminar_idtarea)
			);
		}
	}
}
else if(isset($_POST['tarea_remota_guardar_actividad'])) {
	$current_idtarea = $_POST['current_idtarea'];
	$actividad_form_detalle = $_POST['actividad_form_detalle'];
	$tmp = $wpdb->get_results($wpdb->prepare("SELECT * FROM t WHERE c = %s AND c = false AND c = true",$current_user->user_login));
	$tmp = json_decode(json_encode($tmp), True);
	if (count($tmp) == 1) {
		$tmp2 = $wpdb->get_results($wpdb->prepare("SELECT * FROM t INNER JOIN t ON t.c = t.c WHERE t.c = %d AND t.c = %s" ,$current_idtarea,$current_user->user_login));
		$tmp2 = json_decode(json_encode($tmp2), True);
		if (count($tmp2) == 1) {
			$wpdb->query(
				$wpdb->prepare("INSERT INTO t (c, c, c) VALUES (%d, %s, NOW())",
					$current_idtarea, $actividad_form_detalle)
			);
		}
	}
}
else if(isset($_POST['tarea_remota_guardar_adjunto'])) {
	$current_idactividad = $_POST['current_idactividad'];
	$adjunto_form_nombre = $_POST['adjunto_form_nombre'];
	$adjunto_form_archivo = $_FILES['adjunto_form_archivo'];
	$file_size = $adjunto_form_archivo['size']/1024;
	$tmp = $wpdb->get_results($wpdb->prepare("SELECT * FROM t WHERE c = %s AND c = false AND c = true",$current_user->user_login));
	$tmp = json_decode(json_encode($tmp), True);
	if (count($tmp) == 1 && $file_size < 30720) {
		$tmp2 = $wpdb->get_results($wpdb->prepare("SELECT * FROM t INNER JOIN t ON t.c = t.c INNER JOIN t ON t.c = t.c WHERE t.c = %d AND t.c = %s" ,$current_idactividad,$current_user->user_login));
		$tmp2 = json_decode(json_encode($tmp2), True);
		if (count($tmp2) == 1) {
			$timestamp = (new Datetime())->format('U');
			$adjunto_type = pathinfo($adjunto_form_archivo['name'],PATHINFO_EXTENSION);
			$path_name = "sustento_".$timestamp.rand(1000,9999).".".$adjunto_type;
			$sustento_ruta = ((new Datetime())->format('Y'))."/".((new Datetime())->format('m'))."/".$path_name;
			$upload_meta = wp_upload_bits($path_name,null,file_get_contents($adjunto_form_archivo['tmp_name']));
			$wpdb->query(
				$wpdb->prepare("INSERT INTO t (c, c, c) VALUES (%d, %s, %s)",
					$current_idactividad, $adjunto_form_nombre, $sustento_ruta)
			);
		}
	}
}
else if(isset($_POST['tarea_remota_eliminar_actividad'])) {
	$current_idactividad = $_POST['current_idactividad'];
	$tmp = $wpdb->get_results($wpdb->prepare("SELECT * FROM t WHERE c = %s AND c = false AND c = true",$current_user->user_login));
	$tmp = json_decode(json_encode($tmp), True);
	if (count($tmp) == 1) {
		$tmp2 = $wpdb->get_results($wpdb->prepare("SELECT * FROM t INNER JOIN t ON t.c = t.c INNER JOIN t ON t.c = t.c WHERE t.c = %d AND t.c = %s" ,$current_idactividad,$current_user->user_login));
		$tmp2 = json_decode(json_encode($tmp2), True);
		if (count($tmp2) == 1) {
			$wpdb->query(
				$wpdb->prepare("DELETE FROM t WHERE c = %d",$current_idactividad)
			);
		}
	}
}
else if (isset($_POST['tarea_remota_clock_out'])){
	
	$cu = $current_user->user_login;

	$wpdb->query(
		$wpdb->prepare(
			"UPDATE t SET c = NOW() WHERE date(c) = CURDATE() AND c = %s", $cu
		)
	);
}
else if (isset($_POST['control_asistencia'])){
	$as_nombre = $_POST['as_nombre'];
	$as_inicio = $_POST['as_inicio'];
	$as_fin = $_POST['as_fin'];
	$as_res = null;
	if($as_inicio == '' && $as_fin == ''){
		$as_res = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT * FROM t WHERE e = %s", $as_nombre
			)
		);
	}else if($as_inicio != '' && $as_fin == ''){
		$as_i = $as_inicio." 00:00:00";
		$as_res = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT * FROM t WHERE e = %s AND i >= %s", $as_nombre, $as_i
			)
		);
	}else if($as_inicio == '' && $as_fin != ''){
		$as_f = $as_fin." 23:59:59";
		$as_res = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT * FROM t WHERE e = %s AND i <= %s", $as_nombre, $as_f
			)
		);
	}else if($as_inicio != '' && $as_fin != ''){
		$as_i = $as_inicio." 00:00:00";
		$as_f = $as_fin." 23:59:59";
		$as_res = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT * FROM t WHERE e = %s AND i >= %s AND i <= %s", $as_nombre, $as_i, $as_f
			)
		);
	}
	$as_res = json_decode(json_encode($as_res), True);
	echo json_encode($as_res);
}
else if (isset($_POST['control_asistencia_all'])){
	$as_inicio = $_POST['as_inicio'];
	$as_fin = $_POST['as_fin'];
	$as_res = null;
	if($as_inicio == '' && $as_fin == ''){
		$as_res = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT * FROM t"
			)
		);
	}else if($as_inicio != '' && $as_fin == ''){
		$as_i = $as_inicio." 00:00:00";
		$as_res = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT * FROM t WHERE i >= %s", $as_i
			)
		);
	}else if($as_inicio == '' && $as_fin != ''){
		$as_f = $as_fin." 23:59:59";
		$as_res = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT * FROM t WHERE i <= %s", $as_f
			)
		);
	}else if($as_inicio != '' && $as_fin != ''){
		$as_i = $as_inicio." 00:00:00";
		$as_f = $as_fin." 23:59:59";
		$as_res = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT * FROM t WHERE i >= %s AND i <= %s", $as_i, $as_f
			)
		);
	}
	$as_res = json_decode(json_encode($as_res), True);
	echo json_encode($as_res);
}

function todaysDate() {
	$td = new DateTime();
	return $td->format('Y/m/d H:i:s');
}

function adjuntoInfo($AdjuntoFile, $AdjuntoFile_n) {
	$timestamp = (new Datetime())->format('U');
	if(!fileIsImage($AdjuntoFile,$AdjuntoFile_n)) return null;
	$adjunto_name = pathinfo(utf8_decode($AdjuntoFile['name'][$AdjuntoFile_n]), PATHINFO_FILENAME);
	$adjunto_type = pathinfo($AdjuntoFile['name'][$AdjuntoFile_n], PATHINFO_EXTENSION);
	$adjunto_tmpfile = $AdjuntoFile['tmp_name'][$AdjuntoFile_n];
	$path_name = $timestamp.rand(1000,9999).".".$adjunto_type;
	$res = array($adjunto_name, mb_strtoupper($adjunto_type), $path_name, $adjunto_tmpfile);
	return $res;
}
function adjuntoInfo2($AdjuntoFile, $AdjuntoFile_n) {
	$timestamp = (new Datetime())->format('U');
	$adjunto_type = pathinfo($AdjuntoFile['name'][$AdjuntoFile_n], PATHINFO_EXTENSION);
	$adjunto_tmpfile = $AdjuntoFile['tmp_name'][$AdjuntoFile_n];
	$path_name = $timestamp.rand(1000,9999).".".$adjunto_type;
	$res = array(mb_strtoupper($adjunto_type), $path_name, $adjunto_tmpfile);
	return $res;
}

function fileIsImage($file,$file_n) {
	$type = $file['type'][$file_n];
	$validImageTypes = ['image/jpeg', 'image/png'];
	if (in_array($type, $validImageTypes)) {
		return true;
	}
	return false;
}

function allowUser($wpdb, $current_user, $category = null) {
	$allow = false;
	$roles = $current_user->roles;
	if($roles[0] == "administrator") {
		$allow = true;
	}
	if(!$allow && $category != null){
		$tmp = $wpdb->get_results("SELECT * FROM t WHERE c = \"{$category}\"");
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
?>