<?php
if(file_exists('../../env.inc.php')){
    require_once '../../env.inc.php';
}

ini_set('memory_limit','512M');

const WORK_PATH        = "/home/work/publish/data/";

const RESULT_PATH      = "/home/work/publish/data/similarity.";

const MODEL_PATH       = "/home/work/publish/data/documents/";

const DATA_PATH        = "/home/work/publish/data/newdocs.";

const INDEX_LABEL      = "label_index";

const CONFORM_RATE     = 0.3;