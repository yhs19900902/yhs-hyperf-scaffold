#!/bin/bash
echo "start application in production mode"

module_name=""

base_dir=$(dirname $0)/

if [ -z "$1" ]; then
  EXEC_PHAR="$base_dir/$module_name.phar"
else
  EXEC_PHAR = $3
fi

mkdir -p "$base_dir"/runtime

PID=$(ps -ef |grep "${module_name}" |grep -v 'grep'|awk '{print $2}')

if [ "$PID" ]; then
  echo "Service is running, pid=$PID"
else
  nohup php $EXEC_PHAR start >> /data/runtime/catalina.out 2>&1 </dev/null &
  echo ">>>>>>start successfully!"
fi