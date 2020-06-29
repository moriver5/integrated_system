#/bin/bash
PROJCT=$1
DOMAIN=$2
DIR=/data/www/release/
WWW=/data/www/
STORAGE=/data/www/storage/
SUDO_USER=moritomo
IMAGES=/storage/app/public/$DOMAIN/images
if [ -z "$PROJCT" ]; then
  echo "プロジェクトを指定してください"
  exit
fi
echo "$SUDO_USER SVNのパスワードを入力してください"
read -sp "Password: " pass
if [ -z "$pass" ]; then
  echo "パスワードを入力してください"
  exit
fi

cd ${DIR}/${PROJCT}/master
svn --non-interactive --username $SUDO_USER --password $pass up
if [ "$?" != "0" ]
then
   echo "SVN UPDATE に失敗しました\n"
   exit
fi
RELEASE_VER=`LANG=C svn info |grep Revision:|cut -f 2  --delim=" "`
if [ -e "${DIR}${PROJCT}/release_$RELEASE_VER" ]; then
 echo "${DIR}${PROJCT}/release_$RELEASE_VER ディレクトリは既にありますアップデートできません"
 exit
fi
rsync -pogr --exclude-from=${DIR}${PROJCT}/master/rsync_exclude.txt --delete-after ${DIR}${PROJCT}/master/ ${DIR}${PROJCT}/release_$RELEASE_VER
if [ "$?" != "0" ]
then
   echo "Rsyncに失敗しました\n"
   exit

else
   if [ -e "${STORAGE}${PROJCT}/upload_images" ]; then
     ln -sn ${STORAGE}${PROJCT}/upload_images ${DIR}${PROJCT}/release_$RELEASE_VER/public/upload_images
   else
     echo "${STORAGE}${PROJCT}/upload_imagesがありません"
   fi
   if [ -e "${STORAGE}${PROJCT}/storage/app/public/${DOMAIN}/LP" ]; then
     ln -sn ${STORAGE}${PROJCT}/storage/app/public/${DOMAIN}/LP ${DIR}${PROJCT}/release_$RELEASE_VER/public/${DOMAIN}/
   else
     echo "${STORAGE}${PROJCT}/storage/app/public/${DOMAIN}/LPがありません"
   fi
   if [ -e "${STORAGE}${PROJCT}/storage" ]; then
     ln -sn ${STORAGE}${PROJCT}/storage ${DIR}${PROJCT}/release_$RELEASE_VER/storage
   else
     echo "${STORAGE}${PROJCT}/storageがありません"
   fi
   if [ -e "${STORAGE}${PROJCT}/storage/app/public/banner" ]; then
     ln -sn ${STORAGE}${PROJCT}/storage/app/public/banner ${DIR}${PROJCT}/release_$RELEASE_VER/public/
   else
     echo "${STORAGE}${PROJCT}/storage/app/public/bannerがありません"
   fi
   if [ -e "${STORAGE}${PROJCT}/top_content" ]; then
     ln -sn ${STORAGE}${PROJCT}/top_content ${DIR}${PROJCT}/release_$RELEASE_VER/public/top_content
   else
     echo "${STORAGE}${PROJCT}/top_contentがありません"
   fi
   if [ -e "${STORAGE}${PROJCT}/images" ]; then
     ln -sn ${STORAGE}${PROJCT}/images ${DIR}${PROJCT}/release_$RELEASE_VER/public/
   else
     echo "${STORAGE}${PROJCT}/imagesがありません"
   fi
   if [ -e "${STORAGE}${PROJCT}${IMAGES}" ]; then
     ln -sn ${STORAGE}${PROJCT}${IMAGES} ${DIR}${PROJCT}/release_$RELEASE_VER/public/$DOMAIN/
   else
     echo "${STORAGE}${PROJCT}${IMAGES}がありません"
   fi
   cd ${DIR}${PROJCT}/release_$RELEASE_VER
   /usr/local/bin/composer install --optimize-autoloader --no-dev
   /usr/local/bin/composer dump-autoload --optimize
   php artisan clear-compiled
   php artisan optimize
   php artisan config:cache
   php artisan route:cache
   php artisan view:clear
   php artisan env --env=production
   ln -nfs ${DIR}${PROJCT}/release_$RELEASE_VER ${WWW}/${PROJCT}_pre

DIR_COUNT=`ls -dtr  ${DIR}${PROJCT}/release* | wc -l`
OLD_DIR=`ls -dtr ${DIR}${PROJCT}/release*| head -1`
   if [ $DIR_COUNT > 3 ]; then
     echo "Delete ${OLD_DIR}"
     if [ -e "${OLD_DIR}" ]; then
       rm -rf ${OLD_DIR}
     fi
   fi
   echo "deploy complete!"
fi
