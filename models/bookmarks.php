<?php 
	defined('BASEPATH') or die('Direct script access denied.');
	
	class Bookmarks_model extends Model{
		public function getCategories(){
			$test[0] = array('class' => 'test', 'link' => 'somelink.html', 'title' => 'title1', 'icon' => '/images/wow/placeholder.png');
			$test[0] = array('class' => 'test', 'link' => 'somelink.html', 'title' => 'title1', 'icon' => '/images/wow/placeholder.png');
			$test[0] = array('class' => 'test', 'link' => 'somelink.html', 'title' => 'title1', 'icon' => '/images/wow/placeholder.png');
			return $test;
		}
	}