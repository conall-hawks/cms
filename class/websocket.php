<?php
/**----------------------------------------------------------------------------\
|  WebSocketry.                                                                |
|                                                                              |
|  Requires the Socket PHP extension.                                          |
\-----------------------------------------------------------------------------*/

echo "/*-------------------------------------------------------------------------\\\n";
echo "| Starting WebSocket.                                                      |\n";
echo "\\-------------------------------------------------------------------------*/\n";
feedback('Press <q> then <Enter> to quit.');

// Debugging.
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// Socket configuration.
$host   = '127.0.0.1';
$port   = 8081;
$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

// Socket creation.
socket_set_option($socket, SOL_SOCKET, SO_REUSEADDR, 1);
socket_bind($socket, $host, $port);
socket_listen($socket);

/*-------------------------------------------------------------------------\
|  Main program loop.                                                      |
\-------------------------------------------------------------------------*/
$clients = [$socket]; # Socket connections.
$null    = NULL;      # Needed for socket_select.
$users   = [];        # List of active users.
while(true){
    $changed = $clients;

    print_r($users);
    #foreach($clients as $key => $value) echo $key.': '.$value;

    /*---------------------------------------------------------------------\
    | Preemptively non-block every second.                                 |
    \---------------------------------------------------------------------*/
    socket_select($changed, $null, $null, 1);

    /*---------------------------------------------------------------------\
    | Server socket has activity; accept and process new connections.      |
    \---------------------------------------------------------------------*/
    if(in_array($socket, $changed)){
        $socket_new = socket_accept($socket);
        $header     = header_parse(socket_read($socket_new, 1024));
        print_r($header['Cookie']['PHPSESSID']);
        $clients[]  = $socket_new;
        socket_getpeername($socket_new, $ip);
        $users[$header['Cookie']['PHPSESSID']] = $header['Cookie'];
        perform_handshaking($header, $socket_new, $host, $port);
        $found_socket = array_search($socket, $changed);
        unset($changed[$found_socket]);
        feedback('User '.$ip.' has connected.');
    }

    /*---------------------------------------------------------------------\
    | Process sockets with activity.                                       |
    \---------------------------------------------------------------------*/
    foreach($changed as $changed_socket){

        // Check for incoming data.
        while(socket_recv($changed_socket, $buf, 1024, 0) >= 1){
            $received_text = unmask($buf);
            $json = json_decode($received_text);
            if(!is_object($json)) break 2;
            switch($json->type){
                case 'chat':
                    $username    = $json->name;
                    $user_message = $json->message;
                    $response_text = mask(json_encode([
                        'type'    => 'chat',
                        'name'    => $username,
                        'message' => $user_message
                    ]));
                    feedback('User '.$ip.' says: '.$user_message);
                    send_message($response_text);
                    break;
                case 'http':
                    switch(strtoupper($json->method)){
                        case 'GET':
                            feedback('User '.$ip.' sent a '.$json->method.' request.');
                            break;
                        case 'POST':
                            feedback('User '.$ip.' sent a '.$json->method.' request.');
                            break;
                        default:
                            feedback('User '.$ip.' sent an unknown HTTP method: '.$json->method.'.');
                            break;
                    }
                    $method = $json->method;
                default:
                    print_r($json);
                    break;
            }
            break 2;
        }

        // Check for disconnections.
        $buf = @socket_read($changed_socket, 1024, PHP_NORMAL_READ);
        if($buf === false){
            $found_socket = array_search($changed_socket, $clients);
            socket_getpeername($changed_socket, $ip);
            unset($clients[$found_socket]);
            feedback('User '.$ip.' has disconnected.');
            #$response = mask(json_encode(array('type'=>'system', 'message'=>$ip.' disconnected')));
            #send_message($response);
        }
    }
}
socket_close($socket);

//
function send_message($msg){
    global $clients;
    foreach($clients as $changed_socket){
        @socket_write($changed_socket, $msg, strlen($msg));
    }
    return true;
}


// Unmask incoming message frame.
function unmask($text){
    $length = ord($text[1]) & 127;
    if($length === 126){
        $masks = substr($text, 4, 4);
        $data  = substr($text, 8);
    }elseif($length === 127){
        $masks = substr($text, 10, 4);
        $data  = substr($text, 14);
    }else{
        $masks = substr($text, 2, 4);
        $data  = substr($text, 6);
    }
    $text = '';
    for($i = 0; $i < strlen($data); ++$i){
        $text .= $data[$i] ^ $masks[$i % 4];
    }
    return $text;
}

// Encode message for transfer to client.
function mask($text){
    $b1 = 0x80 | (0x1 & 0x0f);
    $length = strlen($text);

    if($length <= 125) $header = pack('CC', $b1, $length);
    elseif($length > 125 && $length < 65536) $header = pack('CCn', $b1, 126, $length);
    elseif($length >= 65536) $header = pack('CCNN', $b1, 127, $length);
    return $header.$text;
}

// New client handshake.
function perform_handshaking($header, $socket, $host, $port){

    // Generate key.
    $sec_websocket_accept = base64_encode(pack('H*', sha1($header['Sec-WebSocket-Key'].'258EAFA5-E914-47DA-95CA-C5AB0DC85B11')));

    // Handshake header.
    $upgrade  =
        "HTTP/1.1 101 Web Socket Protocol Handshake\r\n".
        "Upgrade: websocket\r\n".
        "Connection: Upgrade\r\n".
        "WebSocket-Origin: $host\r\n".
        "WebSocket-Location: wss://$host:$port/\r\n".
        "Sec-WebSocket-Accept: $sec_websocket_accept\r\n\r\n";

    // Send handshake.
    socket_write($socket, $upgrade, strlen($upgrade));
}

/*-------------------------------------------------------------------------\
|  Parses header data.                                                     |
\-------------------------------------------------------------------------*/
function header_parse($header_raw){

    // Parse the header into an array.
    $header = [];
    $lines = explode(PHP_EOL, $header_raw);
    foreach($lines as $line){
        if(preg_match('/\A(\S+): (.*)\z/', chop($line), $matches)){
            $header[$matches[1]] = $matches[2];
        }
    }

    // Parse the cookie into an array.
    preg_match_all('/(.*?)=(.*?)($|;|,(?! ))/', $header['Cookie'], $matches);
    $header['Cookie'] = [];
    $count = count($matches[1]);
    for($i = 0; $i < $count; $i++){
        $header['Cookie'][$matches[1][$i]] = $matches[2][$i];
    }

    return $header;
}

/*-------------------------------------------------------------------------\
|  Prints a message to the terminal.                                       |
\-------------------------------------------------------------------------*/
function feedback($message, $origin = 'System'){
    echo '['.date('d/m/Y H:i:s').substr((string)microtime(), 1, 4).']['.$origin.'] ';
    print_r($message);
    echo "\r\n";
}
