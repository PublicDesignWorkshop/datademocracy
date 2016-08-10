<?php

//set display errors on and ask to be warned against any kind of bugs

function getJSON($tweet_array){

	$i=0;
	$json = "{ \"type\": \"Object\",\"tweets\": [";

	foreach($tweet_array as $tweet){
		// takes every tweet row and constructs geojson from the contents.
		$json .= "{ \"type\": \"tweet\",";
		$json .= "\"properties\": {";
		$json .= "\"NAME\": \"".	$tweet['username']	."\",";
		$json .= "\"CONTENT\": \"".	$tweet['content']	."\",";
		$json .= "\"TIME\": \"".	$tweet['time']		."\",";
		$json .= "}},";
	}

	$json .= "]};";
	return $json;
}
	
?>