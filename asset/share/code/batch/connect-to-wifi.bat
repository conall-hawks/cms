@ECHO OFF
REM ############################################################################
REM # Get administrative privileges.                                           #
REM ############################################################################
CACLS "%SYSTEMROOT%\System32\config\SYSTEM" >NUL 2>&1
IF "%ERRORLEVEL%" NEQ "0" (GOTO Elevate) ELSE (GOTO Run)

:Elevate
    SET UAC_SCRIPT="%TEMP%\%RANDOM%%RANDOM%%RANDOM%%RANDOM%%RANDOM%%RANDOM%.VBS"
    ECHO Set UAC = CreateObject("Shell.Application")              > %UAC_SCRIPT%
    ECHO UAC.ShellExecute "%~dpnx0", "%*", "%~dp0", "runas", 1   >> %UAC_SCRIPT%
    CSCRIPT %UAC_SCRIPT%
    DEL /F /Q %UAC_SCRIPT%
    EXIT /B

:Run

REM ############################################################################
REM # Connect to preferred WiFi.                                               #
REM ############################################################################
SET CTW_SCRIPT="%TEMP%\%RANDOM%%RANDOM%%RANDOM%%RANDOM%%RANDOM%%RANDOM%.XML"
ECHO ^<?xml version="1.0"?^>                                      > %CTW_SCRIPT%
ECHO ^<WLANProfile xmlns="http://www.microsoft.com/networking/WLAN/profile/v1"^> >> %CTW_SCRIPT%
ECHO     ^<name^>HOME-F32B^</name^>                              >> %CTW_SCRIPT%
ECHO     ^<SSIDConfig^>                                          >> %CTW_SCRIPT%
ECHO         ^<SSID^>                                            >> %CTW_SCRIPT%
ECHO             ^<name^>HOME-F32B^</name^>                      >> %CTW_SCRIPT%
ECHO         ^</SSID^>                                           >> %CTW_SCRIPT%
ECHO     ^</SSIDConfig^>                                         >> %CTW_SCRIPT%
ECHO     ^<connectionType^>ESS^</connectionType^>                >> %CTW_SCRIPT%
ECHO     ^<connectionMode^>auto^</connectionMode^>               >> %CTW_SCRIPT%
ECHO     ^<MSM^>                                                 >> %CTW_SCRIPT%
ECHO         ^<security^>                                        >> %CTW_SCRIPT%
ECHO             ^<authEncryption^>                              >> %CTW_SCRIPT%
ECHO                 ^<authentication^>WPA2PSK^</authentication^>>> %CTW_SCRIPT%
ECHO                 ^<encryption^>AES^</encryption^>            >> %CTW_SCRIPT%
ECHO                 ^<useOneX^>false^</useOneX^>                >> %CTW_SCRIPT%
ECHO             ^</authEncryption^>                             >> %CTW_SCRIPT%
ECHO             ^<sharedKey^>                                   >> %CTW_SCRIPT%
ECHO                 ^<keyType^>passPhrase^</keyType^>           >> %CTW_SCRIPT%
ECHO                 ^<protected^>true^</protected^>             >> %CTW_SCRIPT%
ECHO                 ^<keyMaterial^>01000000D08C9DDF0115D1118C7A00C04FC297EB0100000097FA3D4732BD184D97218FC74BFF800E0000000002000000000010660000000100002000000030565CCFF74331CE0507B4E1DAA0CCE9F8EA0C573EFC9226B6849EFAECC12E30000000000E8000000002000020000000A6672CCAE637806A2C5B06AEE391B4522E35B7E0106F73C1F73C97E739782CFB10000000D98849DBCB930F1F6B2EEFC7E73FF89B400000000A6C5194EF3F2F7F42FB20521BE959FB569A17525A6B13B2D5226109B0394A52F6621D175DA6AA8B40FAE7E8C1DB91E604C240C807033F6622F998D9EAB71A50^</keyMaterial^> >> %CTW_SCRIPT%
ECHO             ^</sharedKey^>                                  >> %CTW_SCRIPT%
ECHO         ^</security^>                                       >> %CTW_SCRIPT%
ECHO     ^</MSM^>                                                >> %CTW_SCRIPT%
ECHO ^</WLANProfile^>                                            >> %CTW_SCRIPT%
ECHO %CTW_SCRIPT%
NETSH WLAN ADD PROFILE FILENAME="%CTW_SCRIPT%" INTERFACE="Wireless Network Connection" USER="all"
DEL /F /Q %CTW_SCRIPT%
NETSH WLAN CONNECT NAME="HOME-F32B" SSID="HOME-F32B" INTERFACE="Wireless Network Connection"
