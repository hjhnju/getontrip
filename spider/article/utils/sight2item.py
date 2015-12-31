# -*- coding: utf-8 -*-
# sight2item  根据景点文件和城市文件导出sight_meta的数据 item
# fyy 2015.12.24
import os
import codecs
import re
from json import *
import json
import time
import scrapy


class sight2item:
    sightList = []
    landscapeList = []
    count = 59999
    # count = 110000
    cityList = []
    typeList = {0: '', 1: "城市", 2: "古镇", 3: "乡村", 4: "海边", 5: "沙漠", 6: "山峰", 7: "峡谷", 8: "冰川", 9: "湖泊", 10: "河流", 11: "温泉", 12: "瀑布", 13: "草原", 14: "湿地", 15: "自然保护区",
                    16: "公园", 17: "展馆", 18: "历史建筑", 19: "现代建筑", 20: "历史遗址", 21: "宗教场所", 22: "观景台", 23: "陵墓", 24: "学校", 25: "故居", 26: "纪念碑", 27: "其他", 28: "购物娱乐", 29: "休闲度假"}
        
    outfile_all = codecs.open(
            'E:\\sight\\all.json', 'w', encoding='utf-8')
    if not outfile_all:
        print("cannot open the file %s for writing" % outfile_all)

    outfile_city_id0 = codecs.open(
            'E:\\sight\\city_id0.json', 'w', encoding='utf-8')
    if not outfile_city_id0:
        print("cannot open the file %s for writing" % outfile_city_id0)
    
    outfile_log = codecs.open(
            'E:\\sight\\log.json', 'w', encoding='utf-8')
    if not outfile_log:
        print("cannot open the file %s for writing" % outfile_log)
    outfile_log.write("let us begin:".decode('utf-8')+time.asctime(time.localtime(time.time()))+"\n")

    @staticmethod
    def main():
        # 打开景点文件
        dir = u"E:\\sight\\meta_test"
        wildcard = ".txt"

        # 打开城市文件，读取城市列表
        cityFile = open('E:\\sight\\cityid_name.txt')
        city_object = cityFile.read().decode('utf-8')
        city_object = re.sub(r'[\r|\n]', '', city_object)
        sight2item.cityList = JSONDecoder().decode(city_object.strip())

        cityFile.close()
       
        

        outfile_waiguo = codecs.open(
            'E:\\sight\\waiguo.json', 'w', encoding='utf-8')
        if not outfile_waiguo:
            print("cannot open the file %s for writing" % outfile_waiguo)

        outfile_zhongguo = codecs.open(
            'E:\\sight\\zhongguo.json', 'w', encoding='utf-8')
        if not outfile_zhongguo:
            print("cannot open the file %s for writing" % outfile_zhongguo)

        sight2item.ListFilesToTxt(
            dir, file, wildcard, 1, outfile_waiguo, outfile_zhongguo)

        sight2item.paraseLandscape(outfile_waiguo, outfile_zhongguo)

        outfile_waiguo.close()
        outfile_zhongguo.close()
        sight2item.outfile_all.close()
        
        sight2item.json2sql(sight2item.sightList,sight2item.landscapeList)
        sight2item.outfile_log.write('total:'+str(sight2item.count)+"\n")
        sight2item.outfile_log.write("end:"+time.asctime(time.localtime(time.time()))+"\n")
        sight2item.outfile_log.close()
        sight2item.outfile_city_id0.close()
        pass

    @staticmethod
    def ListFilesToTxt(dir, file, wildcard, recursion, outfile_waiguo, outfile_zhongguo):
        exts = wildcard.split(" ")
        files = os.listdir(dir)
        for name in files:
            fullname = os.path.join(dir, name)
            if(os.path.isdir(fullname) & recursion):
                sight2item.ListFilesToTxt(fullname, file, wildcard, recursion, outfile_waiguo, outfile_zhongguo)
            else:
                for ext in exts:
                    if(name.endswith(ext)):
                        # print name
                        file_object = open(dir+'\\'+name)
                        try:
                            jsonStr = file_object.read().decode('utf-8')
                            sight2item.paraseItem(
                                jsonStr, outfile_waiguo, outfile_zhongguo,name)
                        except Exception, e:
                            sight2item.outfile_log.write("ERROR:open faild :"+ name+ str(sight2item.count))
                            sight2item.outfile_log.write(str(e.args)+ "\n")   
                        finally:
                            file_object.close()
                        break

    @staticmethod
    def paraseItem(jsonStr, outfile_waiguo, outfile_zhongguo,filename):
        
        item = SightItem()
        jsonStr = re.sub(r'[\r|\n]', '', jsonStr)
        jsonStr = re.sub(r"'", "''", jsonStr)
        jsonStr = re.sub(r"\\'", "'", jsonStr)
       

        result = JSONDecoder().decode(jsonStr.strip()) 
        typeStr = ''
        try: 
            sight2item.count = sight2item.count+1
            print (sight2item.count-59999)
            ex = result.get('ext', {})
            scene_path = result.get('scene_path', [])
            type_list = ex.get('cids', '').split(',')
            pic_list = result.get('pic_list', [])
            map_info = ex.get('map_info', '')
            is_china = result.get('is_china', '0')
            
            surl = result['surl'].strip().encode('utf8', 'ignore').decode('utf8')
            # 增加处理城市的情况，如果类型为城市 且下面还有景点则剪贴该景点的文件到城市目录下
            # 然后return;
            if type_list.count('1') > 0:

                pass
            
            


            item['surl'] = surl
            item['weight'] = result['weight']
            item['is_china'] = is_china
            item['describe'] = ex.get('more_desc', '').strip()
            item['impression'] = ex.get(
                'impression', ex.get('abs_desc', '')).strip()
            item['address'] = ex.get('address', '').strip()
            item['level'] = ex.get('level', '').strip()
            item['url'] = 'http://lvyou.baidu.com/'+surl

            # 特殊处理名称
            if result['sname'] == '':
                item['name'] = surl
                pass
            else:
                item['name'] = result['sname'].strip().encode(
                    'utf8', 'ignore').decode('utf8')
                pass

            # 特殊处理坐标
            if map_info != '' and map_info.find(',') != -1:
                item['x'] = map_info.split(',')[0]
                item['y'] = map_info.split(',')[1]
                pass
            else:
                item['x'] = ''
                item['y'] = ''
                pass

            # 特殊处理图片
            if pic_list == False or len(pic_list) == 0 or pic_list['pic_url'] == None:
                item['image'] = ''
                pass
            else:
                item['image'] = 'http://hiphotos.baidu.com/lvpics/pic/item/' + \
                    pic_list['pic_url']+'.jpg'
                pass

            # 特殊处理景点类型
            for index in range(0, len(type_list)):
                if type_list[index] != '':
                    typeStr += ' ' +sight2item.typeList[int(type_list[index])]
                    pass
                pass
            item['typeStr'] = typeStr.strip().decode('utf8')

            # 特殊处理所属行政区域
            scene_layer_array = []
            continent = ''
            country = ''
            province = ''
            city = ''

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

            # 例子 (湖南3-益阳4-南县4)
            # 则南县作为一个景点
            #  
            item['continent'] = continent
            item['country'] = country
            item['province'] = province
            item['city'] = city
            item['id'] = sight2item.count
            
             
            item['city_id'] = sight2item.getCityID(city)

            if item['city_id']==0:
                line = item['url']+',"country":'+item['country']+',"city":'+item['city']+ "\n"
                
                sight2item.outfile_city_id0.write(line)
                pass

            # 特殊处理景点或者景观
            
            # 景观的情况 scene_layer[5,6] 加入景观列表
            if five_layer == True and six_layer == True:
                item['is_landscape'] = 1
                item['sight_surl'] = five_surl
                sight2item.landscapeList.append(item)
                pass

            else:
                item['is_landscape'] = 0
                item['sight_id'] = -1
                sight2item.sightList.append(item)
                line = json.dumps(dict(item)) + "\n"
                sight2item.outfile_all.write(line)
                # 写入文件
                if is_china == '1': 
                    outfile_zhongguo.write(line)
                    pass
                else: 
                    outfile_waiguo.write(line)
                    pass
                pass
            pass
        except Exception, e:
            sight2item.outfile_log.write("ERROR:"+ filename)
            sight2item.outfile_log.write(str(e.args)+ "\n")  
            line = json.dumps(dict(item)) + "\n"
            sight2item.outfile_log.write("line:"+line.decode('unicode_escape'))
            # raise e
        
        

        pass

    @staticmethod
    def paraseLandscape(outfile_waiguo, outfile_zhongguo):
         
        landscapeList = sight2item.landscapeList
        for x in xrange(len(landscapeList)):
            item = landscapeList[x]
            # 找到所属景点的id
            item['sight_id'] = sight2item.getSightID(item['sight_surl'])
            is_china = item['is_china']
            line = json.dumps(dict(item)) + "\n"
            sight2item.outfile_all.write(line)
            # 写入文件
            if is_china == '1': 
                outfile_zhongguo.write(line)
                pass
            else: 
                outfile_waiguo.write(line)
                pass

            pass

        pass

    # 获取城市id
    @staticmethod
    def getCityID(nameStr):
        cityList = sight2item.cityList
        for x in xrange(len(cityList)):
            cityName = cityList[x]['name']  
            if cityName.find(nameStr) > -1:
                return cityList[x]['id']
                pass
            pass
        return 0

    # 获取景点id
    @staticmethod
    def getSightID(surl):
        sightList = sight2item.sightList
        for x in xrange(len(sightList)):
            sight = sightList[x]
            if sight['surl'].find(surl) > -1:
                return sight['id']
                pass
            pass
        return 0

    # json转sql
    @staticmethod
    def json2sql(sightList, landscapeList):
        sight2item.outfile_log.write("开始json2sql：".decode('utf-8')+"\n")
        for x in xrange(len(landscapeList)):
            sightList.append(landscapeList[x])
            pass
        
        # print json.dumps(data, ensure_ascii=False)
        file_object = codecs.open(
            u"E:\\sight\\sight_meta.sql", 'w', "utf-8")
        file_object.write('TRUNCATE TABLE `sight_meta_test`;'+"\n")
        for x in xrange(len(sightList)):
            
            item = sightList[x] 
            str = u"" 
            if (x+1)%20==1:
                str = str + u"\n" + \
                "INSERT INTO `sight_meta_test` (`id`,`name`,`level`,`image`, `describe`, `impression`,`address`,`type`,`continent`,`country`,`province`,`city`,`url`,`x`,`y`,`is_china`,`weight`,`city_id`,`sight_id`,`create_time`,`update_time`)  VALUES"+"\n"
                pass
            
            str = str + " (%s,'%s', '%s' , '%s' , '%s' ,'%s' ,'%s' ,'%s', '%s' , '%s' , '%s' ,'%s' ,'%s' ,'%s' ,'%s','%s','%s','%s','%s','%s','%s')" % (item['id'],item['name'], item['level'], item['image'], item['describe'],
                 item['impression'], item['address'], item[
                    'typeStr'], item['continent'],
                 item['country'], item['province'], item['city'], item['url'], item['x'], item['y'], item['is_china'], item['weight'],item['city_id'], item['sight_id'],
                 repr(int(time.time())), repr(int(time.time()))
                 )
            if (x+1)%20==0 or (x+1)==len(sightList):
                str = str + ";\n"
                pass
            else:
                str = str + ",\n"
                pass
            file_object.write(str)
            pass

         
        file_object.close()
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


sight2item().main()
