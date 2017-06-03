#!/bin/bash
cd /home/nginx/www/amazon/xutl/queue/console
phpFile="QueueListener.php"

function start()
{
  stop
  php $phpFile &
  return 1
}

function stop()
{
  list=(`ps -aux|grep $phpFile|awk '{print $2}'`)
  lenth=$[${#list[@]} -1]
  for ((i=0;i<$lenth;i++))
  do
    kill -9 ${list[$i]}
  done
  return 1
}

if [ -n "$1" -a -f "$phpFile" -a -x "$phpFile" ]; then
 case $1 in
  "start")
   start
  ;;
	"stop")
    stop
  ;;
  "restart")
   stop
   start
  ;;
  *)
   echo "please input {start|restart|stop}"
  ;;
 esac
fi

exit 0
