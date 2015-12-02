<?
CLASS zIpAddress{
	var $dbFile="zwry.dat";
	var $charSet="GBK";
	var $dbFp=false;
	var $errorType = 0;
	var $errorStr=false;
	var $ipCount=-1;
	var $ipSliceCount=-1;
	var $lastSliceCapability=-1;
	var $ipSliceIdx=Array();

	function __construct($infos=Array()){
		if(array_key_exists("dbFile", $infos)){
			$this->dbFile = $infos["dbFile"];
		}
		if(array_key_exists("charSet", $infos)){
			$this->charSet = strtoupper($infos["charSet"]);
		}

		$this->dbFp = @fopen($this->dbFile, "rb");
		if(!$this->dbFp){
			$this->updateError(1, "fopen dbFile error: (". $this->dbFile .")");
			return $this;
		}

		$str = fread($this->dbFp, 4);
		if(strlen($str) < 4){
			$this->updateError(2, "System Error");
			return $this;
		}
		$this->ipCount = $this->___bufToInt($str);
		$this->ipSliceCount = intval(ceil($this->ipCount/1024));
		$this->lastSliceCapability = intval(ceil($this->ipCount%1024));
		if($this->lastSliceCapability == 0){
			$this->lastSliceCapability = 1024;
		}
		$str = fread($this->dbFp, $this->ipSliceCount * 1024 * 4);
		for($i=0;$i<$this->ipSliceCount;$i++){
			$this->ipSliceIdx[]=$this->___bufToInt(substr($str, $i*4, 4));
		}

		return $this;
	}

	function updateError($type, $errorStr){
		$this->errorType = $type;
		$this->errorStr = $errorStr;
	}

	function error(){
		return $this->errorStr;
	}

	private function ___bufToInt($buf){
		$ret = implode('', unpack('L', $buf)); 
		if($ret < 0){
			$ret += pow(2, 32);
		}
		return $ret;
	}


	function lookup($ip, $redundantInfo=0){
		if(!ereg("^([0-9]{1,3}.){3}[0-9]{1,3}$", $ip)){ 
			$this->updateError(3, "IP Address Invalid");
			return false;
		}

		$ips = explode('.', $ip); 
		$ipNumber = $ips[0] * 256 * 256 * 256 + $ips[1] * 256 * 256 + $ips[2] * 256 + $ips[3]; 

		$beginIdx=0;
		$endIdx = $this->ipSliceCount - 1;
		$findIdx = -1;
		while(1){
			$middleIdx= intval(($beginIdx + $endIdx)/2);
			$middleNumber = $this->ipSliceIdx[$middleIdx];
			if($ipNumber < $middleNumber){
				$endIdx = $middleIdx;
				continue;
			}else if ($ipNumber == $middleNumber){
				$findIdx = $middleIdx;
				break;
			}

			if($middleIdx == $endIdx){
				$findIdx = $endIdx;
				break;
			}

			$succNumber = $this->ipSliceIdx[$middleIdx + 1];
			if($ipNumber == $succNumber){
				$findIdx = $middleIdx + 1;
				break;
			}else if ($ipNumber > $succNumber){
				$beginIdx = $middleIdx + 1;
				continue;
			}
			$findIdx = $middleIdx;
			break;
		}
		if($findIdx == -1){
			$this->updateError(4, "No Data");
			return false;
		}
		fseek($this->dbFp, (4 + $this->ipSliceCount*4 + $findIdx * 1024 * 4));

		$ipCount2 = 1024;
		$ipIdx2=Array();
		if($findIdx == $this->ipSliceCount -1){
			$ipCount2 = $this->lastSliceCapability;
		}
		$str = fread($this->dbFp, $ipCount2 * 4);
		for($i=0;$i<$ipCount2;$i++){
			$ipIdx2[]=$this->___bufToInt(substr($str, $i*4, 4));
			$ipnum = substr($str, $i*4, 4);
			$ipstr=ord($ipnum[3]).'.'.ord($ipnum[2]).'.'.ord($ipnum[1]).'.'.ord($ipnum[0]);
		}

		$beginIdx=0;
		$endIdx = $ipCount2 - 1;
		$findIdx2 = -1;
		while(1){
			$middleIdx= intval(($beginIdx + $endIdx)/2);
			$middleNumber = $ipIdx2[$middleIdx];
			if($ipNumber  < $middleNumber ){
				$endIdx = $middleIdx;
				continue;
			}else if ($ipNumber == $middleNumber){
				$findIdx2 = $middleIdx;
				break;
			}
			if($middleIdx == $endIdx){
				$findIdx2 = $endIdx;
				break;
			}
			$succNumber = $ipIdx2[$middleIdx + 1];
			if($ipNumber == $succNumber){
				$findIdx2 = $middleIdx + 1;
				break;
			}else if ($ipNumber > $succNumber){
				$beginIdx = $middleIdx + 1;
				continue;
			}
			$findIdx2 = $middleIdx;
			break;
		}

		if($findIdx2 == -1){
			$this->updateError(5, "No Data");
			return false;
		}

		fseek($this->dbFp, (4 + $this->ipSliceCount*4 +  $this->ipCount * 4 + ($findIdx * 1024 + $findIdx2) * 6));
		$infoData = fread($this->dbFp, 6);
		$offset1 = $this->___bufToInt(substr($infoData, 0, 3).chr(0));
		$offset1 +=  (4 + $this->ipSliceCount*4 + $this->ipCount * 10);

		$ret1 = '';
		$ret2 = '';
		fseek($this->dbFp, $offset1);
		$len = ord(fread($this->dbFp, 1));
		if($len > 0){
			$ret1 = fread($this->dbFp, $len);
		}

		if(!$redundantInfo){
			return $ret1;
		}

		$offset2 = $this->___bufToInt(substr($infoData, 3, 3).chr(0));
		$offset2 +=  (4 + $this->ipSliceCount*4 + $this->ipCount * 10);
		fseek($this->dbFp, $offset2);
		$len = ord(fread($this->dbFp, 1));
		if($len > 0){
			$ret2 = fread($this->dbFp, $len);
		}
		$ret = "$ret1\n$ret2";

		$charSet = $this->charSet;
		$needConvert=0;
		if(strpos($charSet, "UTF")!==false){
			$needConvert = 1;
		}
		if($needConvert){
			$ret = iconv("GB18030", "$charSet//IGNORE", $ret);
		}

		return $ret;
	}
}
