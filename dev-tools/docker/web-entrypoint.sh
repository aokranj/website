#!/bin/bash



### Configure shell
#
set -e
set -u



### Determine the file ownership, create appropriate user and group
#
cd /var/www/docker.dev.aokranj.com
APPUID=`stat -c '%u' .`
APPGID=`stat -c '%g' .`
echo "DOCKER :: WEB :: Getting app uid and gid (uid:$APPUID, gid:$APPGID)"


APPGROUP=`cat /etc/group | grep -E "^[^:]+:x:$APPGID:" | grep -Eo '^[^:]+'` || true
if [ "$APPGROUP" == "" ]; then
    echo "DOCKER :: WEB :: Creating group for gid $APPGID"
    APPGROUP="group$APPGID"
    groupadd -g $APPGID "$APPGROUP"
fi

APPUSER=`cat /etc/passwd | grep -E "^[^:]+:x:$APPUID:" | grep -Eo '^[^:]+'` || true
if [ "$APPUSER" == "" ]; then
    echo "DOCKER :: WEB :: Creating user for uid $APPUID"
    APPUSER="user$APPUID"
    useradd -M -N -g $APPGID -u $APPUID "$APPUSER"
fi



### Wait for mysql service to become available
#
while ! nc -z mysql 3306; do
    echo "DOCKER :: WEB :: Waiting for MySQL service to become available"
    sleep 0.2;
done;
echo "DOCKER :: WEB :: Excellent, MySQL is up!"



### Run the app init script
#
echo "DOCKER :: WEB :: Running the ./deplog-here.sh script"
sudo -u $APPUSER -- ./sbin/deploy-here



### Start the apache
#
# Handle the ctrl+c (sigterm) signal from the console and shut it down "instantly"
#
_handleSigTerm() {
  echo "DOCKER :: WEB :: Caught SIGTERM signal, passing it to child $childPid"
  kill -TERM "$childPid" 2>/dev/null
}
trap _term SIGTERM


# Start the apache in the foreground (docker mode) but push it into background to enable
# handling of SIGTERM signal
echo "DOCKER :: WEB :: Starting apache..."
/usr/sbin/apache2ctl -D FOREGROUND &
childPid=$!

sleep 3 && echo "DOCKER :: WEB :: The app is waiting for you at http://docker.dev.aokranj.com:8000/" &
wait "$childPid"
