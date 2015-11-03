import sys;
import requests;
from requests.auth import HTTPDigestAuth;
sys.path.append("/usr/lib/domoleaf");
from Logger import *;

LOG_FILE        = '/var/log/glslave';

LOG_FLAG        = True;

class HttpReq:

    def camera_move(addr, pos, login, password):
        url = ('http://' + str(addr) + str(pos));
        s = requests.Session();
        answer = s.get(url, auth=HTTPDigestAuth(str(login), str(password)));
