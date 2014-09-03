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
		$query = "select * from iniciativas_scrapper";
		$data  = $this->mysql->query($query);
		
		die(var_dump($data));
		
		if($data and is_array($data)) return $data;
		else return false;
	}
	
	/*guarda las votaciones de la iniciativa*/
	public function guardarVotacion($id_iniciativa, $votacion) {
		die("guardar votacion");
	}
}
