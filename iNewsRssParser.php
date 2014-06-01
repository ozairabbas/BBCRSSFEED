<?php
/**
 * This is the interface for any news rss parser. 
 * Classes must implement these sorting functions. 
 * 
 *
 * @author     Syed Ozair Abbas
 * @version    Version 1.0
 * 
 * 
 */
interface iNewsRssParser {
	function sort_alphabatically ();
	function sort_alphabatically_reverse ();
	function sort_chronologically ();	
	function sort_chronologically_reverse ();	
	function print_rssFeed ();
}