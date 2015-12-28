# -*- coding: utf-8 -*-

# Define here the models for your scraped items
#
# See documentation in:
# http://doc.scrapy.org/en/latest/topics/items.html

import scrapy


class ArticleItem(scrapy.Item):
    # define the fields for your item here like:
    # name = scrapy.Field()
    title = scrapy.Field()
    content = scrapy.Field()
    source = scrapy.Field()
    source_id = scrapy.Field()
    issue = scrapy.Field()
    author = scrapy.Field()
    url = scrapy.Field()
    subtitle = scrapy.Field()
    keywords = scrapy.Field()

    pass


class SightItem(scrapy.Item): 
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

    surl = scrapy.Field() 
    total = scrapy.Field() 

    page = scrapy.Field()
    index = scrapy.Field()  
    pass
