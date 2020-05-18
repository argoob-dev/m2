sudo setfacl -R -m g:bitnami:rwx /home/bitnami/apps/magento/htdocs/
rm -r /home/bitnami/apps/magento/htdocs/var/*
rm -r /home/bitnami/apps/magento/htdocs/pub/static/*
rm -r /home/bitnami/apps/magento/htdocs/generated/*
cd /home/bitnami/apps/magento/htdocs
sudo bin/magento setup:upgrade
#php bin/magento setup:static-content:deploy -f
sudo bin/magento deploy:mode:set production
sudo bin/magento setup:static-content:deploy -f ar_MA
sudo bin/magento indexer:reindex
#cd /home/dev2
#n98-magerun2 varnish:vcl:generate > /home/bitnami/apps/magento/htdocs/var/default.vcl
#cp /home/dev2/default.vcl /home/bitnami/apps/magento/htdocs/var
##sudo setfacl -R -m g:daemon:rwx public_html/
sudo setfacl -R -m g:daemon:rwx /home/bitnami/apps/magento/htdocs/app/etc
sudo setfacl -R -m g:daemon:rwx /home/bitnami/apps/magento/htdocs/var
sudo setfacl -R -m g:daemon:rwx /home/bitnami/apps/magento/htdocs/pub/media
sudo setfacl -R -m g:daemon:rwx /home/bitnami/apps/magento/htdocs/pub/static
sudo setfacl -R -m g:daemon:rwx /home/bitnami/apps/magento/htdocs/generated

