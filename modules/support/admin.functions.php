<?php

/**
 * @Project NUKEVIET 4.x
 * @Author DANGDINHTU (dlinhvan@gmail.com)
 * @Copyright (C) 2013 Webdep24.com. All rights reserved
 * @Blog http://dangdinhtu.com
 * @License GNU/GPL version 2 or any later version
 * @Createdate 02, 14, 2015 2:20
 */

if( ! defined( 'NV_ADMIN' ) or ! defined( 'NV_MAINFILE' ) or ! defined( 'NV_IS_MODADMIN' ) ) die( 'Stop!!!' );

$allow_func = array( 'main', 'person', 'team' );

define( 'NV_IS_FILE_ADMIN', true );

define( 'TABLE_SUPPORT_NAME', NV_PREFIXLANG . '_' . $module_data );

define( 'ACTION_METHOD', $nv_Request->get_string( 'action', 'get,post', '' ) );

$array_status = array( '0' => $lang_module['disabled'], '1' => $lang_module['enable'] );


$team_array = array();
$sql = 'SELECT *  FROM ' . NV_PREFIXLANG . '_' . $module_data . '_team';
$list = nv_db_cache( $sql, 'team_id', $module_name );
foreach( $list as $l )
{
	$team_array[$l['team_id']] = $l;
}
unset( $sql, $list ); 