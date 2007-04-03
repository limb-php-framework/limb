DROP TABLE sys_session CASCADE;

CREATE TABLE sys_session (
  "session_id" varchar(50) NOT NULL default '',
  "session_data" bytea NOT NULL,
  "last_activity_time" int8 default NULL,
  "user_id" int8 default NULL,
  PRIMARY KEY  (session_id),
    UNIQUE  (user_id)
)  ;

