/*---------------------------------------------------------------------------------------------------------------------\
| An example of how to draw a naive Windows GUI window using C.                                                        |
\---------------------------------------------------------------------------------------------------------------------*/
#include <windows.h>

/*-----------------------------------------------------------------------------\
| Handle window event messages.                                                |
\-----------------------------------------------------------------------------*/
LRESULT CALLBACK WndProc(HWND hWnd, UINT Message, WPARAM wParam, LPARAM lParam){
    switch(Message){

        /* Create the window; start of the program. */
        case WM_CREATE: {

            /* Menu bar. */
            HMENU bar = CreateMenu();

            /* "File" menu option. */
            HMENU menu_file = CreateMenu();
            AppendMenu(bar, MF_POPUP, (UINT_PTR)menu_file, "File");
            AppendMenu(menu_file, MF_STRING, 0, "Exit");

            /* "Edit" menu option. */
            HMENU menu_edit = CreateMenu();
            AppendMenu(bar, MF_POPUP, (UINT_PTR)menu_edit, "Edit");
            AppendMenu(menu_edit, MF_STRING, 9000, "Undo");
            AppendMenu(menu_edit, MF_STRING, 9001, "Redo");

            /* "Tools" menu option. */
            HMENU menu_tools = CreateMenu();
            AppendMenu(bar, MF_POPUP, (UINT_PTR)menu_tools, "Tools");
            AppendMenu(menu_tools, MF_STRING, 1000, "Connect");
            AppendMenu(menu_tools, MF_STRING, 1001, "Host");

            /* "About" menu option. */
            HMENU menu_about = CreateMenu();
            AppendMenu(bar, MF_STRING, (UINT_PTR)menu_about, "About");

            /* Paint menu in window. */
            SetMenu(hWnd, bar);
            break;
        }

        /* Destroy the window; end of the program. */
        case WM_DESTROY: {
            PostQuitMessage(0);

            /* Unless the linker is configured (-mwindows in mingw) for GUI mode this is the best we can do to hide the console. */
            ShowWindow(GetConsoleWindow(), SW_SHOWDEFAULT);
            break;
        }

        /* Handle commands sent to the window. */
        case WM_COMMAND: {
            switch((unsigned int)LOWORD(wParam)){
                case 0: {
                    PostMessage(hWnd, WM_CLOSE, 0, 0);
                    break;
                }
                case 1: {
                        WSADATA wsa;
                        if(WSAStartup(MAKEWORD(2,2),&wsa) != 0){
                            printf("Failed. Error Code : %d", );
                            MessageBox(NULL, WSAGetLastError(), "Error!", MB_ICONERROR|MB_OK);
                            return 1;
                        }
                }
            }
        }

        /* Other events. */
        default:
            return DefWindowProc(hWnd, Message, wParam, lParam);
    }
    return 0;
}

/*-----------------------------------------------------------------------------\
| The 'main' function of Windows GUI programs.                                 |
\-----------------------------------------------------------------------------*/
int WINAPI WinMain(HINSTANCE hInstance, HINSTANCE hPrevInstance, LPSTR lpCmdLine, int nCmdShow){

    /* Unless the linker is configured (-mwindows in mingw) for GUI mode this is the best we can do to hide the console. */
    CloseWindow(GetConsoleWindow());
    ShowWindow(GetConsoleWindow(), SW_HIDE);

    /* Set window properties. */
    WNDCLASSEX wc    = {};
    wc.cbSize        = sizeof(WNDCLASSEX);
    wc.lpfnWndProc   = WndProc;                         /* This is where we will send messages to. */
    wc.hInstance     = hInstance;
    wc.hCursor       = LoadCursor(NULL, IDC_ARROW);
    wc.hbrBackground = (HBRUSH)(COLOR_WINDOW + 1);
    wc.lpszClassName = "WindowClass";
    wc.hIcon         = LoadIcon(NULL, IDI_APPLICATION); /* Load a standard icon. */
    wc.hIconSm       = LoadIcon(NULL, IDI_APPLICATION); /* use the name "A" to use the project icon. */

    /* Register window. */
    if(!RegisterClassEx(&wc)){
        MessageBox(NULL, "Window Registration Failed!", "Error!", MB_ICONERROR|MB_OK);
        return 1;
    }

    /* Create window. */
    HWND hWnd = CreateWindowEx(
        WS_EX_STATICEDGE,
        "WindowClass",
        "Boilerplate Win32 GUI Program",
        WS_VISIBLE|WS_OVERLAPPEDWINDOW,
        CW_USEDEFAULT, /* x */
        CW_USEDEFAULT, /* y */
        640,           /* width */
        480,           /* height */
        NULL,
        NULL,
        hInstance,
        NULL
    );
    if(hWnd == NULL){
        MessageBox(NULL, "Window Creation Failed!", "Error!", MB_ICONERROR|MB_OK);
        return 1;
    }

    /* Handle window messages. */
    MSG msg;
    while(GetMessage(&msg, NULL, 0, 0) > 0){
        TranslateMessage(&msg);
        DispatchMessage(&msg);
    }

    /* Done! */
    return (int)msg.wParam;
}
