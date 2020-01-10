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
REM # Uninstall OneDrive.                                                      #
REM ############################################################################
ECHO Uninstalling OneDrive...
TASKKILL /F /IM "OneDrive.exe"
"%SYSTEMROOT%\System32\OneDriveSetup.exe" /uninstall
"%SYSTEMROOT%\SysWOW64\OneDriveSetup.exe" /uninstall
RD /Q /S "%USERPROFILE%\OneDrive"
RD /Q /S "%LOCALAPPDATA%\Microsoft\OneDrive"
RD /Q /S "%PROGRAMDATA%\Microsoft OneDrive"
RD /Q /S "C:\OneDriveTemp"
REG DELETE "HKCR\CLSID\{018D5C66-4533-4307-9B53-224DE2ED1FE6}" /F
REG DELETE "HKCR\Wow6432Node\CLSID\{018D5C66-4533-4307-9B53-224DE2ED1FE6}" /F
ECHO OneDrive uninstall complete.
PAUSE
