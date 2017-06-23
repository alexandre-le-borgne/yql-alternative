<?php

require_once('phpQuery-onefile.php');

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

$statusCode = 200;
$url = urldecode($_POST['url']) ?? '';

try {
	if(!$url) {
		throw new Exception('Url is required');
	}
	$content = pq(phpQuery::newDocumentFile($url));
	$response = [
		'title' => $content->find('title')->text(),
		'description' => $content->find('meta[name="description"]')->attr('content')
	];
	$ogs = $content->find('meta[property*="og:"]');
	foreach($ogs as $og) {
		$og = pq($og);
		$response[$og->attr('property')] = $og->attr('content');
	}
	$response = json_encode($response);
}
catch(Exception $e) {
	$statusCode = 400;
	$response = $e->getMessage();
}

http_response_code($statusCode);
echo $response;