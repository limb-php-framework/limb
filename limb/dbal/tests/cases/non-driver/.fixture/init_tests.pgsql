DROP TABLE test_one_table_object CASCADE;

CREATE TABLE test_one_table_object (
  "id" SERIAL,
  "annotation" text,
  "content" text,
  "news_date" date default NULL,
  PRIMARY KEY  (id)
)  ;

DROP TABLE test_db_table CASCADE;
CREATE TABLE test_db_table (
  "id" SERIAL,
  "description" text,
  "title" varchar(255) NOT NULL default '',
  PRIMARY KEY  (id)
)  ;

DROP TABLE test_one_table_object CASCADE;
CREATE TABLE test_one_table_object (
  "id" SERIAL,
  "annotation" text,
  "content" text,
  "news_date" date default NULL,
  PRIMARY KEY  (id)
)  ;

DROP TABLE all_types_test CASCADE;
CREATE TABLE all_types_test (
 "field_int" int4 default NULL,
 "field_varchar" varchar(255) default NULL,
 "field_char" varchar(11) default NULL,
 "field_date" date default NULL,
 "field_datetime" timestamp default NULL,
 "field_time" time default NULL,
 "field_text" text,
 "field_smallint" int2 default NULL,
 "field_bigint" int8 default NULL,
 "field_blob" text,
 "field_float" float default NULL,
 "field_decimal" decimal(10,0) default NULL,
 "field_tinyint" int2 default NULL
)   ;
