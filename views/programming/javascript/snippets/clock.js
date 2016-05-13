/*	Returns a string of the current system time in the format H:MM:SS AM/PM.
 *	
 *	For example: 
 *		console.log(clock());
 *	Returns: 
 *		"4:20:00 PM"
 */
function clock(){
	clock.time = new Date();
	var hours = clock.time.getHours();
	var minutes = clock.time.getMinutes();
	var seconds = clock.time.getSeconds();
	var meridiem = 'AM';
	
	// Leading zeroes.
	if(minutes < 10) minutes = '0' + minutes;
	if(seconds < 10) seconds = '0' + seconds;
	
	// Calculate post meridiem.
	if(hours > 12){
		meridiem = 'PM';
		hours -= 12;
	}
	
	// Zero hour is midnight.
	if(hours == 0) hours = 12;
	
	return hours + ':' + minutes + ':' + seconds + ' ' + meridiem;
}