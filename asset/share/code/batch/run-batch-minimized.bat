@ECHO OFF
REM ############################################################################
REM # Minimize to the taskbar.                                                 #
REM ############################################################################
IF "%MINIMIZED%"=="1" GOTO Minimized
SET MINIMIZED=1
START /MIN "" "%COMSPEC%" /C "%~dpnx0" %*
GOTO :EOF
:Minimized

REM ############################################################################
REM # Your batch file here.                                                    #
REM ############################################################################
ECHO I'm minimized, hooray!
PAUSE

REM ############################################################################
REM # Shorthand way of doing this.                                             #
REM ############################################################################
IF NOT DEFINED MINI SET MINI=1 && START "" /MIN "%~dpnx0" %* && EXIT
