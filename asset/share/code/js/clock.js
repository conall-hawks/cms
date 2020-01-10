/*-----------------------------------------------------------------------------\
| Returns a string of the current system time in the format H:MM:SS AM/PM.     |
| Call with no parameters to get the current system time                       |
+------------------------------------------------------------------------------+
| Usage:                                                                       |
|     var time = new Date(Date.UTC(1970, 1, 1, 15, 20));                       |
|     console.log(clock(time));                                                |
|                                                                              |
| Result:                                                                      |
|     "4:20:00 PM"                                                             |
+---------+-----------+----------+---------------------------------------------|
| @param  | object    | date     | A JavaScript Date() object.                 |
| @return | string    |          | A time in HH:MM:SS format.                  |
\-----------------------------------------------------------------------------*/
window.clock = function(date){

    /* Default argument is the current date. */
    if(typeof date !== "object" || !date) date = new Date();

    /* Get granular time information. */
    var hours   = date.getHours();
    var minutes = date.getMinutes();
    var seconds = date.getSeconds();

    /* Pad zeros. */
    minutes = ("0"  + minutes).slice(-2);
    seconds = ("0"  + seconds).slice(-2);

    /* Calculate ante/post meridiem. */
    var meridiem = "AM";
    if(hours > 12){
        meridiem = "PM";
        hours -= 12;
    }

    /* Zero hour is midnight. */
    if(hours === 0) hours = 12;

    /* Return formatted time. */
    return hours + ":" + minutes + ":" + seconds + " " + meridiem;
};

/* Example usage; execute right now and then once every second. */
try{document.getElementById("clock").innerHTML = window.clock();}catch(error){}
setInterval(function(){try{document.getElementById("clock").innerHTML = window.clock();}catch(error){}}, 1000);
