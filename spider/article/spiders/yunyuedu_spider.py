# -*- coding: utf-8 -*-
import sys
from json import *
from scrapy.utils.url import urljoin_rfc
from scrapy.spiders import CrawlSpider, Rule
from scrapy.selector import Selector
from scrapy.http import Request
from scrapy.linkextractors import LinkExtractor as sle

from article.items import ArticleItem


class YunyueduSpider(CrawlSpider):
    name = "yunyuedu"
    # 减慢爬取速度 为1s
    #download_delay = 1
    allowed_domains = ["yuedu.163.com"]
    start_urls = [
        "http://yuedu.163.com/book/category/category/12000/12001",
        # 'http://yuedu.163.com/source/79ac199725044d8e9b4d8464d3ab102c_4'
        # 'http://yuedu.163.com/book_reader/1381d284993e4590a9098ce964db2e2f_4/06cc8c9044b466785cc22e4b93739668_4'
    ]

    rules = [
        # Rule(sle(allow=('/book_reader/'), callback='parse_details', follow=True),
        # Rule(sle(allow=('/source/'), restrict_xpaths=(
        # '//*[@id="page-163-com"]/div[2]/div[3]/div/div[2]/div[2]/div/div[2]/div')), callback='parse_list', follow=True),

        # Rule(sle(allow=('/source/'), restrict_xpaths=(
        #     '//*[@id="page-163-com"]/div[2]/div[3]/div/div[2]/div[2]/div/div[2]/div')), callback='parse_list', follow=True),

        # 分页
        Rule(sle(allow=(),restrict_xpaths=('//*[@class="nums"]')), callback='parse_pages', follow=True),
    ]

    # 所有期次杂志的url list
    def parse_pages(self, response):
        items = []
        for sel in response.xpath('//*[@id="page-163-com"]/div[2]/div[3]/div/div[2]/div[2]/div/div[2]/div'):
            item = ArticleItem()
            sourceUuid = sel.xpath('a/@href').extract()[0].split('/')[2]
            item['author'] = sel.xpath('//*[@class="author-container"]/dl/dd/text()').extract()[0] 
            reload(sys)
            sys.setdefaultencoding('utf-8')
            item['source'] = sel.xpath('a/h2/text()').extract()[0].replace('《','').replace('》',' ').split( )[0] 
            item['issue'] = sel.xpath('a/h2/text()').extract()[0].replace('《','').replace('》',' ').split( )[1]
            item['url'] = 'http://yuedu.163.com/newBookReader.do?operation=info&catalogOnly=true&sourceUuid=' + \
                sourceUuid
            items.append(item)
            # yield item_url
        for item in items:
            yield Request(item['url'],meta={'sourceUuid': sourceUuid,'bookItem':item}, callback=self.parse_list)

    # 某一期 所有文章url list
    def parse_list(self, response):
        
        items = [] 
        bookItem = response.meta['bookItem']
        result = JSONDecoder().decode(response.body)
        for jsonitem in result['catalog']:
            if jsonitem['grade']==2:
                sourceUuid = result['book']['sourceUuid'] 
                item = ArticleItem() 
                item['author'] = bookItem['author']
                item['source'] = bookItem['source']
                item['issue'] = bookItem['issue']
                item['title'] =  jsonitem['title']
                item['url'] = 'http://yuedu.163.com/book_reader/'+sourceUuid+'/' + jsonitem['uuid']
                # 这里用content字段暂时保存下一步的ajax请求url
                item['content'] = 'http://yuedu.163.com/getArticleContent.do?sourceUuid='+sourceUuid+'&articleUuid=' + jsonitem['uuid']
                items.append(item)
                # yield item
                pass 
        for item in items: 
            yield Request(item['content'],meta={'item': item}, callback=self.parse_details) 

    # 文章详情
    def parse_details(self, response): 

        item = response.meta['item']  
        item['content'] = JSONDecoder().decode(response.body)['content'].decode('base64','strict').strip().decode('utf8') 
        
        # TODO 来源ID
        item['source_id'] = ''
        item['subtitle'] = ''
        item['keywords'] = ''
        
        return item

SPIDER = YunyueduSpider()
