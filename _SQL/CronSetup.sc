To execute php script every 1 hour do the following:
# crontab -e
# select nano editor
00 * * * * /var/www/html/SLupload/php/ckExpiredFiles.php
