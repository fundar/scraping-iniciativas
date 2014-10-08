<?php


$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, "http://sil.gobernacion.gob.mx/Reportes/Integracion/HCongreso/ResultIntegHCongreso.php?SID=&Prin_El=0&Entidad=0&Legislatura=62&Camara=1&Partido=0");
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

$resultados = curl_exec($ch);
$explode    = explode("Referencia=", $resultados);


for($i=1; $i <= 500; $i++) {
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


    $array = array("tddatosazul'><","Diputado Propietario:","Diputada Propietario:","por la LXII Legislatura","Fecha:","Entidad:" ,"Ciudad:");

    $jose=str_ireplace($array, '', $resultado);

   $dom = new DOMDocument();
   $html = $jose;
        // cargando html
    $dom->loadHTML($html);
    $xpath = new DOMXPath($dom);

    //obteniendo los datos de td tddatosaul.
   $my_xpath_query = "//table //td[contains(@class, 'tddatosazul')]";
   $result_rows = $xpath->query($my_xpath_query) ;

    $teams = array();

    //iterate all td
  foreach ($result_rows as $result_object){
  echo $teams[] = $result_object->nodeValue."|";
    }


}
