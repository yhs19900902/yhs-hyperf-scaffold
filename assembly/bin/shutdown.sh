#!/bin/bash
echo "shutdown application in production mode"

module_name=""

PID=$(ps -ef |grep "${module_name}" |grep -v 'grep'|awk '{print $2}')
#echo $PID

if [ ${PID} ]; then
  kill $PID
  sleep 3
  PID=$(ps -ef |grep "${module_name}" |grep -v 'grep'|awk '{print $2}')
  if [ ${PID} ]; then
    kill -9 $PID
    sleep 1
    echo "[$module_name] process shutdown by kill -9 $PID"
  fi
fi

echo ">>>>>>shutdown successfully!"