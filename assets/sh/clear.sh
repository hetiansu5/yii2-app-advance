#!/bin/bash

# script for clearing cache,log...

# cd root path
rootPath=$(cd `dirname $0`/../../; pwd)

# clear log and file cache
arr=(
    "backend" "api" "console"
)

for data in ${arr[@]}
do
    if [ -d "${rootPath}/${data}/runtime" ];then
        rm -rf ${rootPath}/${data}/runtime/*
    fi

     if [ -d "${rootPath}/${data}/web/assets" ];then
         rm -rf ${rootPath}/${data}/web/assets/*
     fi
done