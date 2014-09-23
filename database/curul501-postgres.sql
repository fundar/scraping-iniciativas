-- ************ Tablas scrapping *************
CREATE TABLE iniciativas_scrapper (
  id_iniciativa serial,
  id_parent integer not null,
  id_legislature integer not null,
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
create index on iniciativas_scrapper(id_legislature);

CREATE TABLE estatus_iniciativas_scrapper (
  id_estatus serial,
  id_iniciativa integer not null,
  titulo text DEFAULT NULL,
  titulo_limpio text DEFAULT NULL,
  tipo varchar(255) DEFAULT NULL,
  votacion boolean default false
);
create index on estatus_iniciativas_scrapper(id_estatus);
create index on estatus_iniciativas_scrapper(id_iniciativa);
create index on estatus_iniciativas_scrapper(tipo);
create index on estatus_iniciativas_scrapper(votacion);

CREATE TABLE votaciones_partidos_scrapper (
  id_voto serial,
  id_contador_voto integer not null default 1,
  id_iniciativa integer not null,
  id_political_party integer not null default 0,
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
create index on votaciones_partidos_scrapper(id_political_party);
create index on votaciones_partidos_scrapper(tipo);

CREATE TABLE votaciones_representantes_scrapper (
  id_voto_representante serial,
  id_contador_voto integer NOT NULL default 1,
  id_iniciativa integer NOT NULL,
  id_political_party integer NOT NULL default 0,
  nombre varchar(255) NOT NULL default 0,
  partido varchar(255) NOT NULL default 0,
  tipo varchar(255) DEFAULT NULL
);
create index on votaciones_representantes_scrapper(id_voto_representante);
create index on votaciones_representantes_scrapper(id_contador_voto);
create index on votaciones_representantes_scrapper(id_iniciativa);
create index on votaciones_representantes_scrapper(id_political_party);
create index on votaciones_representantes_scrapper(tipo);
create index on votaciones_representantes_scrapper(nombre);
create index on votaciones_representantes_scrapper(partido);

CREATE TABLE representatives_scrapper (
  id_representative serial,
  id_representative_type integer not null,
  name varchar(255) DEFAULT NULL,
  id_political_party integer not null,
  email varchar(255) DEFAULT NULL,
  phone varchar(255) DEFAULT NULL,
  avatar_id varchar(255) DEFAULT NULL,
  birthday varchar(255) DEFAULT NULL,
  created_at timestamp NOT NULL DEFAULT now(),
  updated_at timestamp NOT NULL DEFAULT now(),
  birth_state varchar(255) DEFAULT NULL,
  birth_city varchar(255) DEFAULT NULL,
  election_type varchar(255) DEFAULT NULL,
  zone_state varchar(255) DEFAULT NULL,
  district_circumscription varchar(255) DEFAULT NULL,
  fecha_protesta varchar(255) DEFAULT NULL,
  ubication varchar(255) DEFAULT NULL,
  substitute varchar(255) DEFAULT NULL,
  ultimo_grado_estudios varchar(255) DEFAULT NULL,
  career varchar(255) DEFAULT NULL,
  exp_legislative varchar(255) DEFAULT NULL,
  id_legislature integer not null,
  commisions varchar(255) DEFAULT NULL,
  suplentede varchar(255) DEFAULT NULL
);
create index on representatives_scrapper(id_iniciativa);
create index on representatives_scrapper(id_representative_type);
create index on representatives_scrapper(id_legislature);

 
CREATE TABLE representative_type (
  id_representative_type serial,
  name varchar(255) DEFAULT NULL,
  slug varchar(255) DEFAULT NULL
);
create index on representative_type(id_representative_type);
create index on representative_type(name);

truncate table votaciones_partidos_scrapper;
truncate table iniciativas_scrapper;
truncate table votaciones_representantes_scrapper;
truncate table estatus_iniciativas_scrapper;



-- ************ Tablas Admin *************

--tags
CREATE TABLE tags (
  id_tag serial,
  name varchar(255) NOT NULL,
  slug varchar(255) NOT NULL,
  created_at timestamp NOT NULL DEFAULT now()
);
create index on tags(id_tag);

--comisiones
CREATE TABLE commissions (
  id_commission serial,
  name varchar(255) NOT NULL,
  slug varchar(255) NOT NULL,
  created_at timestamp NOT NULL DEFAULT now(),
  updated_at timestamp NOT NULL DEFAULT now(),
  status boolean NOT NULL DEFAULT true,
  secretario varchar(255) NOT NULL
);
create index on commissions(id_commission);

CREATE TABLE commissions2initiatives (
  id_commission integer,
  id_initiative integer
);
create index on commissions2initiatives(id_commission);
create index on commissions2initiatives(id_initiative);

CREATE TABLE commissions2representatives (
  id_commission integer,
  id_representative integer
);
create index on commissions2representatives(id_commission);
create index on commissions2representatives(id_representative);

CREATE TABLE commissions2secretaries (
  id_commission integer,
  id_representative integer
);
create index on commissions2secretaries(id_commission);
create index on commissions2secretaries(id_representative);

--iniciativas
CREATE TABLE initiative2political_party (
  id_initiative integer,
  id_political_party integer
);
create index on initiative2political_party(id_initiative);
create index on initiative2political_party(id_political_party);

CREATE TABLE initiative2representatives (
  id_initiative integer,
  id_representative integer
);
create index on initiative2representatives(id_initiative);
create index on initiative2representatives(id_representative);

CREATE TABLE initiatives (
  id_initiative serial,
  id_parent integer not null,
  id_legislature integer,
  presented_at timestamp NULL DEFAULT NULL,
  presented_at_str varchar(255) DEFAULT NULL,
  description text NOT NULL,
  title text NOT NULL,
  short_title varchar(255) NOT NULL,
  slug varchar(255) NOT NULL,
  url_dictamen varchar(255) DEFAULT NULL,
  url_published varchar(255) DEFAULT NULL,
  url_gaceta varchar(255) DEFAULT NULL,
  content_html text DEFAULT NULL,
  send_to text DEFAULT NULL,
  turned_to text DEFAULT NULL,
  submitted_to text DEFAULT NULL,
  period varchar(255) DEFAULT NULL,
  created_at timestamp NOT NULL DEFAULT now(),
  updated_at timestamp NOT NULL DEFAULT now()
);
create index on initiatives(id_initiative);
create index on initiatives(id_parent);
create index on initiatives(id_legislature);
create index on initiatives(slug);

CREATE TABLE status_initiatives (
  id_status serial,
  id_initiative integer not null,
  id_type integer not null default 0,
  title text DEFAULT NULL,
  title_clean text DEFAULT NULL,
  type_status varchar(255) DEFAULT NULL,
  votation boolean default false
);
create index on status_initiatives(id_status);
create index on status_initiatives(id_initiative);
create index on status_initiatives(type_status);
create index on status_initiatives(votation);

CREATE TABLE initiatives2topics (
  id_initiative integer not null,
  id_topic integer not null
);
create index on initiatives2topics(id_initiative);
create index on initiatives2topics(id_topic);

CREATE TABLE initiatives2tags (
  id_initiative integer not null,
  id_tag integer not null
);
create index on initiatives2tags(id_initiative);
create index on initiatives2tags(id_tag);

CREATE TABLE initiatives2status (
  id_initiative integer NOT NULL,
  id_status integer NOT NULL,
  description text NOT NULL,
  created_at timestamp NOT NULL DEFAULT now(),
  status boolean NOT NULL DEFAULT true
);
create index on initiatives2status(id_initiative);
create index on initiatives2status(id_status);

CREATE TABLE status (
  id_status serial,
  name varchar(255) NOT NULL,
  slug varchar(255) NOT NULL,
  description text NOT NULL
);
create index on status(id_status);

--Votaciones
CREATE TABLE vote_political_party (
  id_vote serial,
  id_count_vote integer not null default 1,
  id_initiative integer not null,
  id_political_party integer not null default 0,
  favor integer NOT NULL default 0,
  contra integer NOT NULL default 0,
  abstencion integer NOT NULL default 0,
  quorum integer NOT NULL default 0,
  ausente integer NOT NULL default 0,
  total integer NOT NULL default 0
);
create index on vote_political_party(id_vote);
create index on vote_political_party(id_count_vote);
create index on vote_political_party(id_initiative);
create index on vote_political_party(id_political_party);

CREATE TABLE vote_representatives (
  id_vote_representative serial,
  id_count_vote integer NOT NULL default 1,
  id_initiative integer NOT NULL,
  id_political_party integer NOT NULL default 0,
  id_representative integer NOT NULL default 0,
  vote_type varchar(255) DEFAULT NULL
);
create index on vote_representatives(id_vote_representative);
create index on vote_representatives(id_count_vote);
create index on vote_representatives(id_initiative);
create index on vote_representatives(id_political_party);
create index on vote_representatives(id_representative);
create index on vote_representatives(vote_type);

--Legislaturas
CREATE TABLE legislatures (
  id_legislature serial,
  name varchar(255) NOT NULL,
  slug varchar(255) NOT NULL,
  created_at timestamp NOT NULL DEFAULT now(),
  active boolean NOT NULL DEFAULT true
);
create index on legislatures(id_legislature);

--Logs
CREATE TABLE logs (
  id_log integer,
  id_user integer NOT NULL DEFAULT 0,
  created_at timestamp NOT NULL DEFAULT now(),
  action varchar(45) NOT NULL,
  ip varchar(45) NOT NULL,
  url varchar(255) NOT NULL
);
create index on logs(id_log);
create index on logs(id_user);

--Partidos politicos
CREATE TABLE political_parties (
  id_political_party serial,
  name varchar(255) NOT NULL,
  slug varchar(255) NOT NULL,
  short_name varchar(255) NOT NULL,
  url_logo varchar(255) NOT NULL,
  active boolean NOT NULL DEFAULT true,
  created_at timestamp NOT NULL DEFAULT now()
);
create index on political_parties(id_political_party);

--Representantes

CREATE TABLE representatives (
  id_representative serial,
  id_legislature integer not null,
  id_representative_type integer not null,
  id_political_party integer not null,
  name varchar(255) DEFAULT NULL,
  slug varchar(255) NOT NULL,
  email varchar(255) DEFAULT NULL,
  phone varchar(255) DEFAULT NULL,
  avatar varchar(255) DEFAULT NULL,
  birthday varchar(255) DEFAULT NULL,
  created_at timestamp NOT NULL DEFAULT now(),
  updated_at timestamp NOT NULL DEFAULT now(),
  birth_state varchar(255) DEFAULT NULL,
  birth_city varchar(255) DEFAULT NULL,
  election_type varchar(255) DEFAULT NULL,
  zone_state varchar(255) DEFAULT NULL,
  district_circumscription varchar(255) DEFAULT NULL,
  fecha_protesta varchar(255) DEFAULT NULL,
  ubication varchar(255) DEFAULT NULL,
  substitute varchar(255) DEFAULT NULL,
  ultimo_grado_estudios varchar(255) DEFAULT NULL,
  career varchar(255) DEFAULT NULL,
  exp_legislative varchar(255) DEFAULT NULL,
  commisions varchar(255) DEFAULT NULL,
  suplentede varchar(255) DEFAULT NULL
);
create index on representatives(id_representative);
create index on representatives(id_representative_type);
create index on representatives(id_legislature);
create index on representatives(id_political_party);

 
CREATE TABLE representative_type (
  id_representative_type serial,
  name varchar(255) DEFAULT NULL,
  slug varchar(255) DEFAULT NULL
);
create index on representative_type(id_representative_type);
create index on representative_type(name);

--Topics
CREATE TABLE topics (
  id_topic serial,
  name varchar(255) NOT NULL,
  slug varchar(255) NOT NULL,
  description text,
  created_at timestamp NOT NULL DEFAULT now(),
  updated_at timestamp NOT NULL DEFAULT now()
);
create index on topics(id_topic);

--usuarios
CREATE TABLE users (
  id_user serial,
  username varchar(255) NOT NULL,
  slug varchar(255) NOT NULL,
  email varchar(255) NOT NULL,
  password varchar(255) NOT NULL,
  name varchar(255) NOT NULL,
  created_at timestamp NOT NULL DEFAULT now(),
  admin boolean NOT NULL DEFAULT false,
  type varchar(45) NOT NULL DEFAULT 'member',
  active boolean NOT NULL DEFAULT true,
  avatar_url varchar(255) NOT NULL default 'temporal.png'
);
create index on users(id_user);
insert into users (username, slug, email, password, name, admin, type) values ('caarloshugo', 'caarloshugo', 'carlos@fundar.org.mx', md5('Curul_3.14159265359'), 'Carlos Gonzalez', true, 'admin');
insert into users (username, slug, email, password, name, admin, type) values ('guillermo', 'guillermo', 'guillermo@fundar.org.mx', md5('Curul501_Admin_3.141592654'), 'guillermo Avila', true, 'admin');

--Visitas
CREATE TABLE visits2initiatives (
  id_visit serial,
  id_initiative integer NOT NULL,
  ip varchar(45) NOT NULL,
  created_at timestamp NOT NULL DEFAULT now()
);
create index on visits2initiatives(id_visit);

CREATE TABLE visits2representatives (
  id_visit serial,
  id_representative integer NOT NULL,
  ip varchar(45) NOT NULL,
  created_at timestamp NOT NULL DEFAULT now()
);
create index on visits2representatives(id_visit);

