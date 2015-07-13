<?php

class  Wechat_Pay_Exception extends Exception {
	public function errorMessage()
	{
		return $this->getMessage();
	}

}

?>