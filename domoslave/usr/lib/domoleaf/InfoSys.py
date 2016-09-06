#!/usr/bin/python3

from subprocess import *
import os
import socket
import sys
sys.path.append('/usr/lib/domoleaf');
from DaemonConfigParser import *;

SLAVE_CONF_FILE                 = '/etc/domoleaf/slave.conf';

"""
Class representing the local system informations
"""
class InfoSys:

    def motherboardSerial():
        """
        Return the serial of the motherboard
        """
        p = Popen(['dmidecode',  '-s', 'baseboard-serial-number'], stdin=PIPE, stdout=PIPE, stderr=PIPE, bufsize=-1);
        serial, error = p.communicate();
        serial=serial.decode()
        if not serial:
            serial = os.popen("cat /proc/cpuinfo | grep Serial | awk ' {print $3}'").read().split('\n')[0];
        if not serial:
            serial = 'unknown';
        return serial

    def diskDetect():
        """
        Return the disk informations
        """
        disk = os.popen("df / | tail -n 1 | awk '{print $1}'").read().split('\n')[0];
        disk = disk.rstrip('[0-9]')
        if disk[0:7] == "/dev/sd":
            return disk
        disk = os.popen("cat /etc/fstab | grep ' / ' | awk '{print $1}'").read().split('\n')[0];
        if disk[0:11] == "/dev/mmcblk":
            return disk[0:-2]
        return 'unknown'

    def diskSerial():
        """
        Return the disk serial
        """
        disk = InfoSys.diskDetect()
        if disk[0:11] == "/dev/mmcblk":
            return os.popen("udevadm info -a -n "+disk+" | grep -i cid | awk -F \\\" '{print $2}'").read().split('\n')[0];
        elif disk[0:7] == "/dev/sd":
            serial = os.popen("hdparm -i "+disk+" | grep -oE 'SerialNo=.*'").read().split('\n')[0];
            return serial[9:]
        return ""

    def softMaster():
        """
        Return the informations about the master daemon if installed on the system
        """
        return os.popen("dpkg-query -W -f='${Version}\n' domomaster").read().split('\n')[0];

    def softSlave():
        """
        Return the informations about the slave daemon if installed on the system
        """
        return os.popen("dpkg-query -W -f='${Version}\n' domoslave").read().split('\n')[0];

    def softKNX():
        """
        Return the informations about the KNX daemon if installed on the system
        """
        return os.popen("dpkg-query -W -f='${Version}\n' knxd").read().split('\n')[0];

    def uptime():
        """
        Get the uptime of the system
        """
        p = Popen(['cat', '/proc/uptime'], stdin=PIPE, stdout=PIPE, stderr=PIPE, bufsize=-1);
        time, error = p.communicate();
        time=time.decode()
        return str(time).split('.')[0];

    def ipPrivate():
        """
        Return the IP address of the network interface
        """
        try:
            ip = ([(s.connect(('8.8.8.8', 80)), s.getsockname()[0], s.close()) for s in [socket.socket(socket.AF_INET, socket.SOCK_DGRAM)]][0][1])
        except Exception as e:
            ip = ''
        return ip

    def ipv6():
        """
        Return the IPv6 address of the network interface
        """
        try:
            ip = ([(s.connect(('google.com', 80)), s.getsockname()[0], s.close()) for s in [socket.socket(socket.AF_INET6, socket.SOCK_DGRAM)]][0][1])
        except Exception as e:
            ip = ''
        return ip

    def ipVPN():
        """
        Return informations about the VPN
        """
        private = InfoSys.ipPrivate();
        parser = DaemonConfigParser(SLAVE_CONF_FILE);
        server = parser.getValueFromSection('openvpn', 'openvpnserver').encode()
        if server.decode() == 'none':
            return '';
        try:
            vpn = ([(s.connect((server, 80)), s.getsockname()[0], s.close()) for s in [socket.socket(socket.AF_INET, socket.SOCK_DGRAM)]][0][1])
        except Exception as e:
            return ''
        if private != vpn:
            return vpn;
        return '';

    def temperature():
        """
        Return the temperature of the proc
        """
        p = Popen(['cat', '/sys/class/thermal/thermal_zone0/temp'], stdin=PIPE, stdout=PIPE, stderr=PIPE, bufsize=-1);
        temperature, error = p.communicate();
        return temperature.decode().split('\n')[0];
