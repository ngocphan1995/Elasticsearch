<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC (contact@vinades.vn)
 * @Copyright (C) 2014 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate 10-5-2010 0:14
 */

if (! defined('NV_IS_MOD_NEWS')) {
    die('Stop!!!');
}
  /*kết nối host*/
	$hosts = array( $db_config['elas_host'] . ':' . $db_config['elas_port'] );
	$client = Elasticsearch\ClientBuilder::create( )->setHosts( $hosts )->setRetries( 0 )->build();
	/*----------------end----------*/
	$params = array();
	$params['index'] = 'mangvn_com';
	$params['type'] = NV_PREFIXLANG . '_' . $module_data . '_rows';
function GetSourceNews($sourceid)
{
    global $db_slave, $module_data;

    if ($sourceid > 0) {
        $sql = 'SELECT title FROM ' . NV_PREFIXLANG . '_' . $module_data . '_sources WHERE sourceid = ' . $sourceid;
        $re = $db_slave->query($sql);

        if (list($title) = $re->fetch(3)) {
            return $title;
        }
    }
    return '-/-';
}

function BoldKeywordInStr($str, $keyword)
{
    $str = nv_clean60($str, 300);
    if (! empty($keyword)) {
        $tmp = explode(' ', $keyword);
        foreach ($tmp as $k) {
            $tp = strtolower($k);
            $str = str_replace($tp, '<span class="keyword">' . $tp . '</span>', $str);
            $tp = strtoupper($k);
            $str = str_replace($tp, '<span class="keyword">' . $tp . '</span>', $str);
            $k[0] = strtoupper($k[0]);
            $str = str_replace($k, '<span class="keyword">' . $k . '</span>', $str);
        }
    }
    return $str;
}

$key = $nv_Request->get_title('q', 'get', '');
$key = str_replace('+', ' ', $key);
$key = trim(nv_substr($key, 0, NV_MAX_SEARCH_LENGTH));
$keyhtml = nv_htmlspecialchars($key);

$base_url_rewrite = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op;
if (! empty($key)) {
    $base_url_rewrite .= '&q=' . $key;
}

$choose = $nv_Request->get_int('choose', 'get', 0);
if (! empty($choose)) {
    $base_url_rewrite .= '&choose=' . $choose;
}

$catid = $nv_Request->get_int('catid', 'get', 0);
if (! empty($catid)) {
    $base_url_rewrite .= '&catid=' . $catid;
}
$from_date = $nv_Request->get_title('from_date', 'get', '', 0);
$date_array['from_date'] = preg_replace('/[^0-9]/', '.', urldecode($from_date));
if (preg_match('/^([0-9]{1,2})\.([0-9]{1,2})\.([0-9]{4})$/', $date_array['from_date'])) {
    $base_url_rewrite .= '&from_date=' . $date_array['from_date'];
}

$to_date = $nv_Request->get_title('to_date', 'get', '', 0);
$date_array['to_date'] = preg_replace('/[^0-9]/', '.', urldecode($to_date));
if (preg_match('/^([0-9]{1,2})\.([0-9]{1,2})\.([0-9]{4})$/', $date_array['to_date'])) {
    $base_url_rewrite .= '&to_date=' . $date_array['to_date'];
}

$page = $nv_Request->get_int('page', 'get', 1);
if (! empty($page)) {
    $base_url_rewrite .= '&page=' . $page;
}
$base_url_rewrite = nv_url_rewrite($base_url_rewrite, true);

$request_uri = urldecode($_SERVER['REQUEST_URI']);
if ($request_uri != $base_url_rewrite and NV_MAIN_DOMAIN . $request_uri != $base_url_rewrite) {
    header('Location: ' . $base_url_rewrite);
    die();
}

$array_cat_search = array();
foreach ($global_array_cat as $arr_cat_i) {
    $array_cat_search[$arr_cat_i['catid']] = array(
        'catid' => $arr_cat_i['catid'],
        'title' => $arr_cat_i['title'],
        'select' => ($arr_cat_i['catid'] == $catid) ? 'selected' : ''
    );
}
$array_cat_search[0]['title'] = $lang_module['search_all'];

$contents = call_user_func('search_theme', $key, $choose, $date_array, $array_cat_search);
$where = '';
$tbl_src = '';
if (empty($key) and ($catid == 0) and empty($from_date) and empty($to_date)) {
    $contents .= '<div class="alert alert-danger">' . $lang_module['empty_data_search'] . '</div>';
} else {
    $canonicalUrl = nv_url_rewrite(NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=search&amp;q=' . $key, true);
	if (strpos($canonicalUrl, NV_MY_DOMAIN) !== 0) {
	$canonicalUrl = NV_MY_DOMAIN . $canonicalUrl;
	}

	$dbkey = $db_slave->dblikeescape($key);
	$dbkeyhtml = $db_slave->dblikeescape($keyhtml);
	//search keytml

	// Tìm kiếm có 1 trong các từ.
	//$params['body']['query']['match']['title'] ='NukeViet tuyển dụng';

	// Tìm kiếm có tất cả các từ
	$params['body']['query']['multi_match']['query'] = 'triển khai';
	$params['body']['query']['multi_match']['operator'] ='and';
	$params['body']['query']['multi_match']['fields'] = [
	"title",
	"hometext", "bodyhtml"
	];

	if (empty($key)) {
	$page_title = $lang_module[
'search_title'] . ' ' . NV_TITLEBAR_DEFIS . ' ' . $module_info['custom_title'];
} else {
    $page_title = $key . ' ' . NV_TITLEBAR_DEFIS . ' ' . $lang_module['search_title'];
    if ($page > 2) {
        $page_title .= ' ' . NV_TITLEBAR_DEFIS . ' ' . $lang_global['page'] . ' ' . $page;
    }
    $page_title .= ' ' . NV_TITLEBAR_DEFIS . ' ' . $module_info['custom_title'];
}

$key_words = $description = 'no';
$mod_title = isset($lang_module['main_title']) ? $lang_module['main_title'] : $module_info['custom_title'];

include NV_ROOTDIR . '/includes/header.php';
echo nv_site_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';