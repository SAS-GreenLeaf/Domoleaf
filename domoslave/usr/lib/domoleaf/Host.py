class Host:
    def __init__(self, macAddr = '', ipAddr = '', hostname = '', socket = None, Id = 0):
        self._MacAddr = macAddr;
        self._IpAddr = ipAddr;
        self._Hostname = hostname;
        self._Socket = socket;
        self._Id = Id;
