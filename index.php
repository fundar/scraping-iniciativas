<?php
$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, "http://gaceta.diputados.gob.mx/Gaceta/Iniciativas/62/gp62_a2primero.html");
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

$resultados  = curl_exec($ch);
$explode     = explode('<font color="#CC0000">', $resultados);
$iniciativas = array();

if(is_array($explode) and count($explode) > 0) {
	unset($explode[0]);

	foreach($explode as $value) {
		$fecha_array   = explode('</font>', $value);
		$iniciativa    = array();
		$data          = array();
		$data["fecha"] = "";
		
		if(is_array($fecha_array) and count($fecha_array) > 0) {
			$data["fecha"] = $fecha_array[0];
		}
		
		$listas = explode('<ul>', $value);
		
		if(is_array($listas) and count($listas) > 0) {
			unset($listas[0]);
			foreach($listas as $lista) {
				$elementos = explode('</ul>', $lista);
				$elementos = explode('<li>', $elementos[0]);
				
				if(!is_null($elementos[1])) {
					var_dump($elementos[1]);
				}
			}
		}
	}
} else {
	die("Algo extraño ocurrio :/");
}

die("fin");

foreach($ids as $id) {
	curl_setopt($ch, CURLOPT_URL, "http://sil.gobernacion.gob.mx/Librerias/pp_PerfilLegislador.php?SID=&Referencia=" . $id);
	curl_setopt($ch, CURLOPT_TIMEOUT, 30);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

	$resultado = curl_exec($ch);
	
	$explode = explode("<td class='tddatosazul'>Senador Propietario:<br><b>", $resultado);
	
	if(isset($explode[1])) {
		$explode = explode('<br>', $explode[1]);
		$nombre  = $explode[0];
	} else {
		$explode = explode("<td class='tddatosazul'>Senadora Propietario:<br><b>", $resultado);
		if(isset($explode[1])) {
			$explode = explode('<br>', $explode[1]);
			$nombre  = $explode[0];
		} else {
			$nombre = "null";
		}
	}

	
	$explode = explode("<td class='tdcriterio'>&nbsp;Partido:</td>", $resultado);
	if(isset($explode[1])) {
		$explode = explode("<td class='tddatosazul'>", $explode[1]);
		$explode = explode("</td>", $explode[1]);
		$partido = $explode[0];
	} else {
		$partido = "null";
	}


	$explode = explode('nico:</td>', $resultado);
	if(isset($explode[1])) {
		$explode = explode("<td class='tddatosazul'>", $explode[1]);
		$explode = explode("</td>", $explode[1]);
		$email   = $explode[0];
	} else {
		$email = "null";
	}


	$explode = explode("fono:</td>", $resultado);
	if(isset($explode[1])) {
		$explode = explode("<td class='tddatosazul'>", $explode[1]);
		$explode = explode("</td>", $explode[1]);
		$telefono = $explode[0];
	} else {
		$telefono = "null";
	}

	echo $nombre . "|" . $partido . "|" . $email . "|" . $telefono . "<br/>";
}
