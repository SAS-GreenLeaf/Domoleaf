## @package domolib
# Library for domomaster and domoslave.
#
# Developed by GreenLeaf.

import sys;
import requests;
from requests.auth import HTTPDigestAuth;
sys.path.append("/usr/lib/domoleaf");
from Logger import *;

LOG_FILE        = '/var/log/domoleaf/domomaster.log';
LOG_FLAG        = True;

## Class with static util methods for HTTP.
class HttpReq:

    ## Builds and sends an http request.
    #
    # @param json_obj Not used here.
    # @param dev Object describing the action.
    # @param hostname Not used here.
    # @return None
    def http_action(self, json_obj, dev, hostname):
        url = ('http://'+str(dev['addr'])+':'+str(dev['plus1'])+str(dev['addr_dst']));
        s = requests.Session();
        answer = s.get(url, auth=HTTPDigestAuth(str(dev['plus2']), str(dev['plus3'])));
