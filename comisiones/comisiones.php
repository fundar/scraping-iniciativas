<?php
#conexión a la base de datos
//$RepresentatesBD = false;
//$RepresentatesBD = conexionBD();
$ch = curl_init();
$resultados = curl_exec($ch);

for($i=1; $i <= 1; $i++) {
		$explodes = [$i];
		$ids[]    = $explodes[0];
		
	}

foreach($ids as $id) {
	curl_setopt($ch, CURLOPT_URL, "http://sitl.diputados.gob.mx/LXII_leg/integrantes_de_comisionlxii.php?comt=" . $id);
								   
	curl_setopt($ch, CURLOPT_TIMEOUT, 30);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

	$resultado = curl_exec($ch);
	
	//comision
	$explode = explode('<td class="EncabezadoVerde"', $resultado);
	if(isset($explode[1])) {
		$explode = explode('gn="left" >', $explode[1]);
		$explode = explode('</td></tr><tr>', $explode[1]);
		$nombre_comision = $explode[0];
	} else {
		$nombre_comision = "null";
	}
	
	
	echo   $id. "|". $nombre_comision. "|". slug(utf8_encode($nombre_comision))."<br/>";

	
	
}

function slug($string) {	
$characters = array(
"Á" => "A", "Ç" => "c", "É" => "e", "Í" => "i", "Ñ" => "n", "Ó" => "o", "Ú" => "u",
"á" => "a", "ç" => "c", "é" => "e", "í" => "i", "ñ" => "n", "ó" => "o", "ú" => "u",
"à" => "a", "è" => "e", "ì" => "i", "ò" => "o", "ù" => "u", "ã" => "a", "¿" => "",
"?" => "", "¡" => "", "!" => "", ": " => "-"
);
$string = strtr($string, $characters);
$string = strtolower(trim($string));
$string = str_replace(' ', '-', $string);
$string = preg_replace("[^A-Za-z0-9-.\\/]", "", $string);
$string = preg_replace("/-+/", "-", $string);
if(substr($string, strlen($string) - 1, strlen($string)) === "-") {
$string = substr($string, 0, strlen($string) - 1);
}
return $string;
}