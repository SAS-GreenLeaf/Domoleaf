from math import *;
from decimal import Decimal;

def convert_temperature(val):
    """
    Conversion from val to the real value
    """
    factor = 0.01;
    exp = (val & 0x7800) >> 11;
    sign = val & 0x8000;
    mant = val & 0x7ff;
    if sign:
        mant |= 0xfffffffffffff800;
    res = mant * pow(2, exp) * factor;
    res = Decimal(str(round(res, 2)));
    return res;

def convert_temperature_reverse(val):
    """
    Val is converted from integer value into two hexadecimal values
    """
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
