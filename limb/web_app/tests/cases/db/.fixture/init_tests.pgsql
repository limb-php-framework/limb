DROP TABLE test_db_table CASCADE;
CREATE TABLE test_db_table (
  "id" SERIAL,
  "description" text,
  "title" varchar(255) NOT NULL default '',
  PRIMARY KEY  (id)
)  ;

DROP TABLE test_table CASCADE;
CREATE TABLE test_table (
  "field1" int4 NOT NULL,
  "field2" varchar(255) NOT NULL default ''
)  ;

DROP TABLE test_object CASCADE;
CREATE TABLE test_object (
  "id" SERIAL,
  "title" varchar(255) NOT NULL,
  PRIMARY KEY  (id)
)  ;

