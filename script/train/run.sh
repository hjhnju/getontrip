#!/bin/bash

#创建索引
echo createIndex."\r\n"
php ./createIndex.php

#创建词库索引
echo createVocIndex."\r\n"
php ./createVocIndex.php

#话题标题匹配景点名向量
echo getTitleVector."\r\n"
php ./getTitleVector.php

#话题内容分词后的向量
echo getTopicVector."\r\n"
php ./getTopicVector.php

#景点描述的分词后的向量
echo getDescVector."\r\n"
php ./getDescVector.php
