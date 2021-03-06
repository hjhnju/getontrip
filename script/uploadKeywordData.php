<?php
require_once("env.inc.php");
ini_set('memory_limit','512M');
$basePath     = "/home/work/publish/data/51data/";
$resultPath   = $basePath."findSight/";

//人工确定景点的上传景观数据
/*$origin_str     = file_get_contents($resultPath."unsolved.txt","w");
preg_match_all("/(\d+)\s(\d+)\s1\s0\r\n/s",$origin_str,$match);
$logicKeyword   = new Keyword_Logic_Keyword();
foreach ($match[1] as $key => $id){
    $directory   = $basePath."unzips/$id";    
    $ret = array();
    $mydir = dir($directory);
    while($file = $mydir->read()){
        $tmpid = '';
        sscanf($file,"%d:properties",$tmpid);
        if(!empty($tmpid) && !in_array($tmpid,$ret)){
            $ret[] = $tmpid;
        }
    }
    $mydir->close();
    
    foreach ($ret as $data){
        $name  = '';
        $x     = '';
        $y     = '';
        $level = 2;
        $audio = '';
        if(intval($data) == intval($id)){
            $level = 1;
        }
        if(!file_exists($directory."/$data.properties")){
            continue;
        }
        $arrProperty  = file($directory."/$data.properties");
        foreach ($arrProperty as $property){
            $tmp = explode("=",$property);
            if(strstr($property,"scenicName")){
                $name = $tmp[1];
            }elseif(strstr($property,"lng")){               
                $y    = $tmp[1];
            }elseif(strstr($property,"lat")){
                $x    = $tmp[1];
            }
        }
        
        $arrInfo = array(
            'name'     => $name,
            'url'      => 'http://baike.baidu.com/item/'.$name,
            'sight_id' => $match[2][$key],
            'level'    => $level,
            'x'        => $x,
            'y'        => $y,
        );
        $objKeyword = new Keyword_Object_Keyword();
        $objKeyword->fetch(array('sight_id' =>$arrInfo['sight_id'],'name' => $name));
        if(!empty($objKeyword->id)){
            continue;
        }
        if(file_exists($directory."/$data.amr")){
            $arrInfo['audio'] = $logicKeyword->upAudioData($directory."/$data.amr");
        }
        $id = $logicKeyword->addKeywords($arrInfo);
        $objKeyword = new Keyword_Object_Keyword();
        $objKeyword->fetch(array('id' => $id));
        if(empty($objKeyword->image) && file_exists($directory."/$data.png")){
            $objKeyword->image = $logicKeyword->upPicData($directory."/$data.png");
            $objKeyword->save();
        }
        
        //$RET_DATA   = "/home/work/publish/data/51data/findSight/unsolved.txt";
        //$origin_str = file_get_contents($RET_DATA);
        //$update_str = preg_replace("/$match[0][$key]/", "$match[1][$key]\t$match[2][$key]\t1\t1\r\n", $origin_str);
        //$ret        = file_put_contents($RET_DATA, $update_str);
    }
}*/

//自动确认景点的，需要一次上传数据操作
$origin_str     = file_get_contents($resultPath."confirm.txt");
preg_match_all("/(\d+)\s(\d+)\r\n/s",$origin_str,$match);
$logicKeyword   = new Keyword_Logic_Keyword();
foreach ($match[1] as $key => $id){
    $directory   = $basePath."unzips/$id";
    $ret = array();
    $mydir = dir($directory);
    while($file = $mydir->read()){
        $tmpid = '';
        sscanf($file,"%d:properties",$tmpid);
        if(!empty($tmpid) && !in_array($tmpid,$ret)){
            $ret[] = $tmpid;
        }
    }
    $mydir->close();

    foreach ($ret as $data){
        $name  = '';
        $x     = '';
        $y     = '';
        $level = 3;
        $audio = '';
        if(intval($data) == intval($id)){
            $level = 3;
        }
        if(!file_exists($directory."/$data.properties")){
            continue;
        }
        $arrProperty  = file($directory."/$data.properties");
        foreach ($arrProperty as $property){
            $tmp = explode("=",$property);
            if(strstr($property,"scenicName")){
                $name = trim($tmp[1]);
            }elseif(strstr($property,"lng")){
                $x    = $tmp[1];
            }elseif(strstr($property,"lat")){
                $y    = $tmp[1];
            }
        }
        $arrInfo = array(
            'name'     => $name,
            'url'      => 'http://baike.baidu.com/item/'.$name,
            'sight_id' => $match[2][$key],
            'level'    => $level,
            'x'        => $x,
            'y'        => $y,
        );
        $objKeyword = new Keyword_Object_Keyword();
        $objKeyword->fetch(array('sight_id' =>$arrInfo['sight_id'],'name' => $name));
        if(!empty($objKeyword->id)){
                if(empty($objKeyword->audio)){
                    $objKeyword->audio    = $logicKeyword->upAudioData($directory."/$data.mp3");
                    $objKeyword->audioLen = Base_Audio::getInstance()->getLen($directory."/$data.mp3");
                    $objKeyword->save();
                } 
            continue;
        }
        if(file_exists($directory."/$data.mp3")){
            $arrInfo['audio']     = $logicKeyword->upAudioData($directory."/$data.mp3");
            $arrInfo['audio_len'] = Base_Audio::getInstance()->getLen($directory."/$data.mp3");
        }
        $id = $logicKeyword->addKeywords($arrInfo);
        $objKeyword = new Keyword_Object_Keyword();
        $objKeyword->fetch(array('id' => $id));
        if(empty($objKeyword->image) && file_exists($directory."/$data.png")){
            $objKeyword->image = $logicKeyword->upPicData($directory."/$data.png");
            $objKeyword->save();
        }
    }
}