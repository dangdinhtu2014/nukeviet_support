<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC (contact@vinades.vn)
 * @Copyright (C) 2014 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Sat, 10 Dec 2011 06:46:54 GMT
 */

if( ! defined( 'NV_MAINFILE' ) ) die( 'Stop!!!' );

if( ! nv_function_exists( 'nv_block_support_by_team' ) )
{
	function nv_block_config_support_by_team( $module, $data_block, $lang_block )
	{
		global $site_mods;

		$sql = 'SELECT * FROM ' . NV_PREFIXLANG . '_' . $site_mods[$module]['module_data'] . '_team ORDER BY weight ASC';
		$list = nv_db_cache( $sql, 'team_id', $module );
		
		$array_show_team = array( '0'=> $lang_block['no'], '1'=> $lang_block['yes']);
		
		$html = '';
		$html .= '<tr>';
		$html .= '<td>' . $lang_block['team_id'] . '</td>';
		$html .= '<td>';
		$html .= '<select name="config_team_id" class="form-control">';
		$html .= '<option value="0"> -- </option>';
		foreach( $list as $l )
		{
			$html .= '<option value="' . $l['team_id'] . '" ' . ( ( $data_block['team_id'] == $l['team_id'] ) ? ' selected="selected"' : '' ) . '>' . $l['name'] . '</option>';
		}
		$html .= '</select>';
		$html .= '</td>';
		$html .= '</tr>';
		
		$html .= '<tr>';
		$html .= '<td>' . $lang_block['show_team'] . '</td>';
		$html .= '<td>';
		$html .= '<select name="config_show_team" class="form-control">';
		foreach( $array_show_team as $key => $name )
		{
			$html .= '<option value="' . $key . '" ' . ( ( $data_block['show_team'] == $key ) ? ' selected="selected"' : '' ) . '>' . $name . '</option>';
		}
		$html .= '</select>';
		$html .= '</td>';
		$html .= '</tr>';

		return $html;
	}

	function nv_block_config_support_by_team_submit( $module, $lang_block )
	{
		global $nv_Request;
		$return = array();
		$return['error'] = array();
		$return['config'] = array();
		$return['config']['team_id'] = $nv_Request->get_int( 'config_team_id', 'post', 0 );
		$return['config']['show_team'] = $nv_Request->get_int( 'config_show_team', 'post', 0 );

		return $return;
	}

	function nv_block_support_by_team( $block_config )
	{
		global $module_array_cat, $module_info, $module_name, $site_mods, $module_config, $global_config, $db;
		$module = $block_config['module'];
		$mod_data = $site_mods[$module]['module_data'];
		$mod_file = $site_mods[$module]['module_file'];

		if( $module != $module_name )
		{
			$team_array = array();
			$sql = 'SELECT *  FROM ' . NV_PREFIXLANG . '_' . $mod_data . '_team';
			$list = nv_db_cache( $sql, 'team_id', $module );
			foreach( $list as $l )
			{
				$team_array[$l['team_id']] = $l;
			}
			unset( $sql, $list );
		}

		$array_team_person = array();
		$cache_file = NV_LANG_DATA . '_team_' . $block_config['team_id'] . '_' . NV_CACHE_PREFIX . '.cache';
		if( ( $cache = nv_get_cache( $module, $cache_file ) ) != false )
		{
			$array_team_person = unserialize( $cache );
		}
		else
		{

			$db->sqlreset()->select( '*' )->from( NV_PREFIXLANG . '_' . $mod_data . '_person' )->where( 'team_id= ' . $block_config['team_id'] )->order( 'weight ASC' );
			$result = $db->query( $db->sql() );
			while( $rows = $result->fetch() )
			{
				$array_team_person[] = $rows;
			}
			$result->closeCursor();
			$cache = serialize( $array_team_person );
			nv_set_cache( $module, $cache_file, $cache );
		}
		if( ! empty( $array_team_person ) )
		{
			if( file_exists( NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $mod_file . '/block_by_team.tpl' ) )
			{
				$block_theme = $module_info['template'];
			}
			else
			{
				$block_theme = 'default';
			}
			$xtpl = new XTemplate( 'block_by_team.tpl', NV_ROOTDIR . '/themes/' . $block_theme . '/modules/' . $mod_file );
			$xtpl->assign( 'TEAM', isset( $team_array[$block_config['team_id']] ) ? $team_array[$block_config['team_id']]['name'] : '' );
 
			if( $block_config['show_team'] == 1 ) $xtpl->parse( 'main.showteam' );
				
			foreach( $array_team_person as $loop )
			{
				$xtpl->assign( 'LOOP', $loop );
				$xtpl->parse( 'main.loop' );
			}
			$xtpl->parse( 'main' );
			return $xtpl->text( 'main' );
		}
	}
}
if( defined( 'NV_SYSTEM' ) )
{
	global $site_mods;
	$module = $block_config['module'];
	if( isset( $site_mods[$module] ) )
	{
		$content = nv_block_support_by_team( $block_config );
	}
}
