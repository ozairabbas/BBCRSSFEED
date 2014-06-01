<?php
/**
 * Gets the latest stories from the BBC technology RSS Feed (default behaviour).
 * Reorder the stories into Alphabetical order based on the <title> attribute.
 * Displays image, title, description and a link to the original story.
 * 
 * Customize the RSS Feed to get some other RSS Feed (e.g. world/asia/rss.xml or business/rss.xml)
 * Customize the RSS Feed sorting order. 
 * Available options A-Z, Z-A, Latest, and Oldest
 * Feed is refreshed every 60 seconds. This can be modified on line number 60: var refreshRate = 60;
 *
 * @author     Syed Ozair Abbas
 * @version    Version 1.0
 * 
 * 
 */
require_once("bbcRssParser.php");

$feedUrl = "technology/rss.xml";
$sortOrder = "atoz";

if (($_SERVER["REQUEST_METHOD"] == "POST") && !empty($_POST["url"])) {
	$feedUrl = $_POST["url"];
	$sortOrder = $_POST["sortorder"];
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
?>


<!doctype html>
<html>
<head>
	<meta charset="UTF-8">
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
	<script>
		$(document).ready(function(){

		var count = 0;
		
		(function updaterss() {
			var rssFeedUrl = $('#submitedUrl').val();
			var rssSortOrder = $('#sortorder').val();
			var refreshRate = 60; //seconds
		
			$.ajax({
				type: "POST",
				data: { feedUrl: rssFeedUrl, sortOrder: rssSortOrder },
				url: 'updateRss.php', 
				success: function(data) {
					var updateString = "Update No. " + count++ + " (updates every " + refreshRate + " seconds) <hr/>";
					
			    	$('.rssfeedOutput').html(updateString + data);
			    },
				complete: function() {
			    	// Schedule the next request when the current one's complete (currently set to 15 seconds)
			    	setTimeout(updaterss, refreshRate * 1000);
				}
			});
		})();
		
		});
	</script>
	
	<title><?= $rssFeedObj->getFeedTitle();?></title>
	
	<style>
		body {
			font-family: Verdana;
		}
		
		small {
			display: block;
			font-size: 70%;
		}
		
		header {
			background-color: #990000;
			color: #fff;
		}

		form {
			padding-top: 15px;
		}
				
		.clear{
			clear: both;
		}
		
		.thumbnail{
			float:left; 
			margin-right: 5px; 
			padding: 8px; 
			border: 1px solid #ccc;
		}
		.news{
			float:left; 
		}
	</style>
</head>

<body>	

<header>
	<div style="float: left;">
		<?= $rssFeedObj->getFeedImage();?>	
	</div>
	
	<div>
		<form id="rssForm" name="rssForm" method="post" action="index.php">
			<label><?= bbcRssParser::BASEURL;?></label>
			<input type="hidden" name="submitedUrl" id="submitedUrl" value="<?= $feedUrl;?>"/>
			<input type="text" name="url" id="url" value="<?= $feedUrl;?>"/>
			<select name="sortorder" id="sortorder">
				<option value="atoz" <?= ($sortOrder == "atoz") ? 'selected' : ''; ?>>A to Z</option>
				<option value="ztoa" <?= ($sortOrder == "ztoa") ? 'selected' : ''; ?>>Z to A</option>
				<option value="latest" <?= ($sortOrder == "latest") ? 'selected' : ''; ?>>Latest</option>
				<option value="oldest" <?= ($sortOrder == "oldest") ? 'selected' : ''; ?>>Oldest</option>
			</select>
			<input type="submit" name="submit" value="Get Feed!"/>
		</form>
	</div>
		
	<div class="clear"></div>
</header>

<h1><?= $rssFeedObj->getFeedTitle();?><small><?= $rssFeedObj->getFeedDescription();?></small></h1>

<div class="rssfeedOutput">
<?php
	$rssFeedObj->print_rssFeed();
?>
</div>

<footer>
	<center><small><?= $rssFeedObj->getFeedCopyright();?></small></center>
</footer>
</body>
</html>

<?php 
} catch (Exception $e) {
	echo "<h1>" . $e->getMessage() . " <a href='index.php'>Click here to try again</a></h1> ";
	echo "<h2>You can try other rss news feeds starting with " . bbcRssParser::BASEURL . "</h2>";
	die();	
}
?>