<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC (contact@vinades.vn)
 * @Copyright (C) 2014 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Tue, 10 Jun 2014 02:22:18 GMT
 */

if( ! defined( 'NV_IS_FILE_ADMIN' ) ) die( 'Stop!!!' );

$page_title = $lang_module['team'];

if( ACTION_METHOD == 'weight' )
{
	$team_id = $nv_Request->get_int( 'team_id', 'post', 0 );
	$mod = $nv_Request->get_string( 'action', 'get,post', '' );
	$new_vid = $nv_Request->get_int( 'new_vid', 'post', 0 );
	$content = 'NO_' . $team_id;
 	
	if( empty( $new_vid ) ) die( 'NO_' . $mod );

	$sql = 'SELECT team_id FROM ' . TABLE_SUPPORT_NAME . '_team WHERE team_id=' . $team_id;
	$team_id = $db->query( $sql )->fetchColumn();
	if( empty( $team_id ) ) die( 'NO_' . $team_id );


	$sql = 'SELECT team_id FROM ' . TABLE_SUPPORT_NAME . '_team WHERE team_id!=' . $team_id . ' ORDER BY weight ASC';
	$result = $db->query( $sql );

	$weight = 0;
	while( $row = $result->fetch() )
	{
		++$weight;
		if( $weight == $new_vid ) ++$weight;

		$sql = 'UPDATE ' . TABLE_SUPPORT_NAME . '_team SET weight=' . $weight . ' WHERE team_id=' . $row['team_id'];
		$db->query( $sql );
	}

	$sql = 'UPDATE ' . TABLE_SUPPORT_NAME . '_team SET weight=' . $new_vid . ' WHERE team_id=' . $team_id;
	$db->query( $sql );

	nv_del_moduleCache( $module_name );
	
	$content = 'OK_' . $team_id; 
	
	echo $content;
	exit();

}

if( ACTION_METHOD == 'delete' )
{
	$info = array();
	$team_id = $nv_Request->get_int( 'team_id', 'post', 0 );
	$token = $nv_Request->get_title( 'token', 'post', '' );

	$listid = $nv_Request->get_string( 'listid', 'post', '' );
	if( $listid != '' and md5( $global_config['sitekey'] . session_id() ) == $token )
	{
		$del_array = array_map( 'intval', explode( ',', $listid ) );
	}
	elseif( $token == md5( $global_config['sitekey'] . session_id() . $team_id ) )
	{
		$del_array = array( $team_id );
	}

	if( ! empty( $del_array ) )
	{

		$delete = $db->prepare( 'DELETE FROM ' . TABLE_SUPPORT_NAME . '_team WHERE team_id IN ( ' . implode( ',', $del_array ) . ' )' );
		$delete->execute();
		if( $delete->rowCount() )
		{
			nv_insert_logs( NV_LANG_DATA, $module_name, 'log_del_team', implode( ', ', $del_array ), $admin_info['userid'] );

			$nv_Request->unset_request( $module_data . '_success', 'session' );
			$a = 0;
			foreach( $del_array as $team_id )
			{
				$info['id'][$a] = $team_id;
				++$a;
			}
			$info['success'] = $lang_module['team_success'];
		}
		else
		{

			$info['error'] = $lang_module['team_error_no_del'];
		}

	}
	else
	{
		$info['error'] = $lang_module['team_error_no_del'];
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
		'team_id' => 0,
		'name' => '',
		'phone' => '',
		'email' => '',
		'date_added' => NV_CURRENTTIME,
		'date_modified' => NV_CURRENTTIME,
		);

	$error = array();

	$data['team_id'] = $nv_Request->get_int( 'team_id', 'get,post', 0 );
	if( $data['team_id'] > 0 )
	{
		$data = $db->query( 'SELECT *
		FROM ' . TABLE_SUPPORT_NAME . '_team  
		WHERE team_id=' . $data['team_id'] )->fetch();

		$caption = $lang_module['team_edit'];
	}
	else
	{
		$caption = $lang_module['team_add'];
	}

	if( $nv_Request->get_int( 'save', 'post' ) == 1 )
	{

		$data['team_id'] = $nv_Request->get_int( 'team_id', 'post', 0 );
		$data['name'] = nv_substr( $nv_Request->get_title( 'name', 'post', '', '' ), 0, 255 );
		$data['phone'] = nv_substr( $nv_Request->get_title( 'phone', 'post', '', '' ), 0, 255 );
		$data['email'] = nv_substr( $nv_Request->get_title( 'email', 'post', '', '' ), 0, 128 );

		if( empty( $data['name'] ) )
		{
			$error['name'] = $lang_module['team_error_name'];
		}

		if( ( $error_xemail = nv_check_valid_email( $data['email'] ) ) != '' && $data['email'] != '' )
		{
			$error['email'] = $error_xemail;
		}

		if( ! empty( $error ) && ! isset( $error['warning'] ) )
		{
			$error['warning'] = $lang_module['team_error_warning'];
		}

		if( empty( $error ) )
		{
			if( $data['team_id'] == 0 )
			{
				$weight = $db->query( 'SELECT MAX(weight) FROM ' . TABLE_SUPPORT_NAME . '_team' )->fetchColumn();
				$data['weight'] = intval( $weight ) + 1;
					
				$stmt = $db->prepare( 'INSERT INTO ' . TABLE_SUPPORT_NAME . '_team SET 
					date_added=' . intval( $data['date_added'] ) . ',  
					weight=' . intval( $data['weight'] ) . ',  
					name =:name,
					email =:email,
					phone =:phone' );
				$stmt->bindParam( ':name', $data['name'], PDO::PARAM_STR );
				$stmt->bindParam( ':phone', $data['phone'], PDO::PARAM_STR );
				$stmt->bindParam( ':email', $data['email'], PDO::PARAM_STR );
				$stmt->execute();

				if( $data['team_id'] = $db->lastInsertId() )
				{
					nv_insert_logs( NV_LANG_DATA, $module_name, 'Add A team', 'team_id: ' . $data['team_id'], $admin_info['userid'] );

				}
				else
				{
					$error['warning'] = $lang_module['team_error_save'];

				}
				$stmt->closeCursor();

			}
			else
			{
				try
				{

					$stmt = $db->prepare( 'UPDATE ' . TABLE_SUPPORT_NAME . '_team SET 
						date_modified=' . intval( $data['date_modified'] ) . ', 
						name =:name,
						phone =:phone,
						email =:email
						WHERE team_id = ' . $data['team_id'] );
					$stmt->bindParam( ':name', $data['name'], PDO::PARAM_STR );
					$stmt->bindParam( ':phone', $data['phone'], PDO::PARAM_STR );
					$stmt->bindParam( ':email', $data['email'], PDO::PARAM_STR );
					$stmt->execute();

					if( $stmt->execute() )
					{

						nv_insert_logs( NV_LANG_DATA, $module_name, 'Edit A team', 'team_id: ' . $data['team_id'], $admin_info['userid'] );

						nv_del_moduleCache( $module_name );
						Header( 'Location: ' . NV_BASE_ADMINURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op );
						die();
					}
					else
					{
						$error['warning'] = $lang_module['team_error_save'];

					}

					$stmt->closeCursor();

				}
				catch ( PDOException $e )
				{
					$error['warning'] = $lang_module['team_error_save'];
					// var_dump($e);
				}

			}

		}
		if( empty( $error ) )
		{
			nv_del_moduleCache( $module_name );
			Header( 'Location: ' . NV_BASE_ADMINURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=team' );
			die();
		}

	}

	$xtpl = new XTemplate( 'team_add.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file );
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
	$xtpl->assign( 'CURRENT', NV_UPLOADS_DIR . '/' . $module_name );
	$xtpl->assign( 'CAPTION', $caption );
	$xtpl->assign( 'DATA', $data );
	$xtpl->assign( 'CANCEL', NV_BASE_ADMINURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&" . NV_NAME_VARIABLE . "=" . $module_name . "&" . NV_OP_VARIABLE . "=" . $op );

	if( isset( $error['warning'] ) )
	{
		$xtpl->assign( 'error_warning', $error['warning'] );
		$xtpl->parse( 'main.error_warning' );
	}

	if( isset( $error['name'] ) )
	{
		$xtpl->assign( 'error_name', $error['name'] );
		$xtpl->parse( 'main.error_name' );
	}

	if( isset( $error['email'] ) )
	{
		$xtpl->assign( 'error_email', $error['email'] );
		$xtpl->parse( 'main.error_email' );
	}

	$xtpl->parse( 'main' );
	$contents = $xtpl->text( 'main' );
	include NV_ROOTDIR . '/includes/header.php';
	echo nv_admin_theme( $contents );
	include NV_ROOTDIR . '/includes/footer.php';

	exit();
}

/*show list team*/

$per_page = 50;

$page = $nv_Request->get_int( 'page', 'get', 1 );

$sql = TABLE_SUPPORT_NAME . '_team';

$sort = $nv_Request->get_string( 'sort', 'get', '' );

$order = $nv_Request->get_string( 'order', 'get' ) == 'desc' ? 'desc' : 'asc';

$sort_data = array(
	'name',
	'email',
	'phone' );

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

$base_url = NV_BASE_ADMINURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=team&amp;sort=' . $sort . '&amp;order=' . $order . '&amp;per_page=' . $per_page;

$db->sqlreset()->select( '*' )->from( $sql )->limit( $per_page )->offset( ( $page - 1 ) * $per_page );

$result = $db->query( $db->sql() );

$array = array();
while( $rows = $result->fetch() )
{
	$array[] = $rows;
}

$xtpl = new XTemplate( 'team.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file );
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
$xtpl->assign( 'URL_PHONE', NV_BASE_ADMINURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op . '&amp;sort=phone&amp;order=' . $order2 . '&amp;per_page=' . $per_page );
$xtpl->assign( 'URL_EMAIL', NV_BASE_ADMINURL . 'index.php?' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op . '&amp;sort=email&amp;order=' . $order2 . '&amp;per_page=' . $per_page );
$xtpl->assign( 'ADD_NEW', NV_BASE_ADMINURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&" . NV_NAME_VARIABLE . "=" . $module_name . "&" . NV_OP_VARIABLE . "=team&action=add" );

if( ! empty( $array ) )
{
	foreach( $array as $item )
	{

		$item['token'] = md5( $global_config['sitekey'] . session_id() . $item['team_id'] );
		$item['link'] = NV_BASE_ADMINURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&" . NV_NAME_VARIABLE . "=" . $module_name . "&" . NV_OP_VARIABLE . "=team&team_id=" . $item['team_id'];
		$item['edit'] = NV_BASE_ADMINURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&" . NV_NAME_VARIABLE . "=" . $module_name . "&" . NV_OP_VARIABLE . "=team&action=edit&token=" . $item['token'] . "&team_id=" . $item['team_id'];
		
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
