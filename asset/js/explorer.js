/*---------------------------------------------------------------------------------------------------------------------\
| Explorer JavaScript                                                                                                  |
\---------------------------------------------------------------------------------------------------------------------*/

/*-----------------------------------------------------------------------------\
| Auto-convert dates to local time.                                            |
\-----------------------------------------------------------------------------*/
if(window.startFormatDate !== "number") window.startFormatDate = setInterval(function(){
    if(typeof window.formatDate === "function"){
        clearInterval(window.startFormatDate);
        var dates = document.getElementsByClassName("date-modifed");
        for(var i = 0; i < dates.length; i++){
            var date = document.createElement("span");
            date.innerHTML = dates[i].innerHTML;
            var suffixes = date.getElementsByTagName("sup");
            for(var j = 0; j < suffixes.length; j++) date.removeChild(suffixes[j]);
            dates[i].innerHTML = formatDate(date.innerText);
        }
    }
}, 100);
