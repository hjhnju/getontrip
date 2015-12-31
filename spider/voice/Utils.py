#!/usr/bin python3
#encoding:utf-8

import urllib
import os
import json
import http.cookiejar
import hashlib
import os,sys
import shutil

class Utils(object):



    def run(self):
        # url = 'http://c.hiphotos.baidu.com/image/pic/item/6d81800a19d8bc3e7bb5ec0a868ba61ea9d345b5.jpg'
        # self.download(url, 'test.jpg')
        tmpfile  = 'data/mp3tmp/566a29f7b65fe380655.mp3'
        outfname = Utils().calcMd5(tmpfile) + ".mp3"
        outfile  = "data/mp3/" + outfname

        if not os.path.exists(os.path.dirname(outfile)):
            os.makedirs(os.path.dirname(outfile))

        shutil.copyfile(tmpfile, outfile)

    # 下载音频至指定路径
    # url = 'http://c.hiphotos.baidu.com/image/pic/item/6d81800a19d8bc3e7bb5ec0a868ba61ea9d345b5.jpg'
    def download(self, url, outfile):

        cj = http.cookiejar.CookieJar()
        opener = urllib.request.build_opener(urllib.request.HTTPCookieProcessor(cj))
        opener.addheaders = [('User-agent','mobileJingQuBao/2.2 (iPhone; iOS 9.2; Scale/2.00)')]
        urllib.request.install_opener(opener)

        u = urllib.request.urlopen(url)
        f = open(outfile, 'wb')
        meta = u.info()
        file_size = int(meta.get("Content-Length"))
        print("Downloading: {0} Bytes: {1}".format(outfile, file_size))

        file_size_dl = 0
        block_sz = 8192
        status = ""
        while True:
            buffer = u.read(block_sz)
            if not buffer:
                break

            file_size_dl += len(buffer)
            f.write(buffer)
            status = r"%10d  [%3.2f%%]" % (file_size_dl, file_size_dl * 100. / file_size)
            status = status + chr(8)*(len(status)+1)
        print(status)

        f.close()


    def calcSha1(self, filepath):
        with open(filepath,'rb') as f:
            sha1obj = hashlib.sha1()
            sha1obj.update(f.read())
            hash = sha1obj.hexdigest()
            return hash

    def calcMd5(self, filepath):
        with open(filepath,'rb') as f:
            md5obj = hashlib.md5()
            md5obj.update(f.read())
            hash = md5obj.hexdigest()
            return hash

if __name__ == '__main__':
    util = Utils()
    util.run()
