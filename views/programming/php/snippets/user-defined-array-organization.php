/* 
 * Orders an array based on user-defined strings.
 * 
 * An example of usage:
 *     $array = array('about', 'forum', 'images', 'index', 'news', 'privacy', 'videos');
 *     $order = array('index', 'news', 'images', 'videos', 'forum');
 *     $result = sortify($array, $order);
 *     print_r($result);
 * Result: 
 *     Array([0]=>index [1]=>news [2]=>images [3]=>videos [4]=>forum [5]=>privacy [6]=>about)
 * 
 * An example of sorting a glob:
 *     $nav_list = sortify(glob('views/navigation/*.php'), array('index', 'news', 'images', 'videos', 'forum'));
 * 
 * @param	$array	array: The array that will be reordered. 
 * @param	$order	array: The items to reorder, listed first-to-last.
 * @return 			array: The $array reordered according to $order.
 * 
 */
function sortify($array, $order){
	foreach($order as $oldkey => $needle){
		foreach($array as $newkey => $haystack){
			// In case we're sorting globs.
			$haystack = pathinfo($haystack, PATHINFO_FILENAME);
			if($haystack == $needle){
				$temp = $array[$oldkey];
				$array[$oldkey] = $array[$newkey];
				$array[$newkey] = $temp;
			}
		}
	}
	return $array;
}