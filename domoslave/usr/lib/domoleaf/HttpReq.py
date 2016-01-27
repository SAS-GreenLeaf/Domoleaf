import sys;
import requests;
from requests.auth import HTTPDigestAuth;
sys.path.append("/usr/lib/domoleaf");
from Logger import *;

LOG_FILE        = '/var/log/domoleaf/domomaster.log';
LOG_FLAG        = True;

class HttpReq:

    def http_action(self, json_obj, dev, hostname):
        url = ('http://' + str(dev['addr']) + str(dev['addr_dst']));
        s = requests.Session();
        answer = s.get(url, auth=HTTPDigestAuth(str(dev['plus2']), str(dev['plus3'])));
