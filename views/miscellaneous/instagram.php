<a href="https://api.instagram.com/oauth/authorize/?client_id=bb32b3f2113746e28762c38aa0bd82fb&redirect_uri=http://localhost/miscellaneous/instagram&response_type=code">Auth me!</a>

<?php 
	if(isset($_GET['code'])){
		
		
		
		$params = array(
			'code' => $_GET['code'],
			'client_id' => INSTAGRAM_ID, 
			'client_secret' => INSTAGRAM_SECRET, 
			'grant_type' => 'authorization_code', 
			'redirect_uri' => 'http://localhost/miscellaneous/instagram'
		);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json'));
		curl_setopt($ch, CURLOPT_POST, count($params));
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_URL, 'https://api.instagram.com/oauth/access_token');
		$json = curl_exec($ch);
		if(!$json) echo curl_error($ch);
		curl_close($ch);
		print_r(json_decode($json));
	}else{
		header('Location: https://api.instagram.com/oauth/authorize/?client_id=#####&redirect_uri=http://localhost/miscellaneous/instagram&response_type=code');
		die();
		
		/*
		$params = array(
			'username' => ',
			'password' => '', 
		);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json'));
		curl_setopt($ch, CURLOPT_POST, count($params));
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_URL, 'https://api.instagram.com/oauth/authorize/?client_id=#####&redirect_uri=http://localhost/miscellaneous/instagram&response_type=code');
		$test = curl_exec($ch);
		curl_close($ch);
		print_r($test);*/
	}
	
	/*
	<?xml version="1.0" encoding="UTF-8" ?>
	<rss version="2.0" xmlns:media="http://search.yahoo.com/mrss/">
		<channel>
			<title>Sample Feed</title>
			<link>http://localhost</link>
			<description>Sample Media RSS</description>
			<item>
				<title>Item 1</title>
				<description>Item 1 Description</description>
				<media:content url="http://localhost/images/test.jpg" type="image/jpeg"></media:content>
			</item>
		</channel>
	</rss>
	*/
?>