<?phpclass fileManager{	/* &$file , file variabl from form	$allowed asslowed extentions	returns :		image_url		download_url		error	*/		function saveFile(& $file,$allowed='',$max_size=0){		$data->image_url='';		$data->download_url='';		$data->error='';		$data->path='';				if (!is_array($allowed)){			$allowed=array();		}//		//check file size			if ($file["size"] > $max_size && $max_size>0){				$data->error='You can\'t upload files larger than '.round($max_size/(1024*1024),2).'MB';				return $data;			}		//get uploaded file extension				$x=explode('.',$file["name"]);				$extension=strtolower($x[count($x)-1]);						//check allowed extensions			if (count($allowed)>0){				$allowed_exts='';				foreach ($allowed as $key=>$val){					$val=strtolower($val);					$allowed[$key]=$val;					$allowed_exts.=','.$val;				}				$allowed_exts=substr($allowed_exts,1);				if (in_array($extension,$allowed)===false){					$data->error='You can only upload '.$allowed_exts.' files';					return $data;				}			}					//save file an generate download link and image link				global $_STORAGE_PATH,$_SITE_URL;			$tmp_name=md5(date("U").$file["name"]).'.'.$extension;			$toPath=$_STORAGE_PATH.$tmp_name;			copy($file["tmp_name"],$toPath);			$data->image_url=$_SITE_URL.'image.php?id='.$tmp_name;			$data->download_url=$_SITE_URL.'download.php?id='.$tmp_name;			$data->path=$toPath;			$data->error='';			return $data;	}		function deleteFile($link){		$file=$this->getPath($link);		@unlink($file);	}		function getPath($url){		$link_parsed=parse_url($url);		parse_str($link_parsed["query"],$query);		$fileName=$query["id"];		global $_STORAGE_PATH;		return $_STORAGE_PATH.$fileName;	}	}//end class?>