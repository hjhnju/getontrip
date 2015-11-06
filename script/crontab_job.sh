#!/bin/sh
url=http://123.57.46.229:8983
curl $url/solr/city/dataimport
curl $url/solr/sight/dataimport
curl $url/solr/content/dataimport
curl $url/solr/topic/dataimport
curl $url/solr/book/dataimport
curl $url/solr/wiki/dataimport
curl $url/solr/video/dataimport
