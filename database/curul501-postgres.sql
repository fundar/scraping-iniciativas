CREATE TABLE iniciativas_scrapper (
  id_iniciativa serial,
  id_parent integer not null,
  id_legislatura integer not null,
  fecha_listado_tm timestamp NULL DEFAULT NULL,
  fecha_listado varchar(255) DEFAULT NULL,
  titulo text DEFAULT NULL,
  titulo_listado text DEFAULT NULL,
  enlace_dictamen_listado varchar(255) DEFAULT NULL,
  enlace_publicado_listado varchar(255) DEFAULT NULL,
  enlace_gaceta varchar(255) DEFAULT NULL,
  html_listado text DEFAULT NULL,
  contenido_html_iniciativa text DEFAULT NULL,
  enviada text DEFAULT NULL,
  turnada text DEFAULT NULL,
  presentada text DEFAULT NULL,
  periodo varchar(255) DEFAULT NULL
);
create index on iniciativas_scrapper(id_iniciativa);
create index on iniciativas_scrapper(id_parent);
create index on iniciativas_scrapper(id_legislatura);

CREATE TABLE votaciones_partidos_scrapper (
  id_voto serial,
  id_contador_voto integer not null,
  id_iniciativa integer not null,
  id_partido integer not null,
  tipo varchar(255) DEFAULT NULL,
  favor integer NOT NULL default 0,
  contra integer NOT NULL default 0,
  abstencion integer NOT NULL default 0,
  quorum integer NOT NULL default 0,
  ausente integer NOT NULL default 0,
  total integer NOT NULL default 0
);
create index on votaciones_partidos_scrapper(id_voto);
create index on votaciones_partidos_scrapper(id_contador_voto);
create index on votaciones_partidos_scrapper(id_iniciativa);
create index on votaciones_partidos_scrapper(id_partido);
create index on votaciones_partidos_scrapper(tipo);

CREATE TABLE votaciones_representantes_scrapper (
  id_voto_representante serial,
  id_contador_voto integer NOT NULL default 1,
  id_iniciativa integer NOT NULL,
  id_partido integer NOT NULL,
  nombre varchar(255) NOT NULL default 0,
  partido varchar(255) NOT NULL default 0,
  tipo varchar(255) DEFAULT NULL
);
create index on votaciones_representantes_scrapper(id_voto_representante);
create index on votaciones_representantes_scrapper(id_contador_voto);
create index on votaciones_representantes_scrapper(id_iniciativa);
create index on votaciones_representantes_scrapper(id_partido);
create index on votaciones_representantes_scrapper(tipo);
create index on votaciones_representantes_scrapper(nombre);
create index on votaciones_representantes_scrapper(partido);
