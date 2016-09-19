## @package domolib
# Library for domomaster and domoslave.
#
# Developed by GreenLeaf.

import pycurl;
from io import BytesIO;

## Base class for communicating with an Upnp device.
class Upnp:

    ## The constructor.
    #
    # @param ip_addr The IP address of the Upnp device.
    # @param port The port on which the Upnp device is listenning.
    def __init__(self, ip_addr, port):
        self.ip = ip_addr;
        self.port = port;

    ## Builds a request to send, and sends it.
    #
    # @param command The command to send.
    # @param args The arguments for the command.
    #
    # @return The response to the request (some request don't have return values, be careful).
    def send_request(self, command, args):
        control_type = self.get_type(command);
        request = ''.join(['<s:Envelope xmlns:s="http://schemas.xmlsoap.org/soap/envelope/" s:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/">',
                       "<s:Body>", '<u:', command, ' xmlns:u="urn:schemas-upnp-org:service:', self.get_type(command), ':1">']);
        for key in args.keys():
            request += "<"+str(key)+">"+str(args[key])+"</"+str(key)+">";
        request += "</u:"+command+">"+"</s:Body>"+"</s:Envelope>";
        ch = pycurl.Curl();
        ch.setopt(ch.SSL_VERIFYPEER, 0);
        ch.setopt(ch.SSL_VERIFYHOST, 0);
        ch.setopt(ch.URL, "http://"+self.ip+":"+str(self.port)+"/MediaRenderer/"+str(control_type)+"/Control");
        ch.setopt(ch.HEADER, 0);
        ch.setopt(ch.TIMEOUT, 300);
        ch.setopt(ch.HTTPHEADER, ['Content-Type: text/xml',
                                  'SOAPAction: "urn:schemas-upnp-org:service' +
                                  str(control_type) + ':1#' + command + '"']);
        ch.setopt(ch.POST, 1);
        ch.setopt(ch.POSTFIELDS, request);
        res = BytesIO();
        ch.setopt(ch.WRITEDATA, res);
        ch.perform();
        ch.close();
        return (res.getvalue().decode('utf-8'));


    ## Gets control type of the device.
    #
    # @param control_type The type of command to be checked.
    #
    # @return The control type of the device.
    def get_type(self, control_type):
        if control_type == 'Play' or control_type == 'Pause' or control_type == 'Stop' or control_type == 'Next' or control_type == 'Previous' or control_type == 'Record':
            return 'AVTransport';
        elif control_type == 'GetMute' or control_type == 'SetMute' or control_type == 'GetVolume' or control_type == 'SetVolume':
            return 'RenderingControl';
