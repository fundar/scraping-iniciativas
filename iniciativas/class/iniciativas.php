<?php
/*incluir clase para manejra la BD*/
include_once "db/db.php";

class Iniciativas {
	
	public function __construct() {
		/*configuración de base de datos*/
		include_once "config/database.php";
		
		/*conexion con base de datos*/
		$this->pgsql = new Db();
		$this->pgsql->connect($db);
		//$this->pgsql->query("SET NAMES 'utf8'");
	}
	
	/*guarda en base de datos la iniciativa*/
	public function guardar($iniciativa) {
		$data = $this->isExists($iniciativa);
		
		/*eliminamos arrays que no se ocupan*/
		if(isset($iniciativa["presentada_array"])) {
			unset($iniciativa["presentada_array"]);
		}
		
		/*eliminamos arrays que no se ocupan*/
		if(isset($iniciativa["turnada_array"])) {
			unset($iniciativa["turnada_array"]);
		}
		
		#eliminamos arrays de votaciones para solo dejar lo de iniciativas
		if(isset($iniciativa["votaciones"])) {
			unset($iniciativa["votaciones"]);
			unset($iniciativa["votos_nombres"]);
		}
		
		#eliminamos arrays de estatus para solo dejar lo de iniciativas
		if(isset($iniciativa["estatus"])) {
			unset($iniciativa["estatus"]);
		}
		
		//si no hay fechas las eliminamos
		if($iniciativa["fecha_listado_tm"]        == "") unset($iniciativa["fecha_listado_tm"]);
		if($iniciativa["fecha_votacion_tm"]       == "") unset($iniciativa["fecha_votacion_tm"]);
		if($iniciativa["fecha_listado_header_tm"] == "") unset($iniciativa["fecha_listado_header_tm"]);
		
		#si no existe la guarda
		if($data == false) {
			#La guarda pero con un id_parten 0
			$iniciativa["id_parent"] = 0;
			$id_iniciativa 		     = $this->save("iniciativas_scrapper", $iniciativa, "id_initiative");
			
			if(is_int($id_iniciativa)) {
				return $id_iniciativa;
			} else {
				return false;
			}
		} else {
			#si ya existe la guarda pero con un id_parten de la que ya existe
			$iniciativa["id_parent"] = $data[0]["id_initiative"];
			$id_iniciativa 			 = $this->save("iniciativas_scrapper", $iniciativa, "id_initiative");
			
			if(is_int($id_iniciativa)) {
				return array("existe" => "existe", "id_initiative" => $id_iniciativa);
			} else {
				return false;
			}
		}
	}
	
	/*guardamos los que presentan la iniciativa en una talba*/
	public function guardarPresentada($id_iniciativa = false, $array) {
		if($id_iniciativa != false) {
			foreach($array as $key => $value) {
				$relation = false;
				
				if(strpos($value, "Parlamentario") !== false) {
					$slug = "";
					
					if(strpos($value, "PVEM") !== false) {
						$slug = "partido-verde-ecologista-de-mexico";
					}
					
					if(strpos($value, "PRD") !== false) {
						$slug = "partido-de-la-revolucion-democratica";
					}
					
					if(strpos($value, "PRI") !== false) {
						$slug = "partido-revolucionario-institucional";
					}
					
					if(strpos($value, "PAN") !== false) {
						$slug = "partido-accion-nacional";
					}
					
					if(strpos($value, "PT") !== false) {
						$slug = "partido-del-trabajo";
					}
					
					if(strpos($value, "Partido del Trabajo") !== false) {
						$slug = "partido-del-trabajo";
					}
					
					if(strpos($value, "Nueva Alianza") !== false) {
						$slug = "partido-nueva-alianza";
					}
					
					if(strpos($value, "Movimiento Ciudadano") !== false) {
						$slug = "movimiento-ciudadano";
					}
					
					$id_partido = $this->getIDPartido($slug, true);
					
					if($id_partido != 0) {
						$relation["id_political_party"] = $id_partido;
						$relation["id_initiative"]      = $id_iniciativa;
						$relation_save 		            = $this->save("initiative2political_party", $relation, "id_initiative");
					}
				} elseif(strpos($value, "Senadores") !== false or strpos($value, "Congreso") !== false or strpos($value, "Ejecutivo federal") !== false) {
					$value         = trim($value);
					$id_dependency = $this->getIDDependency(slug(utf8_encode($value)), $value);
					
					if($id_dependency != 0) {
						$relation["id_dependency"] = $id_dependency;
						$relation["id_initiative"] = $id_iniciativa;
						$relation_save 		       = $this->save("initiative2dependencies", $relation, "id_initiative");
					}
				} else {
					$value             = trim($value);
					$id_representative = $this->getIDRepresentante($value, false, "slug2");
					
					if($id_representative != 0) {
						$relation["id_representative"] = intval($id_representative);
						$relation["id_initiative"]     = $id_iniciativa;
						$relation_save 		           = $this->save("initiative2representatives", $relation, "id_representative");
					}
				}
				
				$relation = false;
			}
		} else {
			return false;
		}
	}
	
	/*guardar turnada - comisiones*/
	public function guardarTurnada($id_iniciativa = false, $array) {
		if($id_iniciativa != false) {
			foreach($array as $key => $value) {
				$value         = trim($value);
				$id_commission = $this->getIDCommission($value);
				
				if($id_commission != 0) {
					$relation["id_commission"] = intval($id_commission);
					$relation["id_initiative"] = $id_iniciativa;
					$relation_save 		       = $this->save("commissions2initiatives", $relation, "id_commission");
				}
			}
		}
		
		return false;
	}
	
	/*guardamos los pasos/estatus de la iniciativa*/
	public function guardarEstatus($id_iniciativa = false, $estatus) {
		#compruebo que no este en falso la iniciativa
		if($id_iniciativa != false) {
			#recorro los estatus para guardar uno por uno
			foreach($estatus as $key => $value) {
				#formo el query para los estatys
				$query  = "insert into estatus_iniciativas_scrapper";
				$fields = "(id_initiative, titulo, titulo_limpio, tipo, votacion) ";
				$values = "(" . $id_iniciativa . ",'" . $value["titulo"] . "','" .  $value["titulo_limpio"] . "','" .  $value["tipo"] . "'," .  $value["votacion"] . ")";
				
				#inserto el registro en la base de datos
				$query    = utf8_encode($query . " " . $fields . " values " . $values);
				$estatus  = $this->pgsql->query($query);
			}
			
			return true;
		} else {
			return false;
		}
	}
	
	/*guarda las votaciones de la iniciativa*/
	public function guardarVotacion($id_iniciativa = false, $votacion) {
		#compruebo que no este en falso la iniciativa
		if($id_iniciativa != false) {
			#recorro los tipos de votos para guardar uno por uno
			foreach($votacion as $key2 => $voto2) {
				foreach($voto2 as $key => $voto) {
					#formo el query para las votaciones
					$query  = "insert into votaciones_partidos_scrapper";
					$fields = "(id_contador_voto, id_initiative, id_political_party, tipo, favor, contra, abstencion, quorum, ausente, total) ";
					
					$contador = $key2+1;
					$values   = "(" . $contador . "," . $id_iniciativa . "," . $this->getIDPartido($key) . ",'" . $key . "'," . $voto["favor"] . "," .  $voto["contra"] . "," .  $voto["abstencion"] . ",";
					$values  .= $voto["quorum"] . "," .  $voto["ausente"] . "," .  $voto["total"] . ")";
					
					#inserto el registro en la base de datos
					$query = utf8_encode($query . " " . $fields . " values " . $values);
					$voto  = $this->pgsql->query($query);
				}
			}
			
			return true;
		} else {
			return false;
		}
	}
	
	/*guarda los nombres de los representantes en las votaciones de la iniciativa*/
	public function guardarVotacionNombres($id_iniciativa = false, $votos) {
		#compruebo que no este en falso la iniciativa
		if($id_iniciativa != false) {
			#recorro los tipos de votos para guardar uno por uno, recorre los tipos de votos y los nombres de representantes
			foreach($votos as $key2 => $voto2) {
				foreach($voto2 as $key_tipo => $voto) {
					foreach($voto as $key_partido => $tipo) {
						foreach($tipo as $key_nombre => $nombre) {
							#formo el query para las votaciones
							$contador = $key2+1;
							$query    = "insert into votaciones_representantes_scrapper";
							$fields   = "(id_contador_voto, id_initiative, id_political_party, id_representative, nombre, partido, tipo) ";
							$values   = "(" . $contador . "," . $id_iniciativa . "," . $this->getIDPartido($key_partido) . "," . $this->getIDRepresentante($nombre) . ",'" . $nombre . "','" . $key_partido . "','" .  $key_tipo . "')";
							
							#inserto el registro en la base de datos
							$query  = utf8_encode($query . " " . $fields . " values " . $values);
							$result = $this->pgsql->query($query);
						}
					}
				}
			}
			
			return true;
		} else {
			return false;
		}
	}
	
	/*Busca y regresa el ID del representante*/
	public function getIDRepresentante($value = "", $slug = false, $field = "slug") {
		if($slug) {
			$slug = $value;
		} else {
			$slug = slug(utf8_encode($value));
		}
		
		if($slug) {
			$query = "select id_representative from representatives_scrapper where " . $field ."='" . $slug . "'";
			$data  = $this->pgsql->query($query);
			
			if(is_array($data) and isset($data[0]["id_representative"])) {
				return $data[0]["id_representative"];
			} else {
				$query = "select id_representative from representative_repeat where name='" . $value . "'";
				$data  = $this->pgsql->query($query);
				
				if(is_array($data) and isset($data[0]["id_representative"])) {
					return $data[0]["id_representative"];
				} else {
					return 0;
				}
			}
		} else {
			return 0;
		}
	}
	
	/*Busca y regresa el ID del partido politco*/
	public function getIDPartido($partido = "", $slug = false) {
		if($slug) {
			$slug = $partido;
		} else {
			$slug = $this->getSlugPartido($partido);
		}
		
		if($slug) {
			$query = "select id_political_party from political_parties where slug='" . $slug . "'";
			$data  = $this->pgsql->query($query);
			
			if(is_array($data) and isset($data[0]["id_political_party"])) {
				return $data[0]["id_political_party"];
			} else {
				return 0;
			}
		} else {
			return 0;
		}
	}
	
	/*Busca y regresa el ID de la dependencia*/
	public function getIDDependency($slug = "", $value = "") {
		if($slug) {
			$query = "select id_dependency from dependencies where slug='" . $slug . "'";
			$data  = $this->pgsql->query($query);
			
			if(is_array($data) and isset($data[0]["id_dependency"])) {
				return $data[0]["id_dependency"];
			} else {
				$query  = utf8_encode("INSERT INTO dependencies (name, slug) VALUES ('" . $value . "', '" . $slug . "') RETURNING id_dependency");
				$result = $this->pgsql->query($query);
				
				if(is_array($result) and isset($result[0]["id_dependency"])) {
					return intval($result[0]["id_dependency"]);
				} else {
					return 0;
				}
			}
		} else {
			return 0;
		}
	}
	
	/*Busca y regresa el ID de la comision*/
	public function getIDCommission($value = "", $slug = false) {
		if($slug) {
			$slug = $value;
		} else {
			$slug = slug(utf8_encode($value));
		}
		
		if($slug) {
			$query = "select id_commission from commissions where slug='" . $slug . "'";
			$data  = $this->pgsql->query($query);
			
			if(is_array($data) and isset($data[0]["id_commission"])) {
				return $data[0]["id_commission"];
			} else {
				return 0;
			}
		} else {
			return 0;
		}
	}
	
	/*comprueba si existe la iniciativa*/
	public function isExists($iniciativa) {
		$query = utf8_encode("select * from iniciativas_scrapper where titulo_listado='" . $iniciativa["titulo_listado"] . "' and id_parent=0");
		$data  = $this->pgsql->query($query);
		
		return $data;
	}
	
	/*comprueba si la iniciativa es igual [titulo y html]*/
	public function isSame($iniciativa) {
		$query = utf8_encode("select * from iniciativas_scrapper where titulo_listado='" . $iniciativa["titulo_listado"] . "' and html_listado='" . $iniciativa["html_listado"]  . "'");
		$data = $this->pgsql->query($query);
		
		return $data;
	}
	
	/*funcion para hacer un insert en un tabla, paramtros array, tabla e id que regresa*/
	public function save($table, $data, $id_return) {
		$fields = "";
		$values = "";
		
		foreach($data as $field => $value) {
			$fields .= "$field,";
			$values .= "'$value',";
		}
		
		$fields = rtrim($fields, ",");
		$values = rtrim($values, ",");
		$query  = utf8_encode("INSERT INTO $table ($fields) VALUES ($values) RETURNING $id_return");
		$result = $this->pgsql->query($query);
		
		if(is_array($result) and isset($result[0][$id_return])) {
			return intval($result[0][$id_return]);
		} else {
			return false;
		}
	}
	
	/*Obtener el slug del partido*/
	public function getSlugPartido($partido = "") {
		$partido = utf8_encode($partido);
		
		switch($partido) {
			case "pan":
				return "partido-accion-nacional";
				break;
			case "Partido Acción Nacional":
				return "partido-accion-nacional";
				break;
			case "pt":
				return "partido-del-trabajo";
				break;
			case "Partido del Trabajo":
				return "partido-del-trabajo";
				break;
			case "pna":
				return "partido-nueva-alianza";
				break;
			case "Partido Nueva Alianza":
				return "partido-nueva-alianza";
				break;
			case "pri":
				return "partido-revolucionario-institucional";
				break;
			case "Partido Revolucionario Institucional":
				return "partido-revolucionario-institucional";
				break;
			case "prd":
				return "partido-de-la-revolucion-democratica";
				break;
			case "Partido de la Revolución Democrática":
				return "partido-de-la-revolucion-democratica";
				break;
			case "pvem":
				return "partido-verde-ecologista-de-mexico";
				break;
			case "Partido Verde Ecologista de México":
				return "partido-verde-ecologista-de-mexico";
				break;
			case "mc":
				return "movimiento-ciudadano";
				break;
			case "Movimiento Ciudadano":
				return "movimiento-ciudadano";
				break;
			case "sp":
				return "sin-partido";
				break;
			case "total":
				return 0;
				break;
		}
		
		return false;
	}
}
