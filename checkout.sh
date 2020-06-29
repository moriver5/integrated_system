#/bin/bash
DIR=/data/www/release/
#DIR=/data/tmp/
STORAGE=/data/www/storage/
PROJCT=$1
if [ -z "$PROJCT" ]; then
  echo "プロジェクトを指定してください"
  exit
fi
if [ -e "${DIR}${PROJCT}" ]; then
 echo "${DIR}${PROJCT} ディレクトリは既にありますチェックアウトできません"
 exit
fi
echo "$SUDO_USER SVNのパスワードを入力してください"
read -sp "Password: " pass
if [ -z "$pass" ]; then
  echo "パスワードを入力してください"
  exit
fi
mkdir -p $DIR/$PROJCT
svn --no-auth-cache  --username $SUDO_USER --password $pass --config-option servers:global:http-library=serf co https://svn.sea-soft.net/svn/php/$PROJCT/trunk  $DIR/$PROJCT/master
if [ "$?" != "0" ]
then
   echo "SVN に失敗しました\n"
   exit
fi
mkdir -p $STORAGE/$PROJCT
mkdir -p $STORAGE/$PROJCT/upload_images
mkdir -p $STORAGE/$PROJCT/LP
mkdir -p $STORAGE/$PROJCT/storage
mkdir -p $STORAGE/$PROJCT/top_content
mkdir -p $STORAGE/$PROJCT/storage/app
mkdir -p $STORAGE/$PROJCT/storage/framework
mkdir -p $STORAGE/$PROJCT/storage/logs