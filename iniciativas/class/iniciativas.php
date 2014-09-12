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
		
		#si no existe la guarda por primera
		if($data == false) {
			#eliminamos arrays de votaciones para solo dejar lo de iniciativas
			if(isset($iniciativa["votaciones"])) {
				unset($iniciativa["votaciones"]);
				unset($iniciativa["votos_nombres"]);
			}
			
			#La guarda pero con un id_parten 0
			$iniciativa["id_parent"] = 0;
			$id_iniciativa 			 = $this->pgsql->insert("iniciativas_scrapper", $iniciativa);
			
			if(is_int($id_iniciativa)) {
				return $id_iniciativa;
			} else {
				return false;
			}
		} else {
			#si ya existe la guarda pero con un id_parten de la que ya existe
			$iniciativa["id_parent"] = $data[0]["id_iniciativa"];
			$id_iniciativa           = $this->pgsql->insert("iniciativas_scrapper", $iniciativa);
			
			if(is_int($id_iniciativa)) {
				return array("existe" => "existe", "id_iniciativa" => $id_iniciativa);
			} else {
				return false;
			}
		}
	}
	
	/*guarda las votaciones de la iniciativa*/
	public function guardarVotacion($id_iniciativa = false, $votacion) {
		#compruebo que no este en falso la iniciativa
		if($id_iniciativa != false) {
			#recorro los tipos de votos para guardar uno por uno
			foreach($votacion as $key => $voto) {
				#formo el query para las votaciones
				$query  = "insert into votaciones_partidos_scrapper";
				$fields = "(id_iniciativa, tipo, favor, contra, abstencion, quorum, ausente, total) ";
				
				$values  = "(" . $id_iniciativa . ",'" . $key . "'," . $voto["favor"] . "," .  $voto["contra"] . "," .  $voto["abstencion"] . ",";
				$values .= $voto["quorum"] . "," .  $voto["ausente"] . "," .  $voto["total"] . ")";
				
				#inserto el registro en la base de datos
				$query = $query . " " . $fields . " values " . $values;
				$voto = $this->pgsql->query($query);
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
			foreach($votos as $key_tipo => $voto) {
				foreach($voto as $key_partido => $tipo) {
					foreach($tipo as $key_nombre => $nombre) {
						#formo el query para las votaciones
						$query  = "insert into votaciones_representantes_scrapper";
						$fields = "(id_iniciativa, nombre, partido, tipo) ";
						$values = "(" . $id_iniciativa . ",'" . $nombre . "','" . $key_partido . "','" .  $key_tipo . "')";
						
						#inserto el registro en la base de datos
						$query  = $query . " " . $fields . " values " . $values;
						$result = $this->pgsql->query($query);
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
		die(var_dump($data));
		
		return $data;
	}
	
	/*comprueba si la iniciativa es igual [titulo y html]*/
	public function isSame($iniciativa) {
		$query = utf8_encode("select * from iniciativas_scrapper where titulo_listado='" . $iniciativa["titulo_listado"] . "' and html_listado='" . $iniciativa["html_listado"]  . "'");
		$data = $this->pgsql->query($query);
		die(var_dump($data));
		
		return $data;
	}
}
