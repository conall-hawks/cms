' Starts an application hidden. If you wish to kill the task, you must do so through the task manager.
' Note that this is VBScript. Not all applications allowed to be run hidden.
Dim objShell
Set objShell = WScript.CreateObject ("WScript.shell")
objShell.run "mspaint", 0
Set objShell = Nothing