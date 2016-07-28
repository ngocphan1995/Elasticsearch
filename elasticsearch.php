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
    'index' => 'nukeviet4_demo',
    'type' => NV_PREFIXLANG . '_' . $module_data . '_rows',
    'id' => 43,
];
echo '<pre>';
/*Xóa dữ liệu*/
/*$response = $client->delete($params);
print_r($response);die('pass');

//Search for a document
//http://www.sitepoint.com/introduction-to-elasticsearch-in-php/
echo 'Search for a document: ';
$params = array();
$params['index'] = 'mangvn_com';
$params['type'] = NV_PREFIXLANG . '_' . $module_data . '_rows';
// Tìm kiếm có 1 trong các từ.
//$params['body']['query']['match']['title'] ='NukeViet tuyển dụng';

/*
// Tìm kiếm có tất cả các từ
$params['body']['query']['multi_match']['query'] = 'thực tập';
$params['body']['query']['multi_match']['operator'] ='and';
$params['body']['query']['multi_match']['fields'] = [
    "title",
    "hometext", "bodyhtml"
];


$response = $client->search($params);
print_r($response);*/
$params = [
    'index' => 'nukeviet4_demo',
    'type' => NV_PREFIXLANG . '_' . $module_data . '_rows'
    ];

$test1=[
/*"should"=> [
              "multi_match" => [
                "query"=> ['Viet Nam'],
                "type"=> ["cross_fields"],
                "fields"=> [ "title",
    						"hometext",],
                "minimum_should_match"=> ["50%"]
              ]
            ],
];*/
"should"=>["match"=>["sourcetext"=>"Viet Nam"]]
];
$test2=[
	"must"=> [
	            //"term" => [ "id" =>17],
	            "range"=> [ "publtime" =>["gte"=>1453192440,"lte"=>1468565640]],
	            //'term'=>['title'=>"Việt Nam" ]
	        ]
];
//$test=array_merge($test1,$test2);
//print_r($test);die('test');
$title='title';
$test=[
 "bool"=> [
        "should"=> [
              "multi_match" => [
                "query"=> ['tập huấn'],
                "type"=> ["cross_fields"],
                "fields"=> [ "title",
    						"hometext",],
                "minimum_should_match"=> ["50%"]
              ]
            ],
            'should'=> [
	              		'term'=>['admin_id'=>1]
	            		],
          "must"=> [
          //"term" => [ "id" =>17],
            "range"=> [ "publtime" =>["gte"=>1467306000]]
            //'match'=>['sourcetext'=>"ngocphan" ]
        ],
        ]
];
$match =array();
$test=
Array
(
    "should"=> [
              "multi_match" => [
                "query"=> ['tập huấn'],
                "type"=> ["cross_fields"],
                "fields"=> [ "title",
    						"hometext",],
                "minimum_should_match"=> ["50%"]
              ]
            ],

);
$params['body']['query']['bool']=$test;
$params['body']['size']=2;
$params['body']['from']=1;
$response1 = $client->search($params);
print_r($response1);

//tìm kiếm lặp
/*for($i=1;$i<3;$i++)
{
$test=[
 "bool"=> [
        'should'=> [
	              		'term'=>['admin_id'=>$i]
	            		],
        ]
];
*/

//or filter

$match = array();
$match[] = ['match'=>['id'=>67]];
$match[] = ['match'=>['id'=>68]];

$params['body']['query']=[
	 "bool"=> [
		 'filter'=>[
		 	'or'=> $match
		 ]
	 ]
];




echo"--------------------------------------";
print_r($response2);

$response=array_merge($response1,$response2);

echo"--------------------------------------ok";
print_r($response);




echo '</pre>';
$t=number_format((microtime(true) - NV_START_TIME), 3, '.', '');
die('tiem='.$t);
