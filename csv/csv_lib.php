<?
CLASS ZyCsv {
	/* parse string */
	public function unserialize($str){
		if($str==""){
			return Array(Array(), Array());
		}
		$len=strlen($str);
		if($str[$len-1]!="\n"){
			$str.="\n";
			$len++;
		}
		/* parse */
		$RR=Array();
		$rr=Array();
		$word="";
		$quoted=0;
		$last="";
		$okw=1;
		for($i=0;$i<$len;$i++){
			$c=$str[$i];
			if($okw){
				$last="";
				if($c=="\r")continue;
				if($c=="\n"){
					$RR[]=$rr; $rr=Array(); $word=""; $okw=1;
					continue;
				}
				if($c==','){
					$rr[]=""; $word=""; $okw=1;
					continue;
				}
				if($c=='"'){
					$word=""; $okw=0; $quoted=1;
					continue;
				}
				$word=$c; $okw=0; $quoted=0;
				continue;
			}
			if($c=='"'){
				if($quoted){
					if($last=='"'){
						$word.=$c; $last="";
						continue;
					}
					if($c==$str[$i+1]){
						$last=$c;
					}else{
						$last="";
						$quoted=0;
					}
					continue;
				}
				$last="";
				$word.=$c;
				continue;
			}
			$last = "";
			if($quoted){
				$word.=$c;
				continue;
			}
			if($c=="\r")continue;
			if($c=="\n"){
				$rr[]=$word; $RR[]=$rr; $rr=Array(); $word=""; $okw=1;
				continue;
			}
			if($c==","){
				$rr[]=$word; $word=""; $okw=1;
				continue;
			}
			$word.=$c;
		}
		if(count($rr)>0){
			$rr[]=$word;
			$RR[]=$rr;
		}
		if(count($RR)==0){
			return Array(Array(), Array());
		}

		/* get orignal titles AND data */
		$titles=$RR[0];
		$data=array_slice($RR,1);

		/* compete missing rows width empty string */
		$max=0;
		foreach($data as $r){
			$c=count($r);
			if($c>$max) $max=$c;
		}
		for($i=count($titles);$i<$max;$i++){
			$titles[]="";
		}
		$new_data=Array();
		foreach($data as $r){
			for($i=count($r);$i<$max;$i++){
				$r[]="";
			}
			$new_data[]=$r;
		}

		return Array($titles, $new_data);
	}
	public function serialize($titles, $data)
	{
		$rr=Array();
		$rr[]=$this->quote_array($titles);
		foreach($data as $row){
			$rr[]=$this->quote_array($row);
		}
		$rr[]="";
		return join("\r\n", $rr);
	}
	private function quote_array($row){
		$nrow=Array();
		foreach($row as $word){
			$nrow[]=$this->quote($word);
		}
		return join(',', $nrow);
	}
	private function quote($str){
		if($str=="")return "";
		$r=$str;
		if(strpos($str, '"')!==false){
			$r=str_replace('"', '""', $str);
		}
		if($r!=$str){
			return '"'.$r.'"';
		}
		if(strpos($str, ',')!==false){
			return '"'.$r.'"';
		}
		return $r;
	}

	/* extend */
	/* follow, we suppose csv'data is completed and the cvs'titles is not numeric */

	public function map($titles, $data){
		$ndata=Array();
		foreach($data as $row){
			$nrow=Array();
			for($i=0;$i<count($row);$i++){
				$nrow[$titles[$i]]=$row[$i];
			}
			$ndata[]=$nrow;
		}
		return $ndata;
	}
	public function unmap($titles, $data){
		$raw_row=Array();
		$kmap=Array();
		for($i=0;$i<count($titles);$i++){
			$kmap[$titles[$i]]=$i;
			$raw_row[]="";
		}

		$ndata=Array();
		foreach($data as $row){
			$nrow=$raw_row;
			foreach($row as $k=>$w){
				if(!array_key_exists($k, $kmap)){
					continue;
				}
				$nrow[$kmap[$k]]=$w;
			}
			$ndata[]=$nrow;
		}
		return $ndata;
	}

}

