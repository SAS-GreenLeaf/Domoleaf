## @package domolib
# Library for domomaster and domoslave.
#
# Developed by GreenLeaf.

## Class describing a host.
class Host:

    ## The constructor.
    #
    # @param macAddr The MAC address of the host (default '').
    # @param ipAddr The IP address of the host (default '').
    # @param hostname The hostname (default '').
    # @param socket The socket to comminucate with the host (default None).
    # @param Id The ID of the host (default 0).
    def __init__(self, macAddr = '', ipAddr = '', hostname = '', socket = None, Id = 0):
        self._MacAddr = macAddr;
        self._IpAddr = ipAddr;
        self._Hostname = hostname;
        self._Socket = socket;
        self._Id = Id;
