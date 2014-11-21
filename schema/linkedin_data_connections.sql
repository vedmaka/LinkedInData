CREATE TABLE /*_*/linkedin_data_connections (
  id int(11) NOT NULL AUTO_INCREMENT,
  user_id int(11) NOT NULL,
  first_name varchar(255) DEFAULT NULL,
  last_name varchar(255) DEFAULT NULL,
  headline varchar(255) DEFAULT NULL,
  linkedin_id varchar(255) DEFAULT NULL,
  PRIMARY KEY (id)
) /*$wgDBTableOptions*/;