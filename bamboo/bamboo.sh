#!/bin/bash

CUR_DIR=$(cd $(dirname $0) && pwd);
PROJECT_PATH=`readlink -f "$CUR_DIR/../.."`

LIMB_DIR=`readlink -f "$CUR_DIR/.."`
LIMB_DIR_NAME=`basename "$LIMB_DIR"`

rm -rf $CUR_DIR/var/*

if [ "$LIMB_DIR_NAME" != "limb" ]; then
    mv $PROJECT_PATH/$LIMB_DIR_NAME $PROJECT_PATH/limb
fi

php runner.php

if [ "$LIMB_DIR_NAME" != "limb" ]; then
    mv $PROJECT_PATH/limb $PROJECT_PATH/$LIMB_DIR_NAME
fi
