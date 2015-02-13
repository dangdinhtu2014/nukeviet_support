<?php

/**
 * @Project NUKEVIET 4.x
 * @Author DANGDINHTU (dlinhvan@gmail.com)
 * @Copyright (C) 2013 Webdep24.com. All rights reserved
 * @Blog http://dangdinhtu.com
 * @License GNU/GPL version 2 or any later version
 * @Createdate 02, 14, 2015 2:20
 */

if( ! defined( 'NV_IS_FILE_MODULES' ) ) die( 'Stop!!!' );

$sql_drop_module = array();

$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_person";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_team";
 
$sql_create_module = $sql_drop_module;

$sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_person (
	person_id mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
	team_id mediumint(8) unsigned NOT NULL DEFAULT '0',
	name varchar(255) NOT NULL default '',
	phone varchar(64) NOT NULL default '',
	email varchar(128) NOT NULL default '',
 	skype varchar(64) NOT NULL default '',
 	skype_icon varchar(20) NOT NULL default '',
	yahoo varchar(64) NOT NULL default '',
 	yahoo_icon tinyint(1) unsigned NOT NULL DEFAULT '0',
	weight mediumint(8) unsigned NOT NULL DEFAULT '0',
	date_added int(11) unsigned NOT NULL DEFAULT '0',
	date_modified int(11) unsigned NOT NULL DEFAULT '0',
	PRIMARY KEY (person_id),
	KEY name (name)
) ENGINE=MyISAM";	
 
$sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_team (
	team_id mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
	name varchar(255) NOT NULL default '',
 	phone varchar(64) NOT NULL default '',
	email varchar(128) NOT NULL default '',
	weight mediumint(8) unsigned NOT NULL DEFAULT '0',
 	date_added int(11) unsigned NOT NULL DEFAULT '0',
	date_modified int(11) unsigned NOT NULL DEFAULT '0',
	PRIMARY KEY (team_id),
	KEY name (name)
) ENGINE=MyISAM";	
  