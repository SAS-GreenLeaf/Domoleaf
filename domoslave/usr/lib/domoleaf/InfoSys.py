#!/usr/bin/python3

from subprocess import *
import os
import socket
import sys
sys.path.append('/usr/lib/domoleaf');
from DaemonConfigParser import *;

SLAVE_CONF_FILE                 = '/etc/domoleaf/slave.conf';

class InfoSys:
    
    def motherboardSerial():
        p = Popen(['dmidecode',  '-s', 'baseboard-serial-number'], stdin=PIPE, stdout=PIPE, stderr=PIPE, bufsize=-1);
        serial, error = p.communicate();
        serial=serial.decode()
        
        if serial == "":
            serial = os.popen("cat /proc/cpuinfo | grep Serial | awk ' {print $3}'").read().split('\n')[0];
        if serial == "":
            serial = 'unknown';
        return serial
    
    def diskDetect():
        disk = os.popen("df / | tail -n 1 | awk '{print $1}'").read().split('\n')[0];
        disk = disk.rstrip('[0-9]')
        
        if disk[0:7] == "/dev/sd":
            return disk
        
        disk = os.popen("cat /etc/fstab | grep ' / ' | awk '{print $1}'").read().split('\n')[0];
        
        if disk[0:11] == "/dev/mmcblk":
            return disk[0:-2]
        
        return 'unknown'
    
    def diskSerial():
        disk = InfoSys.diskDetect()
        if disk[0:11] == "/dev/mmcblk":
            return os.popen("udevadm info -a -n "+disk+" | grep -i cid | awk -F \\\" '{print $2}'").read().split('\n')[0];
        elif disk[0:7] == "/dev/sd":
            serial = os.popen("hdparm -i /dev/sda | grep -oE 'SerialNo=.*'").read().split('\n')[0];
            return serial[9:]
        return ""
    
    def softMaster():
        file = open('/etc/domoleaf/.domomaster.version', 'r');
        return file.read().split('\n')[0];
    
    def softSlave():
        file = open('/etc/domoleaf/.domoslave.version', 'r');
        return file.read().split('\n')[0];
    
    def softKNX():
        version = os.popen("apt show knxd | grep Version").read().split('\n')[0];
        return version[8:].strip()
    
    def uptime():
        p = Popen(['cat', '/proc/uptime'], stdin=PIPE, stdout=PIPE, stderr=PIPE, bufsize=-1);
        time, error = p.communicate();
        time=time.decode()
        return str(time).split('.')[0];
    
    def ipPrivate():
        ip = ([(s.connect(('8.8.8.8', 80)), s.getsockname()[0], s.close()) for s in [socket.socket(socket.AF_INET, socket.SOCK_DGRAM)]][0][1])
        return ip
    
    def ipVPN():
        private = ipPrivate();
        parser = DaemonConfigParser(SLAVE_CONF_FILE);
        server = parser.getValueFromSection('openvpn', 'openvpnserver').encode()
        
        if server == 'none':
            return '';
        
        vpn = ([(s.connect((server, 80)), s.getsockname()[0], s.close()) for s in [socket.socket(socket.AF_INET, socket.SOCK_DGRAM)]][0][1])
        
        if private != vpn:
            return vpn;
        
        return '';
    
    def temperature():
        p = Popen(['cat', '/sys/class/thermal/thermal_zone0/temp'], stdin=PIPE, stdout=PIPE, stderr=PIPE, bufsize=-1);
        temperature, error = p.communicate();
        return temperature.decode().split('\n')[0];
