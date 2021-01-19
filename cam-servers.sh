#!/bin/bash

if [ "$1" == "start" ]; then
    for entry in "./_cam_servers/rtsp"/*.js
    do
    ./node_modules/pm2/bin/pm2 start "$entry"
    done
fi

if [ "$1" == "stop" ]; then
    ./node_modules/pm2/bin/pm2 stop all
fi

if [ "$1" == "delete" ]; then
    ./node_modules/pm2/bin/pm2 delete all
fi

if [ "$1" == "status" ]; then
    ./node_modules/pm2/bin/pm2 list
fi

if [ "$1" == "restart" ]; then
    ./node_modules/pm2/bin/pm2 delete all
    for entry in "./_cam_servers/rtsp"/*.js
    do
    ./node_modules/pm2/bin/pm2 start "$entry"
    done
fi