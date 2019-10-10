#!/bin/sh
# swoole Enterprise installer rewritten by dixyes
PORTS_TO_DETECT='53456,60286,60986,55332,44624,51449';
# for zsh compatiable
[ -n "$ZSH_VERSION" ] && emulate -L ksh;

# log utils
logi(){
    printf "[0;1;32m[IFO][0;1m $*[0m\n"
}
logw(){
    printf "[0;1;33m[WRN][0;1m $*[0m\n"
}
loge(){
    printf "[0;1;31m[ERR][0;1m $*[0m\n"
}

logi "Swoole Tracker NodeAgent installer"

[ "`uname -s 2>&1`"x != 'Linuxx' ] && loge "Swoole Enterprise NodeAgent only support linux OS (not UNIX or macOS)." && exit 22 # 22 for EINVAL

[ "`id -u 2>&1`"x != '0x' ] && loge "This install script should be run as root." && exit 13 # 13 for EACCES

# this regex will match 99.999.9999.9999 or something like this, however, sometimes you can bind such a string as domain.(i.e. link in docker)
if [ ! -f "./app_deps/node-agent/userid" ]
then
    if [ "`echo $1 | grep -E '^([a-zA-Z0-9][a-zA-Z0-9\-]*?\.)*[a-zA-Z0-9][a-zA-Z0-9\-]*?\.?$'`"x = "x" ]
    then
        logi "Usage: $0 <remote>\n\twhich \"remote\" should be ipv4 address or domain of swoole-admin host\n" &&
        exit 22 # 22 for EINVAL
    else
        ADMIN_ADDR=$1
    fi
else
    ADMIN_ADDR="www.swoole-cloud.com"
fi

# install files
logi 'Start Installing node-agent files'
mkdir -p /opt/swoole/node-agent /opt/swoole/logs /opt/swoole/public /opt/swoole/config 2>&-
chmod 777 /opt/swoole/logs
logi ' Installing files at /opt/swoole/node-agent'
rm -rf /opt/swoole/script
cp -rf ./app_deps/node-agent/* /opt/swoole/node-agent/
ln -s /opt/swoole/node-agent/script/ /opt/swoole/script
chmod +x /opt/swoole/script/*.sh
chmod +x /opt/swoole/script/php/swoole_php
logi ' Installing files at /opt/swoole/public'
cp -rf ./app_deps/public/framework /opt/swoole/public/

# clean cache
logi 'Clean caches at /tmp/mostats'
rm -rf /tmp/mostats

# backup config file: yet no use.
logi 'Backing up config file at /opt/swoole/config to /tmp/swconfigback'
if [ -d "/opt/swoole/config/" ];then
    cp -rf /opt/swoole/config /tmp/swconfigback
    rm -rf /opt/swoole/config
fi
cp -rf ./app_deps/public/config /opt/swoole/
chmod -R 777 /opt/swoole/config
chmod -R 777 /opt/swoole

# remove legacy system-side supervisor nodeagent
if [ -f /etc/supervisor/conf.d/node-agent.conf ]
then
    logw 'Removing legacy system-wide supervisor files for nodeagent.'
    supervisorctl -c /etc/supervisor/supervisord.conf stop node-agent >>/tmp/na_installer.log 2>&1
    rm -f /etc/supervisor/conf.d/node-agent.conf >>/tmp/na_installer.log 2>&1
    supervisorctl -c /etc/supervisor/supervisord.conf update >>/tmp/na_installer.log 2>&1
fi

# remove legacy supervisor in /opt/swoole/pysandbox
if [ -e /opt/swoole/pysandbox ]
then
    logw 'Removing legacy supervisor files at /opt/swoole'
    if [ -S /opt/swoole/supervisor/supervisor.sock ]
    then
        logw ' Stopping legacy python venv supervisor'
        /opt/swoole/pysandbox/bin/supervisorctl -c /opt/swoole/supervisor/supervisord.conf shutdown
    fi
    rm -rf /opt/swoole/pysandbox
    rm -rf /opt/swoole/supervisor
fi

# (Deprecated) use this to disable supervisor check
#logi "Workaround for supervisor dir check"
#echo "{\"supervisor\":{\"config_dir\":[\"/opt/swoole/config\"]}}" > /opt/swoole/config/config.json
mv /opt/swoole/node-agent/userid /opt/swoole/config/ >/tmp/na_installer.log 2>&1

if type kill ps >>/tmp/na_installer.log 2>&1
then
    logi 'All dependencies are ok, skipping dependencies installion.'
else
    # find package manager ,then install dependencies
    #  use varibles for future use.(may be removed if it takes no use at all.)
    # TODO: dnf, yast
    if type apt-get >>/tmp/na_installer.log 2>&1
    then
        logi 'super moo power detected, using apt-get to install dependencies.'
        logi ' Updating apt cache.'
        apt-get update -y >>/tmp/na_installer.log 2>&1
        type ps kill >>/tmp/na_installer.log 2>&1 || {
            logi ' Installing procps for commands: ps, kill.'
            apt-get install -y --no-install-recommends procps >>/tmp/na_installer.log 2>&1
        }
    elif type yum >>/tmp/na_installer.log 2>&1
    then
        logi 'yellow dog detected, using yum to install dependencies.'
        logi ' Updating yum cache.'
        yum makecache >>/tmp/na_installer.log 2>&1
        
        # rpm distros' coreutils have ps, kill things in it, we needn't expclit install it : when script go here, it's installed
        logi ' Installing coreutils ( you should not see this unless your ps or kill command broken ).'
        yum install -y coreutils >>/tmp/na_installer.log 2>&1
    fi
fi

if type apk >>/tmp/na_installer.log 2>&1
then
    logi 'coffe-making-able package manager detected, using apk to install dependencies.'
    logi ' Updating apk cache.'
    apk update >>/tmp/na_installer.log 2>&1
    
    # ldd is for determining libc type.
    type ldd >>/tmp/na_installer.log 2>&1 || {
        logi ' Installing libc-utils for command: ldd.' &&
        apk add -q libc-utils >>/tmp/na_installer.log 2>&1
    }
    # musl workaround TODO: use AWSL rebuild binaries.
    if [ "`ldd 2>&1 | grep -i musl`x" != "x" ]
    then
        logw 'You are using musl as libc, and you have apk,'
        logw ' assuming you are using alpine 3+, preparing dynamic libraries for running shared libraries.'
        # TODO:assuming using alpine, will be modified to work with all musl distros.
        apk add --allow-untrusted musl-compat/glibc-2.29-r0.apk >>/tmp/na_installer.log 2>&1
        cp musl-compat/libgcc_s.so.1 /usr/glibc-compat/lib
    fi
fi


# if something still not installed ,error out
lack_deps=""
type kill >>/tmp/na_installer.log 2>&1 || lack_deps="${lack_deps} kill(from coreutils or psproc(at debian variants)),"
type ps >>/tmp/na_installer.log 2>&1 || lack_deps="${lack_deps} ps(from coreutils or psproc(at debian variants)),"
type df >>/tmp/na_installer.log 2>&1 || lack_deps="${lack_deps} df(from coreutils),"
type rm >>/tmp/na_installer.log 2>&1 || lack_deps="${lack_deps} rm(from coreutils),"
type cat >>/tmp/na_installer.log 2>&1 || lack_deps="${lack_deps} cat(from coreutils),"
type chmod >>/tmp/na_installer.log 2>&1 || lack_deps="${lack_deps} chmod(from coreutils),"
[ "x" != "x$lack_deps" ] && loge "for command(s) ${lack_deps%,}: Dependency installation failed, you need install dependencies mannually, then execute $0." && exit 1

# check remote state and write config.
PORTS_TO_DETECT=${PORTS_TO_DETECT-'9903,9904,9907,9981,9990,8995'}

logi 'Checking if admin host is up (and accessible), then generate config.'
/opt/swoole/script/php/swoole_php << EOF
<?php
foreach([$PORTS_TO_DETECT] as \$port){
    \$cli = new Swoole\\Client(SWOOLE_SOCK_TCP, SWOOLE_SOCK_SYNC);
    if (!@\$cli->connect("$ADMIN_ADDR", \$port)){
        exit(1);
    }
    \$cli->close();
}
exit(0);
EOF
if [ "$?" != "0" ]
then
    loge "Remote host $ADMIN_ADDR not accessable, check network connection or remote swoole-admin state." >&2 &&
    exit 11 # 11 for EAGAIN
fi
# save config json
echo '{"ip":{"product":"'$ADMIN_ADDR'","local":"127.0.0.1"}}' > /opt/swoole/config/config_ip.conf
#echo '' > /opt/swoole/config/config_port.conf

# add startup scripts for openrc / sysvinit / systemd
if [ "x`rc-status -V 2>&1 | grep -i openrc`" != "x" ]
then
    # openrc init
    logi 'Installing node-agent startup script for OpenRC.'
    cat > /etc/init.d/node-agent  << EOF
#!`if [ -e '/sbin/openrc-run' ]; then echo /sbin/openrc-run; else echo /sbin/runscript; fi;`

name="Swoole Enterprise NodeAgent"
command="/opt/swoole/script/php/swoole_php"
pidfile="/var/run/node-agent.pid"
command_args="/opt/swoole/node-agent/src/node.php"
command_user="root"
command_background="yes"
start_stop_daemon_args="--make-pidfile --stdout /opt/swoole/logs/node-agent_stdout.log --stderr /opt/swoole/logs/node-agent_stderr.log"

depend() {
    need net
}

stop() {
    [ ! -e \${pidfile} ] && return
    ebegin "Stopping \${name}"
    /opt/swoole/script/php/swoole_php /opt/swoole/script/killtree.php \`cat \${pidfile}\`
    retval=\$?
    [ "0" = \$retval ] && rm \${pidfile}
    eend \$retval
}
EOF
    chmod 755 /etc/init.d/node-agent
    rc-service node-agent stop >>/tmp/na_installer.log 2>&1
    rc-service node-agent start >>/tmp/na_installer.log 2>&1
elif [ "x`/proc/1/exe --version 2>&1 | grep -i systemd`" != "x" ] || type systemctl >>/tmp/na_installer.log 2>&1
then
    logi 'Installing node-agent startup script for systemd.'
    cat > /etc/systemd/system/node-agent.service << EOF
[Unit]
Description=Swoole Enterprise NodeAgent
After=network.target

[Service]
Type=simple
PIDFile=/var/run/node-agent.pid
ExecStart=/opt/swoole/script/php/swoole_php /opt/swoole/node-agent/src/node.php
ExecStop=/opt/swoole/script/php/swoole_php /opt/swoole/script/killtree.php \$MAINPID
Restart=on-failure
RestartSec=60s

[Install]
WantedBy=multi-user.target

EOF
    chmod 664 /etc/systemd/system/node-agent.service
    # this may fail in docker 'cause can't access dbus-daemon, thus execute it here.
    systemctl daemon-reload >>/tmp/na_installer.log 2>&1 
    systemctl stop node-agent.service >>/tmp/na_installer.log 2>&1
    systemctl restart node-agent.service >>/tmp/na_installer.log 2>&1
    if [ x`systemctl show node-agent.service -p ActiveState` = x'ActiveState=active' ]
    then
        logi ' Done restart systemd service.'
    else
        logw ' (Re)start systemd service failed (maybe in docker?).'
    fi
elif [ "x`/proc/1/exe --version 2>&1 | grep -i upstart`" != "x" ] ||  type chkconfig >>/tmp/na_installer.log 2>&1
then
    # upstart / sysvlike init
    logi 'Installing node-agent startup script for sysvinit-like systems.'
    cat > /etc/init.d/node-agent  << EOF
#!/bin/bash
#
# node-agent    Swoole Enterprise NodeAgent
#
# chkconfig: 345 99 04
# description: Swoole Enterprise NodeAgent
#
# processname: swoole_php
# pidfile: /var/run/node-agent.pid
#
### BEGIN INIT INFO
# Provides: node-agent
# Required-Start: \$all
# Required-Stop: \$all
# Short-Description: node-agent
# Description:       Swoole Enterprise NodeAgent
### END INIT INFO

# Source function library
. /etc/rc.d/init.d/functions 2>&- ||
. /etc/init.d/functions 2>&- ||
. /lib/lsb/init-functions 2>&1

# Path to the supervisorctl script, server binary,
# and short-form for messages.
prog=node-agent
pidfile="/var/run/node-agent.pid"
lockfile="/var/lock/subsys/node-agent"
STOP_TIMEOUT=60
RETVAL=0

start() {
    echo -n "Starting \$prog... "
    [ -e \${lockfile} ] && echo already started && exit 1
    if [ "\`which start-stop-daemon 2>&- \`x" != "x" ]
    then
        start-stop-daemon --pidfile \${pidfile} --start --startas /bin/bash -- -c '/opt/swoole/script/php/swoole_php /opt/swoole/node-agent/src/node.php >>/opt/swoole/logs/node-agent_stdout.log 2>>/opt/swoole/logs/node-agent_stderr.log & echo -n \$! > '\${pidfile}
        RETVAL=\$?
    else
        daemon --pidfile \${pidfile} '/opt/swoole/script/php/swoole_php /opt/swoole/node-agent/src/node.php >>/opt/swoole/logs/node-agent_stdout.log 2>>/opt/swoole/logs/node-agent_stderr.log & echo -n \$! > '\${pidfile};
        RETVAL=\$?
    fi
    echo
    [ -d /var/lock/subsys ] && touch \${lockfile}
    return \$RETVAL
}

stop() {
    echo -n "Stopping \$prog... "
    [ -e \${pidfile} ] && /opt/swoole/script/php/swoole_php /opt/swoole/script/killtree.php \`cat \${pidfile}\`
    RETVAL=\$?
    echo
    [ \$RETVAL -eq 0 ] && rm -rf \${lockfile} \${pidfile}
}

restart() {
    stop
    start
}

case "\$1" in
    start)
        start
        ;;
    stop)
        stop
        ;;
    status)
        status -p \${pidfile} /opt/swoole/script/php/swoole_php
        ;;
    restart)
        restart
        ;;
    condrestart|try-restart)
        if status -p \${pidfile} /opt/swoole/script/php/swoole_php >&-; then
          restart
        fi
        ;;
    *)
        echo \$"Usage: \$prog {start|stop|restart|condrestart|try-restart}"
        RETVAL=2
esac

exit \$RETVAL
EOF
    chmod 755 /etc/init.d/node-agent
    /etc/init.d/node-agent stop >>/tmp/na_installer.log 2>&1
    /etc/init.d/node-agent restart
else
    logw 'Unable to determine init system type (maybe in docker?).'
    logw 'You can mannually add nodeagent into your init system (or docker entrypoint).'
fi
logi "Note: if you are using node-agent in docker,"
logi "\tmannually add  \`/opt/swoole/script/php/swoole_php /opt/swoole/node-agent/src/node.php\` into your entrypoint."
logi "Note: this script won't enable init script automatically,"
logi "\tuse \`systemctl enable node-agent\`(on systemd systems)"
logi "\tor \`rc-update add node-agent\`(on openrc systems) to enable it."

logi Done
