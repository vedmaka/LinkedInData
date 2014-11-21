CREATE TABLE  /*_*/linkedin_data_profiles (
  id int(11) NOT NULL AUTO_INCREMENT,
  first_name varchar(255) DEFAULT NULL,
  last_name varchar(255) DEFAULT NULL,
  formatted_name varchar(255) DEFAULT NULL,
  headline varchar(255) DEFAULT NULL,
  industry varchar(255) DEFAULT NULL,
  num_connections int(11) DEFAULT NULL,
  summary text DEFAULT NULL,
  specialties text DEFAULT NULL,
  picture_url varchar(512) DEFAULT NULL,
  linkedin_id varchar(255) DEFAULT NULL,
  created_at int(11) DEFAULT NULL,
  updated_at int(11) DEFAULT NULL,
  user_id int(11) DEFAULT NULL,
  PRIMARY KEY (id)
) /*$wgDBTableOptions*/;