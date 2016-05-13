/*
 * Reads a string and turns URLs into <a></a> HTML tags that you can click on.
 * 
 * @param	$string	string:	A string possibly containing links.
 * @return			string:	The string with HTML tags wrapped around links.
 */
function linkify($string){
	$string = preg_replace('!(((f|ht)tp(s)?://)[-a-zA-Z?-??-?()0-9@:%\!_+.,~#?&;//=]+)!i', '<a href="$1" target="_blank">$1</a>', $string);
	$string = preg_replace('/\(\<a href\=\"(.*)\)"\ target\=\"\_blank\">(.*)\)\<\/a>/i', '(<a href="$1" target="_blank">$2</a>)', $string);
	$string = preg_replace('/\<a href\=\"(.*)\."\ target\=\"\_blank\">(.*)\.\<\/a>/i', '<a href="$1" target="_blank">$2</a>.', $string);
	$string = preg_replace('/\<a href\=\"(.*)\,"\ target\=\"\_blank\">(.*)\,\<\/a>/i', '<a href="$1" target="_blank">$2</a>,', $string);
	return $string;
}