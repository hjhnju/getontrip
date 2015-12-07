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

        # 不同的spider 调用不同的过滤方法
        spiderName = spider.name
        if spiderName == 'dooland':
            item = self.filter_item(item, spiderName)
            # 插入数据库
            query = self.dbpool.runInteraction(
                self._conditional_insert_article, item)
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
             
            # if item['typeStr'].find('城市'.decode('utf8'))>-1:
            #     query = self.dbpool.runInteraction(self._conditional_insert_sight, item)
            #     pass
            # else:
            #     logging.info("Duplicate item found: %s" % item['name'])
            # raise DropItem("Duplicate item found: %s" % item['name'])
            pass
        # if item['city']=='北京'.decode('utf8'):
        #     tmpitem = SightItem()
        #     tmpitem['name'] = item['name']
        #     tmpitem['weight'] = item['weight'] 
        #     tmpitem['city'] = item['city']
        #     line = json.dumps(dict(tmpitem)) + "\n"
        #     self.file.write(line.decode('unicode_escape'))
        #     pass
       
         
        # line = json.dumps(dict(item)) + "\n"
        # self.file.write(line.decode('unicode_escape'))

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
        content = re.sub(r'<br.*?>', '</p><p>', content)

        # 过滤content  去掉div span标签
        content = re.sub(r'<div.*?>', '<p>', content)
        content = re.sub(r'<\/div>', '</p>', content)
        content = re.sub(r'<span.*?>', '', content)
        content = re.sub(r'<\/span>', '', content)

        # 去掉空白  测试一下
        content = re.sub(r'<p>\s+', '<p>', content)
        content = re.sub(r'<p>(&nbsp)*;', '<p>', content)
        content = content.replace(u'<p>(\u3000)*', u'<p>')

        # 去掉H2标题 HR线
        if spiderName == 'yunyuedu':
            content = re.sub(r'<h2.*?>.*?<\/h2>', '', content)
            content = re.sub(r'<hr.*?\/>', '', content)
            pass

        item['content'] = content
        return item
        pass

    # 插入景点库
    def _conditional_insert_sight(self, tx, item):
        item['name'] = re.sub(r"'", "\\'", item['name'])
        sql = "select * from sight_meta_test \
               where name = '%s' and (city='%s' or city=province)" % (item['name'], item['city'])
        # print sql
        tx.execute(sql)
        result = tx.fetchone()

        if result:
            if result['weight']!=item['weight']:
                 logging.info("Item weight updated in db:id->%s(%s), %s-->> ,%s" %
                         (result['id'],result['name'].decode('utf8'),result['weight'], item['weight']))
                 pass 
            try: 
                tx.execute(
                    "update `sight_meta_test` set `level`=%s,`image`=%s, `describe`=%s, `impression`=%s,`address`=%s,`type`=%s,`continent`=%s,`country`=%s,`province`=%s,`city`=%s,`region`=%s,`url`=%s,`x`=%s,`y`=%s,`is_china`=%s,`weight`=%s,`update_time`=%s"
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
                         (result['id'],item['name'], item['surl'], item['weight']))
        else:
            try:
                tx.execute(
                    "insert into `sight_meta_test` (`name`,`level`,`image`, `describe`, `impression`,`address`,`type`,`continent`,`country`,`province`,`city`,`region`,`url`,`x`,`y`,`is_china`,`weight`,`create_time`,`update_time`)  "
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

        sql = "select * from article \
               where url = '%s'" % (item['url'], )
        tx.execute(sql)
        result = tx.fetchone()

        if result:
            # log.msg("Item already stored in db: %s" % item, level=log.DEBUG)
            logging.info("Item already stored in db: %s ,%s" %
                         (item['title'], item['url']))

        else:
            tx.execute(
                "insert into article (title,content,source, url, author,issue,create_time,update_time) "
                "values (%s, %s , %s , %s ,%s ,%s ,%s ,%s)",
                (item['title'], item['content'], item['source'], item['url'],
                    item['author'], item['issue'],
                 repr(int(time.time())), repr(int(time.time())))
            )
            # log.msg("Item stored in db: %s" % item, level=log.DEBUG)
            logging.info("Item stored in db: %s ,%s" %
                         (item['title'], item['url']))
