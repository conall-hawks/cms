#!/bin/sh
################################################################################
# An example of using named parameters in a Bash script. However, arguments'   #
# keys and values must be joined by an equals sign (=).                        #
#------------------------------------------------------------------------------#
# Usage:                                                                       #
#     ./named-arguments.sh -a1=hello --arg_2="hello world" -a3=foo bar         #
#                                                                              #
# Result:                                                                      #
#     -a1 or --arg_1 is: hello                                                 #
#     -a2 or --arg_2 is: hello world                                           #
#     -a3 or --arg_3 is: foo                                                   #
################################################################################
while [ $# -gt 0 ]; do case "$1" in
    -a1*|--arg_1*) arg_1="${1#*=}" ;;
    -a2*|--arg_2*) arg_2="${1#*=}" ;;
    -a3*|--arg_3*) arg_3="${1#*=}" ;;
esac; shift; done

echo "-a1 or --arg_1 is: ${arg_1}"
echo "-a2 or --arg_2 is: ${arg_2}"
echo "-a3 or --arg_3 is: ${arg_3}"
