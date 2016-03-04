from __future__ import absolute_import, division, print_function;
import scapy.config;
import scapy.layers.l2;
import scapy.route;
import socket;
import math;
import errno;
import sys;
import os;
sys.path.append("/usr/lib/domoleaf")
from Host import *;
import configparser;

HOST_IP_FIELD = 'IP';
HOST_MAC_FIELD = 'MAC';

class Scanner:
    """
    Local network scanning class.
    """
    def __init__(self):
        self._HostList = [];

    def isHostAvailable(self, hostname):
        """
        Takes an 'hostname' as parameter and return whether or not the host 'hostname' is on the local network.
        """
        names = [];
        for host in self._HostList:
            names.append(host._Hostname);
        return hostname in names;

    def addNewHost(self, host):
        """
        Add the host 'host' to the hostlist.
        """
        self._HostList.append(host);

    def addNewHost(self, macAddr, ipAddr, hostname):
        """
        Creates a new host with 'macAddr', 'ipAddr', 'hostname', and adds it to the hostlist
        """
        self._HostList.append(Host(macAddr, ipAddr, hostname.upper(), None, 0));

    def bytesToMask(self, netmask_bytes):
        """
        Converts a netmask of the form 'xxx.xxx.xxx.xxx' to a 32 bit integer.
        """
        return (32 - int(round(math.log(0xFFFFFFFF - netmask_bytes, 2))));

    def bytesToCIDR(self, network_bytes, netmask_bytes):
        """
        Converts a 4 bytes integer containing an IP address to the CIDF form (e.g) '192.168.1.1'
        'network_bytes' IP address stored in an integer
        'netmask_bytes' Netmask (has to be passed to self.bytesToMask() before treatment)
        """
        network = scapy.utils.ltoa(network_bytes);
        netmask = self.bytesToMask(netmask_bytes);
        net = "%s/%s" % (network, netmask);
        if (netmask < 16):
            print('[WARNING]: ' + net + ' Is too big. Skipping.');
            return None;
        return (net);

    def printHosts(self):
        """
        Print of the hostlist on the screen (useful for debug)
        """
        for h in self._HostList:
            print("=== HOST ON NETWORK ===");
            print("IP  : " + h._IpAddr);
            print("MAC : " + h._MacAddr);
            print("HOST: " + h._Hostname);
            print('');

    def getHosts(self, net, interface):
        """
        Retrieving every hosts on the local network with ARP requests.
        'net' IP under the CIDR form (e.g) '192.168.1.1/24'
        'interface' Name of the used network interface
        """
        try:
            ans, unans = scapy.layers.l2.arping(net, iface = interface, timeout = 1, verbose = False);
            for s, r in ans.res:
                macAddr = r.sprintf("%Ether.src%");
                ipAddr = r.sprintf("%ARP.psrc%");
                try:
                    hostnames = socket.gethostbyaddr(r.psrc);
                    hostname = hostnames[0];
                    self.addNewHost(macAddr, ipAddr, hostname.upper());
                except socket.herror:
                    pass;
        except socket.error as e:
            if (e.errno == errno.EPERM):
                print('[ ERROR ]: socket: Permission Denied. Are you root ?');
            else:
                raise;
        self.addNewHost(macAddr = '', ipAddr = '127.0.0.1', hostname = socket.gethostname().upper());

    def scan(self):
        """
        Hosts retrieving.
        """
        for network, netmask, _, interface, address in scapy.config.conf.route.routes:
            if (network == 0 or
                interface == 'lo' or
                address == '127.0.0.1' or
                address == '0.0.0.0' or
                netmask <= 0 or
                netmask == 0xFFFFFFFF or
                interface != scapy.config.conf.iface):
                continue;
            net = self.bytesToCIDR(network, netmask);
            if (net):
                self.getHosts(net, interface);
                # TOFIX
                break;
