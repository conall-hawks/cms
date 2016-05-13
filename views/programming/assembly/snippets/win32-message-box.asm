; Draws a Windows MessageBox using FASM macros. Requires FASM.
; You can switch between win32ax, win32wx, win64ax and win64wx.
include 'win32ax.inc'
.code
	start:
		invoke	MessageBox, HWND_DESKTOP, "Hello world.", invoke GetCommandLine, MB_OK
		invoke	ExitProcess, 0
	.end start