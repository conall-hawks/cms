@ECHO OFF
REM ############################################################################
REM # Print external IP address.                                               #
REM ############################################################################
NSLOOKUP "myip.opendns.com." "resolver3.opendns.com"
PAUSE
