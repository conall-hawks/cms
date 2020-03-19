@ECHO OFF
REM ############################################################################
REM # Print external IP address.                                               #
REM ############################################################################
ECHO Querying public IP address using OpenDNS' service...
NSLOOKUP "myip.opendns.com." "resolver3.opendns.com"
ECHO Your IP address is the one listed under "Non-authoritative answer:"
PAUSE
