<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC (contact@vinades.vn)
 * @Copyright (C) 2014 VINADES.,JSC. All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate 03-05-2010
 */

if (! defined('NV_IS_MOD_SEARCH')) {
    die('Stop!!!');
}
//tìm kiếm elasticsearch
if(isset($db_config['elas_host']))
{
	 /*kết nối host*/
	$hosts = array( $db_config['elas_host'] . ':' . $db_config['elas_port'] );
	$client = Elasticsearch\ClientBuilder::create( )->setHosts( $hosts )->setRetries( 0 )->build();
	/*----------------end----------*/
	//khai bao bien
	$module_data=news;
	$params = [
    'index' => 'nukeviet4_demo',
    'type' => NV_PREFIXLANG . '_' . $module_data . '_rows',
    ];
	//tìm kiếm
	$params['body']['query']['bool']=[
	"should"=> [
              "multi_match" => [//dung multi_match:tim kiem theo nhieu truong
                "query"=> $dbkeyword,//tim kiem theo tu khoa
                "type"=> ["cross_fields"],
                "fields"=> [ "title",
    						"hometext","bodyhtml"],//tim kiem theo 3 trương mặc định là hoặc
		        "minimum_should_match"=> ["50%"]
		         ]
            	],
		];
		$response = $client->search($params);
		//print_r($response);die('test');
		$num_items=$response ['hits']['total'];
		if ($num_items) {
	    $array_cat_alias = array();
	    $array_cat_alias[0] = 'other';

	    $sql_cat = 'SELECT catid, alias FROM ' . NV_PREFIXLANG . '_' . $m_values['module_data'] . '_cat';
	    $re_cat = $db_slave->query($sql_cat);
	    while (list($catid, $alias) = $re_cat->fetch(3)) {
	        $array_cat_alias[$catid] = $alias;
	    }
		$link = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $m_values['module_name'] . '&amp;' . NV_OP_VARIABLE . '=';
		foreach ($response ['hits'] ['hits'] as $key => $value) {
        $content = $value['_source']['hometext'] . strip_tags($value['_source']['bodytext']);
        $url = $link . $array_cat_alias[$value['_source']['catid']] . '/' . $value['_source']['alias'] . '-' . $value['_source']['id'] . $global_config['rewrite_exturl'];
	 	$result_array[] = array(
            'link' => $url,
            'title' => BoldKeywordInStr($value['_source']['title'], $key, $logic),
            'content' =>BoldKeywordInStr($content, $key, $logic)

        );
    }
}
}
else {
	$db_slave->sqlreset()
    ->select('COUNT(*)')
    ->from(NV_PREFIXLANG . '_' . $m_values['module_data'] . '_rows r')
    ->join('INNER JOIN ' . NV_PREFIXLANG . '_' . $m_values['module_data'] . '_detail c ON (r.id=c.id)')
    ->where('(' . nv_like_logic('r.title', $dbkeywordhtml, $logic) . ' OR ' . nv_like_logic('r.hometext', $dbkeyword, $logic) . ' OR ' . nv_like_logic('c.bodyhtml', $dbkeyword, $logic) . ')	AND r.status= 1');

	$num_items = $db_slave->query($db_slave->sql())->fetchColumn();

	if ($num_items) {
	    $array_cat_alias = array();
	    $array_cat_alias[0] = 'other';

	    $sql_cat = 'SELECT catid, alias FROM ' . NV_PREFIXLANG . '_' . $m_values['module_data'] . '_cat';
	    $re_cat = $db_slave->query($sql_cat);
	    while (list($catid, $alias) = $re_cat->fetch(3)) {
	        $array_cat_alias[$catid] = $alias;
	    }
	    $link = NV_BASE_SITEURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $m_values['module_name'] . '&amp;' . NV_OP_VARIABLE . '=';

	    $db_slave->select('r.id, r.title, r.alias, r.catid, r.hometext, c.bodyhtml')
	        ->order('publtime DESC')
	        ->limit($limit)
	        ->offset(($page - 1) * $limit);
	    $result = $db_slave->query($db_slave->sql());
	    while (list($id, $tilterow, $alias, $catid, $hometext, $bodytext) = $result->fetch(3)) {
	        $content = $hometext . strip_tags($bodytext);
	        $url = $link . $array_cat_alias[$catid] . '/' . $alias . '-' . $id . $global_config['rewrite_exturl'];
		 $result_array[] = array(
	            'link' => $url,
	            'title' => BoldKeywordInStr($tilterow, $key, $logic),
	            'content' => BoldKeywordInStr($content, $key, $logic)

	        );
			//print_r(BoldKeywordInStr($content, $key, $logic));echo"<br/>";die('pass');
	    }
	}

}

