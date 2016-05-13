/* 
 * Sorts a glob alphabetically with directories first.
 */
function glob_sort($files){
	usort($files, function($a, $b){
		$a_is_dir = is_dir($a);
		$b_is_dir = is_dir($b);
		if($a_is_dir === $b_is_dir) return strnatcasecmp($a, $b);
		elseif($a_is_dir && !$b_is_dir) return -1;
		elseif(!$a_is_dir && $b_is_dir) return 1;
	});
	return $files;
}