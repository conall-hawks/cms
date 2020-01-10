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
REM # UNIX-style sha256sum.                                                    #
REM ############################################################################

REM Ignore directories.
IF EXIST %1\* (
    ECHO sha256sum: %1: Is a directory
    EXIT /B
)

REM Iterator for wildcards.
SETLOCAL ENABLEDELAYEDEXPANSION
FOR %%F IN ("%1") DO (

    REM Strip double-quotes.
    SET FILE=%%F
    SET FILE=!FILE:"=!

    REM Process hash.
    SET COUNT=1
    FOR /F "tokens=* USEBACKQ" %%L IN (`POWERSHELL -NOPROFILE -EXECUTIONPOLICY BYPASS -COMMAND "$(CertUtil -hashfile \""!FILE!\"" SHA256)[1] -replace \"" \"",\""\"""`) DO (
        SET LINE!COUNT!=%%L
        SET /A COUNT=!COUNT!+1
    )
    SET OUTPUT=!LINE1!!LINE2!!LINE3!!LINE4!!LINE5!!LINE6!!LINE7!!LINE8!!LINE9!!LINE10!!LINE11!!LINE12!!LINE13!!LINE14!!LINE15!!LINE16!  !FILE!

    REM File exists; show hash.
    IF EXIST "!FILE!" (
        ECHO !OUTPUT!

    REM File does not exist; show error.
    ) ELSE (
        ECHO sha256sum: !FILE!: No such file or directory
    )
)
ENDLOCAL
