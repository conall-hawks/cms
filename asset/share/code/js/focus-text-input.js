/*-----------------------------------------------------------------------------\
| Places the text cursor into the specified input text box, and may optionally |
| select a range of text.                                                      |
+---------+-----------+-----------+--------------------------------------------+
| @param  | object    | input     | A textual <input> element.                 |
| @param  | string    | start     | Text selection range offset.               |
| @param  | integer   | end       | Text selection range end.                  |
| @return | undefined |           |                                            |
\---------+-----------+-----------+-------------------------------------------*/
window.focusInput = function(input, start, end){

    // Annoying on iOS; opens on-screen keyboards.
    if(/iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream) return;

    // Preserve window scroll location.
    var x = window.scrollX;
    var y = window.scrollY;

    // Delay is sometimes required.
    setTimeout(function(){

        // Default variables and error checking.
        if(typeof input !== "object" || !input    || typeof input.value !== "string") return;
        if(typeof start !== "number" || start < 0 || start > input.value.length) start = input.value.length;
        if(typeof end   !== "number" || end   < 0 || end   > input.value.length) end   = input.value.length;

        // Move cursor into input box.
        input.focus();
        input.select();

        // Restore window scroll location.
        window.scrollTo(x, y);
        setTimeout(function(){ window.scrollTo(x, y) }, 1);

        // Set text selection range.
        if(typeof input.selectionStart === "number"){
            input.selectionStart = start;
            input.selectionEnd = end;
        }

        // Set text selection range in IE.
        else if(typeof input.createTextRange === "function"){
            var range = input.createTextRange();
            range.collapse(true);
            range.moveEnd("character", end);
            range.moveStart("character", start);
        }
    }, 1);
};
