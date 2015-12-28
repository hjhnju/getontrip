# -*- coding: utf-8 -*-

# Define your item pipelines here
#
# Don't forget to add your pipeline to the ITEM_PIPELINES setting
# See: http://doc.scrapy.org/en/latest/topics/item-pipeline.html
#
# from scrapy import log

from twisted.enterprise import adbapi
from scrapy.exceptions import DropItem
import logging
import os
import time
import json
import codecs
import re
import MySQLdb
import MySQLdb.cursors

from article.items import SightItem
from article.utils.htmlParser import htmlParser 

class ArticlePipeline(object):

    def __init__(self):
        # 打开线下数据库连接
        self.dbpool = adbapi.ConnectionPool(
            dbapiName='MySQLdb',
            host='xingjiaodai.mysql.rds.aliyuncs.com',
            db='getontrip',
            user='xingjiaodai',
            passwd='xingjiaodai',
            cursorclass=MySQLdb.cursors.DictCursor,
            charset='utf8',
            use_unicode=False
        )

        self.file = codecs.open('items.json', 'w', encoding='utf-8')

    # 过滤item
    def process_item(self, item, spider):
        # line = json.dumps(dict(item)) + "\n"
        # self.file.write(line.decode('unicode_escape')) 
        return item
        # 不同的spider 调用不同的过滤方法
        spiderName = spider.name
        if spiderName == 'dooland':
            item = self.filter_item(item, spiderName)
            # 插入数据库
            query = self.dbpool.runInteraction(
                self._conditional_insert_article, item)
            # query = self.dbpool.runInteraction(
            #     self._kanlishi, item)
             
            pass
        elif spiderName == 'yunyuedu':
            item = self.filter_item(item, spiderName)
            # 插入数据库
            query = self.dbpool.runInteraction(
                self._conditional_insert_article, item)
            pass
        elif spiderName == 'baidulvyou':
            # 插入数据库
            query = self.dbpool.runInteraction(
                self._conditional_insert_sight, item)

            pass

        

        return item

    # 过滤html
    def filter_item(self, item, spiderName):
        content = item['content']
        
        
        content = re.sub(r'<br(\s)*\/(\s)*>', '<br>', content)
        if content.find('<p>') == -1:
            content = '<p>'+content+'</p>'
            pass
        # 过滤掉样式
        content = re.sub(r'<p.*?>', '<p>', content)
        content = re.sub(r'<b\s.*?>', '<b>', content)
        # content = re.sub(r'<br.*?>', '<p></p>', content)

        # 过滤content  去掉div span标签
       
        content = re.sub(r'<span.*?>', '', content)
        content = re.sub(r'<\/span>', '', content)

        # 去掉空白  测试一下
        content = re.sub(r'<p>[\s|　]+', '<p>', content)
        content = re.sub(r'<p>[&nbsp]*;', '<p>', content) 
        content = re.sub(ur'<p>[\u3000]*', '<p>',content) 
        # 去掉H2标题 HR线
        if spiderName == 'yunyuedu': 
            content = re.sub(r'<h2.*?>.*?<\/h2>', '', content)
            content = re.sub(r'<hr.*?\/>', '', content)
            pass
       
        content = re.sub(r'<div.*?>', '', content)
        content = re.sub(r'<\/div>', '', content)

        item['content'] = content
        # if content.find('<img'):
        #     print 'sadad'
        #     hp = htmlParser()  
        #     # 传入要分析的数据，是html的。  
        #     hp.feed(content) 
        #     pass
        return item
        pass
    


    # 插入景点库
    def _conditional_insert_sight(self, tx, item):
        item['name'] = re.sub(r"'", "\\'", item['name'])
        sql = "select * from sight_meta \
               where name = '%s' and (city='%s' or city=province)" % (item['name'], item['city'])
        # print sql
        tx.execute(sql)
        result = tx.fetchone()
        if result:
            if result['weight'] != item['weight']:
                logging.info("Item weight updated in db:id->%s(%s), %s-->> ,%s" %
                             (result['id'], result['name'].decode('utf8'), result['weight'], item['weight']))
                pass
            try:
                tx.execute(
                    "update `sight_meta` set `level`=%s,`image`=%s, `describe`=%s, `impression`=%s,`address`=%s,`type`=%s,`continent`=%s,`country`=%s,`province`=%s,`city`=%s,`region`=%s,`url`=%s,`x`=%s,`y`=%s,`is_china`=%s,`weight`=%s,`update_time`=%s"
                    "where `id`=%s  ",
                    (item['level'], item['image'], item['describe'],
                     item['impression'], item['address'], item[
                        'typeStr'], item['continent'],
                     item['country'], item['province'], item['city'], item[
                        'region'], item['url'], item['x'], item['y'], item['is_china'], item['weight'],
                     repr(int(time.time())), result['id']
                     ))
            except MySQLdb.Error, e:
                logging.error("update Item:%s Error %d: %s" %
                              (item['surl'], e.args[0], e.args[1]))

            logging.info("Item already updated in db:id->%s, %s ,%s,%s" %
                         (result['id'], item['name'], item['surl'], item['weight']))
        else:
            try:
                tx.execute(
                    "insert into `sight_meta` (`name`,`level`,`image`, `describe`, `impression`,`address`,`type`,`continent`,`country`,`province`,`city`,`region`,`url`,`x`,`y`,`is_china`,`weight`,`create_time`,`update_time`)  "
                    "values (%s, %s , %s , %s ,%s ,%s ,%s ,%s, %s , %s , %s ,%s ,%s ,%s ,%s,%s,%s,%s,%s)",
                    (item['name'], item['level'], item['image'], item['describe'],
                     item['impression'], item['address'], item[
                        'typeStr'], item['continent'],
                     item['country'], item['province'], item['city'], item[
                        'region'], item['url'], item['x'], item['y'], item['is_china'], item['weight'],
                     repr(int(time.time())), repr(int(time.time()))
                     )
                )

            except MySQLdb.Error, e:
                logging.error("add Item:%s Error %d: %s" %
                              (item['surl'], e.args[0], e.args[1]))

            logging.info("Item stored in db: %s ,%s" %
                         (item['name'], item['surl']))
            pass

    # 插入数据库
    def _conditional_insert_article(self, tx, item):
        
        sql = 'SELECT * FROM recommend_article \
               WHERE url = "%s" ' % (item['url'], )
        tx.execute(sql)
        result = tx.fetchone()
         
        if result:
            try:
                tx.execute(
                    "update `recommend_article` set `content`=%s ,`subtitle`=%s,`keywords`=%s"
                    " where `id`=%s  ",
                    (item['content'],item['subtitle'],item['keywords'], result['id'])
                )
            except MySQLdb.Error, e:
                logging.error("update Item:%s Error %d: %s" %
                              (item['url'], e.args[0], e.args[1]))

            logging.info("Item already updated in db:id->%s, %s ,%s" %
                         (result['id'], item['title'], item['url']))
        else:
            tx.execute(
                "insert into recommend_article (title,subtitle,keywords,content,source, url, author,issue,create_time,update_time) "
                "values (%s, %s , %s , %s ,%s ,%s ,%s ,%s,%s, %s )",
                (item['title'], item['subtitle'], item['keywords'], item['content'], item['source'], item['url'],
                    item['author'], item['issue'],
                 repr(int(time.time())), repr(int(time.time())))
            )
            # log.msg("Item stored in db: %s" % item, level=log.DEBUG)
            logging.info("Item stored in db: %s ,%s" %
                         (item['title'], item['url']))
    

    # 批量修改看历史的文章
    def _kanlishi(self, tx, item):
        print 'aasdadads'
        sql = 'SELECT * FROM article \
               WHERE source = "看历史" ' 
        tx.execute(sql)
        result = tx.fetchall()
        for item in result:
            content = re.sub(r'<imgsrc=', '<img src=', item['content'])
            content = re.sub(r'<imgalt=', '<img alt=', content)
            try:
                tx.execute(
                    "update `article` set `content`=%s"
                    "where `id`=%s  ",
                    (content, item['id'])
                )
            except MySQLdb.Error, e:
                logging.error("update Item:%s Error %d: %s" %
                              (item['id'], e.args[0], e.args[1]))
                
            logging.info("Item already updated in db:id->%s, %s ,%s" %
                         (item['id'], item['title'], item['url']))
            pass