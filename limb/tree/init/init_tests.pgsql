
DROP TABLE test_materialized_path_tree CASCADE;
CREATE TABLE test_materialized_path_tree (
  "id" SERIAL,
  "root_id" int NOT NULL default '0',
  "parent_id" int NOT NULL default '0',
  "level" int NOT NULL default '0',
  "identifier" varchar(128) NOT NULL default '',
  "path" varchar(255) NOT NULL default '',
  "children" int NOT NULL default '0',
  "priority" int default NULL,
  PRIMARY KEY  ("id")
) ;
