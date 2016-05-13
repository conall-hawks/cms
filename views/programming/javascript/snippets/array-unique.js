/*	Returns unique values in an array.
 *	
 *	For example: 
 *		array = [ 1, 1, 2, 1, 3];
 *		console.log(arrayUnique(array));
 *	Returns: 
 *		"Array [ 1, 2, 3 ]"
 */
function arrayUnique(a){
	var b = [];
	for(var i = 0, l = a.length; i < l; i++) if(b.indexOf(a[i]) === -1 && a[i] !== '') b.push(a[i]);
	return b;
}