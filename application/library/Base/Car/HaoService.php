<?php
/**
 * @file 车辆违规查询
 * 第三方网站API http://www.haoservice.com/docs/2
 */
class Base_Car_HaoService{
	//第三方网站API生成的key，必须的
	const KEY = '82caa35084e543928f25c92fcf53887d';

	//API统一请求地址
	const BASE_URL = 'http://apis.haoservice.com/weizhang';

	/**
	 * 根据类型创建不同的URL请求
	 * @param  $type 包含这些值 
	 *         hpzl 车辆（号牌）种类编号查询 
	 *         citys 获取支持城市参数接口
	 *         query 请求违章查询接口
	 *         status 接口剩余请求次数查询
	 *         EasyQuery 简化请求违章查询接口
	 * @param  $data 根据不同的类型会有不同的数据,当类型是query时
	 *         $data = array(
	 *         		'city' => 'GZ_GuiYang', 城市代码								必填
	 *         		'hphm' => '贵A19X37', 	号牌号码完整7位						必填
	 *         		'hpzl' => '02',			号牌种类编号 (参考号牌种类接口)			必填
	 *         		'engineno' => '',		发动机号 (根据城市接口中的参数填写)		非必填
	 *         		'classno'  => '',		车架号 (根据城市接口中的参数填写)			非必填
	 *         		'registno' => ''		车辆登记证书号 (根据城市接口中的参数填写)	非必填
	 *         );
	 *         当类型是EasyQuery时	
	 *         $data = array(
	 *         		'plateNumber' => '', 	号牌号码 完整7位							必填
	 *         		'engineNumber' => '', 	发动机号 (全部位数)						必填
	 *         		'vehicleIdNumber' => '',车架号 (全部位数)							必填
	 *         		'cityName' => '',		城市中文名称								必填
	 *         );	
	 * @return $url
	 */
	public function buildUrl($type='', $data=array()){
		$url = self::BASE_URL;
		$query = '';
		if(!empty($data)) {
			foreach($data as $key=>$value) {
				if($value != ''){
					$query .= '&'.$key.'='.$value;
				}
			}
		}
		switch($type){
			case 'citys':
				$url .= '/citys?key='.self::KEY; 
				break;
			case 'hpzl':
				$url .= '/hpzl?key='.self::KEY; 
				break;
			case 'query':
				$url .= '/query?key='.self::KEY.$query; 
				break;
			case 'status':
				$url .= '/status?key='.self::KEY; 
				break;
			case 'EasyQuery':
				$url .= '/EasyQuery?key='.self::KEY.$query; 
				break;
		}

		return $url;
	}

	/**
	 * 获得所有的城市
	 * @return 省市列表
	 */
	public function getCitys(){
		$url = $this->buildUrl('citys');
        $data = $this->execute($url);
		if($data){
			return $data;
		}
        return false;
	}

	/**
	 * 获得剩余的次数
	 * @return 次数
	 */
	public function getStatus(){
		$url = $this->buildUrl('status');
		$data = $this->execute($url);
		if($data){
			return $data->surplus;
		}
        return false;
	}

	/**
	 * 车辆（号牌）种类编号查询
	 * @return 列表
	 */
	public function getHpzl(){
		$url = $this->buildUrl('hpzl');
		$data = $this->execute($url);
		if($data){
			return $data;
		}
        return false;
	}

	/**
	 * 违章查询
	 * @return
	 */
	public function getQuery($query){
		$url = $this->buildUrl('query', $query);
		$data = $this->execute($url);
		if($data){
			return $data;
		}
        return false;
	}

	/**
	 * 快捷违章查询
	 * @return
	 */
	public function getEasyQuery($query){
		$url = $this->buildUrl('EasyQuery', $query);
		$data = $this->execute($url);
		if($data){
			return $data;
		}
        return false;
	}

	/**
	 * 执行url请求
	 * @param  URL地址
	 * @return 数据列表
	 */
	public function execute($url){
		$http = Base_Network_Http::instance();
        $output = $http->url($url)->exec();
        $data = json_decode($output);
        if($data->error_code){
        	Base_Log::error(array(
                'msg'    => $data->reason,
            ));
            return false;
        }
        return $data->result;
	}
}