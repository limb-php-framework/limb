DROP TABLE lmb_session CASCADE;

CREATE TABLE lmb_session (
  "session_id" varchar(50) NOT NULL default '',
  "session_data" bytea NOT NULL,
  "last_activity_time" int8 default NULL,
  PRIMARY KEY  (session_id)
)  ;

