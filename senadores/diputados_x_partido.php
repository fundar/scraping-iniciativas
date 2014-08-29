<?php
$ch = curl_init();

//PRI
curl_setopt($ch, CURLOPT_URL, "http://curul501.org/partido_politico/1");
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

$resultado = curl_exec($ch);
$array     = explode("class='nombre'>", $resultado);
unset($array[0]);

foreach($array as $explode) {
	if(isset($explode)) {
		$explodes = explode('<a href="', $explode);
		$explodes = explode('">', $explodes[1]);
		$urls1[]   = $explodes[0];
		
		$explodes  = explode('</a>', $explodes[1]);
		$nombres1[] = $explodes[0];
	}
}


foreach($urls1 as $key => $value) {
	echo utf8_decode($nombres1[$key]) . "," . "http://curul501.org" . $value . ",pri<br/>";
}


//PAN
curl_setopt($ch, CURLOPT_URL, "http://curul501.org/partido_politico/2");
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

$resultado = curl_exec($ch);
$array     = explode("class='nombre'>", $resultado);
unset($array[0]);

foreach($array as $explode) {
	if(isset($explode)) {
		$explodes = explode('<a href="', $explode);
		$explodes = explode('">', $explodes[1]);
		$urls2[]   = $explodes[0];
		
		$explodes  = explode('</a>', $explodes[1]);
		$nombres2[] = $explodes[0];
	}
}


foreach($urls2 as $key => $value) {
	echo utf8_decode($nombres2[$key]) . "," . "http://curul501.org" . $value . ",pan<br/>";
}


//PRD
curl_setopt($ch, CURLOPT_URL, "http://curul501.org/partido_politico/3");
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

$resultado = curl_exec($ch);
$array     = explode("class='nombre'>", $resultado);
unset($array[0]);

foreach($array as $explode) {
	if(isset($explode)) {
		$explodes = explode('<a href="', $explode);
		$explodes = explode('">', $explodes[1]);
		$urls3[]   = $explodes[0];
		
		$explodes  = explode('</a>', $explodes[1]);
		$nombres3[] = $explodes[0];
	}
}


foreach($urls3 as $key => $value) {
	echo utf8_decode($nombres3[$key]) . "," . "http://curul501.org" . $value . ",prd<br/>";
}


//PT
curl_setopt($ch, CURLOPT_URL, "http://curul501.org/partido_politico/4");
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

$resultado = curl_exec($ch);
$array     = explode("class='nombre'>", $resultado);
unset($array[0]);

foreach($array as $explode) {
	if(isset($explode)) {
		$explodes = explode('<a href="', $explode);
		$explodes = explode('">', $explodes[1]);
		$urls4[]   = $explodes[0];
		
		$explodes  = explode('</a>', $explodes[1]);
		$nombres4[] = $explodes[0];
	}
}


foreach($urls4 as $key => $value) {
	echo utf8_decode($nombres4[$key]) . "," . "http://curul501.org" . $value . ",pt<br/>";
}

//PV
curl_setopt($ch, CURLOPT_URL, "http://curul501.org/partido_politico/5");
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

$resultado = curl_exec($ch);
$array     = explode("class='nombre'>", $resultado);
unset($array[0]);

foreach($array as $explode) {
	if(isset($explode)) {
		$explodes = explode('<a href="', $explode);
		$explodes = explode('">', $explodes[1]);
		$urls5[]   = $explodes[0];
		
		$explodes  = explode('</a>', $explodes[1]);
		$nombres5[] = $explodes[0];
	}
}


foreach($urls5 as $key => $value) {
	echo utf8_decode($nombres5[$key]) . "," . "http://curul501.org" . $value . ",pv<br/>";
}

//MC
curl_setopt($ch, CURLOPT_URL, "http://curul501.org/partido_politico/6");
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

$resultado = curl_exec($ch);
$array     = explode("class='nombre'>", $resultado);
unset($array[0]);

foreach($array as $explode) {
	if(isset($explode)) {
		$explodes = explode('<a href="', $explode);
		$explodes = explode('">', $explodes[1]);
		$urls6[]   = $explodes[0];
		
		$explodes  = explode('</a>', $explodes[1]);
		$nombres6[] = $explodes[0];
	}
}


foreach($urls6 as $key => $value) {
	echo utf8_decode($nombres6[$key]) . "," . "http://curul501.org" . $value . ",mc<br/>";
}


//na
curl_setopt($ch, CURLOPT_URL, "http://curul501.org/partido_politico/7");
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

$resultado = curl_exec($ch);
$array     = explode("class='nombre'>", $resultado);
unset($array[0]);

foreach($array as $explode) {
	if(isset($explode)) {
		$explodes = explode('<a href="', $explode);
		$explodes = explode('">', $explodes[1]);
		$urls7[]   = $explodes[0];
		
		$explodes  = explode('</a>', $explodes[1]);
		$nombres7[] = $explodes[0];
	}
}


foreach($urls7 as $key => $value) {
	echo utf8_decode($nombres7[$key]) . "," . "http://curul501.org" . $value . ",na<br/>";
}
