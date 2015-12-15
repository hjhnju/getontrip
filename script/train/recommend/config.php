<?php
if(file_exists('../../env.inc.php')){
    require_once '../../env.inc.php';
}

ini_set('memory_limit','512M');

const WORK_PATH        = "/home/work/data/";

const RESULT_PATH      = "/home/work/data/result/similarity.output";

const MODEL_PATH       = "/home/work/data/documents/";

const DATA_PATH        = "/home/work/data/work/newdocs.";

const INDEX_LABEL      = "label_index";