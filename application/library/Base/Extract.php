<?php # Script - class.textExtract.php
/**
 * textExtract - text extraction class
 * Created on 2010-08-10
 * author: Wenfeng Xuan
 * Email: wfxuan@insun.hit.edu.cn
 * Blog: http://hi.baidu.com/xwf_like
 */
class Base_Extract {

	///////////////////////////////////
	// MEMBERS
	///////////////////////////////////
	
	/**
	 * record the web page's source code
	 * @var string
	 */
	public $rawPageCode = '';
	
	/**
	 * 解析的URL
	 * @var string
	 */
	public $url         = '';
	
	/**
	 * record the text after preprocessing
	 * @var array
	 */
	public $textLines   = array();
	
	/**
	 * record the length of each block
	 * @var array
	 */
	public $blksLen     = array();
	
	/**
	 * record the final extracted text
	 * @var string
	 */
	public $text        = '';
	
	/**
	 * set the size of each block ( regards how many single lines as a block )
	 * it is the only parameter of this method
	 * @var int
	 */
	public $blkSize     =  3;
	
	///////////////////////////////////
	// METHODS
	///////////////////////////////////
	
	/**
	 * Set the value of relevant members
	 * @param string $_rawPageCode
	 * @param int $_blkSize
	 * @return void
	 */
	function __construct( $url,$content='') {
	    $this->url         = "compress.zlib://".$url;
	    if(empty($content)){
	        $this->rawPageCode = file_get_contents($this->url);
	    }else{
	        $this->rawPageCode = $content;
	    }
	}
	
	/**
	 * Preprocess the web page's source code
	 * @return string
	 */
	function preProcess() {
		$content = $this->rawPageCode;
		
		$num = preg_match_all('/<meta.*?>/si',$content,$match);
		for( $i = 0; $i < $num; $i++ ){
		    if(false !== stristr($match[0][$i],"charset")){
		        preg_match('/charset=\"?(.*?)(\"|\s|\/|>)/si',$content,$match);
		        $sourceCode = trim($match[1]);
		        $content = mb_convert_encoding($content,"utf8",$sourceCode);
		    }
		}
	
		// 1. DTD information
		$pattern = '/<!DOCTYPE.*?>/si';
		$replacement = '';
		$content = preg_replace( $pattern, $replacement, $content );
		
		// 2. HTML comment
		$pattern = '/<!--.*?-->/s';
		$replacement = '';
		$content = preg_replace( $pattern, $replacement, $content );
		
		// 3. Java Script
		$pattern = '/<script.*?>.*?<\/script>/si';
		$replacement = '';
		$content = preg_replace( $pattern, $replacement, $content );
		
		// 4. CSS
		$pattern = '/<style.*?>.*?<\/style>/si';
		$replacement = '';
		$content = preg_replace( $pattern, $replacement, $content );
		
		$pattern = '/&lt;/';
		$replacement = "<";
		$content = preg_replace( $pattern, $replacement, $content );
		
		$pattern = '/&gt;/';
		$replacement = ">";
		$content = preg_replace( $pattern, $replacement, $content );
		
		$pattern = '/&quot;/';
		$replacement = "\"";
		$content = preg_replace( $pattern, $replacement, $content );
		
		// 5. HTML TAGs
		/*$pattern = '/<[^(img|p|br)].*?>/s';*/
		$pattern = '/<(?!img|p|br).*?>/s';
		$replacement = '';
		$content = preg_replace( $pattern, $replacement, $content );
		
		//**图片中有实体数据，直接过滤掉
		$pattern = '/<img.*?src=\"data.*?>/s';
		$replacement = '';
		$content = preg_replace( $pattern, $replacement, $content );
		
		// 6. some special charcaters
		$pattern = '/&.{1,5};|&#.{1,5};/';
		$replacement = '';
		$content = preg_replace( $pattern, $replacement, $content );
		
		return $content;
	}
	
	/**
	 * Split the preprocessed text into lines by '\n'
	 * after replacing "\r\n", '\n', and '\r' with '\n'
	 * @param string @rawText
	 * @return void
	 */
	function getTextLines( $rawText ) {
		// do some replacement
		$order = array( "\r\n", "\n", "\r" );
		$replace = '\n';
		$rawText = str_replace( $order, $replace, $rawText );
		
		$lines = explode( '\n', $rawText );
		
		foreach( $lines as $line ) {
			// remove the blanks in each line
			$tmp = preg_replace( '/\s+/s', '', $line );
			$this->textLines[] = $tmp;
		}
	}
	
	/**
	 * Calculate the blocks' length
	 * @return void
	 */
	function calBlocksLen() {
		$textLineNum = count( $this->textLines );
		
		// calculate the first block's length
		$blkLen = 0;
		for( $i = 0; $i < $this->blkSize; $i++ ) {
			$blkLen += strlen( $this->textLines[$i] );
		}
		$this->blksLen[] = $blkLen;
		
		// calculate the other block's length using Dynamic Programming method
		for( $i = 1; $i < ($textLineNum - $this->blkSize); $i++ ) {
			$blkLen = $this->blksLen[$i - 1] + strlen( $this->textLines[$i - 1 + $this->blkSize] ) - strlen( $this->textLines[$i - 1] );
			$this->blksLen[] = $blkLen;
		}
	}
	
	/**
	 * Extract the text from the web page's source code
	 * according to the simple idea:
	 * [the text should be the longgest continuous content
	 * in the web page]
	 * @return string
	 */
	function getPlainText() {
		$preProcText = $this->preProcess();
		$this->getTextLines( $preProcText );
		$this->calBlocksLen();
		
		$start = $end = -1;
		$i = $maxTextLen = 0;
		
		$blkNum = count( $this->blksLen );
		while( $i < $blkNum ) {
			while( ($i < $blkNum) && ($this->blksLen[$i] == 0) ) $i++;
			if( $i >= $blkNum ) break;
			$tmp = $i;
			
			$curTextLen = 0;
			$portion = '';
			while( ($i < $blkNum) && ($this->blksLen[$i] != 0) ) {
				$portion .= $this->textLines[$i];
				$curTextLen += $this->blksLen[$i];
				$i++;
			}
			if( $curTextLen > $maxTextLen ) {
				$this->text = $portion;
				$maxTextLen = $curTextLen;
				$start = $tmp;
				$end = $i - 1;
			}
		}
        return $this->dataClean($this->text);
	}
	
	/**
	 * 判断路径是不是全路径，主要用在图片上
	 * @param string $url
	 * @return boolean
	 */
	public function isFullPath($url){
	    $parts = parse_url($url);
	    if(isset($parts['host'])){
	        return true;
	    }
	    return false;
	}
	
	/**
	 * 获取网站域名
	 * @return string
	 */
	public function getUrlBase(){
	    $parts = parse_url( $this->url);
	    return $parts['scheme']."://".$parts['host'];
	}
	
	/**
	 * 对图像及p,br标签数据整理
	 * @param string $content
	 * @return string
	 */
	public function dataClean($content){
	    $content = preg_replace( '/<p.*?>/', '<p>', $content );
	    
	    $num = preg_match_all('/img.*?src=\"(.*?)\".*?>/si',$content,$match);
	    for($i=0;$i<$num;$i++){
	        if($this->isFullPath($match[1][$i])){
	            $content = str_replace($match[0][$i],"img src=\"".$match[1][$i]."\">",$content);
	        }else{
	            if(stristr($match[1][$i],"//")){
	                $content = str_replace($match[0][$i],"img src=\""."http:".$match[1][$i]."\">",$content);
	            }else{
	                $content = str_replace($match[0][$i],"img src=\"".$this->getUrlBase().$match[1][$i]."\">",$content);
	            }
	        }
	    }	    
	    return $content;
	}
}

