set -e
cd /home/mlm/public_html/multi-level-marketing
rm -rf MLM/
rm -rf app/
rm -rf bootstrap/
rm -rf config/
rm -rf database/
rm -rf routes/
rm -rf User/
rm -rf resources/
#git clone git@github.com:Ride-To-The-Future/subscriptions.git
#cp -r subscriptions/* .
#rm -rf subscriptions/
shopt -s dotglob
rsync -rv --exclude=.git temp-project/* .
rm -rf temp-project/
cp -u .env.staging  .env
#composer dumpautoload
composer config --global --auth http-basic.ride-to-the-future.repo.repman.io token 67001fefcf70038c817987b7431f2d17498dc5c2409b4748e51cad87a69b8567
composer update

# Update codebas
#chmod 777 .* -R
#chown -R root:root .
php artisan key:generate
php artisan vendor:publish --all
php artisan migrate:fresh
php artisan db:seed
php artisan scribe:generate
php artisan optimize:clear
php artisan queue:restart
# Exit maintenance mode
#php artisan up
echo "Application deployed!"