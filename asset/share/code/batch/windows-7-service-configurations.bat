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
REM # Set a "minimalist" services configuration.                               #
REM ############################################################################

ECHO ActiveX Installer
SC CONFIG "AxInstSV" START= "demand"

ECHO Adaptive Brightness
SC CONFIG "SensrSvc" START= "disabled"

ECHO Application Experience
SC CONFIG "AeLookupSvc" START= "demand"

ECHO Application Host Helper Service
SC CONFIG "AppHostSvc" START= "demand"

ECHO Application Identity
SC CONFIG "AppIDSvc" START= "demand"

ECHO Application Information
SC CONFIG "Appinfo" START= "demand"

ECHO Application Layer Gateway Service
SC CONFIG "ALG" START= "demand"

ECHO Application Management
SC CONFIG "AppMgmt" START= "disabled"

ECHO ASP.NET State Service
SC CONFIG "aspnet_state" START= "demand"

ECHO Background Intelligent Transfer Service
SC CONFIG "BITS" START= "demand"

ECHO Base Filtering Engine
SC CONFIG "BFE" START= "auto"

ECHO BitLocker Drive Encryption Service
SC CONFIG "BDESVC" START= "demand"

ECHO Block Level Backup Engine Service
SC CONFIG "wbengine" START= "demand"

ECHO Bluetooth Support Service
SC CONFIG "bthserv" START= "demand"

ECHO BranchCache
SC CONFIG "PeerDistSvc" START= "disabled"

ECHO Certificate Propagation
SC CONFIG "CertPropSvc" START= "disabled"

ECHO Client for NFS
SC CONFIG "NfsClnt" START= "demand"

ECHO CNG Key Isolation
SC CONFIG "KeyIso" START= "demand"

ECHO COM+ Event System
SC CONFIG "EventSystem" START= "auto"

ECHO COM+ System Application
SC CONFIG "COMSysApp" START= "demand"

ECHO Computer Browser
SC CONFIG "Browser" START= "demand"

ECHO Credential Manager
SC CONFIG "VaultSvc" START= "demand"

ECHO Cryptographic Services
SC CONFIG "CryptSvc" START= "auto"

ECHO DCOM Server Process Launcher
SC CONFIG "DcomLaunch" START= "auto"

ECHO Desktop Window Manager Session Manager
SC CONFIG "UxSms" START= "auto"

ECHO DHCP Client
SC CONFIG "Dhcp" START= "auto"

ECHO Diagnostic Policy Service
SC CONFIG "DPS" START= "auto"

ECHO Diagnostic Service Host
SC CONFIG "WdiServiceHost" START= "demand"

ECHO Diagnostic System Host
SC CONFIG "WdiSystemHost" START= "demand"

ECHO Diagnostics Tracking Service
SC CONFIG "DiagTrack" START= "demand"

ECHO Disk Defragmenter
SC CONFIG "defragsvc" START= "demand"

ECHO Distributed Link Tracking Client
SC CONFIG "TrkWks" START= "disabled"

ECHO Distributed Transaction Coordinator
SC CONFIG "MSDTC" START= "demand"

ECHO DNS Client
SC CONFIG "Dnscache" START= "auto"

ECHO Encrypting File System (EFS)
SC CONFIG "EFS" START= "demand"

ECHO Extensible Authentication Protocol
SC CONFIG "EapHost" START= "demand"

ECHO Fax
SC CONFIG "Fax" START= "demand"

ECHO Function Discovery Provider Host
SC CONFIG "fdPHost" START= "demand"

ECHO Function Discovery Resource Publication
SC CONFIG "FDResPub" START= "demand"

ECHO Group Policy Client
SC CONFIG "gpsvc" START= "auto"

ECHO Health Key and Certificate Management
SC CONFIG "hkmsvc" START= "demand"

ECHO HomeGroup Listener
SC CONFIG "HomeGroupListener" START= "demand"

ECHO HomeGroup Provider
SC CONFIG "HomeGroupProvider" START= "demand"

ECHO Human Interface Device Access
SC CONFIG "hidserv" START= "demand"

ECHO IIS Admin Service
SC CONFIG "IISADMIN" START= "demand"

ECHO IKE and AuthIP IPsec Keying Modules
SC CONFIG "IKEEXT" START= "demand"

ECHO Indexing Service
SC CONFIG "CISVC" START= "demand"

ECHO Interactive Services Detection
SC CONFIG "UI0Detect" START= "demand"

ECHO Internet Connection Sharing
SC CONFIG "SharedAccess" START= "disabled"

ECHO IP Helper
SC CONFIG "iphlpsvc" START= "demand"

ECHO IPsec Policy Agent
SC CONFIG "PolicyAgent" START= "demand"

ECHO KtmRm for Distributed Transaction Coordinator
SC CONFIG "KtmRm" START= "demand"

ECHO Link-Layer Topology Discovery Mapper
SC CONFIG "lltdsvc" START= "demand"

ECHO LPD Service
SC CONFIG "LPDSVC" START= "demand"

ECHO Media Center Extender Service
SC CONFIG "Mcx2Svc" START= "disabled"

ECHO Message Queuing
SC CONFIG "MSMQ" START= "demand"

ECHO Message Queuing Triggers
SC CONFIG "MSMQTriggers" START= "demand"

ECHO Microsoft .NET Framework NGEN v2.0.50727_X64
SC CONFIG "clr_optimization_v2.0.50727_64" START= "demand"

ECHO Microsoft .NET Framework NGEN v2.0.50727_X86
SC CONFIG "clr_optimization_v2.0.50727_32" START= "demand"

ECHO Microsoft .NET Framework NGEN v4.0.30319_X64
SC CONFIG "clr_optimization_v4.0.30319_64" START= "demand"

ECHO Microsoft .NET Framework NGEN v4.0.30319_X86
SC CONFIG "clr_optimization_v4.0.30319_32" START= "demand"

ECHO Microsoft FTP Service
SC CONFIG "ftpsvc" START= "demand"

ECHO Microsoft iSCSI Initiator Service
SC CONFIG "MSiSCSI" START= "disabled"

ECHO Microsoft Software Shadow Copy Provider
SC CONFIG "swprv" START= "demand"

ECHO Multimedia Class Scheduler
SC CONFIG "MMCSS" START= "auto"

ECHO Net.Msmq Listener Adapter
SC CONFIG "NetMsmqActivator" START= "demand"

ECHO Net.Pipe Listener Adapter
SC CONFIG "NetPipeActivator" START= "demand"

ECHO Net.Tcp Listener Adapter
SC CONFIG "NetTcpActivator" START= "demand"

ECHO Net.Tcp Port Sharing Service
SC CONFIG "NetTcpPortSharing" START= "disabled"

ECHO Netlogon
SC CONFIG "Netlogon" START= "demand"

ECHO Network Access Protection Agent
SC CONFIG "napagent" START= "demand"

ECHO Network Connections
SC CONFIG "Netman" START= "demand"

ECHO Network List Service
SC CONFIG "netprofm" START= "demand"

ECHO Network Location Awareness
SC CONFIG "NlaSvc" START= "auto"

ECHO Network Store Interface Service
SC CONFIG "nsi" START= "auto"

ECHO Offline Files
SC CONFIG "CscService" START= "demand"

ECHO Parental Controls
SC CONFIG "WPCSvc" START= "disabled"

ECHO Peer Name Resolution Protocol
SC CONFIG "PNRPSvc" START= "demand"

ECHO Peer Networking Grouping
SC CONFIG "p2psvc" START= "demand"

ECHO Peer Networking Identity Manager
SC CONFIG "p2pimsvc" START= "demand"

ECHO Performance Logs & Alerts
SC CONFIG "pla" START= "demand"

ECHO Plug and Play
SC CONFIG "PlugPlay" START= "auto"

ECHO PnP-X IP Bus Enumerator
SC CONFIG "IPBusEnum" START= "demand"

ECHO PNRP Machine Name Publication Service
SC CONFIG "PNRPAutoReg" START= "demand"

ECHO Portable Device Enumerator Service
SC CONFIG "WPDBusEnum" START= "demand"

ECHO Power
SC CONFIG "Power" START= "auto"

ECHO Print Spooler
SC CONFIG "Spooler" START= "auto"

ECHO Problem Reports and Solutions Control Panel Support
SC CONFIG "wercplsupport" START= "demand"

ECHO Program Compatibility Assistant Service
SC CONFIG "PcaSvc" START= "demand"

ECHO Protected Storage
SC CONFIG "ProtectedStorage" START= "demand"

ECHO Quality Windows Audio Video Experience
SC CONFIG "QWAVE" START= "demand"

ECHO Remote Access Auto Connection Manager
SC CONFIG "RasAuto" START= "demand"

ECHO Remote Access Connection Manager
SC CONFIG "RasMan" START= "demand"

ECHO Remote Desktop Configuration
SC CONFIG "SessionEnv" START= "demand"

ECHO Remote Desktop Services
SC CONFIG "TermService" START= "demand"

ECHO Remote Desktop Services UserMode Port Redirector
SC CONFIG "UmRdpService" START= "demand"

ECHO Remote Procedure Call (RPC)
SC CONFIG "RpcSs" START= "auto"

ECHO Remote Procedure Call (RPC) Locator
SC CONFIG "RpcLocator" START= "demand"

ECHO Remote Registry
SC CONFIG "RemoteRegistry" START= "demand"

ECHO RIP Listener
SC CONFIG "iprip" START= "demand"

ECHO Routing and Remote Access
SC CONFIG "RemoteAccess" START= "disabled"

ECHO RPC Endpoint Mapper
SC CONFIG "RpcEptMapper" START= "auto"

ECHO SeaPort
SC CONFIG "SeaPort" START= "demand"

ECHO Secondary Logon
SC CONFIG "seclogon" START= "demand"

ECHO Secure Socket Tunneling Protocol Service
SC CONFIG "SstpSvc" START= "demand"

ECHO Security Accounts Manager
SC CONFIG "SamSs" START= "auto"

ECHO Security Center
SC CONFIG "wscsvc" START= "auto"

ECHO Server
SC CONFIG "LanmanServer" START= "auto"

ECHO Shell Hardware Detection
SC CONFIG "ShellHWDetection" START= "auto"

ECHO Simple TCP/IP Services
SC CONFIG "simptcp" START= "demand"

ECHO Smart Card
SC CONFIG "SCardSvr" START= "demand"

ECHO Smart Card Removal Policy
SC CONFIG "SCPolicySvc" START= "demand"

ECHO SNMP Service
SC CONFIG "SNMP" START= "demand"

ECHO SNMP Trap
SC CONFIG "SNMPTRAP" START= "demand"

ECHO Software Protection
SC CONFIG "sppsvc" START= "auto"

ECHO SPP Notification Service
SC CONFIG "sppuinotify" START= "demand"

ECHO SSDP Discovery
SC CONFIG "SSDPSRV" START= "disabled"

ECHO Storage Service
SC CONFIG "StorSvc" START= "demand"

ECHO Superfetch
SC CONFIG "SysMain" START= "demand"

ECHO System Event Notification Service
SC CONFIG "SENS" START= "auto"

ECHO Tablet PC Input Service
SC CONFIG "TabletInputService" START= "demand"

ECHO Task Scheduler
SC CONFIG "Schedule" START= "auto"

ECHO TCP/IP NetBIOS Helper
SC CONFIG "lmhosts" START= "demand"

ECHO Telephony
SC CONFIG "TapiSrv" START= "demand"

ECHO Telnet
SC CONFIG "TlntSvr" START= "disabled"

ECHO Themes
SC CONFIG "Themes" START= "auto"

ECHO Thread Ordering Server
SC CONFIG "THREADORDER" START= "demand"

ECHO TPM Base Services
SC CONFIG "TBS" START= "demand"

ECHO UPnP Device Host
SC CONFIG "upnphost" START= "disabled"

ECHO User Profile Service
SC CONFIG "ProfSvc" START= "auto"

ECHO Virtual Disk
SC CONFIG "vds" START= "demand"

ECHO Volume Shadow Copy
SC CONFIG "VSS" START= "demand"

ECHO Web Management Service
SC CONFIG "WMSVC" START= "demand"

ECHO WebClient
SC CONFIG "WebClient" START= "demand"

ECHO Windows Activation Technologies Service
SC CONFIG "WatAdminSvc" START= "demand"

ECHO Windows Audio
SC CONFIG "AudioSrv" START= "auto"

ECHO Windows Audio Endpoint Builder
SC CONFIG "AudioEndpointBuilder" START= "auto"

ECHO Windows Backup
SC CONFIG "SDRSVC" START= "demand"

ECHO Windows Biometric Service
SC CONFIG "SDRSVC" START= "demand"

ECHO Windows CardSpace
SC CONFIG "idsvc" START= "demand"

ECHO Windows Color System
SC CONFIG "WcsPlugInService" START= "demand"

ECHO Windows Connect Now - Config Registrar
SC CONFIG "wcncsvc" START= "demand"

ECHO Windows Defender
SC CONFIG "WinDefend" START= "auto"

ECHO Windows Driver Foundation - User-mode Driver Framework
SC CONFIG "wudfsvc" START= "auto"

ECHO Windows Error Reporting Service
SC CONFIG "WerSvc" START= "demand"

ECHO Windows Event Collector
SC CONFIG "Wecsvc" START= "demand"

ECHO Windows Event Log
SC CONFIG "EventLog" START= "auto"

ECHO Windows Firewall
SC CONFIG "MpsSvc" START= "auto"

ECHO Windows Font Cache Service
SC CONFIG "FontCache" START= "demand"

ECHO Windows Image Acquisition (WIA)
SC CONFIG "StiSvc" START= "demand"

ECHO Windows Installer
SC CONFIG "msiserver" START= "demand"

ECHO Windows Live Family Safety
SC CONFIG "fsssvc" START= "demand"

ECHO Windows Management Instrumentation
SC CONFIG "Winmgmt" START= "auto"

ECHO Windows Media Center Receiver Service
SC CONFIG "ehRecvr" START= "demand"

ECHO Windows Media Center Scheduler Service
SC CONFIG "ehSched" START= "demand"

ECHO Windows Media Player Network Sharing Service
SC CONFIG "WMPNetworkSvc" START= "disabled"

ECHO Windows Modules Installer
SC CONFIG "TrustedInstaller" START= "demand"

ECHO Windows Presentation Foundation Font Cache 3.0.0.0
SC CONFIG "FontCache3.0.0.0" START= "demand"

ECHO Windows Process Activation Service
SC CONFIG "WAS" START= "demand"

ECHO Windows Remote Management (WS-Management)
SC CONFIG "WinRM" START= "demand"

ECHO Windows Search
SC CONFIG "WSearch" START= "disabled"

ECHO Windows Time
SC CONFIG "W32Time" START= "demand"

ECHO Windows Update
SC CONFIG "wuauserv" START= "demand"

ECHO WinHTTP Web Proxy Auto-Discovery Service
SC CONFIG "WinHttpAutoProxySvc" START= "demand"

ECHO Wired AutoConfig
SC CONFIG "dot3svc" START= "demand"

ECHO WLAN AutoConfig
SC CONFIG "Wlansvc" START= "demand"

ECHO WMI Performance Adapter
SC CONFIG "wmiApSrv" START= "demand"

ECHO Workstation
SC CONFIG "LanmanWorkstation" START= "auto"

ECHO World Wide Web Publishing Service
SC CONFIG "W3SVC" START= "demand"

ECHO WWAN AutoConfig
SC CONFIG "WwanSvc" START= "demand"
