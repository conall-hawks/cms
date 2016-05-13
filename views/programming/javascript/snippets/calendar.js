/*	Returns a string of the current system date in a verbose format.
 *	
 *	For example: 
 *		console.log(calendar());
 *	Returns: 
 *		"January 1st, 2000"
 */
document.onload = console.log(calendar());
function calendar(){
	calendar.date = new Date();
	var year = calendar.date.getFullYear();
	var month = calendar.date.getMonth();
	var day = calendar.date.getDate();
	
	// Verbose month.
	switch(month){
		case 0: month = 'January'; break;
		case 1: month = 'February'; break;
		case 2: month = 'March'; break;
		case 3: month = 'April'; break;
		case 4: month = 'May'; break;
		case 5: month = 'June'; break;
		case 6: month = 'July'; break;
		case 7: month = 'August'; break;
		case 8: month = 'September'; break;
		case 9: month = 'October'; break;
		case 10: month = 'November'; break;
		case 11: month = 'December'; break;
	}
	
	// Date suffix.
	switch(day){
		case 1: day += 'st'; break;
		case 2: day += 'nd'; break;
		case 3: day += 'rd'; break;
		case 21: day += 'st'; break;
		case 22: day += 'nd'; break;
		case 23: day += 'rd'; break;
		case 31: day += 'st'; break;
		default: day += 'th'; break;
	}
	
	return month + ' ' + day + ', ' + year;
}