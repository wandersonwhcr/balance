#!/usr/bin/env bash

dpkg -s 'puppet' >/dev/null 2>&1
if [ ! $? -eq 0 ]; then
    apt-get update
    apt-get install puppet -y
fi
