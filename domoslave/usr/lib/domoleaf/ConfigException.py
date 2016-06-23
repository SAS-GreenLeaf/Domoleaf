#!/usr/bin/python3

import string;
import random;
import socket;

def get_key(key):
    # key = socket.gethostname();
    # key += (16 - len(key)) * '_';
    # FINIR L'ENVOI DE LA CLEF AES
    IV = random.choice(string.ascii_uppercase + string.ascii_lowercase + string.digits) for _ in range(16);
    return "{\"key\": \"" + str(key) + "\", \"IV\": \"" + str(IV) + "\"}";
