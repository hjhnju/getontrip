#!/bin/bash

#创建索引
echo createIndex
php ./createIndex.php

#创建词库索引
echo createVocIndex
php ./createVocIndex.php

#话题标题匹配景点名向量
echo getTitleVector
php ./getTitleVector.php

#话题内容分词后的向量
echo getTopicVector
php ./getTopicVector.php

#景点描述的分词后的向量
echo getDescVector
php ./getDescVector.php
