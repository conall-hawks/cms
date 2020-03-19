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
REM # A shorthand way to minimize to the taskbar.                              #
REM ############################################################################
IF NOT DEFINED MINI SET MINI=1 && START /MIN "" "%COMSPEC%" /C "%~dpnx0" %* && EXIT
