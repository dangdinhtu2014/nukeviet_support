<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC (contact@vinades.vn)
 * @Copyright (C) 2014 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Sat, 10 Dec 2011 06:46:54 GMT
 */

if( ! defined( 'NV_MAINFILE' ) ) die( 'Stop!!!' );

if( ! nv_function_exists( 'nv_block_support_all_team' ) )
{
	function nv_block_config_support_all_team( $module, $data_block, $lang_block )
	{
		global $site_mods;
 
		$array_show_team = array( '0'=> $lang_block['no'], '1'=> $lang_block['yes']);
 
		$html .= '<tr>';
		$html .= '<td>' . $lang_block['show_email'] . '</td>';
		$html .= '<td>';
		$html .= '<select name="config_show_email" class="form-control">';
		foreach( $array_show_team as $key => $name )
		{
			$html .= '<option value="' . $key . '" ' . ( ( $data_block['show_email'] == $key ) ? ' selected="selected"' : '' ) . '>' . $name . '</option>';
		}
		$html .= '</select>';
		$html .= '</td>';
		$html .= '</tr>';
 
		$html .= '<tr>';
		$html .= '<td>' . $lang_block['show_yahoo'] . '</td>';
		$html .= '<td>';
		$html .= '<select name="config_show_yahoo" class="form-control">';
		foreach( $array_show_team as $key => $name )
		{
			$html .= '<option value="' . $key . '" ' . ( ( $data_block['show_yahoo'] == $key ) ? ' selected="selected"' : '' ) . '>' . $name . '</option>';
		}
		$html .= '</select>';
		$html .= '</td>';
		$html .= '</tr>';
 
		$html .= '<tr>';
		$html .= '<td>' . $lang_block['show_skype'] . '</td>';
		$html .= '<td>';
		$html .= '<select name="config_show_skype" class="form-control">';
		foreach( $array_show_team as $key => $name )
		{
			$html .= '<option value="' . $key . '" ' . ( ( $data_block['show_skype'] == $key ) ? ' selected="selected"' : '' ) . '>' . $name . '</option>';
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

	function nv_block_config_support_all_team_submit( $module, $lang_block )
	{
		global $nv_Request;
		$return = array();
		$return['error'] = array();
		$return['config'] = array();
		$return['config']['show_email'] = $nv_Request->get_int( 'config_show_email', 'post', 0 );
		$return['config']['show_yahoo'] = $nv_Request->get_int( 'config_show_yahoo', 'post', 0 );
		$return['config']['show_skype'] = $nv_Request->get_int( 'config_show_skype', 'post', 0 );
		$return['config']['show_team'] = $nv_Request->get_int( 'config_show_team', 'post', 0 );

		return $return;
	}

	function nv_block_support_all_team( $block_config )
	{
		global $module_info, $module_name, $site_mods, $module_config, $global_config, $db;
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

		$array_all_team  = array();
		$cache_file = NV_LANG_DATA . '_all_team_' . NV_CACHE_PREFIX . '.cache';
		if( ( $cache = nv_get_cache( $module, $cache_file ) ) != false )
		{
			$array_all_team = unserialize( $cache );
		}
		else
		{
			foreach( $team_array as $team_id => $value )
			{		
				$array_all_team[$team_id] = array( 'team'=> $value, 'person'=> array() );
			}
			

			$db->sqlreset()->select( '*' )->from( NV_PREFIXLANG . '_' . $mod_data . '_person' )->order( 'weight ASC' );
			$result = $db->query( $db->sql() );
			while( $rows = $result->fetch() )
			{
				$array_all_team[$rows['team_id']]['person'][] = $rows;
			}
 
			$result->closeCursor();
			$cache = serialize( $array_all_team );
			//nv_set_cache( $module, $cache_file, $cache );
		}
		if( ! empty( $array_all_team ) )
		{
			if( file_exists( NV_ROOTDIR . '/themes/' . $module_info['template'] . '/modules/' . $mod_file . '/block_all_team.tpl' ) )
			{
				$block_theme = $module_info['template'];
			}
			else
			{
				$block_theme = 'default';
			}
			$xtpl = new XTemplate( 'block_all_team.tpl', NV_ROOTDIR . '/themes/' . $block_theme . '/modules/' . $mod_file );
			
			foreach( $array_all_team as $team_id => $value )
			{
				$xtpl->assign( 'TEAM', $value['team'] );
				
				if( isset( $value['person'] ) && !empty( $value['person'] ) )
				{
					if( $block_config['show_team'] == 1 ) $xtpl->parse( 'main.team.show_team' );
					
					foreach( $value['person'] as $loop )
					{	
						$xtpl->assign( 'LOOP', $loop );
						
						if( $block_config['show_email'] == 1 ) $xtpl->parse( 'main.team.loop.show_email' );
						if( $block_config['show_yahoo'] == 1 ) $xtpl->parse( 'main.team.loop.show_yahoo' );
						if( $block_config['show_skype'] == 1 ) $xtpl->parse( 'main.team.loop.show_skype' );

						$xtpl->parse( 'main.team.loop' );
					}
					
				}
				
						
				$xtpl->parse( 'main.team' );
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
		$content = nv_block_support_all_team( $block_config );
	}
}
