/**----------------------------------------------------------------------------\
| Basically, it turns your <title> into a <marquee>.                           |
\-----------------------------------------------------------------------------*/
(function(){
    var title = window.document.title + " | ";
    setInterval(function(){
        window.document.title = title = title.substring(1) + title.substring(0, 1);
    }, 250);
})();
