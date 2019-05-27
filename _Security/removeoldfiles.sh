#!/bin/bash
echo '*****' >> del.log
date >> del.log
find /var/www/html/SLupload/uploads -type f -ctime +2 -exec rm -rf {} +  >> del.log
