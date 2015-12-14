<?php
if(file_exists('../../env.inc.php')){
    require_once '../../env.inc.php';
}
ini_set('memory_limit','512M');
const WORK_PATH        = "/home/work/data/";

const INDEX_LABEL      = "label_index";

const INDEX_SIGHT      = "feature_sight_index";
const INDEX_VOC        = "feature_voc_index";

const INDEX_TOPIC      = "content_topic_index";
const INDEX_SIGHT_WIKI = "content_wiki_index";
const INDEX_SIGHT_DESC = "content_desc_index";

const INPUT_VECTOR     = "model_total_vector";

const VOC_VECTOR       = "model_voc_vector";

const TITLE_VECTOR     = "model_title_vector";
const TOPIC_VECTOR     = "model_topic_vector";
const DESC_VECTOR      = "model_desc_vector";