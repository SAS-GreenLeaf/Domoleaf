# Compile Deb files
#
# VERSION               0.0.1
#

FROM     debian:jessie
MAINTAINER Virgil "vr@greenleaf.fr"

ENV DEBIAN_FRONTEND noninteractive

#Update debian
RUN (apt-get update && apt-get upgrade -y -q && apt-get dist-upgrade -y -q && apt-get -y -q autoclean && apt-get -y -q autoremove)

#Install packages
RUN apt-get install nano screen -y
RUN apt-get install make gcc gcc-arm-none-eabi g++ libconfig-dev autoconf libtool libxml2-dev -y

#Copy directories
ADD glmaster /root/mastercommand/glmaster
ADD glslave /root/mastercommand/glslave
ADD monitor_enocean /root/mastercommand/monitor_enocean
ADD monitor_knx /root/mastercommand/monitor_knx
ADD www /root/mastercommand/www
ADD check_compiler /root/mastercommand/check_compiler
ADD Makefile /root/mastercommand/Makefile
