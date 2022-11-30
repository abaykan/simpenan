<?php
/*
Name: cfgetdomainowner.php
Description: Search domain and get domain's owner from CloudFlare accounts
Usage: php cfgetdomainowner.php domain.com

Get your account's 'api_key' from https://dash.cloudflare.com/profile/api-tokens in "API Keys" section then view Global API Key.
*/
$accounts = array(
	[
	    'email' => '',
	    'api_key' => '',
	],
);

$domain = $argv[1];

foreach ($accounts as $account) {
	echo '> Searching in ' . $account['email'];
	echo PHP_EOL;
	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL, 'https://api.cloudflare.com/client/v4/zones');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

	$headers = array();
	$headers[] = 'X-Auth-Email: ' . $account['email'];
	$headers[] = 'X-Auth-Key: ' . $account['api_key'];
	$headers[] = 'Content-Type: application/json';
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

	$result = curl_exec($ch);
	if (curl_errno($ch)) {
	    echo 'Error:' . curl_error($ch);
	}
	curl_close($ch);

	$result = json_decode($result, TRUE);

	foreach ($result['result'] as $key => $value) {
		if ($value['name'] == $domain) {
			$data['domain'] = $value['name'];
			$data['staus'] = $value['status'];
			$data['owner_name'] = $value['account']['name'];
			$data['owner_email'] = $value['owner']['email'];
			$data['name_servers'] = $value['name_servers'];
		} else {
			continue;
		}
	}
}

if (isset($data)) {
	print_r($data);
} else {
	echo "\nNot Found.";
}
