#!usr/bin python3
# -*- coding: utf-8 -*-
# 51导游据抓取
# @author hejunhua
# @since 2016-01-05

import urllib
import http.cookiejar
import os.path
import json
import shutil
from Utils import Utils
import time

class Daoyou51(object):
    outdir     = "51data/"
    citylist   = "citys.json"
    scenicfile = "scenics.txt"
    zippath    = "zips/"

    def __init__(self):
        cj     = http.cookiejar.CookieJar()
        opener = urllib.request.build_opener(urllib.request.HTTPCookieProcessor(cj))
        opener.addheaders = [('channel','AppleStore')]
        opener.addheaders = [('User-agent','51Daoyou/3.5.6 (iPhone; iPhone OS 9.2; zh_CN)')]

        urllib.request.install_opener(opener)

    def run(self):
        citys = self.fetchCityList()
        print("城市总数:{0}".format(len(citys)))

        scenics = self.fetchScenics(citys)
        print("景点总数:{0}".format(len(scenics)))
        fname = u"{0}/{1}".format(self.outdir, self.scenicfile)
        # 保存用于匹配景点id
        Utils().saveTupleList(scenics, fname)

        self.fetchLandscapes(scenics)

    def fetchLandscapes(self, scenics):
        for (scenicId, scenicName, cityName, zipfile) in scenics:
            zipfname = zipfile.split('/')[-1]
            downfile = u"{0}{1}{2}".format(self.outdir, self.zippath, zipfname) 

            if not os.path.isfile(downfile):
                print("file %s not exists, start remote fetch..." % downfile)
                self.download(zipfile, downfile)
                # 不想抓太狠了
                time.sleep(2)

    # 获取所有城市 [(id, name)]
    def fetchCityList(self):
        fname = u"{0}/{1}".format(self.outdir, self.citylist)

        if not os.path.isfile(fname):
            print("[Remote]file %s not exists, start remote fetch..." % fname)
            params = urllib.parse.urlencode({
                "LanKey":"zh-cn"})
            params = params.encode('utf-8')
            req  = urllib.request.Request("http://vtgapi.cabbao.com/vtg/scenic/api.do?method=newCitys", params)
            resp = urllib.request.urlopen(req)
            jstr = resp.read()
            jstr = jstr.decode('utf-8')
            jstr = Utils().stripBOM(jstr)

            if not os.path.exists(os.path.dirname(fname)):
                os.makedirs(os.path.dirname(fname))
            with open(fname, 'w') as f:
                f.write(jstr)
                f.close

        # 否则直接读取
        f = open(fname)
        jobj = json.loads(f.read())
        retCitys = []
        for obj in jobj['data']:
            if len(obj['children']) > 0:
                for city in obj['children']:
                    cityCode = city['vtg_city_code']
                    cityName = city['vtg_city_name']
                    retCitys.append((cityCode, cityName))
            else:
                cityCode = obj['cityCode']
                cityName = obj['provinceName']
                retCitys.append((cityCode, cityName))
        return retCitys

    # 循环获取每个城市的景点[(scenicId, scenicName, cityName, zipfile)]
    def fetchScenics(self, citys):
        retScenics = []
        for (cityCode, cityName) in citys:
            scenics = self.fetchCityScenic(cityCode, cityName)
            retScenics.extend(scenics)
        return retScenics
    
    #获取某个城市的所有景点
    def fetchCityScenic(self, cityCode, cityName):
        fname = u"{0}/citys/{1}{2}.json".format(self.outdir, cityCode, cityName)

        if not os.path.isfile(fname):
            # 友情冬眠2秒
            time.sleep(2)
            print("[Remote]file %s not exists, start remote fetch..." % fname)
 
            params = urllib.parse.urlencode({
                "LanKey":"zh-cn", "CityCode":cityCode})
            params = params.encode('utf-8')
            req  = urllib.request.Request("http://vtgapi.cabbao.com/vtg/scenic/api.do?method=cityScenic", params)
            resp = urllib.request.urlopen(req)
            jstr = resp.read()
            jstr = jstr.decode('utf-8')
            jstr = Utils().stripBOM(jstr)

            if not os.path.exists(os.path.dirname(fname)):
                os.makedirs(os.path.dirname(fname))
            with open(fname, 'w') as f:
                f.write(jstr)
                f.close

        # 否则直接读取
        f = open(fname)
        jobj = json.loads(f.read())
        retScenics = []
        for obj in jobj['data']:
            scenicId   = obj['vtg_scenic_id']
            scenicName = obj['vtg_scenic_name']
            zipfile    = obj['zipFile']
            retScenics.append((scenicId, scenicName, cityName, zipfile))

        return retScenics
 

    #获取某个景点的所有景观
    # 有下载zip文件，该函数不用了
    # langId表示某个景点
    def fetchLandscape(self, langId, scenicName):
        fname = "test.json"
        # fname = u"{0}/scenics/{1}[{2}]".format(outdir, langId, scenicName)

        # mobile表示登录用户
        params = urllib.parse.urlencode({
            "LanguageId":"5154", "Mobile":"18010028337"})
        params = params.encode('utf-8')
        req  = urllib.request.Request("http://vtgapi.cabbao.com/vtg/scenic/api.do?method=scenicDownload", params)
        resp = urllib.request.urlopen(req)
        jstr = resp.read()
        jstr = jstr.decode('utf-8')
        jstr = Utils().stripBOM(jstr)
        with open(fname, 'w') as f:
            f.write(jstr)
            f.close

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

if __name__ == '__main__':
    main = Daoyou51()
    main.run()

