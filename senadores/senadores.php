<?php
$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, "http://sil.gobernacion.gob.mx/Reportes/Integracion/HCongreso/ResultIntegHCongreso.php?SID=&Prin_El=0&Entidad=0&Legislatura=62&Camara=2&Partido=0");
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

$resultados = curl_exec($ch);
$explode    = explode("Referencia=", $resultados);

for($i=1; $i <= 129; $i++) {
	if(isset($explode[$i])) {
		$explodes = explode('",500', $explode[$i]);
		$ids[]    = $explodes[0];
	}
}


foreach($ids as $id) {
	curl_setopt($ch, CURLOPT_URL, "http://sil.gobernacion.gob.mx/Librerias/pp_PerfilLegislador.php?SID=&Referencia=" . $id);
	curl_setopt($ch, CURLOPT_TIMEOUT, 30);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

	$resultado = curl_exec($ch);
	
	$explode = explode("<td class='tddatosazul'>Senadores Propietario:<br><b>", $resultado);
	
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
