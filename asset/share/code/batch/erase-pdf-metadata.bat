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
REM # Erase PDF metadata.                                                      #
REM ############################################################################

SET X=%*

VERIFY OTHER 2>NUL
SETLOCAL ENABLEEXTENSIONS
IF ERRORLEVEL 1 ECHO Unable to enable extensions
IF NOT DEFINED X SET /P X="Enter path(s): "
ENDLOCAL && SET X=%X%

echo %X%

GOTO :EOF


SETLOCAL ENABLEEXTENSIONS
SET ATTR=%~A1
SET DIRATTR=%ATTR:~0,1%
IF /I "%DIRATTR%" == "D" ECHO %1 IS A FOLDER
:EOF



SETLOCAL
CD /D "%X%"
FOR /F "DELIMS=" %%X IN ('DIR /A-D /B /S *.pdf ^2^>NUL ^| FINDSTR .[PpDdFf].*.*.*$') DO (
    TAKEOWN /F "%%~X" /R /D Y
    ICACLS "%%~X" /GRANT Administrators:F /T
    D:\warez\qpdf\exiftool.exe -all:all= -overwrite_original_in_place "%%~X"
    D:\warez\qpdf\qpdf.exe --linearize --compress-streams=y "%%~X" "%%~X.tmp"
    DEL /F /Q "%%~X"
    RENAME "%%~X.tmp" "%%~X"
)
ENDLOCAL
PAUSE
