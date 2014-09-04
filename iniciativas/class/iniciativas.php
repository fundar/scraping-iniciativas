<?php
/*incluir clase para manejra la BD*/
include_once "db/db.php";

class Iniciativas {
	
	public function __construct() {
		/*configuración de base de datos*/
		include_once "config/database.php";
		
		/*conexion con base de datos*/
		$this->mysql = new Db();
		$this->mysql->connect($db);
		$this->mysql->query("SET NAMES 'utf8′");
	}
	
	/*guarda en base de datos la iniciativa*/
	public function guardar($iniciativa) {
		if(isset($iniciativa["votaciones"])) {
			unset($iniciativa["votaciones"]);
		}
		
		$id_iniciativa = $this->mysql->insert("iniciativas_scrapper", $iniciativa);
		
		if(is_int($id_iniciativa)) {
			return $id_iniciativa;
		} else {
			return false;
		}
	}
	
	/*guarda las votaciones de la iniciativa*/
	public function guardarVotacion($id_iniciativa = false, $votacion) {
		#compruebo que no este en falso la iniciativa
		if($id_iniciativa != false) {
			#recorro los tipos de votos para guardar uno por uno
			foreach($votacion as $key => $voto) {
				#formo el query para las votaciones
				$query  = "insert into votaciones";
				$fields = "(id_iniciativa, tipo, favor, contra, abstencion, quorum, ausente, total) ";
				
				$values  = "(" . $id_iniciativa . ",'" . $key . "'," . $voto["favor"] . "," .  $voto["contra"] . "," .  $voto["abstencion"] . ",";
				$values .= $voto["quorum"] . "," .  $voto["ausente"] . "," .  $voto["total"] . ")";
				
				#inserto el registro en la base de datos
				$query = $query . " " . $fields . " values " . $values;
				$voto = $this->mysql->query($query);
			}
			
			return true;
		} else {
			return false;
		}
	}
}
