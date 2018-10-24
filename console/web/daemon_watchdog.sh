#!/bin/sh
source /etc/profile
CURRENT_PATH=$(cd "$(dirname "$0")"; pwd)
cmd="php $CURRENT_PATH/daemon.php > /dev/null &"
proc=`ps ax | grep -v " grep" | grep "$CURRENT_PATH/daemon.php"`
if test -z "$proc"
then
  eval "$cmd"
fi
