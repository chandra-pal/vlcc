#! /bin/bash

#Script to sort logs
#Written by Chandra Pal

Red='\033[0;31m'          # Red
Green='\033[0;32m'        # Green
Yellow='\033[0;33m'       # Yellow
Purple='\033[0;35m'       # Purple
Cyan='\033[0;36m'         # Cyan


read -p "Enter Total hits "  hits
read -p "Enter Total time is Seconds " time

cat access_logs | sort $6 |awk '{print $2 $6}' | uniq -c | sort -nr


echo -e "$Red Sorting the log files by different IP Address......$Green"
cat access_logs | awk '{ print $2 }' | sort | uniq -c | sort -nr	