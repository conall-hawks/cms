/*-----------------------------------------------------------------------------\
| Cross-browser addEventListener(); can handle single or multiple arguments    |
| event.                                                                       |
+------------------------------------------------------------------------------+
| Usage:                                                                       |
|     window.listen("click", myFunction);                                      |
|     window.listen(["load", "popstate"], myFunction);                         |
+---------+----------+---------+-----------------------------------------------|
| @param  | mixed    | event   | Event to listen for.                          |
| @param  | function | func    | Function to attach.                           |
| @param  | object   | element | Element this applies to.                      |
| @return | void     |         |                                               |
\--------------------+---------+----------------------------------------------*/
window.listen = function(event, func, element){

    /* Handle polymorphic argument for event. */
    switch(typeof event){

        /* Process multiple event bindings. */
        case "object":
            for(var i = 0; i < event.length; i++){
                window.listen(event[i], func, element);
            }
            return true;

        /* Process event binding. */
        case "string":

            /* Default argument for element. */
            if(typeof element !== "object"){
                element = window;
            }

            /* Verify function argument. */
            if(typeof func !== "function"){
                throw new Error("Expected function in argument 2, instead got: " + typeof func);
            }

             /* W3C DOM specifications. */
            if(typeof element.addEventListener === "function"){
                return element.addEventListener(event, func);
            }

            /* IE DOM specifications. */
            else if(typeof element.attachEvent === "function"){
                return element.attachEvent("on" + event, func);
            }

            /* Strange browser. */
            throw new Error("Unable to bind argument; neither addEventListener() nor attachEvent() are supported.");

        /* Error. */
        default:
            throw new Error("Expected string or object in argument 1, instead got: " + typeof event + ".");
    }
};
