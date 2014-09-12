CREATE TABLE iniciativas_scrapper (
  `id_iniciativa` serial,
  `id_parent` integer not null,
  `id_legislatura` integer not null,
  `fecha_listado_tm` timestamp NULL DEFAULT NULL,
  `fecha_listado` varchar(255) DEFAULT NULL,
  `titulo` text DEFAULT NULL,
  `titulo_listado` text DEFAULT NULL,
  `enlace_dictamen_listado` varchar(255) DEFAULT NULL,
  `enlace_publicado_listado` varchar(255) DEFAULT NULL,
  `enlace_gaceta` varchar(255) DEFAULT NULL,
  `html_listado` text DEFAULT NULL,
  `contenido_html_iniciativa` text DEFAULT NULL,
  `enviada` text DEFAULT NULL,
  `turnada` text DEFAULT NULL,
  `presentada` text DEFAULT NULL,
  `periodo` varchar(255) DEFAULT NULL
);
create index on iniciativas_scrapper(id_iniciativa);
create index on iniciativas_scrapper(id_parent);
create index on iniciativas_scrapper(id_legislatura);

CREATE TABLE votaciones_partidos_scrapper (
  `id_voto` serial,
  `id_contador_voto` integer not null,
  `id_iniciativa` integer not null,
  `id_partido` integer not null,
  `tipo` varchar(255) DEFAULT NULL,
  `favor` integer NOT NULL default 0,
  `contra` integer NOT NULL default 0,
  `abstencion` integer NOT NULL default 0,
  `quorum` integer NOT NULL default 0,
  `ausente` integer NOT NULL default 0,
  `total` integer NOT NULL default 0
);
create index on votaciones_partidos_scrapper(id_voto);
create index on votaciones_partidos_scrapper(id_contador_voto);
create index on votaciones_partidos_scrapper(id_iniciativa);
create index on votaciones_partidos_scrapper(id_partido);
create index on votaciones_partidos_scrapper(tipo);

CREATE TABLE votaciones_representantes_scrapper (
  `id_voto_representante` serial,
  `id_contador_voto` integer NOT NULL default 1,
  `id_iniciativa` integer NOT NULL,
  `id_partido` integer NOT NULL,
  `nombre` varchar(255) NOT NULL default 0,
  `partido` varchar(255) NOT NULL default 0,
  `tipo` varchar(255) DEFAULT NULL
);
create index on votaciones_representantes_scrapper(id_voto_representante);
create index on votaciones_representantes_scrapper(id_contador_voto);
create index on votaciones_representantes_scrapper(id_iniciativa);
create index on votaciones_representantes_scrapper(id_partido);
create index on votaciones_representantes_scrapper(tipo);
create index on votaciones_representantes_scrapper(nombre);
create index on votaciones_representantes_scrapper(partido);

CREATE TABLE representatives_scrapper (
  `id_representative` serial,
  `id_representative_type` integer not null,
  `name` varchar(255) DEFAULT NULL,
  `id_political_party` integer not null,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `avatar_id` varchar(255) DEFAULT NULL,
  `birthday` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT now(),
  `updated_at` timestamp NULL DEFAULT NULL,
  `birth_state` varchar(255) DEFAULT NULL,
  `birth_city` varchar(255) DEFAULT NULL,
  `election_type` varchar(255) DEFAULT NULL,
  `zone_state` varchar(255) DEFAULT NULL,
  `district_circumscription` varchar(255) DEFAULT NULL,
  `fecha_protesta` varchar(255) DEFAULT NULL,
  `ubication` varchar(255) DEFAULT NULL,
  `substitute` varchar(255) DEFAULT NULL,
  `ultimo_grado_estudios` varchar(255) DEFAULT NULL,
  `career` varchar(255) DEFAULT NULL,
  `exp_legislative` varchar(255) DEFAULT NULL,
  `id_legislature` integer not null,
  `commisions` varchar(255) DEFAULT NULL,
  `suplentede` varchar(255) DEFAULT NULL,
 )  
create index on representatives_scrapper(id_iniciativa);
create index on representatives_scrapper(id_representative_type);
create index on representatives_scrapper(id_legislature);



 
CREATE TABLE representative_type (
  `id_representative_type` serial,
  `name` varchar(255) DEFAULT NULL,
  `slug` varchar(255) DEFAULT NULL,
  ) 
create index on representative_type(id_representative_type);
create index on representative_type(name);


