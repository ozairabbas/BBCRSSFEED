<?php
/**
 * This is the PHPUnit Framework test class.
 * Number of tests: 3
 * Number of assertions: 12
 *
 * @author     Syed Ozair Abbas
 * @version    Version 1.0
 * 
 * 
 */
require_once("bbcRssParser.php");

class bbcRssParserTest extends PHPUnit_Framework_TestCase {
	protected $xmlObject;
	
	public function setUp() {
		$this->xmlObject = new bbcRssParser("technology/rss.xml"); 
	}
	
	public function testGetFeedCount(){
		#This will always be greater than 0 if the feed is working
		$this->assertGreaterThan(0, $this->xmlObject->getFeedCount());
	}
	
	public function testGetFeedTitle() {
		#There must always be a title of the feed	
		$this->assertNotEmpty($this->xmlObject->getFeedTitle());
	}
	
	public function testGetRssFeedArray() {
		#Test RSS Feed array is not empty and is set properly
		$array = $this->xmlObject->getRssFeedArray();
		
		$this->assertArrayHasKey("title", $array[0]);
		$this->assertNotEmpty($array[0]["title"]);
		
		$this->assertArrayHasKey("description", $array[0]);
		$this->assertNotEmpty($array[0]["description"]);
		
		$this->assertArrayHasKey("link", $array[0]);
		$this->assertNotEmpty($array[0]["link"]);
		
		$this->assertArrayHasKey("guid", $array[0]);
		$this->assertNotEmpty($array[0]["guid"]);
		
		$this->assertArrayHasKey("pubDate", $array[0]);
		$this->assertNotEmpty($array[0]["pubDate"]);
	}
	
	/*
	 * I would test each attribute and method like so
	 * OK (3 tests, 12 assertions)
	 */
}