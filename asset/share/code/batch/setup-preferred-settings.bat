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

REM ####################################################################################################################
REM # Normalize Windows to preferred settings.                                                                         #
REM ####################################################################################################################

REM ############################################################################
REM # Accounts.                                                                #
REM ############################################################################

REM Enable the Administrator account.
NET USER Administrator /ACTIVE:YES

REM Allow blank password use.
REG ADD "HKLM\SYSTEM\CurrentControlSet\Control\Lsa" /V "LimitBlankPasswordUse" /T "REG_DWORD" /F /D "0x00000001"

REM Blank password.
NET USER Administrator ""

REM Disable User Account Control.
REG ADD "HKLM\SOFTWARE\Microsoft\Windows\CurrentVersion\Policies\System" /V "EnableLUA" /T "REG_DWORD" /F /D "0x00000000"

REM ############################################################################
REM # Power.                                                                   #
REM ############################################################################

REM Disable hibernation.
POWERCFG /H OFF

REM ############################################################################
REM # Explorer.                                                                #
REM ############################################################################

REM Show all taskbar icons.
REG ADD "HKCU\Software\Microsoft\Windows\CurrentVersion\Explorer" /V "EnableAutoTray" /T "REG_DWORD" /F /D "0x00000001"

REM Remove "Network" from Explorer left pane.
REG ADD "HKLM\SOFTWARE\Microsoft\Windows\CurrentVersion\policies\NonEnum" /V "{F02C1A0D-BE21-4350-88B0-7367FC96EF3C}" /T "REG_DWORD" /F /D "0x00000001"

REM Remove "Libraries" from Explorer left pane.
REG ADD "HKCR\CLSID\{031E4825-7B94-4dc3-B131-E946B44C8DD5}\ShellFolder" /V "Attributes" /T "REG_DWORD" /F /D "0xb080010d"
