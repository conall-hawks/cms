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
REM # Reset all filesystem timestamps to 01/01/1980 00:00.                     #
REM ############################################################################
SET RTS_SCRIPT="%TEMP%\%RANDOM%%RANDOM%%RANDOM%%RANDOM%%RANDOM%%RANDOM%.PS1"
ECHO ################################################################################ >> %RTS_SCRIPT%
ECHO # Resets all the timestamps in a file or folder.                               # >> %RTS_SCRIPT%
ECHO ################################################################################ >> %RTS_SCRIPT%
ECHO #                                                                                >> %RTS_SCRIPT%
ECHO # Get all files/folders recursively.                                             >> %RTS_SCRIPT%
ECHO function GetFiles($path = "."){                                                  >> %RTS_SCRIPT%
ECHO     foreach($node in Get-ChildItem $path -force){                                >> %RTS_SCRIPT%
ECHO         ResetTimestamps $node.FullName                                           >> %RTS_SCRIPT%
ECHO         if(Test-Path $node.FullName -PathType Container){                        >> %RTS_SCRIPT%
ECHO             GetFiles $node.FullName                                              >> %RTS_SCRIPT%
ECHO         }                                                                        >> %RTS_SCRIPT%
ECHO     }                                                                            >> %RTS_SCRIPT%
ECHO }                                                                                >> %RTS_SCRIPT%
ECHO #                                                                                >> %RTS_SCRIPT%
ECHO # Reset the timestamp of a file/folder. Windows epoch is January 1st, 1980 00:00 >> %RTS_SCRIPT%
ECHO function ResetTimestamps($node){                                                 >> %RTS_SCRIPT%
ECHO     if(-Not ($node)){ $node = $pwd }                                             >> %RTS_SCRIPT%
ECHO     $epoch = $(Get-Date "01/01/1980 00:00:00").ToUniversalTime()                 >> %RTS_SCRIPT%
ECHO     try{                                                                         >> %RTS_SCRIPT%
ECHO         $file = $(Get-Item $node -force)                                         >> %RTS_SCRIPT%
ECHO         if($file.isReadOnly){                                                    >> %RTS_SCRIPT%
ECHO             Set-ItemProperty $node -name IsReadOnly -value $false                >> %RTS_SCRIPT%
ECHO             $file.creationtime   = $epoch                                        >> %RTS_SCRIPT%
ECHO             $file.lastaccesstime = $epoch                                        >> %RTS_SCRIPT%
ECHO             $file.lastwritetime  = $epoch                                        >> %RTS_SCRIPT%
ECHO             Set-ItemProperty $node -name IsReadOnly -value $true                 >> %RTS_SCRIPT%
ECHO         }else{                                                                   >> %RTS_SCRIPT%
ECHO             $file.creationtime   = $epoch                                        >> %RTS_SCRIPT%
ECHO             $file.lastaccesstime = $epoch                                        >> %RTS_SCRIPT%
ECHO             $file.lastwritetime  = $epoch                                        >> %RTS_SCRIPT%
ECHO         }                                                                        >> %RTS_SCRIPT%
ECHO         Write-Output "Cleared timestamp of: $($node)"                            >> %RTS_SCRIPT%
ECHO     }catch{                                                                      >> %RTS_SCRIPT%
ECHO         Write-Output "Unable to clear timestamp of: $($node)"                    >> %RTS_SCRIPT%
ECHO     }                                                                            >> %RTS_SCRIPT%
ECHO }                                                                                >> %RTS_SCRIPT%
ECHO #                                                                                >> %RTS_SCRIPT%
ECHO # Reset the timestamp of a file/folder and its children.                         >> %RTS_SCRIPT%
ECHO ResetTimestamps $args[0]                                                         >> %RTS_SCRIPT%
ECHO GetFiles $args[0]                                                                >> %RTS_SCRIPT%

ECHO Erasing timestamps...
:ArgumentLoop
    POWERSHELL %RTS_SCRIPT% %1
    SHIFT
    IF NOT "%1" == "" GOTO ArgumentLoop

DEL /F /Q %RTS_SCRIPT%
ECHO Finished!
