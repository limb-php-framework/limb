
DROP TABLE test_materialized_path_tree CASCADE;
CREATE TABLE test_materialized_path_tree (
  "id" SERIAL,
  "p_parent_id" int NOT NULL default '0',
  "p_level" int NOT NULL default '0',
  "p_identifier" varchar(128) NOT NULL default '',
  "p_path" varchar(255) NOT NULL default '',
  PRIMARY KEY  ("id")
) ;

DROP TABLE test_nested_sets_tree CASCADE;
CREATE TABLE test_nested_sets_tree (
  "id"      SERIAL,
  "p_parent_id" int NOT NULL default '0',
  "p_left"    int NOT NULL,
  "p_right"   int NOT NULL,
  "p_level"     int NOT NULL,
  "p_identifier" varchar(128) NOT NULL default '',
  PRIMARY KEY("id")
) ;