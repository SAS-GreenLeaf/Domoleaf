#!/bin/bash
# run this script on the target arch
# bash build.sh

export LOCAL=`pwd`

# define environment
export BUILD_PATH=${LOCAL}/eibdbuild
export INSTALL_PREFIX=/usr

# clean up
rm -rf $BUILD_PATH
mkdir $BUILD_PATH

# create folders
cp -r pthsem $BUILD_PATH/
chmod +x $BUILD_PATH/pthsem/configure

cp -r bcusdk $BUILD_PATH/

# PTHSEM
cd $BUILD_PATH
cd pthsem
autoreconf -i
touch aclocal.m4 Makefile.in config.h.in configure
./configure --enable-static=yes --prefix=$INSTALL_PREFIX CFLAGS="-static -static-libgcc -static-libstdc++" LDFLAGS="-static -static-libgcc -static-libstdc++"
chmod +x ./shtool
make && make install

# Add pthsem library to libpath
export LD_LIBRARY_PATH=$INSTALL_PREFIX/lib:$LD_LIBRARY_PATH

# BUSSDK
cd $BUILD_PATH
cd bcusdk
libtoolize
aclocal
autoheader
autoconf
automake -a
autoreconf -i
touch aclocal.m4 Makefile.in config.h.in configure
./configure \
    --enable-onlyeibd \
    --enable-tpuarts \
    --enable-tpuart \
    --enable-ft12 \
    --enable-eibnetip \
    --enable-eibnetiptunnel \
    --enable-eibnetipserver \
    --enable-groupcache \
    --enable-static=yes \
    --prefix=$INSTALL_PREFIX CFLAGS="-static -static-libgcc -static-libstdc++" LDFLAGS="-static -static-libgcc -static-libstdc++ -s" CPPFLAGS="-static -static-libgcc -static-libstdc++"
#    --enable-static=yes --prefix=$INSTALL_PREFIX --with-pth=$INSTALL_PREFIX CFLAGS="-static -static-libgcc -static-libstdc++" LDFLAGS="-static -static-libgcc -static-libstdc++ -s" CPPFLAGS="-static -static-libgcc -static-libstdc++"
echo -e "[ \033[33mOK\033[0m ]./configure"
make
mkdir ${BUILD_PATH}/build
cd ${BUILD_PATH}

# Files to save
# LIB
cp bcusdk/eibd/client/c/.libs/libeibclient.a build/
cp bcusdk/eibd/client/c/libeibclient.la build/
cp /usr/lib/libpthsem.a build/
cp /usr/lib/libpthsem.la build/
cp pthsem/pthsem.pc build/

# BIN
cp bcusdk/eibd/server/eibd build/
cp bcusdk/eibd/examples/vbusmonitor1 build/
cp bcusdk/eibd/examples/groupwrite build/
cp bcusdk/eibd/examples/groupswrite build/
cp bcusdk/eibd/examples/groupread build/

echo "Copier le contenu du dossier build dans le sous-dossier de votre architecture dans le dossier bin"
