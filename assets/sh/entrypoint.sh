#!/bin/bash

# script for docker init

echo -e "Configuring Logistics Develop Environment...\n"

echo "YII_ENV=dev" >> /etc/crontab

echo "export YII_ENV=\"dev\"" >> /etc/profile

source /etc/profile

appName="frameworks"

sh /www/${appName}/assets/sh/init.sh &

echo -e "Configuring Nginx\n"
cp /www/${appName}/assets/nginx_conf/*    /usr/local/nginx/conf/server/
service nginx restart > /dev/null

echo -e "Start Services\n"
/usr/local/php7/sbin/www-fpm start
/etc/init.d/supervisord start
/etc/init.d/rsyslog start
/etc/init.d/crond start
while true
do
    echo "hello world" > /dev/null
    sleep 6s
done


