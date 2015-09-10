<?php
/**
 * 景点发布及词条发布后通过此接口异步抓取百科、视频、书籍数据
 * @author huwei
 */
ini_set("max_execution_time", "180000");
class InitDataController extends Base_Controller_Page {
    
    const PAGE_SIZE = 20;
	//初始化
    public function init(){
        $this->setNeedLogin(false);
        parent::init();
    }
    public function indexAction() {
        $conf    = new Yaf_Config_INI(CONF_PATH. "/application.ini", ENVIRON);
        $sightId = isset($_REQUEST['sightId'])?intval($_REQUEST['sightId']):'';
        $type    = isset($_REQUEST['type'])?trim($_REQUEST['type']):'';
        $num     = isset($_REQUEST['num'])?intval($_REQUEST['num']):intval($conf['thirddata']['initnum']);
     
        //获取景点ID数组
        if(empty($sightId)){
            $model = new SightModel();
            $arr   = $model->getSightList(1, PHP_INT_MAX);
            foreach ($arr as $val){
                $arrSight[] = $val['id'];
            }
        }else{
            $arrSight[] = $sightId;
        }
        foreach ($arrSight as $id){
            switch($type){
                case 'Book':
                    $logicBook = new Book_Logic_Book();
                    $page      = ceil($num/self::PAGE_SIZE);
                    for( $i = 1;$i <= $page; $i++ ){
                        $logicBook->getJdBooks($id, $i,self::PAGE_SIZE);
                    }
                    break;
                case 'Video':
                    $logicVideo = new Video_Logic_Video();
                    $page = ceil($num/self::PAGE_SIZE);
                    for( $i = 1;$i <= $page; $i++ ){
                        $logicVideo->getAiqiyiSource($id, $i);
                    }
                    break;
                case 'Wiki':
                    $logicWiki = new Wiki_Logic_Wiki();
                    $logicWiki->getWikiSource($id,1,$num,Wiki_Type_Status::PUBLISHED);
                    break;
                case 'All':
                    $logicBook  = new Book_Logic_Book();
                    $logicVideo = new Video_Logic_Video();
                    $logicWiki  = new Wiki_Logic_Wiki();
                    $page       = ceil($num/self::PAGE_SIZE);
                    for( $i = 1; $i <= $page; $i++ ){
                        $logicBook->getJdBooks($id, $i,self::PAGE_SIZE);
                        $logicVideo->getAiqiyiSource($id, $i);
                    }
                    $logicWiki->getWikiSource($id,1,$num,Wiki_Type_Status::PUBLISHED);
                    break;
                default:
                    break;
            }
        }
        exit;  
    }
}