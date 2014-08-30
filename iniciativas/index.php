<?php
$ch = curl_init();

#Curl a la primera parte de las iniciativas legislatura 62
curl_setopt($ch, CURLOPT_URL, "http://gaceta.diputados.gob.mx/Gaceta/Iniciativas/62/gp62_a2primero.html");
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

#obtenemos el resultado y quitamos script tags menos las permitidas, quitamos head-style, solo permite p,a,li,ul,font y br
$resultado = curl_exec($ch);
$resultado = eregi_replace("<head[^>]*>.*</head>", "",     $resultado);
$resultado = eregi_replace("<style[^>]*>.*</style>", "",   $resultado);
$resultado = strip_tags($resultado, '<p><a><li><ul><font><br><br/>');

#Hacemos el explode de este codigo que delimita las fechas de seeciones de las iniciativas con titulos rojos
$explode     = explode('<font color="#CC0000">', $resultado);
$iniciativas = array();

#si exsite un array y es mayor a 1 si no "algo anda mal" by pacojaso! y  eliminamos la primiera posicion no nos sirve porque es el header del html
if(is_array($explode) and count($explode) > 1) {
	unset($explode[0]);

	#recorremos el array de los grupos
	foreach($explode as $value) {
		#obtenemos la fecha en que se publico la inicitava haciendo un explode de font donde termina el titulo rojo
		$fecha_array   = explode('</font>', $value);
		
		#declaramos arrays que ocuparemos adelante
		$iniciativa    = array();
		$data          = array();
		$data["fecha"] = "";
		
		#si las fecha existe la guardamos en array
		if(is_array($fecha_array) and count($fecha_array) > 0) {
			$data["fecha"] = $fecha_array[0];
		}
		
		#despues viena la lista de inicativas por bloque en ul>li
		$listas = explode('<ul>', $value);
		
		#comprobamos que exista al menos 1 y eliminamos la prima posicion que es basura de html
		if(is_array($listas) and count($listas) > 0) {
			unset($listas[0]);
			
			#recorremos el array de la lista de iniciativas y hacemos un explode para determinar el inicio y fin /ul li
			foreach($listas as $lista) {
				$elementos = explode('</ul>', $lista);
				$elementos = explode('<li>', $elementos[0]);
				
				#si no es nulo el elemento
				if(!is_null($elementos[1])) {
					#obtiene el titulo separado por salto de linea
					$titulo_array = explode('<br>', $elementos[1]);
					$titulo_array = explode('</br>', $titulo_array[0]);
					
					#titulos
					echo trim($titulo_array[0]) . "<br/>";
				}
			}
		}
	}
} else {
	die("Algo extraño ocurrio :/");
}
