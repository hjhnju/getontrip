# -*- coding: utf-8 -*-
import sys
import logging 
from json import *
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
        # 'http://lvyou.baidu.com/destination/ajax/jingdian?format=ajax&pn=1&rn=50&seasonid=5&cid=500&surl=zhongguo'
        'http://lvyou.baidu.com/plan/ajax/getcitylist?format=ajax'  
    ]

    rules = [
        # Rule(sle(allow=('/plan/ajax/getcitylist')),callback='parse_country',follow=True) 
    ]

    # 第一步 抓取所有国家名称 返回城国家列表页url
    def parse(self, response): 

        items = []

        # 转换成json
        result = JSONDecoder().decode(
            response.body)['data']['list'][2]['sub_info']

        for jsonitem in result:
            countryItem = jsonitem['cities']
            for country in countryItem:
                item = SightItem()
                item['url'] = 'http://lvyou.baidu.com/destination/ajax/jingdian?format=ajax&pn=1&rn=50&seasonid=5&cid=500&surl='+country['surl']
                items.append(item)

        # 初始化中国的数据
        # item = SightItem()
        # item['country'] = '中国'
        # item['surl'] = 'zhongguo'
        # item['url'] = 'http://lvyou.baidu.com/destination/ajax/jingdian?format=ajax&pn=1&rn=50&seasonid=5&cid=500&surl=zhongguo'
        # items.append(item)

        for item in items:
            yield Request(item['url'], callback=self.parse_city)

    # 根据国家名称 查询城市名称 返回城市列表页url
    def parse_city(self, response):
    # def parse(self, response):
         
        items = []
        pItems = []
        result = JSONDecoder().decode(response.body)['data']
        total = result['scene_total']
        if total > 0:
            for jsonitem in result['scene_list']:
                item = SightItem()
                item['url'] = 'http://lvyou.baidu.com/destination/ajax/jingdian?format=ajax&pn=1&rn=50&seasonid=5&surl='+jsonitem['surl']
                items.append(item)

                # 如果是第一页，则生成后续页码的url，并返回当前parse_city函数
                if response.url.count('pn=1&') == 1:
                    pageNum = (total/50)+1 
                    for x in range(2,(pageNum+1)):  
                        item = SightItem()
                        item['url'] = 'http://lvyou.baidu.com/destination/ajax/jingdian?format=ajax&pn=%d&rn=50&seasonid=5&cid=500&surl=%s' % (x,result['surl'])
                        pItems.append(item)
                        pass
                    
                    pass

                pass

            for item in pItems:
                yield Request(item['url'],  callback=self.parse_city)
                pass 

            for item in items:
                yield Request(item['url'],  callback=self.parse_sight)
                pass
            pass

    # 根据城市名称 查询景点列表 返回景点列表页url
    # def parse(self, response):
    def parse_sight(self, response):
        items = []
        pItems = []
        result = JSONDecoder().decode(response.body)['data']
        total = result['scene_total'] 
        if total > 0:
            for jsonitem in result['scene_list']:
                item = SightItem()
                item['url'] = 'http://lvyou.baidu.com/destination/ajax/jingdian?format=ajax&surl='+jsonitem['surl']
                items.append(item)

                # 如果是第一页，则生成后续页码的url，并返回当前parse_sight函数
                if response.url.count('pn=1&') == 1:
                    pageNum = (total/50)+1 
                    for x in range(2,(pageNum+1)):  
                        item = SightItem()
                        item['url'] = 'http://lvyou.baidu.com/destination/ajax/jingdian?format=ajax&pn=%d&rn=50&seasonid=5&surl=%s' % (x,result['surl'])
                        pItems.append(item)
                        pass
                    
                    pass
                pass

            for item in pItems:
                yield Request(item['url'],  callback=self.parse_sight)
                pass 

            for item in items:
                yield Request(item['url'],  callback=self.parse_details)
                pass 

            pass

        

    # 景点详情详情
    def parse_details(self, response):
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

        item = SightItem() 
        surl = result['surl'].strip().encode('utf8', 'ignore').decode('utf8')
        item['surl'] = surl
        item['is_china'] = result['is_china']
        item['describe'] = ex.get('more_desc','').strip()
        item['impression'] = ex.get('impression', '').strip()
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
            item['city'] = scene_path[2]['sname'].strip()
            item['region'] = ''
            pass
        elif pathLevel == 5:
            item['city'] = scene_path[3]['sname'].strip()
            item['region'] = ''
            pass
        elif pathLevel == 6:
            item['city'] = scene_path[3]['sname'].strip()
            item['region'] = scene_path[4]['sname'].strip()
            pass
        elif pathLevel>6:
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
        if len(pic_list)==0 or pic_list['pic_url']==None:
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
            yield Request('http://lvyou.baidu.com/destination/ajax/jingdian?format=ajax&pn=1&rn=50&seasonid=5&surl=%s' % (result['surl']),  callback=self.parse_sight) 
            pass

        else:
            yield item


# SPIDER = BaidulvyouSpider()
