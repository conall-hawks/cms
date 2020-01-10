<?php
/**----------------------------------------------------------------------------\
| Converts to title casing. There's a lot of prepositions so only the most     |
| common are listed here. Add more propositions and special words as needed.   |
+------------------------------------------------------------------------------+
| Usage:                                                                       |
|     $title = "tHe quiCK broWn FoX writes some PhP and goes agaINst apple'S   |
|               wishes as he JAILbreaks His IphoNE'S ios";                     |
|     echo title($title);                                                      |
|                                                                              |
| Result:                                                                      |
|     The Quick Brown Fox Writes some PHP and Goes against Apple's Wishes as   |
|     He Jailbreaks His iPhone's iOS                                           |
+---------+---------+------------+---------------------------------------------+
| @param  | string  | $input     | A string to be turned into a title.         |
| @param  | array   | $input     | Array of strings to be turned into titles.  |
| @param  | boolean | $normalize | Normalize string to lowercase.              |
|         |         |            |                                             |
| @return | string  |            | A string in "Title Case".                   |
| @return | array   |            | An array of strings in "Title Case".        |
\---------+---------+------------+--------------------------------------------*/
function title($input, $normalize = true){

    // If the parameter is a string then convert it to a titular casing.
    if(is_string($input)){

        // Lowercase words in titles.
        $articles     = ['a', 'an', 'the', 'some'];
        $conjunctions = ['and', 'but', 'for', 'nor', 'or', 'so', 'yet'];
        $prepositions = ['of', 'in', 'to', 'for', 'with', 'on', 'at', 'from',
            'by', 'about', 'as', 'into', 'like', 'through', 'after', 'over',
            'between', 'out', 'against', 'during', 'without', 'before', 'under',
            'around', 'among'];
        $lcwords = array_merge($articles, $conjunctions, $prepositions);

        // Specially-cased words to look for. Add more as needed.
        $scwords = ['AJAX', 'AMI', 'AngularJS', 'CDN', 'CMD', 'CSS', 'DHCP',
            'DS', 'EPMAP', 'FASM', 'GIF', 'GIFs', 'HTML', 'HTTP', 'httpd', 'ID',
            'IGMP', 'iOS', 'IP', 'iPhone', 'iptables', 'IPv6', 'JavaScript',
            'JS', 'JSON', 'LLMNR', 'MAC', 'MD5', 'md5sum', 'MIME', 'MSI',
            'MySQL', 'NAT', 'NetBIOS', 'NTP', 'OneDrive', 'OSX', 'PC', 'PDF',
            'PHP', 'phpMyAdmin', 'PostgreSQL', 'PowerShell', 'RegEx', 'RPC',
            'sha256sum', 'sha512sum', 'SMB', 'SQL', 'SVG', 'TV', 'UAC', 'UPnP',
            'URL', 'WiFi', 'WinSxS', 'WUDO', 'x64', 'x86'];
        $scwords = array_combine(array_map('strtolower', $scwords), $scwords);

        // Special words to look for. Add more as needed.
        $specials = [
            'iblocklist'  => 'I-Blocklist',
            'letsencrypt' => 'Let\'s Encrypt',
            'redhat'      => 'Red Hat'
        ];

        // Normalize to lowercase.
        if($normalize) $input = mb_strtolower($input);

        // Break apart into an array; space, underscore, and dash delimited.
        $input = str_replace([' ', '_'], '-', $input);
        $input = explode('-', $input);

        // Title-case iterator.
        foreach($input as $i => $word){
            if(!in_array($word, $lcwords) || $i === 0){
                $input[$i] = ucwords($word);
            }

            if(isset($scwords[$word])){
                $input[$i] = $scwords[$word];
            }

            if(isset($specials[$word])){
                $input[$i] = $specials[$word];
            }
        }

        // Convert back into a string.
        $input = implode(' ', $input);

        // Special expressions to look for. Add more as needed.
        $specials = [
            '/\bc\s+sharp\b/i'      => 'C#',
            '/\b,?\s+corp\b\.?/i'   => ', Corp.',
            '/\b,?\s+inc\b\.?/i'    => ', Inc.',
            '/\b,?\s+l\.?p\b\.?/i'  => ', LP',
            '/\b,?\s+ltd\b\.?/i'    => ', Ltd.',
            '/\b,?\s+plc\b\.?/i'    => ', PLC'
        ];
        foreach($specials as $search => $replace){
            $input = preg_replace($search, $replace, $input);
        }
    }

    // If the parameter is an array then process each individual item.
    elseif(is_array($input)){
        foreach($input as $key => $value) $input[$key] = title($value);
    }

    // Done!
    return $input;
}
