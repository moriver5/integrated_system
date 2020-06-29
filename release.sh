#/bin/bash
PROJCT=$1
DIR=/data/www/release/
WWW=/data/www/
if [ -z "$PROJCT" ]; then
  echo "プロジェクトを指定してください"
  exit
fi
cd ${DIR}/${PROJCT}/master
RELEASE_VER=`LANG=C svn info |grep Revision:|cut -f 2  --delim=" "`
echo "$PROJCTの$RELEASE_VER を本番環境に反映させます"
read -p "[yes no]: " pass
if [ "$pass" = "yes" ]; then
  if [ -e "${DIR}${PROJCT}/release_$RELEASE_VER" ]; then
   ln -nfs ${DIR}${PROJCT}/release_$RELEASE_VER ${WWW}/${PROJCT}
   echo "反映しました"
 else
   echo "${DIR}${PROJCT}/release_$RELEASE_VER ディレクトリがありません反映できません"
fi
else
 echo "反映しませんでした"
fi