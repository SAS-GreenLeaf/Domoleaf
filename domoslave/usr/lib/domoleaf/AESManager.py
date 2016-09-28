#!/usr/bin/python3

## @package domolib
# Library for domomaster and domoslave.
#
# Developed by GreenLeaf.

## Module used to manage AES keys.

import string;
import random;
import socket;

## Generates an IV of 16 bytes long.
#
# @return The new generated IV.
def get_IV():
    IV = ''.join([random.choice(string.ascii_uppercase + string.ascii_lowercase + string.digits) for _ in range(16)]);
    return IV;

## CHECK IF THIS MODULE IS USEFUL.
