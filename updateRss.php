<?php
require_once("bbcRssParser.php");

static $count = 0;

$feedUrl = "technology/rss.xml";
$sortOrder = "atoz";

if (isset($_POST["feedUrl"])) {
	$feedUrl = $_POST["feedUrl"];
}
if (isset($_POST["sortOrder"])) {
	$sortOrder = $_POST["sortOrder"];
}

try {
	$rssFeedObj = new bbcRssParser($feedUrl);
	
	switch($sortOrder) {
		case "atoz" :
			$rssFeedObj->sort_alphabatically();
			break;
		case "ztoa" :
			$rssFeedObj->sort_alphabatically_reverse();
			break;
		case "latest" :
			$rssFeedObj->sort_chronologically();
			break;
		case "oldest" :
			$rssFeedObj->sort_chronologically_reverse();
			break;
	}
	
	if ($rssFeedObj->getFeedCount() > 0) {
		$rssFeedObj->print_rssFeed();
	} else {
		echo "error";
	}
} catch(Exception $e) {
	echo "<h1>" . $e->getMessage() . " <a href='index.php'>Click here to try again</a></h1> ";
	echo "<h2>You can try other rss news feeds starting with " . bbcRssParser::BASEURL . "</h2>";
}