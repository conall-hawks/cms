#!/bin/bash
################################################################################
# Configure ownership, permissions, ACLs, and SELinux for httpd.               #
################################################################################

# Ensure we have root privileges.
if [ $EUID != 0 ]; then
    sudo "$0" "$@"
    exit $?
fi

# Variables.
HTML_DIR="/var/www/html"
HTTP_SERVER="apache"

# Configure ownership.
chown root:root -R "$HTML_DIR"

# Configure permissions.
find "$HTML_DIR" -type f -exec chmod 0640 {} \;
find "$HTML_DIR" -type d -exec chmod 0750 {} \;

# Configure access control lists; allow httpd to access web pages' directory and allow centos user to write for SFTP synchronize.
setfacl -bRm "u::rwX,u:${HTTP_SERVER}:rX,u:centos:rwX,g::rwX,o:-,m:rwX,d:u::rwX,d:u:${HTTP_SERVER}:rX,d:u:centos:rwX,d:g::rwX,d:o:-,d:m:rwX" "$HTML_DIR"

# Configure access control lists; allow httpd to write for file uploads and allow centos user to write for SFTP synchronize.
setfacl -bRm "u::rwX,u:${HTTP_SERVER}:rwX,u:centos:rwX,g::rwX,o:-,m:rwX,d:u::rwX,d:u:${HTTP_SERVER}:rwX,d:u:centos:rwX,d:g::rwX,d:o:-,d:m:rwX" "${HTML_DIR}/asset/upload"

# SELinux: Allow httpd to serve web pages.
chcon -R -t httpd_sys_content_t "$HTML_DIR"

# SELinux: Allow httpd to write to these directories.
chcon -R -t httpd_sys_rw_content_t "${HTML_DIR}/asset/upload"
