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
//id de photo



     // Nombre
	$explode = explode("<td class='tddatosazul'>Diputado Propietario:<br><b>", $resultado);
	if(isset($explode[1])) {
		$explode = explode('<br>', $explode[1]);
		$nombre  = $explode[0];
	}

     else {
		$explode = explode("<td class='tddatosazul'>Diputada Propietario:<br><b>", $resultado);
		if(isset($explode[1])) {
			$explode = explode('<br>', $explode[1]);
			$nombre  = $explode[0];
		}
           else {
                       $explode = explode("<td class='tddatosazul'>Diputado Suplente:<br><b>", $resultado);
		if(isset($explode[1])) {
			$explode = explode('<br>', $explode[1]);
			$nombre  = $explode[0];
		}
                   else {
		$explode = explode("<td class='tddatosazul'>Diputada Suplente:<br><b>", $resultado);
		if(isset($explode[1])) {
			$explode = explode('<br>', $explode[1]);
			$nombre  = $explode[0];
		}
           else{

			$nombre = "null";
            }
            }

		}
	}
             //Partido
	$explode = explode("<td class='tdcriterio'>&nbsp;Partido:</td>", $resultado);
	if(isset($explode[1])) {
		$explode = explode("<td class='tddatosazul'>", $explode[1]);
		$explode = explode("</td>", $explode[1]);
		$partido = $explode[0];
	} else {
		$partido = "null";
	}

      //correo electronico
	$explode = explode('nico:</td>', $resultado);
	if(isset($explode[1])) {
		$explode = explode("<td class='tddatosazul'>", $explode[1]);
		$explode = explode("</td>", $explode[1]);
		$email   = $explode[0];
	} else {
		$email = "null";
	}

        //telefono
	$explode = explode("fono:</td>", $resultado);
	if(isset($explode[1])) {
		$explode = explode("<td class='tddatosazul'>", $explode[1]);
		$explode = explode("</td>", $explode[1]);
		$telefono = $explode[0];
	} else {
		$telefono = "null";
	}

     // Nacimientos

     $explode = explode("miento:</td>", $resultado);
	if(isset($explode[1])) {
		$explode = explode("<td class='tddatosazul'>Fecha: ", $explode[1]);
		$explode = explode("<br>", $explode[1]);
		$nacimiento = $explode[0];
	} else {
		$nacimiento = "null";
	}

    //Entidad

     $explode = explode("<br>Entidad: ", $resultado);
	if(isset($explode[1])) {
     	$explode = explode("<br>", $explode[1]);
		$entidad = $explode[0];
	} else {
		$entidad= "null";
	}
    // Ciudad
     $explode = explode("<br>Ciudad: ", $resultado);
	if(isset($explode[1])) {
     	$explode = explode("<br>", $explode[1]);
		$ciudad = $explode[0];
	} else {
		$ciudad= "null";
	}

    // Prinicipio de Eleccion

     $explode = explode("&oacute;n:</td>", $resultado);
	if(isset($explode[1])) {
		$explode = explode("<td class='tddatosazul'>", $explode[1]);
		$explode = explode("</td>", $explode[1]);
		$eleccion = $explode[0];
	} else {
		$eleccion = "null";
	}

    //Zona : Entidad

     $explode = explode("&nbsp;Zona:</td>", $resultado);
	if(isset($explode[1])) {
		$explode = explode("<td class='tddatosazul'>
                    Entidad: ", $explode[1]);
		$explode = explode("<br>", $explode[1]);
		$zona = $explode[0];
	} else {
		$zona = "null";
	}

    // distrito o circunscripcion

$explode = explode("<br>Distrito:", $resultado);
	if(isset($explode[1])) {
     	$explode = explode("<br>", $explode[1]);
		$distrito = $explode[0];
	}
    else {
		    $explode = explode("<br>Circunscripcion:", $resultado);
		    if(isset($explode[1])) {
			$explode = explode('<br>', $explode[1]);
			$distrito  = $explode[0];
                                     }
     else {
		$distrito= "null";
	}
        }

 // Fecha_protesta
     $explode = explode("testa:</td>", $resultado);
	if(isset($explode[1])) {
		$explode = explode("<td class='tddatosazul'>", $explode[1]);
		$explode = explode("</td>", $explode[1]);
		$protesta = $explode[0];
	} else {
		$protesta = "null";
	}


//Ubicacion

     $explode = explode("Ubicaci&oacute;n:</td>", $resultado);
	if(isset($explode[1])) {
		$explode = explode("<td class='tddatosazul'>", $explode[1]);
		$explode = explode("</td>", $explode[1]);
		$ubicacion = $explode[0];
	} else {
		$ubicacion = "null";
	}


 // supplente

     $explode = explode("Suplente:</td>", $resultado);
	if(isset($explode[1])) {
		$explode = explode("<td class='tddatosazul'>", $explode[1]);
		$explode = explode("</td>", $explode[1]);
		$suplente = $explode[0];
	} else {
		$suplente = "null";
	}

 // Grado

     $explode = explode("estudios:</td>", $resultado);
	if(isset($explode[1])) {
		$explode = explode("<td class='tddatosazul'>", $explode[1]);
		$explode = explode("</td>", $explode[1]);
		$grado = $explode[0];
	} else {
		$grado = "null";
	}

 // carrera

     $explode = explode("mica:</td>", $resultado);
	if(isset($explode[1])) {
		$explode = explode("<td class='tddatosazul'>", $explode[1]);
		$explode = explode("</td>", $explode[1]);
		$carrera = $explode[0];
	} else {
		$carrera = "null";
	}

 // experiencia

     $explode = explode("ativa:</td>", $resultado);
	if(isset($explode[1])) {
    $explode = explode("<td class='tddatosazul'>
                    <table>", $explode[1]);
		$explode = explode("</table>", $explode[1]);
		$xp = $explode[0];
	} else {
		$xp = "null";
	}

 //comisiones
     $explode = explode("tdcriterio'>Comisi&oacute;n</td>", $resultado);
	    if(isset($explode[1])) {
        $explode = explode("<td class='tddatosazul'>", $explode[1]);
		$explode = explode("</table>", $explode[1]);
        $comisiones=$explode[0];
	} else {
		$comisiones = "null";
	}


//suplente de

    $explode = explode("tdcriterio'>Suplente de:</td>", $resultado);
	    if(isset($explode[1])) {
        $explode = explode("<td class='tddatosazul'>", $explode[1]);
		$explode = explode("</td>", $explode[1]);
        $suplentede=$explode[0];
	} else {
		$suplentede = "null";
	}




echo "http://sil.gobernacion.gob.mx/Archivos/Fotos/".$id.".jpg"."|" . $nombre . "|" . $partido . "|" . $email . "|" . $telefono .  "|" . $nacimiento . "|" . $entidad ."|" . $ciudad . "|" . $eleccion .  "|" . $zona . "|" . $distrito . "|" . $protesta . "|".  $ubicacion . "|".   $suplente . "|".    $grado . "|".    $carrera . "|".    $xp . "|". $comisiones . "|".  $suplentede . "|".  "<br/>";

}
