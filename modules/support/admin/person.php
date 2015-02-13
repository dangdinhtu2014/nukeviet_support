<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC (contact@vinades.vn)
 * @Copyright (C) 2014 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Tue, 10 Jun 2014 02:22:18 GMT
 */

if( ! defined( 'NV_IS_FILE_ADMIN' ) ) die( 'Stop!!!' );

$page_title = $lang_module['person'];

if( ACTION_METHOD == 'weight' )
{
	$person_id = $nv_Request->get_int( 'person_id', 'post', 0 );
	$mod = $nv_Request->get_string( 'action', 'get,post', '' );
	$new_vid = $nv_Request->get_int( 'new_vid', 'post', 0 );
	$content = 'NO_' . $person_id;
 	
	if( empty( $new_vid ) ) die( 'NO_' . $mod );

	$sql = 'SELECT person_id FROM ' . TABLE_SUPPORT_NAME . '_person WHERE person_id=' . $person_id;
	$person_id = $db->query( $sql )->fetchColumn();
	if( empty( $person_id ) ) die( 'NO_' . $person_id );


	$sql = 'SELECT person_id FROM ' . TABLE_SUPPORT_NAME . '_person WHERE person_id!=' . $person_id . ' ORDER BY weight ASC';
	$result = $db->query( $sql );

	$weight = 0;
	while( $row = $result->fetch() )
	{
		++$weight;
		if( $weight == $new_vid ) ++$weight;

		$sql = 'UPDATE ' . TABLE_SUPPORT_NAME . '_person SET weight=' . $weight . ' WHERE person_id=' . $row['person_id'];
		$db->query( $sql );
	}

	$sql = 'UPDATE ' . TABLE_SUPPORT_NAME . '_person SET weight=' . $new_vid . ' WHERE person_id=' . $person_id;
	$db->query( $sql );

	nv_del_moduleCache( $module_name );
	
	$content = 'OK_' . $person_id; 
	
	echo $content;
	exit();

}

if( ACTION_METHOD == 'delete' )
{
	$info = array();
	$person_id = $nv_Request->get_int( 'person_id', 'post', 0 );
	$token = $nv_Request->get_title( 'token', 'post', '' );
	
	$listid = $nv_Request->get_string( 'listid', 'post', '' );
	if( $listid != '' and md5( $global_config['sitekey'] . session_id() ) == $token )
	{
		$del_array = array_map( 'intval', explode( ',', $listid ) );
	}
	elseif( $token == md5( $global_config['sitekey'] .  session_id() . $person_id ) )
	{
		$del_array = array( $person_id );
	}
 
	if( ! empty( $del_array ) )
	{
			
		$delete = $db->prepare('DELETE FROM ' . TABLE_SUPPORT_NAME . '_person WHERE person_id IN ( '. implode( ',', $del_array ) .' )');
		$delete->execute();
		if( $delete->rowCount() )
		{
 			nv_insert_logs( NV_LANG_DATA, $module_name, 'log_del_person', implode( ', ', $del_array ), $admin_info['userid'] );
			
			$nv_Request->unset_request( $module_data . '_success', 'session' );
			$a = 0;
			foreach( $del_array as $person_id )
			{
				$info['id'][$a] = $person_id;
				++$a;
			}
			$info['success'] = $lang_module['person_success'] ;
		}
		else
		{
 
			$info['error'] = $lang_module['person_error_no_del'];
		}
			
		
	}else
	{
		$info['error'] = $lang_module['person_error_no_del'];
	}
	echo json_encode( $info );
	exit();
}
 
if( ACTION_METHOD == 'add' || ACTION_METHOD == 'edit' )
{
	if( defined( 'NV_EDITOR' ) )
	{
		require_once NV_ROOTDIR . '/' . NV_EDITORSDIR . '/' . NV_EDITOR . '/nv.php';
	}
		
 
	$data = array(
		'person_id' => 0,
		'team_id' => 0,
		'name' => '',
		'phone' => '',
		'email' => '',
		'yahoo' => '',
		'yahoo_icon' => 2,
		'skype' => '',
		'skype_icon' => 'smallclassic',
		'date_added' => NV_CURRENTTIME,
		'date_modified' => NV_CURRENTTIME,
	);
	 
	$error = array();
 
	$data['person_id'] = $nv_Request->get_int( 'person_id', 'get,post', 0 );
 	if( $data['person_id'] > 0 )
	{
		$data = $db->query( 'SELECT *
		FROM ' . TABLE_SUPPORT_NAME . '_person  
		WHERE person_id=' . $data['person_id'] )->fetch();
 
		$caption = $lang_module['person_edit'];
	}
	else
	{
		$caption = $lang_module['person_add'];
	}

	if( $nv_Request->get_int( 'save', 'post' ) == 1 )
	{

		$data['person_id'] = $nv_Request->get_int( 'person_id', 'post', 0 );
		$data['team_id'] = $nv_Request->get_int( 'team_id', 'post', '', 1 );
		$data['name'] = nv_substr( $nv_Request->get_title( 'name', 'post', '', '' ), 0, 255 );
 		$data['email'] = nv_substr( $nv_Request->get_title( 'email', 'post', '', '' ), 0, 128 );
		$data['phone'] = nv_substr( $nv_Request->get_title( 'phone', 'post', '', '' ), 0, 64 );
		$data['yahoo'] = nv_substr( $nv_Request->get_title( 'yahoo', 'post', '', '' ), 0, 64 );
		$data['yahoo_icon'] = $nv_Request->get_int( 'yahoo_icon', 'post', '', 1 );
		$data['skype'] = nv_substr( $nv_Request->get_title( 'skype', 'post', '', '' ), 0, 64 );
		$data['skype_icon'] = nv_substr( $nv_Request->get_title( 'skype_icon', 'post', '', '' ), 0, 20 );
 
		if( empty( $data['team_id'] ) )
		{
			$error['team'] = $lang_module['person_error_team'];	
		}
		
		if( empty( $data['name'] ) )
		{
			$error['name'] = $lang_module['person_error_name'];	
		}
		if( ( $error_xemail = nv_check_valid_email( $data['email'] ) ) != '' && $data['email'] != '')
		{
			$error['email'] = $error_xemail;
		}
		if( ! empty( $error ) && ! isset( $error['warning'] ) )
		{
			$error['warning'] = $lang_module['person_error_warning'];
		}
 

		if( empty( $error ) )
		{
 
			if( $data['person_id'] == 0 )
			{
				$weight = $db->query( 'SELECT MAX(weight) FROM ' . TABLE_SUPPORT_NAME . '_person' )->fetchColumn();
				$data['weight'] = intval( $weight ) + 1;
				
				$stmt = $db->prepare( 'INSERT INTO ' . TABLE_SUPPORT_NAME . '_person SET 
					date_added=' . intval( $data['date_added'] ) . ',  
					weight=' . intval( $data['weight'] ) . ',  
					team_id=' . intval( $data['team_id'] ) . ',  
					name =:name,
					phone =:phone,
					email =:email,
					skype = :skype,
					skype_icon = :skype_icon,
					yahoo = :yahoo,
					yahoo_icon = :yahoo_icon' );
 				$stmt->bindParam( ':name', $data['name'], PDO::PARAM_STR );
				$stmt->bindParam( ':phone', $data['phone'], PDO::PARAM_STR );
				$stmt->bindParam( ':email', $data['email'], PDO::PARAM_STR );
 				$stmt->bindParam( ':skype', $data['skype'], PDO::PARAM_STR );
 				$stmt->bindParam( ':skype_icon', $data['skype_icon'], PDO::PARAM_STR );
 				$stmt->bindParam( ':yahoo', $data['yahoo'], PDO::PARAM_STR );
 				$stmt->bindParam( ':yahoo_icon', $data['yahoo_icon'], PDO::PARAM_INT );
 				$stmt->execute();

				if( $data['person_id'] = $db->lastInsertId() )
				{
					nv_insert_logs( NV_LANG_DATA, $module_name, 'Add A Person', 'person_id: ' . $data['person_id'], $admin_info['userid'] );	 

				}
				else
				{
					$error['warning'] = $lang_module['person_error_save'];

				}
				$stmt->closeCursor();

			}
			else
			{
				try
				{
					
					$stmt = $db->prepare( 'UPDATE ' . TABLE_SUPPORT_NAME . '_person SET 
						date_modified=' . intval( $data['date_modified'] ) . ', 
						team_id=' . intval( $data['team_id'] ) . ', 
						name =:name,
						phone =:phone,
						email =:email,
						skype = :skype,
						skype_icon = :skype_icon,
						yahoo = :yahoo,
						yahoo_icon = :yahoo_icon 
						WHERE person_id = '. $data['person_id'] );
					$stmt->bindParam( ':name', $data['name'], PDO::PARAM_STR );
					$stmt->bindParam( ':phone', $data['phone'], PDO::PARAM_STR );
					$stmt->bindParam( ':email', $data['email'], PDO::PARAM_STR );
					$stmt->bindParam( ':skype', $data['skype'], PDO::PARAM_STR );
					$stmt->bindParam( ':skype_icon', $data['skype_icon'], PDO::PARAM_STR );
					$stmt->bindParam( ':yahoo', $data['yahoo'], PDO::PARAM_STR );
					$stmt->bindParam( ':yahoo_icon', $data['yahoo_icon'], PDO::PARAM_INT );
					$stmt->execute();
					
					if( $stmt->execute() )
					{

						nv_insert_logs( NV_LANG_DATA, $module_name, 'Edit A Person', 'person_id: ' . $data['person_id'], $admin_info['userid'] );
 
						nv_del_moduleCache( $module_name );
						Header( 'Location: ' . NV_BASE_ADMINURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op );
						die();
					}
					else
					{
						$error['warning'] = $lang_module['person_error_save'];

					}

					$stmt->closeCursor();

				}
				catch ( PDOException $e )
				{ 
					$error['warning'] = $lang_module['person_error_save'];
					// var_dump($e);
				}

			}

		}
		if( empty( $error ) )
		{
			nv_del_moduleCache( $module_name );
			Header( 'Location: ' . NV_BASE_ADMINURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=person' );
			die();
		}

	}
 
	$xtpl = new XTemplate( 'person_add.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file );
	$xtpl->assign( 'LANG', $lang_module );
	$xtpl->assign( 'GLANG', $lang_global );
	$xtpl->assign( 'NV_LANG_VARIABLE', NV_LANG_VARIABLE );
	$xtpl->assign( 'NV_LANG_DATA', NV_LANG_DATA );
	$xtpl->assign( 'NV_BASE_SITEURL', NV_BASE_SITEURL );
	$xtpl->assign( 'THEME', $global_config['site_theme'] );
	$xtpl->assign( 'NV_BASE_ADMINURL', NV_BASE_ADMINURL );
	$xtpl->assign( 'NV_NAME_VARIABLE', NV_NAME_VARIABLE );
	$xtpl->assign( 'NV_OP_VARIABLE', NV_OP_VARIABLE );
	$xtpl->assign( 'MODULE_FILE', $module_file );
	$xtpl->assign( 'MODULE_NAME', $module_name );
	$xtpl->assign( 'OP', $op );
 	$xtpl->assign( 'CAPTION', $caption );
	$xtpl->assign( 'DATA', $data );
	$xtpl->assign( 'CANCEL', NV_BASE_ADMINURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&" . NV_NAME_VARIABLE . "=" . $module_name . "&" . NV_OP_VARIABLE . "=" . $op );
 	
	foreach( $team_array as $team_id => $value )
	{
		$xtpl->assign( 'TEAM', array( 'key'=> $team_id, 'name'=> $value['name'], 'selected'=> ( $data['team_id'] == $team_id ) ? 'selected="selected"' : ''  ) );
		$xtpl->parse( 'main.team' );
		
	}

	$skype_icon_array = array( 'balloon', 'bigclassic', 'smallclassic', 'smallicon', 'mediumicon' );
	foreach( $skype_icon_array as $key )
	{
		$xtpl->assign( 'SKYPE', array( 'key'=> $key, 'name'=> $key, 'selected'=> ( $data['skype_icon'] == $key ) ? 'selected="selected"' : ''  ) );
		$xtpl->parse( 'main.skype_icon' );
		
	}
	
	for( $i=1; $i <= 24; ++$i )
	{
		$xtpl->assign( 'YAHOO', array( 'key'=> $i, 'name'=> $i, 'selected'=> ( $data['yahoo_icon'] == $i ) ? 'selected="selected"' : ''  ) );
		$xtpl->parse( 'main.yahoo_icon' );
		
	}
	
	if( isset( $error['warning'] ) )
	{
		$xtpl->assign( 'error_warning', $error['warning'] );
		$xtpl->parse( 'main.error_warning' );
	}

	if( isset( $error['team'] ) )
	{
		$xtpl->assign( 'error_team', $error['team'] );
		$xtpl->parse( 'main.error_team' );
	}
 	if( isset( $error['name'] ) )
	{
		$xtpl->assign( 'error_name', $error['name'] );
		$xtpl->parse( 'main.error_name' );
	}
 
	$xtpl->parse( 'main' );
	$contents = $xtpl->text( 'main' );
	include NV_ROOTDIR . '/includes/header.php';
	echo nv_admin_theme( $contents );
	include NV_ROOTDIR . '/includes/footer.php';

	exit();
}

/*show list person*/

$per_page = 50;

$page = $nv_Request->get_int( 'page', 'get', 1 );
 
$sql = TABLE_SUPPORT_NAME . '_person';

$sort = $nv_Request->get_string( 'sort', 'get', '' );

$order = $nv_Request->get_string( 'order', 'get' ) == 'desc' ? 'desc' : 'asc';

$sort_data = array( 'name', 'job', 'native_place', 'email', 'phone' );

if( isset( $sort ) && in_array( $sort, $sort_data ) )
{

	$sql .= " ORDER BY " . $sort;
}
else
{
	$sql .= " ORDER BY date_added";
}

if( isset( $order ) && ( $order == 'desc' ) )
{
	$sql .= " DESC";
}
else
{
	$sql .= " ASC";
}

$num_items = $db->query( 'SELECT COUNT(*) FROM ' . $sql )->fetchColumn();

$base_url = NV_BASE_ADMINURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=person&amp;sort=' . $sort . '&amp;order=' . $order . '&amp;per_page=' . $per_page;

$db->sqlreset()->select( '*' )->from( $sql )->limit( $per_page )->offset( ( $page - 1 ) * $per_page );

$result = $db->query( $db->sql() );

$array = array();
while( $rows = $result->fetch() )
{
	$array[] = $rows;
}

$xtpl = new XTemplate( 'person.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file );
$xtpl->assign( 'LANG', $lang_module );
$xtpl->assign( 'NV_LANG_VARIABLE', NV_LANG_VARIABLE );
$xtpl->assign( 'NV_LANG_DATA', NV_LANG_DATA );
$xtpl->assign( 'NV_BASE_SITEURL', NV_BASE_SITEURL );
$xtpl->assign( 'THEME', $global_config['site_theme'] );
$xtpl->assign( 'NV_BASE_ADMINURL', NV_BASE_ADMINURL );
$xtpl->assign( 'NV_NAME_VARIABLE', NV_NAME_VARIABLE );
$xtpl->assign( 'NV_OP_VARIABLE', NV_OP_VARIABLE );
$xtpl->assign( 'MODULE_FILE', $module_file );
$xtpl->assign( 'MODULE_NAME', $module_name );
$xtpl->assign( 'TOKEN', md5( $global_config['sitekey'] . session_id() ) );

$order2 = ( $order == 'asc' ) ? 'desc' : 'asc';
$xtpl->assign( 'URL_NAME', NV_BASE_ADMINURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op . '&amp;sort=name&amp;order=' . $order2 . '&amp;per_page=' . $per_page );
$xtpl->assign( 'URL_TEAM', NV_BASE_ADMINURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op . '&amp;sort=team_id&amp;order=' . $order2 . '&amp;per_page=' . $per_page );
$xtpl->assign( 'ADD_NEW', NV_BASE_ADMINURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&" . NV_NAME_VARIABLE . "=" . $module_name . "&" . NV_OP_VARIABLE . "=person&action=add" );

if( ! empty( $array ) )
{
	foreach( $array as $item )
	{
 
		$item['token'] = md5( $global_config['sitekey'] . session_id() . $item['person_id'] );
		$item['team'] =  isset( $team_array[$item['team_id']] ) ? $team_array[$item['team_id']]['name'] : '' ;
		$item['link'] = NV_BASE_ADMINURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&" . NV_NAME_VARIABLE . "=" . $module_name . "&" . NV_OP_VARIABLE . "=person&person_id=" . $item['person_id'];
		$item['edit'] = NV_BASE_ADMINURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&" . NV_NAME_VARIABLE . "=" . $module_name . "&" . NV_OP_VARIABLE . "=person&action=edit&token=" . $item['token'] . "&person_id=" . $item['person_id'];
		for( $i = 1; $i <= $num_items; ++$i )
		{
			$xtpl->assign( 'WEIGHT', array(
				'key' => $i,
				'name' => $i,
				'selected' => ( $i == $item['weight'] ) ? ' selected="selected"' : ''
			) );

			$xtpl->parse( 'main.loop.weight' );
		}
		$xtpl->assign( 'LOOP', $item );
		$xtpl->parse( 'main.loop' );
	}
}
 
$generate_page = nv_generate_page( $base_url, $num_items, $per_page, $page );
if( ! empty( $generate_page ) )
{
	$xtpl->assign( 'GENERATE_PAGE', $generate_page );
	$xtpl->parse( 'main.generate_page' );
}

$xtpl->parse( 'main' );
$contents = $xtpl->text( 'main' );
include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme( $contents );
include NV_ROOTDIR . '/includes/footer.php';
