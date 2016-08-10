<?

function twitterSearch($searchlat, $searchlng, $rad, $term){
//this is what the Twitter library wants as arguments for the search_for_a_term function
$query = $term."&geocode=".$searchlat.",".$searchlng.",".$rad;
$typeresults = "mixed";
$numresults  = "100";

//authenticate, search with query, destroy token
$bearer_token = get_bearer_token();
$json = search_for_a_term($bearer_token, $query, $result_type=$typeresults, $count=$numresults);
invalidate_bearer_token($bearer_token);

//take results as json and look them over
$json_output = json_decode($json);

return $json_output;
}

function analyzeTweets($json_output){
	$geolocated = array();
	$tweets = array();
	$ratings = array();
	
	$great = "";
	$good = "";
	$neutral = "";
	$bad = "";
	$terrible = "";

	foreach ($json_output->statuses as $tweet) {
		// variables for tweet info
		$content    = utf8_decode(emoji_unified_to_html($tweet -> text));
		$lat        = mysql_escape_string(trim($tweet -> coordinates -> coordinates[0]));
		$lng        = mysql_escape_string(trim($tweet -> coordinates -> coordinates[1]));
		$time    	= mysql_escape_string(trim($tweet -> created_at));
		$type 		= ""; //saving this for later

		// $time = int strtotime($rawtime);

		// Get cURL resource to check sentiment
		$curl = curl_init();
		curl_setopt_array($curl, array(
	    	CURLOPT_RETURNTRANSFER => 1,
	    	CURLOPT_URL => 'http://www.datasciencetoolkit.org/text2sentiment',
	    	CURLOPT_POST => 1,
	    	CURLOPT_POSTFIELDS => array(item1 => $content)
		));
		$response = curl_exec($curl);
		curl_close($curl);

		$json = json_decode($response, true);
		$rating = (float)$json['score'];

	    //place the ratings into variables
	  	if ($rating >= "2.5"){
	  		$type = "great";
	    	$great++;
	    	// echo ", adding to 'great'";
	  	}
	  	elseif($rating > "0" && $rating < "2.5"){
	  		$type = "good";
			$good++;
			// echo ", adding to 'good'";
		}
	  	 elseif($rating == "0"){
	  	 	$type = "neutral";
			$neutral++;
			// echo ", adding to 'neutral'";
		}
	  	elseif($rating > "-2.5" && $rating < "0"){
	  		$type = "bad";
			$bad++;
			// echo ", adding to 'bad'";
	  	}
	  	elseif($rating <= "-2.5"){
	  		$type = "terrible";
			$terrible++;
			// echo ", adding to 'terrible'";
	  	}

	  	array_push($tweets, array($content, $lat, $lng, $time, $rating));

	 	//echo out the results (for debug)
		// $output .= "<li><p>";
		// $output .= $content."</p><p class=\"stats\">";
		// $output .= "<b>Lat: </b> ".		$lat        ."<br />";
		// $output .= "<b>Lng: </b> ".		$lng        ."<br />";
		// $output .= "<b>Time: </b> ".	$time 	
		// $output .= "<b>Rating: </b>".	$rating;	."<br />";
	    

		$output .= ".</p></li>";

		if(isset($lat)&&($lat!="")){
			array_push($geolocated, array($lat, $lng, $content, $rating));
		}
	}
	array_push($ratings, array($great, $good, $neutral, $bad, $terrible));

	return array($tweets, $geolocated, $ratings);
}