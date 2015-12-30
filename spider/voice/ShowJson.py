#!/usr/bin/env python3
#encoding:utf-8

import sys,os
import json
from pprint import pprint

if __name__ == '__main__':
    if len(sys.argv) < 2:
        print('No json file specified.')
        sys.exit()

    jfile = sys.argv[1]
    f = open(jfile)
    jstr  = json.loads(f.read())
    pprint(jstr)

