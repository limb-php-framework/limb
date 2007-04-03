DROP TABLE test_one_table_object CASCADE;

CREATE TABLE test_one_table_object (
  "id" SERIAL,
  "annotation" text,
  "content" text,
  "news_date" date default NULL,
  PRIMARY KEY  (id)
)  ;

DROP TABLE test_one_table_typed_object CASCADE;

CREATE TABLE test_one_table_typed_object (
  "id" SERIAL,
  "title" text,
  "kind" varchar(255) NOT NULL,
  PRIMARY KEY  (id)
)  ;

DROP TABLE course_for_typed_test CASCADE;

CREATE TABLE course_for_typed_test (
 "id" SERIAL,
 "title" varchar(255) default NULL,
 PRIMARY KEY  (id)
)  ;

DROP TABLE lecture_for_typed_test CASCADE;

CREATE TABLE lecture_for_typed_test (
  "id" SERIAL,
  "title" varchar(255) default NULL,
  "course_id" int8 default NULL,
  "kind" varchar(255) NOT NULL,
  PRIMARY KEY  (id)
)  ;


DROP TABLE social_security_for_test CASCADE;

CREATE TABLE social_security_for_test (
"id" SERIAL,
"code" varchar(255) default NULL,
PRIMARY   KEY  (id)
)  ;

DROP TABLE person_for_test CASCADE;

CREATE TABLE person_for_test (
"id" SERIAL,
name varchar(255) default NULL,
"ss_id" int8 default NULL,
PRIMARY   KEY  (id)
)  ;

DROP TABLE course_for_test CASCADE;

CREATE TABLE course_for_test (
 "id" SERIAL,
 "title" varchar(255) default NULL,
 PRIMARY KEY  (id)
)  ;

DROP TABLE lecture_for_test CASCADE;

CREATE TABLE lecture_for_test (
  "id" SERIAL,
  "title" varchar(255) default NULL,
  "course_id" int8 default NULL,
  "alt_course_id" int8 default NULL,
  PRIMARY KEY  (id)
)  ;

DROP TABLE lesson_for_test CASCADE;

CREATE TABLE lesson_for_test (
  "id" SERIAL,
  "date_start" int8 default NULL,
  "date_end" int8 default NULL,
  PRIMARY KEY  (id)
)  ;

DROP TABLE group_for_test CASCADE;

CREATE TABLE group_for_test (
  "id" SERIAL,
  "title" varchar(255)  default NULL,
  PRIMARY KEY  (id)
)  ;

DROP TABLE user_for_test CASCADE;

CREATE TABLE user_for_test (
  "id" SERIAL,
  "first_name" varchar(255)  default NULL,
  PRIMARY KEY  (id)
)  ;

DROP TABLE user2group_for_test CASCADE;

CREATE TABLE user2group_for_test (
 "id" SERIAL,
 "user_id" int8 default NULL,
 "group_id" int8 default NULL,
 PRIMARY KEY  (id)
)  ;

