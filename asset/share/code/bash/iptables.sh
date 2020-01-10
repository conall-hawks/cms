#!/bin/sh
################################################################################
# An example of strict custom firewall rules for a web server.                 #
################################################################################
# Allowed services:                                                            #
#                                                                              #
#     DNS Client                                                               #
#     DHCP Client                                                              #
#     HTTP Client                                                              #
#     HTTPS Client                                                             #
#     MySQL Client (Loopback Only)                                             #
#     WebSocket Client Proxy (Loopback Only)                                   #
#                                                                              #
#     SSH Server                                                               #
#     HTTP Server                                                              #
#     HTTPS Server                                                             #
#     MySQL Server (Loopback Only)                                             #
#     WebSocket Server (Loopback Only)                                         #
#     WebSocket Server Proxy                                                   #
#                                                "I AM THE LAW!" ~ Judge Dredd #
################################################################################

echo "*filter
################################################################################
# Deny all connections by default.                                             #
################################################################################
:INPUT   DROP [0:0]
:FORWARD DROP [0:0]
:OUTPUT  DROP [0:0]

################################################################################
# Allow DNS client.                                                            #
################################################################################
-A OUTPUT -p udp --dport 53 -m state --state NEW,ESTABLISHED -j ACCEPT
-A INPUT  -p udp --sport 53 -m state --state ESTABLISHED     -j ACCEPT

################################################################################
# Allow DHCP client.                                                           #
################################################################################
-A OUTPUT -p udp --dport 67 --sport 68 -m state --state NEW,ESTABLISHED -j ACCEPT
-A INPUT  -p udp --dport 68 --sport 67 -m state --state ESTABLISHED     -j ACCEPT

################################################################################
# Martian filtering.                                                           #
################################################################################
-A OUTPUT -d 0.0.0.0/8,10.0.0.0/8,100.64.0.0/10,169.254.0.0/16,172.16.0.0/12,192.0.0.0/24,192.0.2.0/24,192.168.0.0/16,198.18.0.0/15,198.51.100.0/24,203.0.113.0/24,224.0.0.0/4,240.0.0.0/4 -j DROP
-A INPUT  -s 0.0.0.0/8,10.0.0.0/8,100.64.0.0/10,169.254.0.0/16,172.16.0.0/12,192.0.0.0/24,192.0.2.0/24,192.168.0.0/16,198.18.0.0/15,198.51.100.0/24,203.0.113.0/24,224.0.0.0/4,240.0.0.0/4 -j DROP

################################################################################
# Allow HTTP client.                                                           #
################################################################################
-A OUTPUT -p tcp --dport 80 -m state --state NEW,ESTABLISHED -j ACCEPT
-A INPUT  -p tcp --sport 80 -m state --state ESTABLISHED     -j ACCEPT

################################################################################
# Allow HTTPS client.                                                          #
################################################################################
-A OUTPUT -p tcp --dport 443 -m state --state NEW,ESTABLISHED -j ACCEPT
-A INPUT  -p tcp --sport 443 -m state --state ESTABLISHED     -j ACCEPT

################################################################################
# Allow MySQL client on loopback.                                              #
################################################################################
-A OUTPUT -o lo -s 127.0.0.0/8 -d 127.0.0.0/8 -p tcp --dport 3306 -m state --state NEW,ESTABLISHED -j ACCEPT
-A INPUT  -i lo -s 127.0.0.0/8 -d 127.0.0.0/8 -p tcp --sport 3306 -m state --state ESTABLISHED     -j ACCEPT

################################################################################
# Allow WebSocket client proxy on loopback.                                    #
################################################################################
-A OUTPUT -o lo -s 127.0.0.0/8 -d 127.0.0.0/8 -p tcp --dport 8080 -m state --state NEW,ESTABLISHED -j ACCEPT
-A INPUT  -i lo -s 127.0.0.0/8 -d 127.0.0.0/8 -p tcp --sport 8080 -m state --state ESTABLISHED     -j ACCEPT

################################################################################
# Allow WebSocket client on loopback.                                          #
################################################################################
-A OUTPUT -o lo -s 127.0.0.0/8 -d 127.0.0.0/8 -p tcp --dport 8081 -m state --state NEW,ESTABLISHED -j ACCEPT
-A INPUT  -i lo -s 127.0.0.0/8 -d 127.0.0.0/8 -p tcp --sport 8081 -m state --state ESTABLISHED     -j ACCEPT

################################################################################
# Allow SSH server with throttles:                                             #
#   - Drop any new connections if the there were more than ~60 connection      #
#     attempts made in the past minute.                                        #
#   - Drop new connections from a specific IP address if there were more than  #
#     10 connection attempts made from that address.                           #
################################################################################
-A INPUT  -p tcp --dport 22 -m state --state NEW         -m limit     --limit           60/minute --limit-burst    10 -j ACCEPT
-A INPUT  -p tcp --dport 22 -m state --state NEW         -m connlimit --connlimit-above 20        --connlimit-mask 24 -j DROP
-A INPUT  -p tcp --dport 22 -m state --state ESTABLISHED                                                              -j ACCEPT
-A OUTPUT -p tcp --sport 22 -m state --state ESTABLISHED                                                              -j ACCEPT

################################################################################
# Allow HTTP server with throttles:                                            #
#   - Drop any new connections if the there were more than ~60 connection      #
#     attempts made in the past minute.                                        #
#   - Drop new connections from a specific IP address if there were more than  #
#     10 connection attempts made from that address.                           #
################################################################################
-A INPUT  -p tcp --dport 80 -m state --state NEW         -m limit     --limit           60/minute --limit-burst    10 -j ACCEPT
-A INPUT  -p tcp --dport 80 -m state --state NEW         -m connlimit --connlimit-above 20        --connlimit-mask 24 -j DROP
-A INPUT  -p tcp --dport 80 -m state --state ESTABLISHED                                                              -j ACCEPT
-A OUTPUT -p tcp --sport 80 -m state --state ESTABLISHED                                                              -j ACCEPT

################################################################################
# Allow HTTPS server with throttles:                                           #
#   - Drop any new connections if the there were more than ~60 connection      #
#     attempts made in the past minute.                                        #
#   - Drop new connections from a specific IP address if there were more than  #
#     10 connection attempts made from that address.                           #
################################################################################
-A INPUT  -p tcp --dport 443 -m state --state NEW         -m limit     --limit           60/minute --limit-burst    10 -j ACCEPT
-A INPUT  -p tcp --dport 443 -m state --state NEW         -m connlimit --connlimit-above 20        --connlimit-mask 24 -j DROP
-A INPUT  -p tcp --dport 443 -m state --state ESTABLISHED                                                              -j ACCEPT
-A OUTPUT -p tcp --sport 443 -m state --state ESTABLISHED                                                              -j ACCEPT

################################################################################
# Allow MySQL server on loopback.                                              #
################################################################################
-A INPUT  -i lo -s 127.0.0.0/8 -d 127.0.0.0/8 -p tcp --dport 3306 -m state --state NEW,ESTABLISHED -j ACCEPT
-A OUTPUT -o lo -s 127.0.0.0/8 -d 127.0.0.0/8 -p tcp --sport 3306 -m state --state ESTABLISHED     -j ACCEPT

################################################################################
# Allow WebSocket server on loopback.                                          #
################################################################################
-A INPUT  -i lo -s 127.0.0.0/8 -d 127.0.0.0/8 -p tcp --dport 8081 -m state --state NEW,ESTABLISHED -j ACCEPT
-A OUTPUT -o lo -s 127.0.0.0/8 -d 127.0.0.0/8 -p tcp --sport 8081 -m state --state ESTABLISHED     -j ACCEPT

################################################################################
# Allow WebSocket server proxy with throttles:                                 #
#   - Drop any new connections if the there were more than ~60 connection      #
#     attempts made in the past minute.                                        #
#   - Drop new connections from a specific IP address if there were more than  #
#     10 connection attempts made from that address.                           #
################################################################################
-A INPUT  -p tcp --dport 8080 -m state --state NEW         -m limit     --limit           60/minute --limit-burst    10 -j ACCEPT
-A INPUT  -p tcp --dport 8080 -m state --state NEW         -m connlimit --connlimit-above 20        --connlimit-mask 24 -j DROP
-A INPUT  -p tcp --dport 8080 -m state --state ESTABLISHED                                                              -j ACCEPT
-A OUTPUT -p tcp --sport 8080 -m state --state ESTABLISHED                                                              -j ACCEPT

################################################################################
# Log all dropped packets to /var/log/messages. (Keep this at the bottom.)     #
################################################################################
-A INPUT  -m limit --limit 12/minute --limit-burst 2 -j LOG
-A OUTPUT -m limit --limit 12/minute --limit-burst 2 -j LOG

# Persist.
COMMIT
" > /etc/sysconfig/iptables
service iptables restart
