<?php 
	defined('BASEPATH') or die('Direct script access denied.');
	
	class Benchmark{
		/* @var	array:	List of benchmark markers. */
		private $marker = array();
		
		/* 
		 * Set a benchmark mark.
		 * 
		 * @param	$name	string:	A name for the marker.
		 */
		public function mark($name){
			$this->marker[$name] = microtime(TRUE);
		}
		
		// --------------------------------------------------------------------
		
		/* 
		 * Calculates the time difference between two marks.
		 * 
		 * @param	$mark1		string: 	A particular marked point.
		 * @param	$mark2		string:		A particular marked point.
		 * @param	$decimals	integer:	Number of significant figures.
		 * @return				string:		The time that has elapsed.
		 */
		public function calculate($mark1, $mark2 = '', $decimals = 4){
			if(!isset($this->marker[$mark2])) $this->marker[$mark2] = microtime(TRUE);
			return number_format($this->marker[$mark2] - $this->marker[$mark1], $decimals);
		}
	}