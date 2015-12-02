# -*- coding: utf-8 -*-
# 用于云阅读文章内容解码
# fyy 2015.11.24

import re

class contentDecode:

    _key = {}
    _tbl = {}
    _pad = ''

    @staticmethod
    def getBitCake():
        
        baseCode1 = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32,
             33, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54, 55, 56, 57, 58, 59, 60, 61, 62, 63]
        baseCode2 = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/="

        for n in range(0, 63):
            # print 'index:'+str(baseCode2[baseCode1[n]])
            contentDecode._key.setdefault(n, baseCode2[baseCode1[n]]) 
            contentDecode._tbl.setdefault(contentDecode._key[n], n) 
            contentDecode._pad = baseCode2[64] 
        

    @staticmethod
    def deCode(content): 
        content = re.sub(r'[^0-9A-Za-z+/=]', "", content) 
        # print content
        contentArray = [] 
        temp = []
        s = 0
        l = 0
        for s in range(0, len(content)):
            if s<len(content):
                n = contentDecode._tbl[content[s]]
                s = s+1
                r = contentDecode._tbl[content[s]]
                s = s+1
                i = contentDecode._tbl[content[s]]
                s = s+1
                o = contentDecode._tbl[content[s]]
                s = s+1  
                temp.append(n << 2 | r >> 4)
                # temp.setdefault(l,n << 2 | r >> 4) 
                l = l+1
                temp.append((15 & r) << 4 | i >> 2)
                # temp.setdefault(l, (15 & r) << 4 | i >> 2)
                l = l+1
                temp.append((3 & i) << 6 | o)
                # temp.setdefault(l, (3 & i) << 6 | o)
                l = l+1
                pass 
            pass
        c = content[(len(content)-2):len(content)] 
        if c[0] == contentDecode._pad:
             temp.pop()
             temp.pop()
             pass 
        elif c[1] == contentDecode._pad:
             temp.pop()
             pass
        contentDecode.utf8to16(temp)

    @staticmethod
    def utf8to16(temp):
        o = len(temp)
        i = []
        a = 0
        for a in range(0,o):
            t = e[a]
            a = a+1
            t >> 4
            if [0,1,2,3,4,5,6,7].count(t)>0:
                i.append = String.fromCharCode(e[a - 1]);
                pass
            pass
        
        pass
    

    @staticmethod
    def getContent(content):
        print content.decode('base64','strict')
        contentDecode.getBitCake()
        # contentDecode.deCode(content)
        
        # a = [],
      #       s = 0,
      #       l = 0;
