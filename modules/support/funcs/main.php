<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC (contact@vinades.vn)
 * @Copyright (C) 2014 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate Mon, 22 Jul 2013 21:41:59 GMT
 */

if( ! defined( 'NV_IS_MOD_SUPPORT' ) ) die( 'Stop!!!' );

$page_title = $module_info['custom_title'];
$key_words = $module_info['keywords'];
  
if( ! $home )
{
	header( 'Location:' . nv_url_rewrite( NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA, true ) );
}

include ( NV_ROOTDIR . '/includes/header.php' );
echo nv_site_theme( '' );
include ( NV_ROOTDIR . '/includes/footer.php' );

