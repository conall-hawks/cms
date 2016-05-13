/* 
 * Converts to "Title Case". There's lots of prepositions so only the most common 25 are listed here. Add more as needed.
 * 
 * An example of usage:
 * 		$title = "tHe quiCK broWn FoX writes some PhP and goes agaINst apple'S wishes as he JAILbreaks His IphoNE'S ios";
 * 		echo title($title);
 * Result:
 * 		The Quick Brown Fox Writes some PHP and Goes against Apple's Wishes as He Jailbreaks His iPhone's iOS
 * 
 * @param	$string	string:	A string to be turned into a title.
 * @return			string:	The string in "Title Case".
 * 
 */
function title($string){
	$title = strtolower($string);
	
	// Lowercase words in titles.
	$articles = array('a', 'an', 'the', 'some');
	$prepositions = array('of', 'in', 'to', 'for', 'with', 'on', 'at', 'from', 'by', 'about', 'as', 'into', 'like', 'through', 'after', 'over', 'between', 'out', 'against', 'during', 'without', 'before', 'under', 'around', 'among');
	$conjunctions = array('and', 'but', 'for', 'nor', 'or', 'so', 'yet');
	$lcwords = array_merge($articles, $prepositions, $conjunctions);
	
	// Title case iterator.
	$words = str_replace(' ', '-', $title);
	$words = explode('-', $words);
	foreach($words as $i => $word) if(!in_array($word, $lcwords)) $words[$i] = ucwords($word);
	$title = implode(' ', $words);
	
	// Special words to look for.
	$special = array('CSS', 'HTML', 'iOS', 'iPhone', 'JavaScript', 'MySQL', 'PHP', 'RegEx', 'x64', 'x86');
	$title = str_ireplace($special, $special, $title);
	
	return $title;
}