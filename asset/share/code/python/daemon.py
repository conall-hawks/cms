#!/usr/bin/python3
################################################################################
# POSIX daemon.                                                                #
################################################################################
import atexit
import os
import signal
import sys
import time

class Daemon:
    ############################################################################
    # Construct.                                                               #
    ############################################################################
    def __init__(self):

        # Set PID file.
        self.pidfile = '/var/run/' + self.__class__.__name__.lower() + '.pid'

    ############################################################################
    # Start daemon.                                                            #
    ############################################################################
    def start(self):

        # Feedback.
        sys.stdout.write('Starting:                                                ')

        # Check if daemon is already running.
        try:
            with open(self.pidfile, 'r') as file:
                pid = int(file.read().strip())

            if pid:
                sys.stdout.write('[ \033[91mFailed\033[0m ]\n')
        except:
            pass

        # Fork process.
        try:
            if os.fork() > 0: sys.exit(0)
        except Exception as error:
            sys.stdout.write('[ \033[91mFailed\033[0m ]\n')
            sys.exit(1)

        # Decouple.
        os.chdir('/')
        os.setsid()
        os.umask(0)

        # Fork process again.
        try:
            if os.fork() > 0: sys.exit(0)
        except OSError as error:
            sys.stdout.write('[ \033[91mFailed\033[0m ]\n')
            sys.exit(1)

        # Feedback.
        sys.stdout.write('[   \033[92mOK\033[0m   ]\n')

        # Wipe output buffers.
        sys.stdout.flush()
        sys.stderr.flush()

        # Redirect input/outputs.
        stdin  = open(os.devnull, 'r')
        stdout = open(os.devnull, 'a+')
        stderr = open(os.devnull, 'a+')

        # Duplicate file descriptors.
        os.dup2(stdin.fileno(), sys.stdin.fileno())
        os.dup2(stdout.fileno(), sys.stdout.fileno())
        os.dup2(stderr.fileno(), sys.stderr.fileno())

        # Delete PID file on exit.
        atexit.register(os.remove(self.pidfile))

        # Write pidfile.
        with open(self.pidfile, 'w+') as file:
            file.write(str(os.getpid()) + '\n')

        # Run main program loop.
        self.run()

    ############################################################################
    # Stop daemon.                                                             #
    ############################################################################
    def stop(self):

        # Feedback.
        sys.stdout.write('Stopping:                                                ')

        # Get the PID from the PID file.
        try:
            with open(self.pidfile, 'r') as file:
                pid = int(file.read().strip())
        except:
            pid = None

        if not pid:
            sys.stdout.write('[ \033[91mFailed\033[0m ]\n')
            return

        # Kill process using PID.
        try:
            while 1:
                os.kill(pid, signal.SIGTERM)
                time.sleep(0.1)
        except OSError as error:
            sys.stdout.write('[ \033[91mFailed\033[0m ]\n')
            error = str(error.args)
            if error.find('No such process') > 0:
                if os.path.exists(self.pidfile): os.remove(self.pidfile)
            else:
                sys.stderr.write('Error: ' + error)
                sys.exit(1)

        sys.stdout.write('[   \033[92mOK\033[0m   ]\n')

    ############################################################################
    # Restart daemon.                                                          #
    ############################################################################
    def restart(self):
        self.stop()
        self.start()

    ############################################################################
    # Get status of daemon.                                                    #
    ############################################################################
    def status(self):

        # Get the PID from the PID file.
        try:
            with open(self.pidfile, 'r') as file:
                pid = int(file.read().strip())
        except:
            pid = None

        if not pid:
            sys.stderr.write(self.__class__.__name__.lower() + ' is not running')
            return

        sys.stdout.write(' (pid ' + pid + ') is running...')

    ############################################################################
    # Main program loop.                                                       #
    ############################################################################
    def run(self):
        while 1:

            # Throttle
            time.sleep(1)

            # Do stuff.
            with open(self.pidfile, 'a+') as file:
                file.write('Still here! \n')

################################################################################
# Initializer.                                                                 #
################################################################################
if __name__ == '__main__':
    if len(sys.argv) == 2:
        if   'start'   == sys.argv[1]:
            Daemon().start()
        elif 'stop'    == sys.argv[1]:
            Daemon().stop()
        elif 'restart' == sys.argv[1]:
            Daemon().restart()
        elif 'status'  == sys.argv[1]:
            Daemon().status()
        else:
            print('Unknown command: ' + sys.argv[1])
            sys.exit(2)
        sys.exit(0)
    else:
        print('Usage: ' + sys.argv[0] + ' start|stop|restart|status')
        sys.exit(2)
