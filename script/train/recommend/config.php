<?php
if(file_exists('../../env.inc.php')){
    require_once '../../env.inc.php';
}

ini_set('memory_limit','512M');

const WORK_PATH        = "/home/work/data/";

const RESULT_PATH      = "/home/work/data/similarity.output";

const MODEL_PATH       = "/home/work/data/feature/";

const DATA_PATH        = "/home/work/data/documents/";

const INDEX_LABEL      = "label_index";