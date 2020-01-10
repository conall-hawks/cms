;------------------------------------------------------------------------------\
; Draws a Windows MessageBox using FASM macros.                                |
;------------------------------------------------------------------------------/
include 'win32ax.inc'
;include 'win32wx.inc'
;include 'win64ax.inc'
;include 'win64wx.inc'

.code
    start:
        invoke  MessageBox, HWND_DESKTOP, "Hello world.", invoke GetCommandLine, MB_OK
        invoke  ExitProcess, 0
    .end start
