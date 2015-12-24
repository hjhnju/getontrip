<?php
if(file_exists('../../env.inc.php')){
    require_once '../../env.inc.php';
}

ini_set('memory_limit','512M');

const WORK_PATH        = "/home/work/publish/data/";

const RESULT_PATH      = "/home/work/publish/data/similarity.";

const MODEL_SIGHT_PATH     = "/home/work/publish/data/sight_model/";

const MODEL_TAG_PATH       = "/home/work/publish/data/tag_model/";

const DATA_PATH        = "/home/work/publish/data/newdocs.";

const INDEX_LABEL_SIGHT      = "label_sight";

const INDEX_LABEL_TAG        = "label_tag";

const CONFORM_RATE     = 0.3;