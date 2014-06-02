<?php
/**
 * This is the BBC News rss parser. It implements iNewsRssParser.
 * Subsequent news rss feed would have there own classes as there RSS feed format might differ.
 * Classes must implement the sorting functions in iNewsRssParser.
 * 
 * @author     Syed Ozair Abbas
 * @version    Version 1.0
 * 
 */
require_once("iNewsRssParser.php");

class bbcRssParser implements iNewsRssParser {
	#CLASS ATTRIBUTES
	const BASEURL = "http://feeds.bbci.co.uk/news/";	

	protected $feedLink;	
	protected $feedTitle;
	protected $feedDescription;
	protected $feedLanguage;
	protected $feedLastBuildDate;
	protected $feedCopyright;
	protected $feedImage;		
	protected $feedCount;
	
	private $rssFeedArray;
	
	/**
	 * Construct for bbcRssParser.
	 * Takes portion of the rss link and pouplates relevant attributes so the object is ready for 
	 * consumption. Throws an exception if rss cant be read.
	 */
 	public function __construct($rssLink)
	{
		$xml = @simplexml_load_file(self::BASEURL . $rssLink);
		if ($xml === false) {
			throw new Exception("Could not parse XML File.");
	    } else {
			$this->setFeedTitle($xml->channel->title);
			$this->setFeedLink($xml->channel->link);
			$this->setFeedDescription($xml->channel->description);
			$this->setFeedLanguage($xml->channel->language);
			$this->setFeedLastBuildDate($xml->channel->lastBuildDate);
			$this->setFeedCopyright($xml->channel->copyright);
			$this->setFeedImage($xml->channel->image);
			
			#echo "<pre>" . print_r($xml, 1) . "</pre>";			
			
			$count = $this->setRssFeedArray($xml);
			$this->setFeedCount($count);
		}
	}
	
	/**
	 * Prints the rss feed using heredoc format.
	 */
	public function print_rssFeed(){
		$count = 1;
		foreach($this->rssFeedArray as $key => $newsItem) {
			$title = $newsItem["title"]; 
			$date = $newsItem["pubDate"];
			$description = $newsItem["description"];
			$link = $newsItem["guid"];
			$imageSrc = $newsItem["image"];
			
			echo <<<EOD
			<div>
				<div class="thumbnail">
					<img src="$imageSrc" />
				</div>
				
					<h4>$count. $title<small>$date</small></h4> 
					<p>$description</p>
					<a href="$link" target="_blank">Read More ( $link ) ...</a>
				
			</div>
			<br/>
			<hr/>
EOD;

			$count++;
		}
	}

	/**
	 * Sort the news feed by date published. Latest first.
	 */
	public function sort_chronologically() {
		usort($this->rssFeedArray, function ($x, $y) {			
			return strtotime($y["pubDate"]) - strtotime($x["pubDate"]);
		});
	}

	/**
	 * Sort the news feed by date published. Oldest first.
	 */
	public function sort_chronologically_reverse() {
		usort($this->rssFeedArray, function ($x, $y) {
			return strtotime($x["pubDate"]) - strtotime($y["pubDate"]);
		});
	}
	
	/**
	 * Sort the news feed by title. A - Z. Ignore appostrophes(') in title.
	 */
	public function sort_alphabatically() {
		usort($this->rssFeedArray, function ($x, $y) {
			return strcasecmp(str_replace("'", "", $x["title"]), str_replace("'", "", $y["title"]));
		});
	}
	
	/**
	 * Sort the news feed by title. Z - A. Ignore appostrophes(') in title.
	 */
	public function sort_alphabatically_reverse() {
		usort($this->rssFeedArray, function ($x, $y) {
			return strcasecmp(str_replace("'", "", $y["title"]), str_replace("'", "", $x["title"]));
		});
	}
	
	/**
	 * Class Setters
	 */
	protected function setFeedLink($value) { 
		$this->feedLink = $value; 
	} 
	
	protected function setFeedTitle($value) { 
		$this->feedTitle = $value; 
	} 

	protected function setFeedDescription($value) { 
		$this->feedDescription = $value; 
	} 

	protected function setFeedLanguage($value) { 
		$this->feedLanguage = $value; 
	} 

	protected function setFeedLastBuildDate($value) { 
		$this->feedLastBuildDate = $value; 
	} 

	protected function setFeedCopyright($value) { 
		$this->feedCopyright = $value; 
	} 
	
	protected function setFeedImage($value) {
		$this->feedImage["url"] = (string)$value->url;
		$this->feedImage["title"] = (string)$value->title;
		$this->feedImage["link"] = (string)$value->link;
		$this->feedImage["width"] = (int)$value->width;
		$this->feedImage["height"] = (int)$value->height;
	}
	
	protected function setFeedCount($value) {
		$this->feedCount = $value;
	}
	
	protected function setRssFeedArray($value) {
		$namespace = "http://search.yahoo.com/mrss/";	
		$array = array();
		$count = 0;

		foreach ($value->channel->item as $item){
			$array[$count]["title"] = (string)$item->title;	
			$array[$count]["description"] = (string)$item->description;
			$array[$count]["link"] = (string)$item->link;
			$array[$count]["guid"] = (string)$item->guid;
			$array[$count]["pubDate"] = date("d M Y H:i:s", strtotime($item->pubDate));
			
			if(!is_object($item->children($namespace)->thumbnail[1]))continue;
			$image = $item->children($namespace)->thumbnail[1]->attributes();			
			$array[$count]["image"] = $image["url"];
			
			$count++;
		}
		
		$this->rssFeedArray = $array;
		return $count;
	}
	
	/**
	 * Class Getters
	 */
	public function getFeedTitle() { 
		return $this->feedTitle; 
	}

	public function getFeedDescription() { 
		return $this->feedDescription; 
	} 

	public function getFeedLanguage() { 
		return $this->feedLanguage; 
	} 

	public function getFeedLastBuildDate() { 
		return $this->feedLastBuildDate; 
	} 

	public function getFeedCopyright() { 
		return $this->feedCopyright; 
	} 
	
	public function getFeedImage() {
		$img = sprintf("<img src='%s' title='%s' width='%d' height='%d' />", $this->feedImage["url"], $this->feedImage["title"], $this->feedImage["width"], $this->feedImage["height"]);
		return $img;
	}

	public function getFeedCount() {
		return $this->feedCount;
	}
	
	public function getRssFeedArray() {
		return $this->rssFeedArray;
	}
}