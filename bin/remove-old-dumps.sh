#!/bin/bash

args=("$@")
storage=${args[0]}

if [[ -z "${storage}" ]]; then
	echo "First arg must contain dumper data dir path"
	exit 1
fi

days=${args[1]}

if [[ -z "${days}" ]]; then
	echo "Second arg must contain amount of days after which you want to delete dumper data"
	exit 1
fi

for dir in `find $storage -maxdepth 1 -type d -mtime +${days}`; do
	rm $dir/* 2>/dev/null
	rmdir $dir
done