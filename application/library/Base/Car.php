<?php
class Base_Car{
	protected static $_objInstance;
	protected static $_hanlder;
	protected static $_citys;

	/**
	 * 实例化当前类
	 * @return 当前类的对象
	 */
	public static function getInstance() {
        if (!self::$_objInstance) {
            self::$_objInstance = new self();
        }
        return self::$_objInstance;
    }

    public function __construct() {
    	self::$_hanlder = new Base_Car_HaoService();
    }

    /**
     * 获得所有支持的省和城市，城市中有很多有用的信息
     * @return 返回省市的详细列表
     */
   	public function getCitys(){
   		if(!self::$_citys){
   			self::$_citys = self::$_hanlder->getCitys();
   		}
   		return self::$_citys;
	}

	/**
	 * 获得支持的省
	 */
	public function getSupportProvince(){
		$all = $this->getCitys();
		$province = array();
		foreach($all as $key=>$item){
			$province[$item->province] = array(
				'province' => $item->province,
				'province_code' => $item->province_code,
			);
		}
		return $province;
	}

	/**
	 * 获得支持的城市
	 */
	public function getSupportCitys($province){
		$all = $this->getCitys();
		$citys = array();
		foreach($all as $key=>$item){
			if($province == $item->province){
				$citys = $item->citys;
				break;
			}
		}
		return $citys;
	}

	/**
	 * 查看当前还剩下多少次
	 * @return 返回剩下的次数
	 */
	public function getStatus(){
   		return self::$_hanlder->getStatus();
	}

	/**
	 * 返回车的类型，小型车，大型车......
	 * @return 车型列表
	 */
	public function getHpzl(){
   		return self::$_hanlder->getHpzl();
	}

	/**
	 * 返回违章列表
	 * @param  $data 
	 *         $data = array(
	 *         		'city' => 'GZ_GuiYang', 城市代码								必填
	 *         		'hphm' => '贵A19X37', 	号牌号码完整7位						必填
	 *         		'hpzl' => '02',			号牌种类编号 (参考号牌种类接口)			必填
	 *         		'engineno' => '',		发动机号 (根据城市接口中的参数填写)		非必填，城市中写的是什么，这里就写什么
	 *         		'classno'  => '',		车架号 (根据城市接口中的参数填写)			非必填
	 *         		'registno' => ''		车辆登记证书号 (根据城市接口中的参数填写)	非必填
	 *         );
	 * @return 违章列表
	 */
	public function getQuery($data){
   		return self::$_hanlder->getQuery($data);
	}
}