#!usr/bin python3
# -*- coding: utf-8 -*-
# 景区宝数据抓取
# @author hejunhua
# @since 2015-12-29

import urllib
import http.cookiejar
import os.path
import json
import shutil
from Utils import Utils
import time

class JingqubaoSpider(object):

    outdir = "data/"

    destfile = "destlist.json"

    # 景观数据：景观名，景观音频文件，所属景点，所属城市，所属国家，音频描述
    voiceOutfname = "data/jingqubao.txt"

    mp3Outdir = "data/mp3/"

    def __init__(self):
        cj     = http.cookiejar.CookieJar()
        opener = urllib.request.build_opener(urllib.request.HTTPCookieProcessor(cj))
        opener.addheaders = [('User-agent','mobileJingQuBao/2.2 (iPhone; iOS 9.2; Scale/2.00)')]
        urllib.request.install_opener(opener)

        self.voiceOutfile = open(self.voiceOutfname, "w")

    # 输出audio文件夹，包含每个景观的音频json
    def run(self):
        destList   = self.fetchDestList()
        scenicList = self.fetchScenicList(destList)
        self.fetchLandscapeList(scenicList)

    # 所有目的地 destlist.json
    # act=destination_list&app=api_v2&mod=Scenic&oauth_token=&oauth_token_secret=
    def fetchDestList(self):
        fname = self.outdir + self.destfile
        if not os.path.isfile(fname):
            print("[Remote]file %s not exists, start remote fetch..." % fname)

            params = urllib.parse.urlencode({
                "act":"destination_list", "app":"api_v2", "mod":"Scenic", "oauth_token":"", "oauth_token_secret":""
            })
            params = params.encode('utf-8')
            req  = urllib.request.Request("http://v2.jingqubao.com/index.php?", params)
            resp = urllib.request.urlopen(req)
            jstr = resp.read()
            jstr = jstr.decode('utf-8')

            if not os.path.exists(os.path.dirname(fname)):
                os.makedirs(os.path.dirname(fname))
            with open(fname, "w") as f:
                f.write(jstr)
                f.close

        retList = []
        f = open(fname)
        destJson = json.loads(f.read())
        for dest in destJson['data']:
            destId = dest['area_id']
            destTitle  = dest['title']
            retList.append((destId, destTitle))
        return retList

    # 每个目的地的所有景点 scenic/{destTitle}.json
    # act=get_scenic_list&app=api_v2&area_id=341000&lat=39.979969&lng=116.302902&mod=Scenic&oauth_token=&oauth_token_secret=
    def fetchScenicList(self, destList):
        retList = []
        for (destId, destTitle) in destList:
            slist = self.fetchScenic(destId, destTitle)
            retList.extend(slist)
        return retList

    def fetchScenic(self, destId, destTitle):

        fname = u"{0}/scenic/{1}.json".format(self.outdir, destTitle)
        if not os.path.isfile(fname):
            print("[Remote]file %s not exists, start remote fetch..." % fname)

            params = urllib.parse.urlencode({
                "act":"get_scenic_list","app":"api_v2","area_id":destId,"lat":"39.979950","lng":"116.302933","mod":"Scenic","oauth_token":"","oauth_token_secret":""})
            params = params.encode('utf-8')
            req  = urllib.request.Request("http://v2.jingqubao.com/index.php?", params)
            resp = urllib.request.urlopen(req)
            jstr = resp.read()
            jstr = jstr.decode('utf-8')

            if not os.path.exists(os.path.dirname(fname)):
                os.makedirs(os.path.dirname(fname))
            with open(fname, "w") as f:
                print("writing: %s" % fname)
                f.write(jstr)

        # 否则直接读取
        retList = []
        f = open(fname)
        scenicJson = json.loads(f.read())
        for scenic in scenicJson['data']:
            scenicId   = scenic['scenic_id']
            scenicName = scenic['scenic_region_name']
            categoryId = self.getCategoryId(scenicId)
            retList.append((scenicId, scenicName, categoryId, destId, destTitle))
        return retList

    # 解析每个目的地的景点
    def fetchLandscapeList(self, scenicList):
        for (scenicId, scenicName, categoryId, destId, destTitle) in scenicList:
            self.fetchLandscape(scenicId, scenicName, categoryId, destId, destTitle)

    # 景点的所有景观（景观里有音频) audio/{destTitle}/{scenicName}.json
    # act=get_scenic_category_audios&app=api_v2&category_id=222&mod=Scenic&oauth_token=&oauth_token_secret=&scenic_id=69
    def fetchLandscape(self, scenicId, scenicName, categoryId, destId, destTitle):

        fname = u"{0}/audio/{1}/{2}.json".format(self.outdir, destTitle, scenicName)
        if not os.path.isfile(fname):
            print("[Remote]file %s not exists, start remote fetch..." % fname)

            params = urllib.parse.urlencode({"act":"get_scenic_category_audios","app":"api_v2","category_id":categoryId,"mod":"Scenic","oauth_token":"","oauth_token_secret":"","scenic_id":scenicId})
            params = params.encode('utf-8')
            req  = urllib.request.Request("http://v2.jingqubao.com/index.php?", params)
            resp = urllib.request.urlopen(req)
            jstr = resp.read()
            jstr = jstr.decode('utf-8')

            if not os.path.exists(os.path.dirname(fname)):
                os.makedirs(os.path.dirname(fname))
            with open(fname, "w") as f:
                print("writing: %s" % fname)
                f.write(jstr)

        # 否则直接读取
        f = open(fname)
        ldJson = json.loads(f.read())
        for landscape in ldJson['data']:
            ldname  = landscape['scenic_spots_name']
            mp3url  = landscape['src']
            if mp3url == '':
                mp3url = landscape['try_src'].rstrip('_try.mp3') + '.mp3'

            if ldname == '景区音频':
                ldname = scenicName

            localMp3file = self.downloadVedio(mp3url)
            print("{0}\t{1}".format(ldname, localMp3file))
            self.voiceOutfile.write("{0}\t{1}\t{2}\t{3}\t{4}\n".format(ldname, localMp3file, scenicName, destTitle, "中国"))

    def downloadVedio(self, mp3url):
        tmpfname = mp3url.split('/')[-1]
        tmpfile  = self.outdir + "/mp3tmp/" + tmpfname

        if not os.path.isfile(tmpfile):
            print("file %s not exists, start remote fetch..." % tmpfile)
            self.download(mp3url, tmpfile)
            # 不想抓太狠了
            time.sleep(1)

        outfname = Utils().calcMd5(tmpfile) + ".mp3"
        outfile  = self.outdir + "/mp3/" + outfname

        if not os.path.exists(os.path.dirname(outfile)):
            os.makedirs(os.path.dirname(outfile))
        shutil.copyfile(tmpfile, outfile)

        return outfname

    # 下载音频至指定路径
    # url = 'http://c.hiphotos.baidu.com/image/pic/item/6d81800a19d8bc3e7bb5ec0a868ba61ea9d345b5.jpg'
    def download(self, url, fname):

        if not os.path.exists(os.path.dirname(fname)):
            os.makedirs(os.path.dirname(fname))

        u = urllib.request.urlopen(url)
        f = open(fname, 'wb')
        meta = u.info()
        file_size = int(meta.get("Content-Length"))
        print("Downloading: {0} Bytes: {1}".format(fname, file_size))

        file_size_dl = 0
        block_sz = 8192
        status   = ""
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

    #act=get_scenic_audiocategories&app=api_v2&mod=Scenic&oauth_token=&oauth_token_secret=&scenic_id=152
    def getCategoryId(self, scenicId):
        fname = "{0}/category/{1}.json".format(self.outdir, scenicId)
        if not os.path.isfile(fname):
            print("[Remote]file %s not exists, start remote fetch..." % fname)

            params = urllib.parse.urlencode({
                "act":"get_scenic_audiocategories", "app":"api_v2", "mod":"Scenic", "oauth_token":"", "oauth_token_secret":"","scenic_id":scenicId
            })
            params = params.encode('utf-8')
            req  = urllib.request.Request("http://v2.jingqubao.com/index.php?", params)
            resp = urllib.request.urlopen(req)
            jstr = resp.read()
            jstr = jstr.decode('utf-8')
            if not os.path.exists(os.path.dirname(fname)):
                os.makedirs(os.path.dirname(fname))
            with open(fname, "w") as f:
                print("writing: %s" % fname)
                f.write(jstr)
                f.close()

        # 读取文件得到catid
        f = open(fname)
        catId = 0
        catJson = json.loads(f.read())
        for cat in catJson['data']:
            catId = cat['audio_category_id']

        return catId


if __name__ == '__main__':
    spider = JingqubaoSpider()
    spider.run()

