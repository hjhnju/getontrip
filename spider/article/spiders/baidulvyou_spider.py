# -*- coding: utf-8 -*-
# import sys
import logging  
import codecs
import json
from json import *
import re 
import os

import urlparse
from scrapy.utils.url import urljoin_rfc
from scrapy.spiders import CrawlSpider, Rule
from scrapy.selector import Selector
from scrapy.http import Request
from scrapy.linkextractors import LinkExtractor as sle

from article.items import SightItem


class BaidulvyouSpider(CrawlSpider):
    name = "baidulvyou"
    # 减慢爬取速度 为1s
    # download_delay = 1
    allowed_domains = ["lvyou.baidu.com"]
    start_urls = [
        # 'http://lvyou.baidu.com/destination/ajax/jingdian?format=ajax&pn=1&rn=50&seasonid=5&surl=taiguo'
        # 'http://lvyou.baidu.com/destination/ajax/jingdian?format=ajax&pn=4&rn=1&seasonid=5&cid=500&surl=zhongguo'
        'http://lvyou.baidu.com/plan/ajax/getcitylist?format=ajax',
         # 'http://lvyou.baidu.com/destination/ajax/jingdian?format=ajax&surl=aerbeituojamanduotiyuchang' 
        # 'http://lvyou.baidu.com/destination/ajax/jingdian?format=ajax&pn=1&rn=50&seasonid=5&surl=nuofuke',
        # 'http://lvyou.baidu.com/destination/ajax/jingdian?format=ajax&pn=1&rn=50&seasonid=5&surl=lasiweijiasi',
        # 'http://lvyou.baidu.com/destination/ajax/jingdian?format=ajax&pn=1&rn=50&seasonid=5&surl=lundun',
        # 'http://lvyou.baidu.com/destination/ajax/jingdian?format=ajax&pn=1&rn=50&seasonid=5&surl=tuerku',
        # 'http://lvyou.baidu.com/destination/ajax/jingdian?format=ajax&pn=2&rn=50&seasonid=5&surl=saercibao',
        # 'http://lvyou.baidu.com/destination/ajax/jingdian?format=ajax&pn=1&rn=50&seasonid=5&surl=aizhi',
        # 'http://lvyou.baidu.com/destination/ajax/jingdian?format=ajax&pn=3&rn=50&seasonid=5&surl=shennaichuan',
        # 'http://lvyou.baidu.com/destination/ajax/jingdian?format=ajax&pn=1&rn=50&seasonid=5&surl=lvbeike',
        # 'http://lvyou.baidu.com/destination/ajax/jingdian?format=ajax&pn=3&rn=50&seasonid=5&surl=xini',
        # 'http://lvyou.baidu.com/destination/ajax/jingdian?format=ajax&pn=2&rn=50&seasonid=5&surl=yuekejun',
        # 'http://lvyou.baidu.com/destination/ajax/jingdian?format=ajax&pn=1&rn=50&seasonid=5&surl=jiaergeda',
        # 'http://lvyou.baidu.com/destination/ajax/jingdian?format=ajax&pn=1&rn=50&seasonid=5&surl=luerdao',
        # 'http://lvyou.baidu.com/destination/ajax/jingdian?format=ajax&pn=1&rn=50&seasonid=5&surl=shengandongniao',
        # 'http://lvyou.baidu.com/destination/ajax/jingdian?format=ajax&pn=1&rn=50&seasonid=5&surl=gongcheng',
    ]

    rules = [
        # Rule(sle(allow=('/plan/ajax/getcitylist')),callback='parse_country',follow=True) 
    ]

    zhixiashi = ['chongqing','shanghai','beijing','tianjin','taiwan','xianggang','aomen']

    # 第一步 抓取所有国家名称 返回城国家列表页url
    def parse(self, response): 
        BaidulvyouSpider.mkdir(r'E:/sight/meta/waiguo/')
        BaidulvyouSpider.mkdir(r'E:/sight/meta/zhongguo/')

        items = []
         
        # 转换成json
        result = JSONDecoder().decode(
            response.body)['data']['list'][2]['sub_info']

        # 初始化中国的数据
        item = SightItem()
        item['country'] = '中国'
        item['surl'] = 'zhongguo'
        item['url'] = 'http://lvyou.baidu.com/destination/ajax/jingdian?format=ajax&pn=1&rn=50&seasonid=5&surl=zhongguo'
        items.append(item)

        for jsonitem in result:
            countryItem = jsonitem['cities']
            for country in countryItem:
                item = SightItem()
                item['url'] = 'http://lvyou.baidu.com/destination/ajax/jingdian?format=ajax&pn=1&rn=50&seasonid=5&surl='+country['surl']
                # items.append(item)



        for item in items:
            yield Request(item['url'], callback=self.parse_city)

    # 根据国家名称 查询城市名称 返回城市列表页url
    def parse_city(self, response):
    # def parse(self, response): 
        items = []
        pItems = []
        result = JSONDecoder().decode(response.body)['data']
         
        #总数
        total = result['scene_total']  
        
        if total > 0: 
            scene_list = result['scene_list'] 
            # province = result['scene_path'][2]['surl']
            province = ''
            currentUrl = response.url
            urlParams = dict([(k,v[0]) for k,v in urlparse.parse_qs(urlparse.urlparse(currentUrl).query).items()])
            # 当前页码
            currentPage = int(urlParams['pn'])
            # 如果是第一页，则生成后续页码的url，并返回当前parse_city函数
            if currentPage == 1:
                pageNum = (total/50)+1  
                for x in range(2,(pageNum+1)):  
                    item = SightItem()
                    if SPIDER.zhixiashi.count(province)>0:
                        item['url'] = 'http://lvyou.baidu.com/destination/ajax/jingdian?format=ajax&pn=%d&rn=50&seasonid=5&surl=%s' % (x,result['surl'])
                        pass
                    else:
                        item['url'] = 'http://lvyou.baidu.com/destination/ajax/jingdian?format=ajax&pn=%d&rn=50&seasonid=5&cid=500&surl=%s' % (x,result['surl'])
                        pass
                    pItems.append(item)
                    pass
                
                pass

            for index in range(len(scene_list)): 
                jsonitem = scene_list[index]  
                item = SightItem() 
                item['surl'] =  jsonitem['surl'] 
                item['weight'] = (currentPage-1)*50+index+1 
                item['url'] = 'http://lvyou.baidu.com/destination/ajax/jingdian?format=ajax&pn=1&rn=50&seasonid=5&surl='+jsonitem['surl']
                items.append(item) 
                pass
            

            

            for item in pItems: 
                yield Request(item['url'],  callback=self.parse_city)
                pass 

            for item in items:
                yield Request(item['url'],meta={'psurl': item['surl']}, callback=self.parse_sight)
            
            

            pass

    # 根据城市名称 查询景点列表 返回景点列表页url
    # def parse(self, response):
    def parse_sight(self, response):  
        items = []
        pItems = [] 
        dItems = []
        urlParams = dict([(k,v[0]) for k,v in urlparse.parse_qs(urlparse.urlparse(response.url).query).items()]) 
        result = JSONDecoder().decode(response.body)['data']
        total = result['scene_total'] 
        psurl = response.meta.get('psurl',urlParams['surl'])
        pathLevel = len(result['scene_path']) 
        scene_path = result['scene_path']
        # province = result['scene_path'][2]['surl']
        

        savepath = u'E:\\sight\\meta';
        if result['is_china']=='1':
            savepath = savepath + '\\zhongguo\\'
            pass
        else:
            savepath = savepath + '\\waiguo\\'
            pass
        # 如果当前后续没有景点，则本身作为一个景点处理
        if total==0: 
            item = SightItem()
            item['weight'] = 1
            item['url'] = 'http://lvyou.baidu.com/destination/ajax/jingdian?format=ajax&surl='+result['surl']
            # surl作为文件名，去掉非法字符 \ / : * ? " < > |
            filename = result['surl']
            filename = re.sub(r'[|]', '', filename)
            filename = re.sub(r'[\\|/|:|\*|\?|\"|<|>]', '', filename+'.txt')

            # items.append(item) 
            if not os.path.exists(savepath+filename):
                yield Request(item['url'] ,meta={'item': item}, callback=self.parse_details)
                pass  
            pass
         
        if total > 0 and (pathLevel<5 or (pathLevel>=5 and result['scene_path'][3]==psurl)):
        # if total > 0 and ((pathLevel<5 and ((SPIDER.zhixiashi.count(province)>0 and psurl==province) or (SPIDER.zhixiashi.count(province)<0))) or (pathLevel>=5 and result['scene_path'][3]==psurl)):
            
            scene_list = result['scene_list']
              

            # 当前页码 
            currentPage = int(urlParams['pn']) 
            # 如果是第一页，则生成后续页码的url，并返回当前parse_sight函数
            if currentPage == 1:
                pageNum = (total/50)+1 
                for x in range(2,(pageNum+1)):  
                    item = SightItem()
                    item['surl'] =  urlParams['surl']  
                    item['url'] = 'http://lvyou.baidu.com/destination/ajax/jingdian?format=ajax&pn=%d&rn=50&seasonid=5&surl=%s' % (x,result['surl'])
                    pItems.append(item)
                    pass
                
                pass

            for index in range(len(scene_list)): 
                jsonitem = scene_list[index]   
          
                item = SightItem()
                item['weight'] = (currentPage-1)*50+index+1
                item['url'] = 'http://lvyou.baidu.com/destination/ajax/jingdian?format=ajax&surl='+jsonitem['surl']
                # surl作为文件名，去掉非法字符 \ / : * ? " < > |
                filename = jsonitem['surl']
                filename = re.sub(r'[|]', '', filename)
                filename = re.sub(r'[\\|/|:|\*|\?|\"|<|>]', '', filename)
                # items.append(item)
                if not os.path.exists(savepath+filename+'.txt'):
                    items.append(item)
                    pass 
                  
                pass

            # 如果当前城市的级别为5，则本身作为一个景点处理
            if scene_path[len(scene_path)-1]['scene_layer']=='5': 
                item = SightItem()
                item['weight'] = (currentPage-1)*50+index+1
                item['url'] = 'http://lvyou.baidu.com/destination/ajax/jingdian?format=ajax&surl='+result['surl']
                # surl作为文件名，去掉非法字符 \ / : * ? " < > |
                filename = result['surl']
                filename = re.sub(r'[|]', '', filename)
                filename = re.sub(r'[\\|/|:|\*|\?|\"|<|>]', '', filename+'.txt')

                # items.append(item) 
                if not os.path.exists(savepath+filename):
                    items.append(item) 
                    pass  
                pass

            # for item in dItems: 
            #     yield Request(item['url'] ,meta={'item': item}, callback=self.parse_details)
            #     pass 

            for item in pItems:
                yield Request(item['url'],meta={'psurl': item['surl']},  callback=self.parse_sight)
                pass 

            for item in items:
                yield Request(item['url'] ,meta={'item': item}, callback=self.parse_details)
                pass 
            
            pass
 

    # 景点详情详情
    def parse_details1(self, response):
    # def parse(self, response): 
        typeList = {0: '', 1: "城市", 2: "古镇", 3: "乡村", 4: "海边", 5: "沙漠", 6: "山峰", 7: "峡谷", 8: "冰川", 9: "湖泊", 10: "河流", 11: "温泉", 12: "瀑布", 13: "草原", 14: "湿地", 15: "自然保护区",
                    16: "公园", 17: "展馆", 18: "历史建筑", 19: "现代建筑", 20: "历史遗址", 21: "宗教场所", 22: "观景台", 23: "陵墓", 24: "学校", 25: "故居", 26: "纪念碑", 27: "其他", 28: "购物娱乐", 29: "休闲度假"}
        typeStr = ''

        result = JSONDecoder().decode(response.body)['data']
        
        ex = result['ext']
        scene_path = result['scene_path']
        type_list = ex.get('cids','').split(',')
        pic_list = result.get('pic_list', [])
        map_info = ex.get('map_info','')
 
        item = response.meta.get('item',SightItem())

        surl = result['surl'].strip().encode('utf8', 'ignore').decode('utf8')
        item['surl'] = surl
         
        item['is_china'] = result['is_china']
        item['describe'] = ex.get('more_desc','').strip()
        item['impression'] = ex.get('impression', ex.get('abs_desc','')).strip()
        item['address'] = ex.get('address','').strip()
        item['level'] = ex.get('level','').strip()
        item['url'] = 'http://lvyou.baidu.com/'+surl
        # 特殊处理名称
        if result['sname'] == '':
            item['name'] = surl
            pass
        else:
            item['name'] = result['sname'].strip().encode('utf8', 'ignore').decode('utf8')
            pass
        # 特殊处理所属行政区域
        item['continent'] = scene_path[0]['sname'].strip()
        item['country'] = scene_path[1]['sname'].strip()
        item['province'] = scene_path[2]['sname'].strip()
        pathLevel = len(scene_path)
        if pathLevel == 4:
            #类型为：城市 ，
            if type_list.count('1')>0: 
                item['city'] = scene_path[3]['sname'].strip() 
                pass
            else:
                item['city'] = scene_path[2]['sname'].strip()
                pass 
            item['region'] = ''
            pass
        elif pathLevel == 5: 
            item['city'] = scene_path[3]['sname'].strip()
            # 直辖市 则不分配地区
            if type_list.count('1')>0:  
                item['region'] = scene_path[4]['sname'].strip()
            else:
                item['region'] = ''
            pass
        elif pathLevel >= 6:
            item['city'] = scene_path[3]['sname'].strip()
            item['region'] = scene_path[4]['sname'].strip()
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
        if pic_list==False or len(pic_list)==0 or pic_list['pic_url']==None:
            item['image'] = ''
            pass
        else:
            item['image'] = 'http://hiphotos.baidu.com/lvpics/pic/item/' + \
                pic_list['pic_url']+'.jpg'
            pass

        # 特殊处理景点类型
        for index in range(0, len(type_list)):
            if type_list[index] != '':
                typeStr += ' ' + typeList[int(type_list[index])]
                pass
            pass
        item['typeStr'] = typeStr.strip().decode('utf8')
        
        if type_list.count('1')>0: 
            logging.info("Item type is city: %s :,%s" % (result['sname'],result['surl'])) 
            # yield Request('http://lvyou.baidu.com/destination/ajax/jingdian?format=ajax&pn=1&rn=50&seasonid=5&surl=%s' % (result['surl']),  callback=self.parse_sight) 
            pass 
        yield item
    
    def parse_details(self, response):
    # def parse(self, response): 
        typeList = {0: '', 1: "城市", 2: "古镇", 3: "乡村", 4: "海边", 5: "沙漠", 6: "山峰", 7: "峡谷", 8: "冰川", 9: "湖泊", 10: "河流", 11: "温泉", 12: "瀑布", 13: "草原", 14: "湿地", 15: "自然保护区",
                    16: "公园", 17: "展馆", 18: "历史建筑", 19: "现代建筑", 20: "历史遗址", 21: "宗教场所", 22: "观景台", 23: "陵墓", 24: "学校", 25: "故居", 26: "纪念碑", 27: "其他", 28: "购物娱乐", 29: "休闲度假"}
        typeStr = ''
        item = response.meta.get('item',SightItem())

        result = JSONDecoder().decode(response.body)['data'] 
        
        result['weight'] = result.get('weight',item['weight'])
         


        surl = result['surl'].strip().encode('utf8', 'ignore').decode('utf8')

        scene_path = result['scene_path']
        scene_layer_array = []
        province = ''
        city =''
        for x in xrange(len(scene_path)):
            scene_layer = scene_path[x]['scene_layer'];
            scene_layer_array.append(scene_layer)
            if scene_layer=='1':
                continent = scene_path[x]['surl'].strip()
                pass
            elif scene_layer=='2':
                country = scene_path[x]['surl'].strip()
                pass
            elif scene_layer=='3':
                province = scene_path[x]['surl'].strip()
                pass 
            elif scene_layer=='4':
                city = scene_path[x]['surl'].strip()
                pass 
            pass
         
        
        
        # 定义要创建的目录
        mkpath = r"E:/sight/data/"+ continent+'/' +country+'/'
        if province!='': 
            mkpath = mkpath+province+'/'
            pass
        if city!='':
            mkpath = mkpath+city+'/'
            pass  
        # 调用函数
        BaidulvyouSpider.mkdir(mkpath)
        
        # surl作为文件名，去掉非法字符 \ / : * ? " < > |
        filename = surl
        filename = re.sub(r'[|]', '', filename)
        filename = re.sub(r'[\\|/|:|\*|\?|\"|<|>]', '', filename)

        line = json.dumps(dict(result)) + "\n" 
        
        dataFile = codecs.open(mkpath+filename+'.txt', 'w', encoding='utf-8') 
         
        dataFile.write(line) 
        
        
        pathname = 'waiguo/'
        if result['is_china']=='1':
            pathname = 'zhongguo/'
            pass
        metaFile = codecs.open(r'E:/sight/meta/' + pathname + filename+'.txt', 'w', encoding='utf-8') 
        
        metaFile.write(line) 
        
        logging.info("sight saved to: %s.txt" % (mkpath+filename)) 
        # logging.info("Item type is city: %s :,%s" % (result['sname'],result['surl'])) 
        item = SightItem()     
        yield item


    @staticmethod
    def mkdir(path):
     
        # 去除首位空格
        path=path.strip()
        # 去除尾部 \ 符号
        path=path.rstrip("\\")
     
        # 判断路径是否存在
        # 存在     True
        # 不存在   False
        isExists=os.path.exists(path)
     
        # 判断结果
        if not isExists:
            # 如果不存在则创建目录
            print path+'创建成功'.decode('utf-8')
            logging.info("path created success: %s" % (path)) 
            # 创建目录操作函数
            os.makedirs(path)
            return True
        else:
            # 如果目录存在则不创建，并提示目录已存在
            print path+' 目录已存在'.decode('utf-8')
            logging.info("path already exists: %s" % (path)) 
            return False

SPIDER = BaidulvyouSpider()
