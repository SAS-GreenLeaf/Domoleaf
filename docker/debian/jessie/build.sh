SYS="debian"
DISTRIB="jessie"
NAME="domoleaf${SYS}${DISTRIB}"
CURRENT=${PWD##*/} 
if [ $CURRENT = $DISTRIB ] ; then
	cd ../../../
fi
cp docker/$SYS/$DISTRIB/dockerfile .
docker build -t $NAME .
rm -f dockerfile
docker run --name $NAME -itd $NAME
for file in $(docker exec $NAME ls /root/ | grep .deb$) ; do
	docker cp $NAME:/root/$file .
done
sleep 1
docker stop $NAME
docker rm $NAME
