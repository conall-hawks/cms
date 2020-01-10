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
REM # Randomize MAC addresses.                                                 #
REM ############################################################################
SETLOCAL ENABLEDELAYEDEXPANSION
SETLOCAL ENABLEEXTENSIONS

REM Generate a random MAC address.
FOR /F "TOKENS=1" %%X IN ('WMIC NIC WHERE PHYSICALADAPTER^=TRUE GET DEVICEID ^| FINDSTR [0-9]') DO (
    CALL :CREATE_MAC
    FOR %%Y IN (0 00 000) DO (
        REG ADD "HKEY_LOCAL_MACHINE\SYSTEM\CurrentControlSet\Control\Class\{4D36E972-E325-11CE-BFC1-08002bE10318}\%%Y%%X" /F /V "NetworkAddress" /T "REG_SZ" /D !MAC!
    )
)

REM Disable power-saving mode for NICs.
FOR /F "TOKENS=1" %%X IN ('WMIC NIC WHERE PHYSICALADAPTER^=TRUE GET DEVICEID ^| FINDSTR [0-9]') DO (
    FOR %%Y IN (0 00 000) DO (
        REG ADD "HKEY_LOCAL_MACHINE\SYSTEM\CurrentControlSet\Control\Class\{4D36E972-E325-11CE-BFC1-08002bE10318}\%%Y%%X" /V "PnPCapabilities" /T "REG_DWORD" /D "24" /F
    )
)

REM Restart NICs.
FOR /F "TOKENS=2 DELIMS=, SKIP=2" %%X IN ('"WMIC NIC WHERE (NETCONNECTIONID LIKE '%%') GET NETCONNECTIONID,NETCONNECTIONSTATUS /FORMAT:CSV"') DO (
    NETSH INTERFACE SET INTERFACE NAME="%%X" DISABLE
    NETSH INTERFACE SET INTERFACE NAME="%%X" ENABLE
)

GOTO :EOF

REM Generate a random MAC address.
:CREATE_MAC
    SET COUNT=0
    SET HEX=ABCDEF0123456789
    SET HEX2=26AE
    SET MAC=

    :GENERATE_MAC
        SET /A COUNT+=1

        SET /A RND=%RANDOM% %% 16
        SET RNDHEX=!HEX:~%RND%,1!

        SET /A RND2=%RANDOM% %% 4
        SET RNDHEX2=!HEX2:~%RND2%,1!

        IF !COUNT! EQU 2 (SET MAC=!MAC!!RNDHEX2!) ELSE (SET MAC=!MAC!!RNDHEX!)
        IF !COUNT! LEQ 11 GOTO GENERATE_MAC
