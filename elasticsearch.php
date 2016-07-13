<?php

/**
 * @Project NUKEVIET 4.x
 * @Author VINADES.,JSC (contact@vinades.vn)
 * @Copyright (C) 2014 VINADES.,JSC.
 * All rights reserved
 * @License GNU/GPL version 2 or any later version
 * @Createdate 31/05/2010, 00:36
 */

define('NV_SYSTEM', true);

// Xac dinh thu muc goc cua site
define('NV_ROOTDIR', pathinfo(str_replace(DIRECTORY_SEPARATOR, '/', __file__), PATHINFO_DIRNAME));

require NV_ROOTDIR . '/includes/mainfile.php';

$hosts = [
    '10.0.0.124:9200'
];
$client = Elasticsearch\ClientBuilder::create()->setHosts($hosts)
    ->setRetries(0)
    ->build();

//https://www.elastic.co/guide/en/elasticsearch/client/php-api/2.0/_quickstart.html

$module_data = 'news';

$params = [
    'index' => 'nukeviet4',
    'type' => NV_PREFIXLANG . '_' . $module_data . '_rows',
    'id' => 0,
    'body' => []
];
echo '<pre>';
/*
//Index a document: Thêm mới 1 row
$db_slave->sqlreset()
    ->select('*')
    ->from(NV_PREFIXLANG . '_' . $module_data . '_rows')
    ->where('status=1 AND inhome=1')
    ->order('publtime DESC');

$result = $db_slave->query($db_slave->sql());
while ($row = $result->fetch()) {
    $params['id'] = $row['id'];
    $params['body'] = $row;
    $response = $client->index($params);
    if ($response['created']) {
        print_r($response);
    }
}


//Get a document: : Lấy dữ liệu 1 row
$params = [
    'index' => 'nukeviet4',
    'type' => NV_PREFIXLANG . '_' . $module_data . '_rows',
    'id' => 8
];
try {
    $response = $client->get($params);
    $row = $response['_source'];
    print_r($row);
} catch (Exception $e) {
    $row = [];
}
*/

//Search for a document
//http://www.sitepoint.com/introduction-to-elasticsearch-in-php/
echo 'Search for a document: ';
$params = array();
$params['index'] = 'nukeviet4';
$params['type'] = NV_PREFIXLANG . '_' . $module_data . '_rows';
// Tìm kiếm có 1 trong các từ.
//$params['body']['query']['match']['title'] ='NukeViet tuyển dụng';


// Tìm kiếm có tất cả các từ
$params['body']['query']['multi_match']['query'] = 'NukeViet nguồn mở';
$params['body']['query']['multi_match']['operator'] ='and';
$params['body']['query']['multi_match']['fields'] = [
    "title",
    "hometext"
];

$response = $client->search($params);
print_r($response);
echo '</pre>';

die('Xong');
