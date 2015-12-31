# -*- coding: utf-8 -*-
# updatesight  根据景点文件和城市文件导出sight_meta的数据 item
# fyy 2015.12.29
import os
import codecs
import re
from json import *
import json
import time
import scrapy

class updatesight:
    sightList = []
    cityList = []
    typeList = {0: '', 1: "城市", 2: "古镇", 3: "乡村", 4: "海边", 5: "沙漠", 6: "山峰", 7: "峡谷", 8: "冰川", 9: "湖泊", 10: "河流", 11: "温泉", 12: "瀑布", 13: "草原", 14: "湿地", 15: "自然保护区",
                    16: "公园", 17: "展馆", 18: "历史建筑", 19: "现代建筑", 20: "历史遗址", 21: "宗教场所", 22: "观景台", 23: "陵墓", 24: "学校", 25: "故居", 26: "纪念碑", 27: "其他", 28: "购物娱乐", 29: "休闲度假"}
        
    updatefile = codecs.open(
            'E:\\sight\\updatefiles\\update.sql', 'w', encoding='utf-8')
    if not updatefile:
        print("cannot open the file %s for writing" % updatefile)

    updatefile2 = codecs.open(
            'E:\\sight\\updatefiles\\update2.sql', 'w', encoding='utf-8')
    if not updatefile2:
        print("cannot open the file %s for writing" % updatefile2)

    cityfile = codecs.open(
            'E:\\sight\\updatefiles\\city.sql', 'w', encoding='utf-8')
    if not cityfile:
        print("cannot open the file %s for writing" % cityfile)

    deletefile = codecs.open(
            'E:\\sight\\updatefiles\\delete.sql', 'w', encoding='utf-8')
    if not deletefile:
        print("cannot open the file %s for writing" % deletefile)

    outfile_log = codecs.open(
            'E:\\sight\\updatefiles\\log.json', 'w', encoding='utf-8')
    if not outfile_log:
        print("cannot open the file %s for writing" % outfile_log)
    outfile_log.write(u"日志开始:"+time.asctime(time.localtime(time.time()))+"\n")
    
    outfile_city_id0 = codecs.open(
            'E:\\sight\\updatefiles\\city_id0.json', 'w', encoding='utf-8')
    if not outfile_city_id0:
        print("cannot open the file %s for writing" % outfile_city_id0)

    @staticmethod
    def main():
        # 打开城市文件，读取城市列表
        cityFile = open('E:\\sight\\cityid_name.txt')
        city_object = cityFile.read().decode('utf-8')
        city_object = re.sub(r'[\r|\n]', '', city_object)
        updatesight.cityList = JSONDecoder().decode(city_object.strip()) 
        cityFile.close()
        # 打开景点文件，读取景点列表
        sightFile = open('E:\\sight\\updatefiles\\sight_meta_xianshang.json')
        # sightFile = open('E:\\sight\\updatefiles\\sight_meta_test.json')
        sight_object = sightFile.read().decode('utf-8')
        sight_object = re.sub(r'[\r|\n]', '', sight_object)
        sight_object = sight_object.replace("/** Export to JSON plugin for PHPMyAdmin @version 0.1 */// Database 'ontrip'// ontrip.sight_meta",'')
        sight_object = re.sub(r'[\r|\n]', '', sight_object) 
        
        
        updatesight.sightList = JSONDecoder().decode(sight_object.strip()) 
        sightFile.close()
        
        

        updatesight.dealTheDelItem()
        

        updatesight.outfile_log.write(u"日志结束:"+time.asctime(time.localtime(time.time()))+"\n")
        updatesight.outfile_log.close()
        updatesight.outfile_city_id0.close()
        updatesight.cityfile.close()
        pass
    
    # 批量更新城市ID
    @staticmethod
    def updateCityID():
        updatesight.outfile_log.write(u"开始处理城市ID:"+time.asctime(time.localtime(time.time()))+"\n")
        for x in xrange(len(updatesight.sightList)):
            item = updatesight.sightList[x]
            # print item['id']
            oldCityID = item['city_id']
            newCityID = updatesight.getCityID(item)

            # 找不到城市的情况
            if newCityID==0:
                line = item['url']+',"country":'+item['country']+',"city":'+item['city']+ "\n"
                updatesight.outfile_city_id0.write(line)
                pass
            # 城市ID需要修改的话
            if int(oldCityID)!=newCityID and newCityID!=0: 
                line = 'UPDATE `sight_meta_test` SET `city_id` =%s  WHERE `id`=%s'  % (newCityID,item['id'])
                line = line +";\n"
                updatesight.updatefile.write(line)
                pass
            pass
        
        pass
    

    # 单个更新城市ID
    @staticmethod
    def updateOneCityID(olditem,item): 
        # print 'aaaa:'+olditem['id']
        oldCityID = olditem['city_id']
        newCityID = updatesight.getCityID(item)

        # 找不到城市的情况
        if newCityID==0:
            line = olditem['url']+',"country":'+olditem['country']+',"city":'+olditem['city']+ "\n"
            updatesight.outfile_city_id0.write(line)
            pass
        # 城市ID需要修改的话
        if int(oldCityID)!=newCityID and newCityID!=0:
             
            line = "UPDATE `sight_meta_test` SET `city_id` =%s  WHERE `id`=%s AND `city_id`='%s' AND `name`='%s'"  % (newCityID,olditem['id'],olditem['city_id'],olditem['name'])
            line = line +";\n"
            updatesight.updatefile.write(line)
            pass
       
        
        pass

    # 获取城市id
    @staticmethod
    def getCityID(item):
        cityList = updatesight.cityList
        city = item['city']
        country = item['country']
        is_china = item['is_china']
        for x in xrange(len(cityList)): 
            cityName = updatesight.setCityName(cityList[x]['name'],is_china)
            cityId = cityList[x]['id'] 
            countryname = cityList[x]['countryname']
            if cityName.find(city) > -1 and countryname.find(country)>-1: 
            # if cityName.find(city) > -1 and countryname.find(country)>-1 and ((is_china=='1' and cityId<10000) or (is_china!='1' and cityId>10000)):
                return cityId
                pass
            pass
        return 0
        pass
   
    #特殊处理需城市名称
    @staticmethod
    def setCityName(city,is_china):  
        if is_china=='1': 
            city = re.sub(u"[市|县|地区]", "", city)
            pass
        
        if city==u'海西州' or city==u'海北州' or city==u'甘孜州' or city==u'海南州' or city==u'海东州': 
            city = re.sub(u"[州]", "", city)
            pass
        return city
        pass

    #处理需要删除的城市 
    @staticmethod
    def dealTheDelItem():
        updatesight.outfile_log.write(u"开始处理要删除的城市:"+time.asctime(time.localtime(time.time()))+"\n")
        for x in xrange(len(updatesight.sightList)):
            item = updatesight.sightList[x]
            sight_id = item['id']
            # 根据url找到surl
            item['surl'] = item['url'].replace('http://lvyou.baidu.com/','')
            # 根据surl打开文件
            # surl作为文件名，去掉非法字符 \ / : * ? " < > |
            filename = item['surl']
            filename = re.sub(r'[|]', '', filename)
            filename = re.sub(r'[\\|/|:|\*|\?|\"|<|>]', '', filename+'.txt')
            try: 
                file_object = open('E:\\sight\\meta\\all\\'+filename)
                jsonStr = file_object.read().decode('utf-8') 
                updatesight.paraseItem(item,jsonStr,filename)
            except Exception, e:
                updatesight.outfile_log.write("ERROR:open faild :"+ filename+ str(item['id']))
                updatesight.outfile_log.write(str(e.args)+ "\n")   
            finally:
                file_object.close()
            

            pass
        pass

    @staticmethod
    def paraseItem(olditem, jsonStr, filename):
        typeStr = ''
        sight_id = olditem['id'] 
        sight_city = olditem['city']
        is_china = olditem['is_china']
        print sight_id
        item = SightItem()
        item['is_china'] = is_china
        jsonStr = re.sub(r'[\r|\n]', '', jsonStr)
        jsonStr = re.sub(r"'", "''", jsonStr)
        jsonStr = re.sub(r"\\'", "'", jsonStr)
        result = JSONDecoder().decode(jsonStr.strip()) 
        try:   
            scene_total = result.get('scene_total', [])
            scene_path = result.get('scene_path', [])
            ex = result.get('ext', {})
            type_list = ex.get('cids', '').split(',')
            surl = result['surl'].strip().encode('utf8', 'ignore').decode('utf8')
            
            # 特殊处理名称
            if result['sname'] == '':
                item['name'] = surl
                pass
            else:
                item['name'] = result['sname'].strip().encode(
                    'utf8', 'ignore').decode('utf8')
                pass

            # 特殊处理景点类型
            for index in range(0, len(type_list)):
                if type_list[index] != '':
                    typeStr += ' ' + updatesight.typeList[int(type_list[index])]
                    pass
                pass
            item['typeStr'] = typeStr.strip().decode('utf8')

            
            # 特殊处理所属行政区域
            scene_layer_array = []
            continent = ''
            country = ''
            province = ''
            city = ''
            citySurl = ''

            five_surl = ''
            five_name = ''
            six_name = ''


            four_layer = False
            five_layer = False
            six_layer = False

            for x in xrange(len(scene_path)):
                scene_layer = scene_path[x]['scene_layer']
                scene_layer_array.append(scene_layer)
                if scene_layer == '1':
                    continent = scene_path[x]['sname'].strip()
                    pass
                elif scene_layer == '2':
                    country = scene_path[x]['sname'].strip()
                    pass
                elif scene_layer == '3':
                    province = scene_path[x]['sname'].strip()
                    pass
                elif scene_layer == '4':
                    if city=='':
                        city = scene_path[x]['sname'].strip()
                        citySurl = scene_path[x]['surl'].strip()
                        pass 
                    four_layer = True
                    pass
                elif scene_layer == '5':
                    if five_name=='':
                        five_name = scene_path[x]['sname'].strip()
                        pass  
                    five_surl = scene_path[x]['surl'].strip()
                    five_layer = True
                    pass
                elif scene_layer == '6':
                    six_name = scene_path[x]['sname'].strip()
                    six_layer = True
                    pass
                pass
            
            # 直接属于国家的城市(没有4级别的城市)，同时也作为了景点
            # 作为城市，属于景点级别5，
            # 例子(澳大利亚2-昆士兰州3-布里斯班5)
            # 则该城市作为景点属于自己
            if type_list.count('1') > 0 and four_layer == False:
                city = item['name']
                pass
            # 例子：(马尔代夫2-天堂岛5)
            # 直接属于国家的景点，没有城市，则创建一个与当前国家同名的城市
            if not four_layer and province=='':
                city = country
                pass
             
            
            # 例子(肯尼亚2 - 阿伯德尔国家公园4)
            # 级别为4，但是并不是城市，而是一个景点,
            if four_layer and type_list.count('1') < 0:
                if province!='':
                    city = province
                    pass 
                else:
                    city = country
                    pass
                pass


            # 直接属于省的景点，没一城市，则创建一个与当前省份同名的城市
            # 例子 (西澳大利亚州3 珀斯5 军营门6)
            if not four_layer and province!='' and five_name!=u"大兴安岭":
                city = province
                pass 
            # 特殊例子 (黑龙江3 - 大兴安岭5)
            if not four_layer and province!='' and five_name==u"大兴安岭":
                city = u"大兴安岭"
                pass

            
            if is_china=='1': 
                city = re.sub(u"[市|县|地区]", "", city)
                pass
            
            city = updatesight.citySpecial(city)

            

            # 判断当前景点级别4 是不是城市 ，不是城市则放到与国家同名的城市下面
            if citySurl!='' and not updatesight.isCity(citySurl)  and olditem['country']!=u'中国': 
                city = updatesight.citySpecial2(city,country)
                olditem['name'] = re.sub(r"'", "''", olditem['name'])
                olditem['name'] = re.sub(r"\\'", "'", olditem['name'])
                if city!=olditem['city']:
                    line = "UPDATE `sight_meta_test` SET `city` ='%s'  WHERE `id`=%s AND `name`='%s' AND `city`='%s'"  % (city,olditem['id'],olditem['name'],olditem['city'])
                    line = line + ";\n"
                    updatesight.updatefile2.write(line)
                    pass 
                pass
            

            item['continent'] = continent
            item['country'] = country
            item['province'] = province
            item['city'] = city  
            updatesight.updateOneCityID(olditem,item)
            
            # 如果类型为城市 且下面还有景点则加入到删除sql
            
            # if type_list.count('1') > 0 and scene_total>0:
            if type_list.count('1') > 0 and four_layer and not five_layer and not six_layer and scene_total>0 and city==item['name']:
                # 增加特例
                if item['name']==u'拉森火山国家公园':
                    return
                    pass

                line = "DELETE FROM `sight_meta_test`  WHERE `id`=%s AND `name`='%s' AND  `type`='%s'" % (sight_id,olditem['name'],olditem['type'])
                line = line + ";\n"
                updatesight.deletefile.write(line)
                pass 
            
             
            
           
            pass
        except Exception, e: 
            updatesight.outfile_log.write("ERROR:"+ filename)
            updatesight.outfile_log.write(str(e.args)+ "\n")  
            line = json.dumps(dict(item)) + "\n"
            updatesight.outfile_log.write("line:"+line.decode('unicode_escape'))
            # raise e
        
        pass
    



    # 查询每一个景点，layer==4的 类型，
    @staticmethod
    def isCity(surl):
        if surl=='':
            return False
            pass
        # 根据surl打开文件
        # surl作为文件名，去掉非法字符 \ / : * ? " < > |
        filename = surl
        filename = re.sub(r'[|]', '', filename)
        filename = re.sub(r'[\\|/|:|\*|\?|\"|<|>]', '', filename+'.txt')
        isCity = False
        try: 
            path1 = 'E:\\sight\\meta\\all\\'+filename
            path2 = 'E:\\sight\\meta\\city\\'+filename

            isExists1=os.path.exists(path1)
            isExists2=os.path.exists(path2)
            jsonStr = ''

            if not isExists1 and not isExists2:
                jsonStr = updatesight.HtmlParser(surl)
                pass 
            elif isExists1:
                file_four = open(path1) 
                jsonStr = file_four.read().replace('\\\\','').decode('utf-8')
                file_four.close() 
                pass
            elif isExists2:
                file_four = open(path2) 
                jsonStr = file_four.read().replace('\\\\','').decode('utf-8') 
                file_four.close()
                pass 
            isCity = updatesight.getLayerItem(jsonStr)

        except Exception, e:  
            updatesight.outfile_log.write("ERROR:file_four open faild :"+ filename)
            updatesight.outfile_log.write(str(e.args)+ "\n")   
         
        return isCity
        pass
    
    # 查询每一个景点，layer==4的 类型，
    @staticmethod
    def getLayerItem(jsonStr):
        jsonStr = re.sub(r'[\r|\n]', '', jsonStr)
        jsonStr = re.sub(r"'", "''", jsonStr)
        jsonStr = re.sub(r"\\'", "'", jsonStr)
        result = JSONDecoder().decode(jsonStr.strip()) 
        ex = result.get('ext', {})
        type_list = ex.get('cids', '').split(',')
        if type_list.count('1') > 0:
            return True
            pass
        return False
        pass
    
    # city特例
    @staticmethod
    def citySpecial (city):
        if city==u'海西州' or city==u'海北州' or city==u'甘孜州' or city==u'海南州' or city==u'海东州': 
            city = re.sub(u"[州]", "", city)
            pass
        # 注意  普洱(原名思茅)  克孜勒苏柯尔克孜(原名 克孜勒苏州) 
        # 济源 直接属于河南省
        # 阿拉尔,图木舒克 直接属于新疆
        
        # 石首 隶于荆州
        if city==u'石首':
            city = u'荆州'
            pass

        # 呼玛 隶于大兴安岭
        if city==u'呼玛':
            city = u'大兴安岭'
            pass
        # 习水隶属于遵义 
        if city==u'习水':
            city = u'遵义'
            pass
        # 霍山隶属于六安
        if city==u'霍山':
            city = u'六安'
            pass
            
        # 樟树隶属于宜春
        if city==u'樟树':
            city = u'宜春'
            pass
        # 麻城隶属于黄冈
        if city==u'麻城':
            city = u'黄冈'
            pass
        # 江西铅山隶属于上饶
        if city==u'江西铅山':
            city = u'上饶'
            pass
        # 梅河口隶属于通化
        if city==u'梅河口':
            city = u'通化'
            pass
        return city
        pass

    # city特例
    @staticmethod
    def citySpecial2 (city,country): 
        if city.find(u'公园')>-1 or city.find(u'酒店')>-1 or city.find(u'学院')>-1:
            return country
            pass
        elif city.find(u'博物馆')>-1 or city.find(u'王陵')>-1 or city.find(u'泳池')>-1:
            return country
            pass
        elif city.find(u'艺术馆')>-1 or city.find(u'行宫')>-1 or city.find(u'湖')>-1:
            return country
            pass
        elif city.find(u'大厅')>-1 or city.find(u'餐厅')>-1 or city.find(u'商业城')>-1:
            return country
            pass 
        elif city.find(u'度假村')>-1 or city.find(u'艺术中心')>-1 or city.find(u'教堂')>-1:
            return country
            pass
        elif city.find(u'剧院')>-1 or city.find(u'剧场')>-1 or city.find(u'大学')>-1:
            return country
            pass
        elif city.find(u'村落')>-1 or city.find(u'夜市')>-1:
            return country
            pass
        else:
            return city
            pass 
        pass

    # 从网页中提取文本
    @staticmethod
    def HtmlParser (surl): 
        import urllib 
        import sys 
        url = "http://lvyou.baidu.com/destination/ajax/jingdian?format=ajax&surl="+surl  
        wp = urllib.urlopen(url)
        print "start download..."
        content = wp.read()
        result = JSONDecoder().decode(content)['data'] 
         
        # surl作为文件名，去掉非法字符 \ / : * ? " < > |
        filename = surl
        filename = re.sub(r'[|]', '', filename)
        filename = re.sub(r'[\\|/|:|\*|\?|\"|<|>]', '', filename)

        line = json.dumps(dict(result))
        
        dataFile = codecs.open('E:\\sight\\meta\\city\\'+filename+'.txt', 'w', encoding='utf-8') 
         
        dataFile.write(line)
        return line
        pass
    
    pass
 
class SightItem(scrapy.Item):
    id = scrapy.Field()
    name = scrapy.Field()
    image = scrapy.Field()
    level = scrapy.Field()
    describe = scrapy.Field()
    impression = scrapy.Field()
    address = scrapy.Field()
    typeStr = scrapy.Field()
    continent = scrapy.Field()
    country = scrapy.Field()
    province = scrapy.Field()
    city = scrapy.Field()
    region = scrapy.Field()
    is_china = scrapy.Field()
    x = scrapy.Field()
    y = scrapy.Field()
    url = scrapy.Field()
    weight = scrapy.Field()
    is_landscape = scrapy.Field()
    city_id = scrapy.Field()
    sight_id = scrapy.Field()
    sight_surl = scrapy.Field()

    surl = scrapy.Field()
    total = scrapy.Field()

    page = scrapy.Field()
    index = scrapy.Field()
    pass





updatesight().main()