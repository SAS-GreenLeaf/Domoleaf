## @package domolib
# Library for domomaster and domoslave.
#
# Developed by GreenLeaf.

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
from Logger import *;

HOST_IP_FIELD = 'IP';
HOST_MAC_FIELD = 'MAC';

LOG_FILE = '/var/log/domoleaf/domoslave.log';

## Local network scanning class.
class Scanner:

    ## The constructor.
    def __init__(self, log_flag):
        self._HostList = [];
        self.log_flag = log_flag;
        self.logger = Logger(log_flag, LOG_FILE);

    ## Checks if a hostname is available on local network.
    #
    # @param hostname The hostname to check.
    #
    # @return True of False if the hostname is available.
    def isHostAvailable(self, hostname):
        names = [];
        append = names.append;
        for host in self._HostList:
            append(host._Hostname);
        return hostname in names;

    ## Adds a new host in the hostlist.
    #
    # @param host The host to add.
    # @return None
    def addNewHost(self, host):
        self._HostList.append(host);

    ## Builds and adds a new host in the hostlist.
    #
    # @param macAddr The MAC address of the new host.
    # @param ipAddr The IP address of the new host.
    # @param hostname The hostname of the new hsot.
    # @return None
    def addNewHost(self, macAddr, ipAddr, hostname):
        self._HostList.append(Host(macAddr, ipAddr, hostname.upper(), None, 0));

    ## Converts a netmask of the form 'xxx.xxx.xxx.xxx' to a 32 bit integer.
    #
    # @param netmask_bytes The address in the form xxx.xxx.xxx.xxx.
    #
    # @return The 32 bit integer containing the IP address.
    def bytesToMask(self, netmask_bytes):
        return (32 - int(round(math.log(0xFFFFFFFF - netmask_bytes, 2))));

    ## Converts a 4 bytes integer containing an IP address from an integer form to the CIDR form.
    #
    # @param network_bytes IP address in integer form.
    # @param netmask_bytes Netmask (has to be passed to bytesToMask() before treatment).
    #
    # @return The CIDR form of the address and its mask.
    def bytesToCIDR(self, network_bytes, netmask_bytes):
        network = scapy.utils.ltoa(network_bytes);
        netmask = self.bytesToMask(netmask_bytes);
        net = "%s/%s" % (network, netmask);
        if (netmask < 16):
            self.logger.info('[WARNING]: ' + net + ' Is too big. Skipping.')
            return None;
        return (net);

    ## Displays a list of the available hosts.
    #
    # @return None
    def printHosts(self):
        for h in self._HostList:
            self.logger.info("=== HOST ON NETWORK ===");
            self.logger.info("IP  : ", h._IpAddr);
            self.logger.info("MAC : ", h._MacAddr);
            self.logger.info("HOST: ", h._Hostname);
            self.logger.info('');

    ## Gets every hosts on the local network with ARP requests.
    #
    # @param net IP address under CIDR form.
    # @param interface Name of the used network interface.
    # @return None
    def getHosts(self, net, interface):
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
                self.logger.error('[ ERROR ]: socket: Permission Denied. Are you root ?');
            else:
                raise;
        self.addNewHost(macAddr = '', ipAddr = '127.0.0.1', hostname = socket.gethostname().upper());

    ## Scans the local network and gets all the MAC and IP of hosts.
    #
    # @return None
    def scan(self):
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
