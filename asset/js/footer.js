/*---------------------------------------------------------------------------------------------------------------------\
| Footer JavaScript                                                                                                    |
\---------------------------------------------------------------------------------------------------------------------*/

/*-----------------------------------------------------------------------------\
| Logger Auto-scroll-to-bottom                                                 |
\-----------------------------------------------------------------------------*/
document.addEventListener("click", function(event){
    if(event.button !== 0) return;
    for(var target = event.target; target && target !== this; target = target.parentNode){
        if(typeof target.matches === "function" && target.matches("#log")){
            setTimeout(function(){
                target.scrollTop = target.scrollHeight;
            }, 1);
            return;
        }
    }
});

/*-----------------------------------------------------------------------------\
| WebSocket.                                                                   |
\-----------------------------------------------------------------------------*/
window.websocket = function(){

    /* Get relevant DOM elements. */
    websocket.label = document.querySelector("label[for=\"chat\"]");
    websocket.chat = document.getElementById("chat-messages");

    /* Feedback. */
    websocket.label.className   = "orange";
    websocket.label.textContent = "Connecting";

    /* Create the WebSocket object. */
    try{
        websocket.handle = new WebSocket("wss://" + window.websocketAddress + ":8080");
    }

    /* Feedback. */
    catch(error){
        websocket.label.className   = "red";
        websocket.label.title       = "Error: " + error;
        websocket.label.textContent = "Offline";
    }

    /*-------------------------------------------------------------------------\
    | The connection has been established.                                     |
    \-------------------------------------------------------------------------*/
    websocket.handle.onopen = function(event){

        /* Feedback. */
        websocket.label.className = "green";
        websocket.label.textContent = "Online";

        /* Feedback in chat. */
        var message         = document.createElement("span");
        message.class       = "system";
        message.textContent = "Connected!";
        websocket.chat.appendChild(message);
        websocket.chat.scrollTop = websocket.chat.scrollHeight;
    };

    /*-------------------------------------------------------------------------\
    | Data has been recieved.                                                  |
    \-------------------------------------------------------------------------*/
    websocket.handle.onmessage = function(event){
        var data = JSON.parse(event.data);
        switch(data.type){

            /* Chat message from user. */
            case "chat":
                message = document.createElement("div");
                message.innerHTML  = "<span class=\"user-name\">"    + data.name    + "</span>: ";
                message.innerHTML += "<span class=\"user-message\">" + data.message + "</span>";
                websocket.chat.appendChild(message);
                websocket.chat.scrollTop = websocket.chat.scrollHeight;
                break;

            case "link":
                // Not yet implemented.
                break;

            case "email":
                // Not yet implemented.
                break;

            case "upload":
                // Not yet implemented.
                break;

            default:
                // Not yet implemented.
        }
    };

    /*-------------------------------------------------------------------------\
    | An error has occurred.                                                   |
    \-------------------------------------------------------------------------*/
    websocket.handle.onerror = function(event){
        console.warn(event);
    };

    /*-------------------------------------------------------------------------\
    | The connection has closed.                                               |
    \-------------------------------------------------------------------------*/
    websocket.handle.onclose = function(event){

        /* Feedback in chat. */
        message = document.createElement("div");
        message.innerHTML  = "<span class=\"user-name\">System</span>: ";
        message.innerHTML += "<span class=\"user-message\">Connection closed.</span>";
        websocket.chat.appendChild(message);
        websocket.chat.scrollTop = websocket.chat.scrollHeight;

        /* Reconnect. */
        setTimeout(websocket, 3000);
    };
}

/* Execute on page load and transition. */
window.listen("load", websocket);
