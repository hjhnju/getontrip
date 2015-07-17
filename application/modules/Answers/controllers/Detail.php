<?php
/**
 * 答案详情接口
 * @author huwei
 *
 */
class DetailController extends Base_Controller_Api {
    
    public function init() {
        $this->setNeedLogin(false);
        parent::init();        
    }
    
    /**
     * 接口1：/answers/detail
     * 答案详情接口
     * @param integer answerId，答案ID
     * @return json
     */
    public function indexAction() {
        $answerId   = isset($_POST['answerId'])?intval($_POST['answerId']):'';
        if(empty($answerId) || empty($topicId)){
            return $this->ajaxError(Base_RetCode::PARAM_ERROR,Base_RetCode::getMsg(Base_RetCode::PARAM_ERROR));
        }
        $logic      = new Answers_Logic_Answers();
        $ret        = $logic->getAnswerDetail($answerId);
        return $this->ajax($ret);
    }
    
    /**
     * 接口2：/answers/detail/next
     * 获取下一条答案详情
     * @param integer answerId，答案ID
     * @param integer topicId，话题ID
     * @return json
     */
    public function nextAction(){
        $answerId   = isset($_POST['answerId'])?intval($_POST['answerId']):'';
        $topicId   = isset($_POST['topicId'])?intval($_POST['topicId']):'';
        if(empty($answerId) || empty($topicId)){
            return $this->ajaxError(Base_RetCode::PARAM_ERROR,Base_RetCode::getMsg(Base_RetCode::PARAM_ERROR));
        }
        $logic      = new Answers_Logic_Answers();
        $ret        = $logic->getNextAnswer($topicId,$answerId);
        return $this->ajax($ret);
    }    
}
