<?php
$ch = curl_init();

#Curl a la primera parte de las iniciativas legislatura 62
curl_setopt($ch, CURLOPT_URL, "http://gaceta.diputados.gob.mx/Gaceta/Iniciativas/62/gp62_a2primero.html");
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

#obtenemos el resultado y quitamos script tags menos las permitidas, quitamos head-style, solo permite p,a,li,ul,font y br
$resultado = curl_exec($ch);
$resultado = eregi_replace("<head[^>]*>.*</head>", "",   $resultado);
$resultado = eregi_replace("<style[^>]*>.*</style>", "", $resultado);
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
		$iniciativa_array = array();
		$iniciativa_array["fecha_listado"]  = "";
		
		#si las fecha existe la guardamos en array
		if(is_array($fecha_array) and count($fecha_array) > 0) {
			$iniciativa_array["fecha_listado"] = $fecha_array[0];
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
				
				#elemento de la iniciativa
				$iniciativa = $elementos[1];
				
				#si no es nulo el elemento
				if(!is_null($iniciativa[1])) {
					#obtiene el titulo separado por salto de linea
					$titulos_array = explode('<br>', $iniciativa);
					$titulo_array  = explode('</br>', $titulos_array[0]);
					
					#presentante o enviada por
					if(isset($titulos_array[1])) {
						$pre_envia = trim($titulos_array[1]);
						
						if(strpos($pre_envia, "Enviada") !== false) {
							$iniciativa_array["enviada"] = $pre_envia;
						} elseif(strpos($pre_envia, "Presentada") !== false) {
							$iniciativa_array["presentada"] = $pre_envia;
						}
					}
					
					#turnada por
					if(isset($titulos_array[2])) {
						$iniciativa_array["turnada"] = trim($titulos_array[2]);
					}
					
					#variable que contiene el titulo de la iniciativa en el listado
					$titulo_listado = trim($titulo_array[0]);
					
					#separa en array los enlaces y elimina la primer posicion
					$enlaces_array = explode('<a href="', $iniciativa);
					
					#comprueba que exista el array
					if(is_array($enlaces_array)) {
						unset($enlaces_array[0]);
						
						#recorremos los enlaces
						foreach($enlaces_array as $value) {
							$enlace_array = explode('</a>', $value);
							$enlace_array = $enlace_array[0];
							
							#divimos en partes el enlace para sacar el href y el texto
							$enlace_array = explode('">', $enlace_array);
							$href	      = explode('">', $enlace_array[0]);
							$href	      = explode('" target="', $href[0]);
							$texto_enlace = explode('">', $enlace_array[1]);
							
							#sacamos el texto, target y href del enlace guardamos en array de enlaces
							$enlances[] = array(
								"href" 	 => $href[0],
								"target" => $href[1],
								"titulo" => $texto_enlace[0]
							);
						}
						
						foreach($enlances as $value) {
							if($value["titulo"] == "Gaceta Parlamentaria") {
								$ancla = explode("#", $value["href"]);
								
								#comprobamos si existe un anlcla si no guardamos la url
								if(is_array($ancla) and isset($ancla[1])) {
									$ancla = $ancla[1];
									
									#obtenemos el html
									curl_setopt($ch, CURLOPT_URL, "http://gaceta.diputados.gob.mx/" . $value["href"]);
									curl_setopt($ch, CURLOPT_TIMEOUT, 30);
									curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
									curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
								
									#limpiamos el html
									$gaceta_html = curl_exec($ch);
									$gaceta_html = eregi_replace("<head[^>]*>.*</head>", "",   $gaceta_html);
									$gaceta_html = eregi_replace("<style[^>]*>.*</style>", "", $gaceta_html);
									
									#dividimos el html por el ancla y eliminamos la primer posición
									$array_gaceta = explode('<a name="' . $ancla . '"></a>', $gaceta_html);
									unset($array_gaceta[0]);
									
									#dividimos por la clase brincos y nos quedamos con la primera parte para sacar el contenido correspondiente
									$array_gaceta   = explode('<div class="BrincoS"></div>', $array_gaceta[1]);
									
									#guardamos el contenido (la posición 0), eliminamos parrafos en blanco, parrafos sin contenido, atributos de clases y hacemos trim
									$contenido_html = $array_gaceta[0];
									$contenido_html = trim($contenido_html);
									$contenido_html = str_replace('<p></p>', '', $contenido_html);
									$contenido_html = str_replace('<p>...</p>', '', $contenido_html);
									$contenido_html = str_replace(' class="Atentamente"', '', $contenido_html);
									$contenido_html = str_replace(' class="Negritas"', '', $contenido_html);
									$contenido_html = str_replace(' class="Nobrinco"', '', $contenido_html);
									$contenido_html = str_replace(' class="Derecha"', '', $contenido_html);
									$contenido_html = str_replace(' class="Versales"', '', $contenido_html);
									$contenido_html = str_replace(' class="Centrar"', '', $contenido_html);
									$contenido_html = str_replace(' class="Sangria"', '', $contenido_html);
									$contenido_html = str_replace(' class="Sangrota"', '', $contenido_html);
									$contenido_html = str_replace(' class="Italicas"', '', $contenido_html);
									
									#eliminamos saltos de linea y tabs
									$contenido_html = preg_replace("/\r\n+|\r+|\n+|\t+/i", "", $contenido_html);
									
									#guardamos el contenido en el array de la iniciativa
									$iniciativa_array["contenido_html_iniciativa"] = $contenido_html;
								} else {
									$iniciativa_array["enlace_gaceta"] = "http://gaceta.diputados.gob.mx/" . $value["href"];
								}
							} elseif($value["titulo"] == "Dictaminada") {
								$iniciativa_array["enlace_dictamen_listado"] = "http://gaceta.diputados.gob.mx/" . $value["href"];
							} elseif($value["titulo"] == "Publicado") {
								$iniciativa_array["enlace_publicado_listado"] = "http://gaceta.diputados.gob.mx/" . $value["href"];
							} else {
								#obtenemos el html de la votación y lo limpiamos
								curl_setopt($ch, CURLOPT_URL, "http://gaceta.diputados.gob.mx/" . $value["href"]);
								curl_setopt($ch, CURLOPT_TIMEOUT, 30);
								curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
								curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
							
								$html_votacion = curl_exec($ch);
								$html_votacion = eregi_replace("<head[^>]*>.*</head>", "",   $html_votacion);
								$html_votacion = eregi_replace("<style[^>]*>.*</style>", "", $html_votacion);
								
								#solo permitimos input td y tr
								$html_votacion = strip_tags($html_votacion, '<input><tr><td>');
								#convertimos todo a minisculas
								$html_votacion = strtolower($html_votacion);
								
								#eliminamos atrbibutos que no sirven
								$html_votacion = str_replace('input type="submit"', '', $html_votacion);
								$html_votacion = str_replace('input type=button border="5" ', '', $html_votacion);
								$html_votacion = str_replace(' width="8%" valign="top"', '', $html_votacion);
								$html_votacion = str_replace(' width="14%" valign="top"', '', $html_votacion);
								$html_votacion = str_replace(' width="14%" valign="top"', '', $html_votacion);
								$html_votacion = str_replace(' cellpadding=10', '', $html_votacion);
								
								#remplazamos lo que venga dentro de lola[], valores en 0, atributo name y atributo value para dejar los puros valores
								$html_votacion = eregi_replace('lola\[[0-9][0-9]\]', "", $html_votacion);
								$html_votacion = str_replace(' name="" ', '', $html_votacion);
								$html_votacion = str_replace('<value=" 0 ">', '0', $html_votacion);
								$html_votacion = str_replace('<value="', '', $html_votacion);
								$html_votacion = str_replace('">', '', $html_votacion);
								
								#remplazamos tr, para dejar puros td
								$html_votacion = str_replace('</tr>', '', $html_votacion);
								$html_votacion = str_replace('</td>', '', $html_votacion);
								
								#lo dividimos en las tablas y eliminaos posiciones que no sirven
								$tablas_votacion = explode('<tr>', $html_votacion);
								unset($tablas_votacion[count($tablas_votacion) - 1]);
								unset($tablas_votacion[0]);
								unset($tablas_votacion[1]);
								
								#separamos los encabezados de las tablas
								$encabezados = explode('<td>', trim($tablas_votacion[2]));
								unset($encabezados[0]);
								unset($encabezados[1]);
								
								#array de votaciones
								$iniciativa_array["votaciones"] = array();
								
								foreach($encabezados as $key => $value) {
									$tablas3 = explode('<td>', trim($tablas_votacion[3]));
									$tablas4 = explode('<td>', trim($tablas_votacion[4]));
									$tablas5 = explode('<td>', trim($tablas_votacion[5]));
									$tablas6 = explode('<td>', trim($tablas_votacion[6]));
									$tablas7 = explode('<td>', trim($tablas_votacion[7]));
									$tablas8 = explode('<td>', trim($tablas_votacion[8]));
									unset($tablas3[0]);
									unset($tablas4[0]);
									unset($tablas5[0]);
									unset($tablas6[0]);
									unset($tablas7[0]);
									unset($tablas8[0]);
									
									
									$iniciativa_array["votaciones"][trim($value)] = array(
										"favor" 	 => trim($tablas3[$key]),
										"contra" 	 => trim($tablas4[$key]),
										"abstencion" => trim($tablas5[$key]),
										"quorum" 	 => trim($tablas6[$key]),
										"ausente" 	 => trim($tablas7[$key]),
										"total"		 => trim($tablas8[$key])
									);
								}
							}
						}
					}
					
					//lo guardamos en el array de la iniciativa
					$iniciativa_array["titulo_listado"] = $titulo_listado;
					var_dump($iniciativa_array);
				}
			}
		}
	}
} else {
	die("Algo extraño ocurrio :/");
}
