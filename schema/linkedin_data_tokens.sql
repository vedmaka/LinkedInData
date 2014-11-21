CREATE TABLE /*_*/linkedin_data_tokens (
  id int(11) NOT NULL AUTO_INCREMENT,
  user_id int(11) NOT NULL,
  token varchar(1024) NOT NULL,
  updated_at int(11) NOT NULL,
  created_at int(11) NOT NULL,
  PRIMARY KEY (id)
) /*$wgDBTableOptions*/;