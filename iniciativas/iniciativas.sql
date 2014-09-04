CREATE TABLE iniciativas_scrapper (
  `id_iniciativa` int(11) NOT NULL AUTO_INCREMENT,
  `id_legislatura` int(11) NOT NULL,
  `fecha_listado_tm` timestamp NULL DEFAULT NULL,
  `fecha_listado` varchar(255) DEFAULT NULL,
  `titulo` text DEFAULT NULL,
  `titulo_listado` text DEFAULT NULL,
  `enlace_dictamen_listado` varchar(255) DEFAULT NULL,
  `enlace_publicado_listado` varchar(255) DEFAULT NULL,
  `enlace_gaceta` varchar(255) DEFAULT NULL,
  `contenido_html_iniciativa` text DEFAULT NULL,
  `enviada` text DEFAULT NULL,
  `turnada` text DEFAULT NULL,
  `presentada` text DEFAULT NULL,
  PRIMARY KEY (`id_iniciativa`)
);


CREATE TABLE votaciones (
  `id_voto` int(11) NOT NULL AUTO_INCREMENT,
  `id_iniciativa` int(11) NOT NULL,
  `tipo` varchar(255) DEFAULT NULL,
  `favor` integer NOT NULL default 0,
  `contra` integer NOT NULL default 0,
  `abstencion` integer NOT NULL default 0,
  `quorum` integer NOT NULL default 0,
  `ausente` integer NOT NULL default 0,
  `total` integer NOT NULL default 0,
  PRIMARY KEY (`id_voto`)
);
