#!/usr/bin/php -q

<?php
/*To-do*/

echo "Hora y fecha actual de inicio: " . date("Y-m-d H:i:s") . "\n";
echo "Iniciando scrapping .... esperar \n\n";

$ch      = curl_init();
$baseurl = "http://gaceta.diputados.gob.mx";

#ver la tabla de legislaturas del admin 1 = LXII
$id_legislatura = 1; 

#incluir array de periodos
include_once "class/config/array_periodos.php";

#conexión a la base de datos
$IniciativasBD = false;
$IniciativasBD = conexionBD();
$contador      = 0;

foreach($array_periodos as $periodo) {
	#Curl a las iniciativas legislatura 62
	curl_setopt($ch, CURLOPT_URL, $baseurl . $periodo["url"]);
	curl_setopt($ch, CURLOPT_TIMEOUT, 30);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

	#obtenemos el resultado y quitamos script tags menos las permitidas, quitamos head-style, solo permite p,a,li,ul,font y br
	$resultado = curl_exec($ch);
	$resultado = eregi_replace("<head[^>]*>.*</head>", "",   $resultado);
	$resultado = eregi_replace("<style[^>]*>.*</style>", "", $resultado);
	$resultado = strip_tags($resultado, '<p><a><li><ul><font><br><br/>');

	#Hacemos el explode de este codigo que delimita las fechas de seeciones de las iniciativas con titulos rojos
	$explode       = explode('<font color="#CC0000">', $resultado);
	$iniciativas   = array();

	#si exsite un array y es mayor a 1 si no "algo anda mal" by pacojaso! y  eliminamos la primiera posicion no nos sirve porque es el header del html
	if(is_array($explode) and count($explode) > 1) {
		echo "Scrapping del periodo \033[4m" . $periodo["periodo"] . "\033[0m del \033[4m" . $periodo["ano"] . "\033[0m\n";
		echo "Scrapping URL: \033[4m" . $periodo["url"] . "\033[0m\n\n";
		
		unset($explode[0]);
		
		#imprimimos para el log
		echo "Numero de grupos: " . count($explode) .  "\n\n";
		
		#recorremos el array de los grupos
		foreach($explode as $keygrupo => $value) {
			#obtenemos la fecha en que se publico la inicitava haciendo un explode de font donde termina el titulo rojo
			$fecha_array   = explode('</font>', $value);
			
			#declaramos arrays que ocuparemos adelante
			$iniciativa_array = array();
			$iniciativa_array["numero_iniciativa"]       = "";
			$iniciativa_array["fecha_listado"]           = "";
			$iniciativa_array["fecha_listado_tm"]        = "";
			$iniciativa_array["fecha_votacion"]          = "";
			$iniciativa_array["fecha_votacion_tm"]       = "";
			$iniciativa_array["fecha_listado_header"]    = "";
			$iniciativa_array["fecha_listado_header_tm"] = "";
			
			#si las fecha existe la guardamos en array
			if(is_array($fecha_array) and count($fecha_array) > 0) {
				$iniciativa_array["fecha_listado_header"] = $fecha_array[0];
				
				if($fecha_array[0] == "") {
					unset($iniciativa_array["fecha_listado_header_tm"]);
				} else {
					$fecha_listado_header    = str_replace(" de ", " ", $fecha_array[0]);
					$fecha_listado_header    = trim($fecha_listado_header);
					$fecha_listado_header    = explode(" ", $fecha_listado_header);
					$fecha_listado_header    = strtotime($fecha_listado_header[1] . '-' . getMes(ucfirst($fecha_listado_header[2])) . '-' . $fecha_listado_header[3]);
					$iniciativa_array["fecha_listado_header_tm"] = date("Y-m-d H:i:s", $fecha_listado_header);
					if($iniciativa_array["fecha_listado_header_tm"] == "") unset($iniciativa_array["fecha_listado_header_tm"]);
				}
			}
			
			#despues viena la lista de inicativas por bloque en ul>li
			$listas = explode('<ul>', $value);
			
			#comprobamos que exista al menos 1 y eliminamos la prima posicion que es basura de html
			if(is_array($listas) and count($listas) > 0) {
				unset($listas[0]);
				
				#imprimimos para el log
				echo "Numero de iniciativas del grupo " . $keygrupo . ": " . count($listas) .  "\n\n";
				
				#recorremos el array de la lista de iniciativas y hacemos un explode para determinar el inicio y fin /ul li
				foreach($listas as $lista) {
					$elementos = explode('</ul>', $lista);
					$elementos = explode('<li>', $elementos[0]);
					
					if(is_array($elementos) and isset($elementos[1])) {
						#elemento de la iniciativa
						$iniciativa = $elementos[1];
						
						#si no es nulo el elemento
						if(!is_null($iniciativa)) {
							#obtiene el titulo separado por salto de linea
							$titulos_array = explode('<br>', $iniciativa);
							$titulo_array  = explode('</br>', $titulos_array[0]);
							
							#presentante o enviada por
							if(isset($titulos_array[1])) {
								$pre_envia = trim($titulos_array[1]);
								
								if(strpos($pre_envia, "Enviada") !== false or strpos($pre_envia, "Enviado") !== false) {
									$iniciativa_array["enviada"] = $pre_envia;
								} 
								
								if(strpos($pre_envia, "Presentada") !== false) {
									$pre_envia_full = $pre_envia;
									
									//remplazamos cosas que no queremos
									$array_replace1 = array('Presentada por integrantes del ', 'Presentada por diputados del ', 'Presentada por los diputados ', 'Presentada por las diputadas ', 'Presentada por el diputado ', 'Presentada por el diputado ', 'Presentada por la diputada ', 'Presentada por el senador ', 'Presentada por el ', 'Presentada por la senadora ', 'Presentada por el senador ', 'Presentada por los senadores ');
									$array_replace2 = array('.', ';', ', PRD', ', PRI', ', PAN', ', PT', ', PVEM', ', Movimiento Ciudadano', ', Nueva Alianza', 'suscrita por integrantes del ');
									
									$pre_envia = str_replace($array_replace1, '', $pre_envia);
									$pre_envia = str_replace($array_replace2, '', $pre_envia);
									$pre_envia = str_replace(' y ', ',', $pre_envia);
									$pre_envia = explode(',', $pre_envia);
									
									$iniciativa_array["presentada"]       = $pre_envia_full;
									$iniciativa_array["presentada_array"] = $pre_envia;
								}
							}
							
							#turnada
							if(isset($titulos_array[2])) {
								#agregar comisiones, ligarlos a la tabla de relación
								$pre_turnada  = trim($titulos_array[2]);
								$pre_turnada2 = $pre_turnada;
								
								$array_replace  = array('Turnada a la ', 'Turnada a las ', utf8_decode('Comisión de '), 'Comisiones de ', utf8_decode('Comisión '), 'Comisiones ', '.');
								$array_replace2 = array('Unidas de ');
								$pre_turnada2   = str_replace($array_replace, '', $pre_turnada2);
								$pre_turnada2   = str_replace($array_replace2, '', $pre_turnada2);
								
								if(strpos($pre_turnada2, "y de ") !== false) {
									$pre_turnada2 = explode(' y de ', $pre_turnada2);
								} elseif(strpos($pre_turnada2, utf8_decode(', con opinión de la ')) !== false) {
									$pre_turnada2 = explode(utf8_decode(', con opinión de la '), $pre_turnada2);
								} else {
									$pre_turnada2 = array($pre_turnada2);
								}
								
								$iniciativa_array["turnada"] 	   = $pre_turnada;
								$iniciativa_array["turnada_array"] = $pre_turnada2;
								
							}
							
							#variable que contiene el titulo de la iniciativa en el listado
							$titulo_listado = trim($titulo_array[0]);
							$titulo_listado = strip_tags($titulo_listado);
							
							#guardamos el html, titulo, perido y legislatura para futuras comparaciones
							$iniciativa_array["titulo_listado"] = $titulo_listado;
							$iniciativa_array["html_listado"]   = $iniciativa;
							$iniciativa_array["periodo"]        = $periodo["periodo"];
							$iniciativa_array["ano"]        	= $periodo["ano"];
							$iniciativa_array["id_legislature"] = $id_legislatura;
							
							#comparamos si es identica e imprime el log
							if(isSame($iniciativa_array, $IniciativasBD) == false) {
								#guardo los pasos/estatus de la iniciativa en un array
								$iniciativa_array["estatus"] = pasos($iniciativa);
								
								#separa en array los enlaces y elimina la primer posicion
								$enlaces_array = explode('<a href="', $iniciativa);
								
								#comprueba que exista el array
								if(is_array($enlaces_array) and count($enlaces_array) > 1) {
									unset($enlaces_array[0]);
									
									#limipamos los enlaces
									$enlances = "";
									$enlances = array();
									
									#recorremos los enlaces
									foreach($enlaces_array as $value) {
										#fecha y numero de iniciativa
										if(strpos($value, "Gaceta Parlamentaria") !== false) {
											$data_gaceta = explode('. (', $value);
											
											$number = explode(")", $data_gaceta[1]);
											$number = trim($number[0]);
											$fecha  = explode(",", $data_gaceta[0]);
											$fecha  = trim($fecha[count($fecha)-1]);
											$fecha  = str_replace(".", "", $fecha);
											$fecha  = str_replace("+", "", $fecha);
											$fecha  = str_replace("</li>", "", $fecha);
											$fecha  = trim($fecha);
											
											$fecha_listado    = str_replace(" de ", " ", $fecha);
											$fecha_listado    = explode(" ", $fecha_listado);
											$fecha_listado    = strtotime($fecha_listado[1] . '-' . getMes(ucfirst($fecha_listado[2])) . '-' . $fecha_listado[3]);
											
											$iniciativa_array["numero_iniciativa"] = $number;
											$iniciativa_array["fecha_listado"]     = $fecha;
											$iniciativa_array["fecha_listado_tm"] = date("Y-m-d H:i:s", $fecha_listado);
											if($iniciativa_array["fecha_listado_tm"] == "") unset($iniciativa_array["fecha_listado_tm"]);
										}
										
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
											"titulo" => $texto_enlace[0]
										);
									}
									
									$count_votacion = 0;
									#array de votaciones
									$iniciativa_array["votaciones"]    = array();
									$iniciativa_array["votos_nombres"] = array();
									
									foreach($enlances as $value) {
										if($value["titulo"] == "Gaceta Parlamentaria") {
											$ancla = explode("#", $value["href"]);
											
											#comprobamos si existe un anlcla si no guardamos la url
											if(is_array($ancla) and isset($ancla[1])) {
												$ancla = $ancla[1];
												
												#obtenemos el html
												curl_setopt($ch, CURLOPT_URL, $baseurl . $value["href"]);
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
												$iniciativa_array["enlace_gaceta"]             = $baseurl . $value["href"];
												$iniciativa_array["contenido_html_iniciativa"] = $contenido_html;
											} else {
												$iniciativa_array["enlace_gaceta"] = $baseurl . $value["href"];
											}
										} elseif($value["titulo"] == "Dictaminada") {
											$enlace = explode('"', $value["href"]);
											$iniciativa_array["enlace_dictamen_listado"] = $baseurl . $enlace[0];
										} elseif($value["titulo"] == "Publicado") {
											$iniciativa_array["enlace_publicado_listado"] = $baseurl . $value["href"];
										} elseif(utf8_encode($value["titulo"]) == "Votación") {
											#obtenemos el html de la votación y lo limpiamos
											curl_setopt($ch, CURLOPT_URL, $baseurl . $value["href"]);
											curl_setopt($ch, CURLOPT_TIMEOUT, 30);
											curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
											curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
										
											$html_votacion = curl_exec($ch);
											$html_votacion = eregi_replace("<head[^>]*>.*</head>", "",   $html_votacion);
											$html_votacion = eregi_replace("<style[^>]*>.*</style>", "", $html_votacion);
											
											#guardamos los inputs hiddens para la petición donde obtenemos los nombres de los diputados [action, nomit y evento]
											$nomtit = explode('nomtit" VALUE="', $html_votacion);
											$nomtit = explode('">', $nomtit[1]);
											$nomtit = $nomtit[0];
											
											/*fecha votación*/
											$fecha_votacion = explode("<p>", $nomtit);
											$fecha_votacion = trim($fecha_votacion[1]);
											$iniciativa_array["fecha_votacion"] = $fecha_votacion;
											
											if($fecha_votacion == "") {
												unset($iniciativa_array["fecha_votacion_tm"]);
											} else {
												$fecha_votacion    = str_replace(" de ", " ", $fecha_votacion);
												$fecha_votacion    = explode(" ", $fecha_votacion);
												$fecha_votacion    = strtotime($fecha_votacion[0] . '-' . getMes(ucfirst($fecha_votacion[1])) . '-' . $fecha_votacion[2]);
												$iniciativa_array["fecha_votacion_tm"] = date("Y-m-d H:i:s", $fecha_votacion);
												if($iniciativa_array["fecha_votacion_tm"] == "") unset($iniciativa_array["fecha_votacion_tm"]);
											}
											
											$evento = explode('evento" VALUE="', $html_votacion);
											$evento = explode('">', $evento[1]);
											$evento = $evento[0];
											
											$action = explode('<form enctype= method="post" action="', $html_votacion);
											$action = explode('">', $action[1]);
											$action = $action[0];
											
											$url_nom_diputados = $baseurl . $action . "?evento=" . urlencode($evento) . "&nomtit=" . urlencode($nomtit) . "&";
											
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
											$html_votacion = str_replace('<value=" 0 ">', '0', $html_votacion);
											$html_votacion = str_replace('">', '', $html_votacion);
											
											#remplazamos tr, para dejar puros td
											$html_votacion = str_replace('</tr>', '', $html_votacion);
											$html_votacion = str_replace('</td>', '', $html_votacion);
											
											#declaramos variables para sacar los parametros
											$html_lola = $html_votacion;
											
											/*esta parte esta comentada para obteher la url de los diputados que votaron en que sentido*/
											$html_votacion = eregi_replace('lola\[[0-9][0-9]\]', "", $html_votacion);
											
											#remplazamos valores en 0, atributo name y atributo value para dejar los puros valores
											$html_votacion = str_replace('< name="" value="', '', $html_votacion);
											$html_votacion = str_replace('" value="', '', $html_votacion);
											$html_votacion = str_replace(' name="" ', '', $html_votacion);
											$html_votacion = str_replace('<value="', '', $html_votacion);
											$html_votacion = str_replace('<value="', '', $html_votacion);
											
											#lo dividimos en las tablas y eliminaos posiciones que no sirven
											$tablas_votacion = explode('<tr>', $html_votacion);
											unset($tablas_votacion[count($tablas_votacion) - 1]);
											unset($tablas_votacion[0]);
											unset($tablas_votacion[1]);

											#lo dividimos en las tablas y eliminaos posiciones que no sirven lola parametros
											$tablas_lola_votacion = explode('<tr>', $html_lola);
											unset($tablas_lola_votacion[count($tablas_lola_votacion) - 1]);
											unset($tablas_lola_votacion[0]);
											unset($tablas_lola_votacion[1]);
											
											#separamos los encabezados de las tablas
											$encabezados = explode('<td>', trim($tablas_votacion[2]));
											unset($encabezados[0]);
											unset($encabezados[1]);
											
											#arrays de votaciones - renglones
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
											
											#todo - convertir esto a un array para ahorrar lineas
											#arrays de parametros lola - renglones
											$tabla_lola[3] = explode('<td>', trim($tablas_lola_votacion[3]));
											$tabla_lola[4] = explode('<td>', trim($tablas_lola_votacion[4]));
											$tabla_lola[5] = explode('<td>', trim($tablas_lola_votacion[5]));
											$tabla_lola[6] = explode('<td>', trim($tablas_lola_votacion[6]));
											$tabla_lola[7] = explode('<td>', trim($tablas_lola_votacion[7]));
											
											foreach($tabla_lola as $key => $tabla) {
												unset($tabla[0]);
												$search = strpos(trim($tabla[2]), "lola");
												
												if($key == 3) $tipo     = "favor";
												elseif($key == 4) $tipo = "contra";
												elseif($key == 5) $tipo = "abstencion";
												elseif($key == 6) $tipo = "quorum";
												elseif($key == 7) $tipo = "ausente";
												
												#comparacion para saber si si tiene ese parametro
												if($search !== FALSE) {
													$str = str_replace('< name="', '', $tabla[2]);
													$str = explode('" value="', trim($str));
													$name  = $str[0];
													$value = $str[1];
													
													#construimos la url
													$url_str = $url_nom_diputados . urlencode($name) . "=" . $value;
													
													#obtenemos la url con curl
													curl_setopt($ch, CURLOPT_URL, $url_str);
													curl_setopt($ch, CURLOPT_TIMEOUT, 30);
													curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
													curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
												
													$html_votantes = curl_exec($ch);
													$html_votantes = eregi_replace("<head[^>]*>.*</head>", "",   $html_votantes);
													$html_votantes = eregi_replace("<style[^>]*>.*</style>", "", $html_votantes);
													
													#solo permitimos input br y center
													$html_votantes = str_replace('</center>', '', $html_votantes);
													$html_votantes = strip_tags($html_votantes, '<br><center>');
													
													#convertimos a un array por partido
													$partidos_votos = explode('<center>', $html_votantes);
													unset($partidos_votos[0]);
													unset($partidos_votos[count($partidos_votos)]);
													
													#reocrremos el array de las secciones de votantes por partido
													foreach($partidos_votos as $key2 => $partido_array) {
														$partido_array = str_replace('<br><br>', '', $partido_array);
														$lista_array   = explode('<br>', $partido_array);
														
														#se guarda la primer posicion del partido y se elimina del array
														$partido = trim($lista_array[0]);
														unset($lista_array [0]);
														
														#condiciones para sacar el nombre del partido
														if(strpos($partido, "Diputados del ") !== FALSE) {
															$partido = explode('Diputados del ', $partido);
															$partido = explode(' que', $partido[1]);
															$partido = $partido[0];
														} elseif(strpos($partido, "Diputados de ") !== FALSE) {
															$partido = explode('Diputados de ', $partido);
															$partido = explode(' que', $partido[1]);
															$partido = $partido[0];
														} elseif(strpos($partido, "Diputados ") !== FALSE) {
															$partido = explode('Diputados ', $partido);
															$partido = explode(' que', $partido[1]);
															$partido = $partido[0];
														} else {
															$partido = "Desconocido";
														}
														
														$count = 0;
														#limpiamos el array de representates
														foreach($lista_array as $number => $diputado) {
															if(trim($diputado) != "") {
																$diputado             = explode(":", $diputado);
																$lista_array[$number] = trim($diputado[1]);
																$count++;
															} else {
																unset($lista_array[$number]);
															}
														}
														
														if($count >= 1) {
															$iniciativa_array["votos_nombres"][$count_votacion][$tipo][$partido] = $lista_array;
														}
													}
												}
											}
											
											$array_votaciones2 = array();
											
											foreach($encabezados as $key => $value) {
												$array_votaciones2[trim($value)] = array(
													"favor" 	 => trim($tablas3[$key]),
													"contra" 	 => trim($tablas4[$key]),
													"abstencion" => trim($tablas5[$key]),
													"quorum" 	 => trim($tablas6[$key]),
													"ausente" 	 => trim($tablas7[$key]),
													"total"		 => trim($tablas8[$key])
												);
											}
											
											$iniciativa_array["votaciones"][$count_votacion] = $array_votaciones2;
											$count_votacion++;
										}
									}
									
									if($count_votacion == 0) {
										unset($iniciativa_array["votaciones"]);
										unset($iniciativa_array["votos_nombres"]);
									}
								}
								
								#guardamos iniciativa en la BD
								$contador = guardaIiniciativa($iniciativa_array, $IniciativasBD, $contador);
							}
						}
					}
				}
			}
			
			#separación de grupos
			echo "\n\n ......................................................... \n\n";
		}
	} else {
		echo "\n\n Algo extraño ocurrio :/ \n\n";
		die("");
	}
}

#mensaje de termino de scrapping
echo "\n\nEl scrapping ha terminado, total de iniciativas guardadas: " . $contador . " - Revisa la base de datos #MezcalSinControl :)\n";
echo "Hora y fecha actual del fin: " . date("Y-m-d H:i:s") . "\n";

#incluye los archivos y crea la conexión a la base de datos
function conexionBD() {
	include_once "class/functions/string.php";
	include_once "class/iniciativas.php";
	
	$Conexion = new Iniciativas();
	return $Conexion;
}

#recibe el array a guadar y la conexión de la base de datos
function guardaIiniciativa($iniciativa, $IniciativasBD, $contador) {
	#guaardo la iniciativa
	$id_iniciativa = $IniciativasBD->guardar($iniciativa);
	
	#compruebo que no hubo erro
	if($id_iniciativa !== false) {
		#si ya existe pero tiene modificaciones
		if(is_array($id_iniciativa) and isset($id_iniciativa["existe"])) {
			#aumentamos el contador de iniciativas guardadas e imprimimos el log
			$contador++;
			
			#log de que ya existe pero tiene modifiaciones
			echo "\n\n########################## \n";
				echo "\n " . $contador . ".- Iniciativa ya existe pero tiene modificaciones: " . utf8_encode($iniciativa["titulo_listado"]) . "\n";
				echo "El ID de esta iniciativa es: " . $id_iniciativa["id_initiative"] . "\n";
				
				#guardamos los que presentan
				if(isset($iniciativa["presentada_array"])) {
					$presentada  = $IniciativasBD->guardarPresentada($id_iniciativa["id_initiative"], $iniciativa["presentada_array"]);
				}
				
				#guardamos las comisiones a las que han sido turnadas
				if(isset($iniciativa["turnada_array"])) {
					$presentada  = $IniciativasBD->guardarTurnada($id_iniciativa["id_initiative"], $iniciativa["turnada_array"]);
				}
				
				#guardamos los pasos/estatus de la iniciativa
				$estatus  = $IniciativasBD->guardarEstatus($id_iniciativa["id_initiative"], $iniciativa["estatus"]);
				
				#compruebo que existan votaciones para guardarlas en la base de datos, guarda tambipen los nombres de los representantes
				if(isset($iniciativa["votaciones"])) {
					$votacion  = $IniciativasBD->guardarVotacion($id_iniciativa["id_initiative"], $iniciativa["votaciones"]);
					$votos_nom = $IniciativasBD->guardarVotacionNombres($id_iniciativa["id_initiative"], $iniciativa["votos_nombres"]);
					
					if($votacion === true) {
						echo "**** Votación Guardada ****\n";
					} else {
						echo "**** Votación NO Guardada ****\n";
					}
				} else {
					echo "**** No hay votación ****\n";
				}
				
			echo "########################## \n\n";
		} else {
			#aumentamos el contador de iniciativas guardadas e imprimimos el log
			$contador++;
			
			echo "\n\n##########################";
				echo "\n " . $contador . ".- Iniciativa Guardada: " . utf8_encode($iniciativa["titulo_listado"]) . "\n";
				echo "El ID de esta iniciativa es: " . $id_iniciativa . "\n";
				
				#guardamos los que presentan
				if(isset($iniciativa["presentada_array"])) {
					$presentada  = $IniciativasBD->guardarPresentada($id_iniciativa, $iniciativa["presentada_array"]);
				}
				
				#guardamos las comisiones a las que han sido turnadas
				if(isset($iniciativa["turnada_array"])) {
					$presentada  = $IniciativasBD->guardarTurnada($id_iniciativa, $iniciativa["turnada_array"]);
				}
				
				#guardamos los pasos/estatus de la iniciativa
				$estatus  = $IniciativasBD->guardarEstatus($id_iniciativa, $iniciativa["estatus"]);
				
				#compruebo que existan votaciones para guardarlas en la base de datos, guarda tambipen los nombres de los representantes
				if(isset($iniciativa["votaciones"])) {
					$votacion  = $IniciativasBD->guardarVotacion($id_iniciativa, $iniciativa["votaciones"]);
					$votos_nom = $IniciativasBD->guardarVotacionNombres($id_iniciativa, $iniciativa["votos_nombres"]);
					
					if($votacion === true) {
						echo "**** Votación Guardada ****\n";
					} else {
						echo "**** Votación NO Guardada ****\n";
					}
				} else {
					echo "**** No hay votación ****\n";
				}
			echo "##########################\n\n";
		}
	} else {
		#mando el log que no se pudo guardar y hago un dump de la variable
		echo "\n\n########################## \n";
		echo "\n Iniciativa NO Guardada: " . utf8_encode($iniciativa["titulo_listado"]) . "\n";
		
		echo "\n\n################\n\n";
	}
	
	return $contador;
}

/*comprueba si la iniciativa ya existe y es identica*/
function isSame($iniciativa, $IniciativasBD) {
	$result = $IniciativasBD->isSame($iniciativa);	
	
	if(is_array($result) and $result != false) {
		echo "\n\n########################## \n";
			echo "Existente - es identica a: " . utf8_encode($iniciativa["titulo_listado"]) . "\n";
			echo "El registro (ID) con el cual es identica es: " . $result[0]["id_initiative"] . "\n";
		echo "########################## \n\n";
		
		return true;
	} else {
		return false;
	}
}

/*obtiene los pasos/estatus que contiene una inicitiava*/
function pasos($contenido_html) {
	$contenido_html = strip_tags($contenido_html, '<a><br>');
	$contenido_html = strip_tags($contenido_html, '<a><br>');
	
	$pasos_array 	= explode('<br>', $contenido_html);
	$pasos          = array();
	
	if(is_array($pasos_array) and count($pasos_array) > 1) {
		unset($pasos_array[0]);
		
		foreach($pasos_array as $paso) {
			$titulo        = $paso;
			$titulo_limpio = strip_tags($paso, '');
			
			$pasos[] = array(
				"titulo"        => trim($titulo),
				"titulo_limpio" => trim($titulo_limpio),
				"tipo" 			=> tipo($titulo_limpio),
				"votacion"      => esVotacion($titulo_limpio)
			);
		}
		
		return $pasos;
	}
	
	return false;
}

/*Comprueba si en el string se tiene una votación*/
function esVotacion($string = "") {
	if(strpos(utf8_encode($string), "Votación") !== false) {
		return "true";
	} else {
		return "false";
	}
}

/*obtiene el tipo de paso/estatus de la iniciativa*/
function tipo($string = "") {
	if(strpos($string, "Enviada") !== false or strpos($string, "Enviado") !== false) {
		$tipo = "Enviada";
	} elseif(strpos($string, "Presentada") !== false) {
		$tipo = "Presentada";
	} elseif(strpos($string, "Turnada") !== false) {
		$tipo = "Turnada";
	} elseif(strpos($string, "Dictaminada y aprobada") !== false) {
		$tipo = "Dictaminada y aprobada ";
	} elseif(strpos($string, "Dictaminada en sentido negativo") !== false) {
		$tipo = "Dictaminada en sentido negativo";
	} elseif(strpos($string, "Dictaminada") !== false) {
		$tipo = "Dictaminada";
	} elseif(strpos($string, "Publicado") !== false) {
		$tipo = "Publicado";
	} elseif(strpos($string, "Devuelta") !== false) {
		$tipo = "Devuelta";
	} elseif(strpos($string, "Gaceta Parlamentaria") !== false) {
		$tipo = "Gaceta Parlamentaria";
	} elseif(strpos($string, "Se le dispensaron") !== false) {
		$tipo = utf8_decode("Se le dispensaron todos los trámites");
	} elseif(strpos($string, "Aprobada") !== false) {
		$tipo = "Aprobada";
	} elseif(strpos(utf8_encode($string), "Prórroga") !== false) {
		$tipo = utf8_decode("Prórroga");
	} elseif(strpos($string, "Precluida") !== false) {
		$tipo = "Precluida";
	} elseif(strpos($string, "Desechada") !== false) {
		$tipo = "Desechada";
	} else {
		$tipo = "Otro";
	}
	
	return $tipo;
}

function getMes($mes) {
	switch($mes) {
	   case 'Enero': return 1; break;
	   case 'Febrero': return 2; break;
	   case 'Marzo': return 3; break;
	   case 'Abril': return 4; break;
	   case 'Mayo': return 5; break;
	   case 'Junio': return 6; break;
	   case 'Julio': return 7; break;
	   case 'Agosto': return 8; break;
	   case 'Septiembre': return 9; break;
	   case 'Octubre': return 10; break;
	   case 'Noviembre': return 11; break;
	   case 'Diciembre': return 12; break;
	}
}
