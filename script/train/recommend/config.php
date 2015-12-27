<?php
if(file_exists('../../env.inc.php')){
    require_once '../../env.inc.php';
}

ini_set('memory_limit','512M');

const WORK_PATH            = "/home/work/publish/data/";

const RESULT_SIGHT_PATH    = "/home/work/publish/data/similarity_sight.";

const RESULT_TAG_PATH      = "/home/work/publish/data/similarity_tag.";

const MODEL_SIGHT_PATH     = "/home/work/publish/data/documents_sight/";

const MODEL_TAG_PATH       = "/home/work/publish/data/documents_tag/";

const DATA_PATH            = "/home/work/publish/data/newdocs.";

const INDEX_LABEL_SIGHT    = "label_sight";

const INDEX_LABEL_TAG      = "label_tag";