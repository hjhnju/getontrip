# -*- coding: utf-8 -*-
 
from scrapy.utils.url import urljoin_rfc
from scrapy.spiders import CrawlSpider, Rule
from scrapy.selector import Selector
from scrapy.http import Request 
from scrapy.linkextractors import LinkExtractor as sle

from article.items import ArticleItem


class DmozSpider(CrawlSpider):
    name = "dmoz"
    allowed_domains = ["dooland.com"]
    start_urls = [
        # "http://www.dmoz.org/Computers/Programming/Languages/Python/Books/",
        # "http://www.dmoz.org/Computers/Programming/Languages/Python/Resources/"
        "http://www.dooland.com/magazine/article_784457.html"
    ]

    rules = [
        # Rule(sle(allow=('/Resources/')),callback='parse_details'),
        Rule(sle(allow=('/magazine/article_784457\.html')),
             callback='parseBooks', follow=False),
        # Rule(sle(allow=()), callback='parse_pages'),
        # 分页
        # Rule(sle(allow=(),restrict_xpaths=('//*[@id="page"]')),callback='parse_pages',follow=True)
    ]

    def parseBooks(self, response): 
        for sel in response.xpath('//*[@id="main"]/article/div[1]/div[1]'):
            item = ArticleItem()
            item['url'] = sys.getdefaultencoding()
            # item['title'] = sys.getdefaultencoding()
            item['title'] = sel.xpath('h1/text()').extract()[0].strip()
            #
            #
            return item
