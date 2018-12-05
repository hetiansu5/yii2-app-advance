#!/bin/bash

appName="frameworks"


# cd root path
rootPath=$(cd `dirname $0`/../../; pwd)


# composer vendor
if [[ "$*" =~ "-i" ]]; then
    echo "ignore composer"
else
    if [ "$YII_ENV" == "dev" ];then
        composer update -o --no-plugins --no-scripts
    else
        composer update -o --no-dev --no-plugins --no-scripts
    fi
fi


# modify directory access
arr=(
    "backend" "api" "console"
)

for data in ${arr[@]}
do
    if [ -d "${rootPath}/${data}/runtime" ];then
        chmod 777 ${rootPath}/${data}/runtime
    fi

    if [ -d "${rootPath}/${data}/web/assets" ];then
        chmod 777 ${rootPath}/${data}/web/assets
    fi
done


# set file is executable
chmod +x ${rootPath}/console/web/yii


# soft link when it is dev environment
if [ "$YII_ENV" == "dev" ];then
    if [ ! -d "${rootPath}/assets/log" ]; then
        mkdir -p ${rootPath}/assets/log
        ln -s ${rootPath}/assets/log /www/privdata/${appName}/log
    fi
else
    # create log dir
    if [ ! -d /www/privdata/${appName}/log ]; then
        mkdir -p /www/privdata/${appName}/log
    fi
    chmod 777 /www/privdata/${appName}/log
fi

echo "success"