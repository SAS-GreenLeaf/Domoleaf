## @package domomaster
# Master daemon for D3 boxes.
#
# Developed by GreenLeaf.

from math import *;
from decimal import Decimal;

## Strange function returning its first parameter.
#
# @param val The returned value.
# @param dpt_id Unused parameter.
#
# @return The first parameter.
def convert_none(val, dpt_id=0):
    return val;

## Divides "val" by 100.
#
# @param val The value to divide.
#
# @return The parameter divided by 100.
def convert_hundred(val):
    return val/100;

## Converts "val" from its raw form to its real value.
#
# @param val The value to convert.
#
# @return The value converted.
def convert_temperature(val):
    factor = 0.01;
    exp = (val & 0x7800) >> 11;
    sign = val & 0x8000;
    mant = val & 0x7ff;
    if sign:
        mant |= 0xfffffffffffff800;
    res = mant * pow(2, exp) * factor;
    res = Decimal(str(round(res, 2)));
    return res;

## Converts "val" from integer to hexadecimal.
#
# @param val The value to convert.
#
# @return The hexadecimal form of "val".
def convert_temperature_reverse(val):
    new_val = val * 100;
    exp = 0;
    while new_val > 2047:
        new_val /= 2;
        exp += 1;
    bin_str = bin(int(ceil(new_val)))[2:]
    bin_str = ('0' * (12 - len(bin_str))) + bin_str
    exp_bin = bin(exp)[2:]
    exp_bin = ('0' * (4 - len(exp_bin))) + exp_bin
    res_str = bin_str[0] + exp_bin + bin_str[1:];
    res = hex(int(res_str, 2))[2:];
    res = ('0' * (4 - len(res))) + res;
    return [res[:2], res[2:]];

## Converts a number to a float on 32 bits.
#
# @param val The value to convert.
#
# @return The converted value to float 32 bits.
def convert_float32(val):
    factor = 0.01;
    exp = (val & 0x7F800000) >> 23;
    sign = val >> 31;
    mant = val & 0x007FFFFF;
    if sign:
        mant = mant | 0xFFFFFFFFFF800000;
    return Decimal(str(round(mant * pow(2, exp) * factor, 2)));

## Depending on dpt_id, 1 or 0 will ben returned (on / off EnOcean).
#
# @param val The value to return once modified.
# @param dpt_id The value of the condition.
#
# @return 0 or 1 depending on "dpt_id".
def eno_onoff(val, dpt_id):
    if dpt_id == 471:
        if val == 16:
            return 1
        elif val == 48:
            return 0
        else:
            return None;
    elif dpt_id == 472:
        if val == 80:
            return 1
        elif val == 112:
            return 0
        else:
            return None;
    return None;

## Checks to format of a string representing an IP address.
#
# @param ip The String containing the IP.
#
# @return True or False depending on the parsing result.
def is_valid_ip(ip):
    tmp = ip.split('.');
    if len(tmp) != 4:
        return False;
    for x in tmp:
        if not x.isdigit():
            return False;
        i = int(x);
        if i < 0 or i > 255:
            return False;
    return True;
