/**----------------------------------------------------------------------------\
| Format a string representing a local date.                                   |
+---------+--------+-------+---------------------------------------------------+
| @param  | object | date | Date to format.                                    |
|         |        |      |                                                    |
| @return | string |      | Date in pretty format (e.g. January 1st, 1970).    |
\---------+--------+------+---------------------------------------------------*/
function formatDate(date){

    // Coalesce string argument to date object.
    if(typeof date === "string") date = new Date(date);

    // Default argument to the current date.
    if(typeof date !== "object" || !date) date = new Date();

    // Convert to local time.
    date = new Date(date.toString());

    // Get month.
    var month = [
        "January", "February", "March", "April", "May", "June", "July",
        "August", "September", "October", "November", "December"
    ][date.getMonth()];

    // Get day (with suffix).
    var day       = date.getDate();
    var lastDigit = day % 10;
    var suffix    = ~~(day % 100 / 10) === 1 ? "th" :
                             lastDigit === 1 ? "st" :
                             lastDigit === 2 ? "nd" :
                             lastDigit === 3 ? "rd" : "th";
    day = ("0" + day).slice(-2) + "<sup>" + suffix + "</sup>";

    // Get year.
    var year = date.getFullYear();

    // Get time.
    var hours   = ("0" + date.getHours()).slice(-2);
    var minutes = ("0" + date.getMinutes()).slice(-2);
    var time    = hours + ":" + minutes;

    // Get timezone abbreviation.
    var timezone = date.toLocaleString("en", {timeZoneName: "short"}).split(' ').pop();

    // Return date in pretty format (e.g. January 1st, 1970).
    return month + " " + day + ", " + year + " " + time + " " + timezone;
}
