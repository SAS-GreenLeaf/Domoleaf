#!/usr/bin/python3

import string;
import random;
import socket;

# Pas sur qu'elle soit encore utile A CHECKER
def get_key(key):
    IV = ''.join([random.choice(string.ascii_uppercase + string.ascii_lowercase + string.digits) for _ in range(16)])
    return "{\"key\": \"" + str(key) + "\", \"IV\": \"" + str(IV) + "\"}";

def get_IV():
    """
    Generates an IV of 16 bytes long
    """
    IV = ''.join([random.choice(string.ascii_uppercase + string.ascii_lowercase + string.digits) for _ in range(16)]);
    return IV;
