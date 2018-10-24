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
        sudo chmod 777 ${rootPath}/${data}/runtime
    fi

    if [ -d "${rootPath}/${data}/web/assets" ];then
        sudo chmod 777 ${rootPath}/${data}/web/assets
    fi
done


# set file is executable
sudo chmod +x ${rootPath}/console/web/yii


# create log dir
if [ ! -d /www/privdata/${appName}/log ]; then
    sudo mkdir -p /www/privdata/${appName}/log
fi
sudo chmod 777 /www/privdata/${appName}/log

# soft link when it is dev environment
if [ "$YII_ENV" == "dev" ];then
    if [ ! -d "${rootPath}/assets/log" ]; then
        ln -s /www/privdata/${appName}/log ${rootPath}/assets/log
    fi
fi

echo "success"