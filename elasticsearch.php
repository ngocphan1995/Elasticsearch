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
function convert_vi_to_en($str) {
        $str = preg_replace("/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/", 'a', $str);
        $str = preg_replace("/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/", 'e', $str);
        $str = preg_replace("/(ì|í|ị|ỉ|ĩ)/", 'i', $str);
        $str = preg_replace("/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/", 'o', $str);
        $str = preg_replace("/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/", 'u', $str);
        $str = preg_replace("/(ỳ|ý|ỵ|ỷ|ỹ)/", 'y', $str);
        $str = preg_replace("/(đ)/", 'd', $str);
        $str = preg_replace("/(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)/", 'A', $str);
        $str = preg_replace("/(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)/", 'E', $str);
        $str = preg_replace("/(Ì|Í|Ị|Ỉ|Ĩ)/", 'I', $str);
        $str = preg_replace("/(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)/", 'O', $str);
        $str = preg_replace("/(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)/", 'U', $str);
        $str = preg_replace("/(Ỳ|Ý|Ỵ|Ỷ|Ỹ)/", 'Y', $str);
        $str = preg_replace("/(Đ)/", 'D', $str);
        return $str;
    }
$str='Phan Thị Ngọc';
$str=convert_vi_to_en($str);
print_r($str);die('test');

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

/*$params['settings']=
[
	'analysis'=>[
		"filter"=>[
			"vietnamese_stop"=>[
			 "type" => "stop",
          "stopwords"=>"_vietnamese_"
		],
		"vietnamese_keywords"=>[
          "type"=>"keyword_marker",
          "keywords"=>[]
        ],
        "vietnamese_stemmer"=>[
          "type"=> "stemmer",
          "language"=>"vietnamese"
        ],
        "vietnamese_possessive_stemmer"=>[
          "type"=>"stemmer",
          "language"=>"possessive_vietnamese"
        ]
		],
		"analyzer"=>[
        "vietnamese"=>[
          "tokenizer"=>"standard",
          "filter"=>[
            "vietnamese_possessive_stemmer",
            "lowercase",
            "vietnamese_stop",
            "vietnamese_keywords",
            "vietnamese_stemmer"
          ]
        ]
      ]
	]
	]
;*/

$test=
Array
(
    "should"=> [
              "multi_match" => [
                "query"=> ['Việt Nam'],
                "type"=> ["cross_fields"],
                "fields"=> [ 'title',
	    						'bodyhtml','author'],
                "minimum_should_match"=> ["50%"]
              ]
            ],

);
$params['body']['query']['bool']=$test;
//$params['body']=$setting;
/*$params['body']['size']=2;
$params['body']['from']=1;*/
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
