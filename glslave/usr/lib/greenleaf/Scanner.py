from __future__ import absolute_import, division, print_function;
import scapy.config;
import scapy.layers.l2;
import scapy.route;
import socket;
import math;
import errno;
import sys;
import os;
sys.path.append("/usr/lib/greenleaf")
from Host import *;
import configparser;

HOST_IP_FIELD = 'IP';
HOST_MAC_FIELD = 'MAC';

class Scanner:
    """
    Local network scanning class.
    """
    def __init__(self, host_conf):
        self._HostList = [];
        self._host_conf_file = host_conf;

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

    def writeHostsToFile(self):
        """
        Cette fonction parcoure la liste d'hotes et les ecrit dans le fichier
        hosts.conf dans le dossier courant du programme appelant le Scanner.
        Ce fichier est sous forme de fichier de configuration.
        """
        config = configparser.ConfigParser();
        for host in self._HostList:
            config[host._Hostname.upper()] = {};
            config[host._Hostname.upper()][HOST_IP_FIELD] = host._IpAddr;
            config[host._Hostname.upper()][HOST_MAC_FIELD] = host._MacAddr;
        with open(self._host_conf_file, 'w') as configfile:
            config.write(configfile);

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
                    print('Adding new host');
                    print("IP  : " + ipAddr);
                    print("MAC : " + macAddr);
                    print("HOST: " + hostname);
                    print('');
                    self.addNewHost(macAddr, ipAddr, hostname.upper());
                except socket.herror:
                    pass;
        except socket.error as e:
            if (e.errno == errno.EPERM):
                print('[ ERROR ]: socket: Permission Denied. Are you root ?');
            else:
                raise;
        self.addNewHost(macAddr = '', ipAddr = '127.0.0.1', hostname = socket.gethostname().upper());
        self.writeHostsToFile();

    def getHostsFromFile(self):
        """
        Retrieves the hosts stored in hosts.conf file passed in parameter at construct.
        """
        config = configparser.ConfigParser();
        if not config.read(self._host_conf_file):
            return False;
        for section in config.sections():
            if HOST_IP_FIELD not in config[section]:
                raise Exception("'" + HOST_IP_FIELD + "' is not defined in section '" + section + "' in file " + self._host_conf_file);
            elif HOST_MAC_FIELD not in config[section]:
                raise Exception("'" + HOST_MAC_FIELD + "' is not defined in section '" + section + "' in file " + self._host_conf_file);
            host = Host(macAddr = config[section][HOST_MAC_FIELD], ipAddr = config[section][HOST_IP_FIELD], hostname = section, socket = None, Id = 0);
            self._HostList.append(host);
        return True;

    def scan(self, rewrite):
        """
        Hosts retrieving.
        'rewrite' is a boolean which defines if the network has to be scanned again.
        - True => Erase existing hosts conf file and scan network again
        - False => If hosts conf file exists, reads the file and retrieves hosts.
        """
        if rewrite:
            os.remove(self._host_conf_file);
        print('#########################');
        print('# LOCAL NETWORK SCANNER #');
        print('#########################');
        print('[ .. ] Scanning network...');
        if not self.getHostsFromFile():
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
                    # Essayer de ne pas mettre le break
                    break;
        print('[ OK ]: Done scanning network.');
