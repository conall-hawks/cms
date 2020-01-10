#!/bin/sh
################################################################################
# Installs C++ 2014 on Red Hat 7-based distributions.                          #
################################################################################

# On CentOS, install SCL repository.
if [ -e "/etc/centos-release" ]; then
    sudo yum install centos-release-scl

# On RHEL, enable SCL repository.
else if [ -e "/etc/redhat-release" ]; then
    sudo yum-config-manager --enable rhel-server-rhscl-7-rpms
fi

# Install devtoolset 8 collection.
sudo yum install devtoolset-8

# Runtime enable.
source /opt/rh/devtoolset-8/enable

# Permanently enable.
echo "
# Enable devtoolset.
source /opt/rh/devtoolset-8/enable
" >> ~/.bashrc

# Done!
echo "Done!"
g++ -v
