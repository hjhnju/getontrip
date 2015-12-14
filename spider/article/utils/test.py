# -*- coding: utf-8 -*-
# 测试
# fyy 2015.11.24

# from contentDecode import contentDecode
import os
import codecs

def ListFilesToTxt(dir, file, wildcard, recursion):
    exts = wildcard.split(" ")
    files = os.listdir(dir)
    count = 0
    for name in files:
        fullname = os.path.join(dir, name)
        if(os.path.isdir(fullname) & recursion):
            ListFilesToTxt(fullname, file, wildcard, recursion)
        else:
            for ext in exts:
                if(name.endswith(ext)): 
                    aid = name.split('_')[2]
                    count = count + 1
                    file.write('"http://www.dooland.com/magazine/article_'+aid + '.html",'+ "\n") 
                    break
    print count
def Test():
    dir = "E:\\kanlishi"
    outfile = "kanlishi.txt"
    wildcard = ".txt .exe .dll .lib"

    file = codecs.open(outfile, 'w', encoding='utf-8')
    if not file:
        print("cannot open the file %s for writing" % outfile)

    ListFilesToTxt(dir, file, wildcard, 1)

    file.close()

Test()
