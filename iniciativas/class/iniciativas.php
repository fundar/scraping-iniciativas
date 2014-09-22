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
		$this->pgsql->query("SET NAMES 'utf8'");
	}
	
	/*guarda en base de datos la iniciativa*/
	public function guardar($iniciativa) {
		$data = $this->isExists($iniciativa);
		
		#eliminamos arrays de votaciones para solo dejar lo de iniciativas
		if(isset($iniciativa["votaciones"])) {
			unset($iniciativa["votaciones"]);
			unset($iniciativa["votos_nombres"]);
		}
		
		#eliminamos arrays de estatus para solo dejar lo de iniciativas
		if(isset($iniciativa["estatus"])) {
			unset($iniciativa["estatus"]);
		}
			
		#si no existe la guarda por primera
		if($data == false) {
			#La guarda pero con un id_parten 0
			$iniciativa["id_parent"] = 0;
			$id_iniciativa 		     = $this->save("iniciativas_scrapper", $iniciativa, "id_iniciativa");
			
			if(is_int($id_iniciativa)) {
				return $id_iniciativa;
			} else {
				return false;
			}
		} else {
			#si ya existe la guarda pero con un id_parten de la que ya existe
			$iniciativa["id_parent"] = $data[0]["id_iniciativa"];
			$id_iniciativa 			 = $this->save("iniciativas_scrapper", $iniciativa, "id_iniciativa");
			
			if(is_int($id_iniciativa)) {
				return array("existe" => "existe", "id_iniciativa" => $id_iniciativa);
			} else {
				return false;
			}
		}
	}
	
	/*guardamos los pasos/estatus de la iniciativa*/
	public function guardarEstatus($id_iniciativa = false, $estatus) {
		#compruebo que no este en falso la iniciativa
		if($id_iniciativa != false) {
			#recorro los estatus para guardar uno por uno
			foreach($estatus as $key => $value) {
				#formo el query para los estatys
				$query  = "insert into estatus_iniciativas_scrapper";
				$fields = "(id_iniciativa, titulo, titulo_limpio, tipo, votacion) ";
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
					$fields = "(id_contador_voto, id_iniciativa, tipo, favor, contra, abstencion, quorum, ausente, total) ";
					
					$contador = $key2+1;
					$values   = "(" . $contador . "," . $id_iniciativa . ",'" . $key . "'," . $voto["favor"] . "," .  $voto["contra"] . "," .  $voto["abstencion"] . ",";
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
							$fields   = "(id_contador_voto, id_iniciativa, nombre, partido, tipo) ";
							$values   = "(" . $contador . "," . $id_iniciativa . ",'" . $nombre . "','" . $key_partido . "','" .  $key_tipo . "')";
							
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
	
	/*funcion para hacer un insert en un tabla, paramtros array y tabla*/
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
}
