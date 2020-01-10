#!/bin/sh
################################################################################
# Enables root, removes its password, and configures auto-login for GNOME.     #
################################################################################

# Unlock root's password.
sudo passwd -uf root

# Remove root's password.
sudo passwd -d root

# Auto-login as root.
sudo echo "# GDM configuration storage

[daemon]
AutomaticLoginEnable=true
AutomaticLogin=root

[security]

[xdmcp]

[greeter]

[chooser]

[debug]
" > /etc/gdm/custom.conf
