<?php

include("Oauth.php");
include("tweet_date.php");
include("tweet_json.php");
include("emoji.php");
include("tweet_finder.php");

//variables to hold results for chart
$great 		= 0;
$good 		= 0;
$neutral 	= 0;
$bad 		= 0;
$terrible 	= 0;

//these variables build the twitter query
$term = $_GET['issue'];
$searchlat = "33.787423";
$searchlng = "-84.372597";
$rad = "5mi";

$geolocated = array();

?>

<!doctype html>
<html>
	<head>
		<title>Data Democracy - <? echo $term; ?></title>

		<link rel="stylesheet" media="screen" href="http://openfontlibrary.org/face/gauge" rel="stylesheet" type="text/css"/>
		<link rel="stylesheet" href="http://cdn.leafletjs.com/leaflet-0.7.3/leaflet.css" />
		<link rel="stylesheet" type="text/css" href="../css/results.css">
		

		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
		<script src="../js/main.js"></script>
		<script src="../js/vendor/Chart.js"></script>
	
	</head>


<?
//CALL THE TWITTER SEARCH FUNCTION
$json_output  = twitterSearch($searchlat, $searchlng, $rad, $term);
$tweetsAndGeo = analyzeTweets($json_output);

$maxs = array_search(max($tweetsAndGeo[2][0]), $tweetsAndGeo[2][0]);

$feeling="";
if ($maxs == "0"){
	$feeling = "great";
}elseif ($maxs == "1"){
	$feeling = "good";
}elseif ($maxs == "2"){
	$feeling = "neutral";
}elseif ($maxs == "3"){
	$feeling = "bad";
}elseif ($maxs == "4"){
	$feeling = "terrible";
}

?>
	<body>
	<div id="pseudobody">
	<div id="container">
	 	<h1 id="header"><a href="../">data<span>&nbsp;</span>democracy</a></h1>

		<div id="map"></div>
		<div id="canvas-holder">
			<canvas id="chart-area" width="300" height="300"/>
		</div>

		<div id="recommendations">
	 		<h2>Atlanta thinks that <?echo $term; ?> is <? echo $feeling; ?>!</h2>
	 		<p>Here are some suggestions that may affect <?echo $term; ?>:</p>
	 		<ul>
	 			<li>You're doing a great job with $positiveTweet</li>
	 			<li>Think about doing something else to solve $negativeTweet</li>
	 			<li>You may also want to consider addressing $mostPopularTweet</li>

	 		<?// gonna have some code up in hurr ?>
	 	</div>

<?php

echo "<h3 id='tweet_toggle' class='closed'>
	  &raquo; Show recent '".$term."' tweets near 
	  <span>".$searchlat."</span>, 
	  <span>".$searchlng."</span></h3>";

echo "<div id='tweets'>";

echo "<ol>";
foreach($tweetsAndGeo[0] as $tweet){
	echo "<li><p>";
	echo $tweet[0]."</p><p class=\"stats\">";
	echo "<b>Lat: </b> ".	$tweet[1]."<br />";
	echo "<b>Lng: </b> ".	$tweet[2]."<br />";
	echo "<b>Time: </b> ".	$tweet[3]."<br />";
	echo "<b>Rating: </b>".	$tweet[4]."<br />";
}
echo "</ol>";

echo "</div>";

//THIS IS SUPER GROSS, I'M SORRY LORD
if($tweetsAndGeo[2][0][0] > 0){
	$great = $tweetsAndGeo[2][0][0];
}else{
	$great = "0";
}
if($tweetsAndGeo[2][0][1] > 0){
	$good = $tweetsAndGeo[2][0][1];
}else{
	$good = "0";
}
if($tweetsAndGeo[2][0][2] > 0){
	$neutral = $tweetsAndGeo[2][0][2];
}else{
	$neutral = "0";
}
if($tweetsAndGeo[2][0][3] > 0){
	$bad = $tweetsAndGeo[2][0][3];
}else{
	$bad = "0";
}
if($tweetsAndGeo[2][0][4] > 0){
	$terrible = $tweetsAndGeo[2][0][4];
}else{$terrible = "0";
}

echo "<div id=\"analysis\">";
echo "<h3>< Sentiment Analysis</h3>";
echo "<p>Great: <span id='ana_great'>".$great."</span></p>";
echo "<p>Good: <span id='ana_good'>".$good."</span></p>";
echo "<p>Neutral: <span id='ana_neutral'>".$neutral."</span></p>";
echo "<p>Bad: <span id='ana_bad'>".$bad."</span></p>";
echo "<p>Terrible: <span id='ana_terrible'>".$terrible."</span></p>";
echo "<br />";
echo "<h3 class=\"right\">tweet locations ></h3>";
echo "<p style=\"text-align: right\"><b>".count($tweetsAndGeo[1])."</b> geolocated tweets</p>";
echo "</div>";

?>

<script>

	var pieData = [
			{
				value: <?php echo $great; ?>,
				color:"#2D60B4",
				highlight: "#3671d1",
				label: "Great"
			},
			{
				value: <?php echo $good; ?>,
				color: "#00B8EE",
				highlight: "#00c5ff",
				label: "Good"
			},
			{
				value: <?php echo $neutral; ?>,
				color: "#DDD",
				highlight: "#eee",
				label: "Neutral"
			},
			{
				value: <?php echo $bad; ?>,
				color: "#fa3133",
				highlight: "#ff6163",
				label: "Bad"
			},
			{
				value: <?php echo $terrible; ?>,
				color: "#BE0002",
				highlight: "#d50204",
				label: "Terrible"
			}

		];

		window.onload = function(){
			var ctx = document.getElementById("chart-area").getContext("2d");
			window.myPie = new Chart(ctx).Pie(pieData);
		};

	</script>

		<script src="http://cdn.leafletjs.com/leaflet-0.7.3/leaflet.js"></script>
		<script type="text/javascript" src="http://maps.stamen.com/js/tile.stamen.js?v1.3.0"></script>
		<script src="../js/map.js"></script>
		<script>

			function onEachFeature(feature, layer) {
 				// does this feature have a property named popupContent?
    			if (feature.properties && feature.properties.popupContent) {
        			layer.bindPopup(feature.properties.popupContent);
				}
			}

			//hacky way to grab current tweet geolocation for the map 
			var geojsonFeature = { 
				"type": "FeatureCollection",
    				    "features": [
			<? foreach( $tweetsAndGeo[1] as $tweet ){
				$long 	 = $tweet[1] + 0;
				$lat  	 = $tweet[0] + 0;
				$content = $tweet[2];
				$type 	 = $tweet[3];

				echo"
					{
				    	\"type\": \"Feature\",
				    	\"geometry\": {
				        	\"type\": \"Point\",
				        	\"coordinates\": [".$lat.", ".$long."]
				    	},
				    	\"properties\": {
				    		\"popupContent\": \"".$content."\",
				    		\"type\": \"".$type."\"
				    	}
					},"; 
				}?>
			]};

			// TODO: generate different icons for tweet types so that 
			// the geolocated tweets can be visibly differentiated

			L.geoJson(geojsonFeature).addTo(map);

		</script>
		</div>
		</div>
	 	<div id="footer">
        	<div id="footer_container">
	            <div class="logo">
	                <h3>data<span>&nbsp;</span>democracy</h3>
	                &copy; 2014 Data Democracy
	            </div>
	            <a href="#">Contact</a>
	            <a href="#">About us</a>
	            <a href="#">How it works</a>
	            <a href="#">Other link</a>
	            <a href="#">Last link</a>
	            <hr>
	            <p>This is a work of satire, and of course is not available as a real product.<br/> 
	            That would be bonkers.</p>
	        </div>
        </div>
	</body>
</html>