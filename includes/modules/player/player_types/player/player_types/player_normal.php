<?phpclass Player{		public $user_type='normal';		public $id=0;		public $email='';		private $pass='';		private $pass_md5='';		public $display_name='';		public $avatar='';		public $balance=0;		public $points=0;		public $rank=0;				public $profile_link='';				private $is_logged=false;				public $table_id=0;		public $seat_id=0;		public $in_turn=false;		public $turn_ts=0;		public $amount=0;				public $bet=0;		public $bet_name='';		public $card_1='';		public $card_2='';				public $last_action_ts=0; // logout after 60 seconds of no connection		public function __construct($id=0){		if ($id>0){			$this->id=$id;			$this->loadTableData();			$this->loadBetData();			$this->loadData();					}	}//end constructor		public function getLoggedInUser($params=''){ // load logged in user				session_start();		if (!isset($_SESSION["player"])){session_write_close(); return false;}		if (!isset($_SESSION["player"]->id)){session_write_close(); return false;}		if ($_SESSION["player"]->id=='0'){session_write_close(); return false;}						foreach($_SESSION["player"] as $key=>$value){			$this->{$key}=$value;		}// end foreach		session_write_close();		$this->pass_md5=md5($this->pass);		$this->is_logged=true;				$this->loadTableData();		$this->loadBetData();				$this->last_action_ts=date("U");		$this->saveData();		return true;	}		public function login($email,$pass){		$email=strtolower($email);		if (trim($email)=='' || trim($pass)==''){return false;}				global $db;		$temp=$db->getRows("select * from player where email='$email' and pass='".md5($pass)."'");		if (isset($temp[0]->id) && $temp[0]->id>'0'){			$this->calculateRank();			foreach($temp[0] as $key=>$value){				$this->{$key}=$value;			}// end foreach			$this->pass_md5=md5($this->pass);			$this->is_logged=true;			$this->updateSession();			//updates session			$this->saveData();			return true;		}else{			$this->is_logged=false;			$this->saveData();			return false;		}	}		public function is_logged (){		return $this->is_logged;	}		public function logout(){		global $game;		$game->unseatPlayer($this);		$this->leaveTable();		session_start();		unset($_SESSION["player"]);		session_destroy();		session_write_close();	}		public function save(){		global $db;		$this->email=strtolower($this->email);		$db->query("update player set						display_name='".$this->display_name."' ,						email='".$this->email."' ,						avatar='".$this->avatar."' ,						pass='".$this->pass."' ,						balance='".$this->balance."' ,						points='".$this->points."' 					where id='".$this->id."'		");		$this->last_action_ts=date("U");		$this->saveData();		$this->updateSession();			//updates session	}		public function updateAvatar($url){		$this->avatar=$url;		$this->save();	}			public function setPass($pass){		$this->pass=md5($pass);	}		public function create(){// create new player if password !=''		global $db;		if ($this->pass==''){return false;}		$this->email=strtolower($this->email);		$db->query("insert into player (display_name,email,avatar,pass,balance)									values										('".$this->display_name."',											'".$this->email."' ,											'".$this->avatar."',											'".$this->pass."',											'10000'										)");		$this->id=$db->newId();	}//end create		public function enterTable($tableId){ // set memcached table 		$this->loadData();		$this->last_action_ts=date("U");		$this->loadTableData();		$this->table_id=$tableId;		$this->saveTableData();		$this->saveData();		return true;	}//end enterTable		public function leaveTable(){ // delete table info , controller should delete user from table info , thus this function should be called from the controller only			$this->loadTableData();			$this->table_id=0;			$this->saveTableData();	}//end enterTable		public function load($id){		global $db;		$temp=$db->getRows("select * from player where id='$id'");		if (isset($temp[0]->id) && $temp[0]->id>'0'){			foreach($temp[0] as $key=>$value){				$this->{$key}=$value;			}// end foreach			$this->pass_md5=md5($this->pass);			$this->loadTableData(); // loads table info from memcached			$this->loadBetData(); // loads table info from memcached			$this->loadData(); // loads table info from memcached		}	}		public function loadTableData(){ //		global $rts;		//echo "--------\n Loading table data for ".$this->display_name."\n";		if ($t_info=$rts->read('user.table.data_'.$this->id)){ // load table info if any , otherwise no table !			foreach ($t_info as $key=>$val){				$this->{$key}=$val;				//echo "$key = $val\n";			}		}	}//	public function loadBetData(){ //		global $rts;		//echo "--------\n Loading bet data for ".$this->display_name."\n";		if ($t_info=$rts->read('user.bet.data_'.$this->id)){ // load bet data			foreach ($t_info as $key=>$val){				$this->{$key}=$val;				//echo "$key = $val\n";			}		}	}//		public function loadData(){ //		global $rts;		if ($t_info=$rts->read('user.data_'.$this->id)){ // load other data			foreach ($t_info as $key=>$val){				$this->{$key}=$val;				//echo "$key=$val \n<br>";			}		}		$this->generateProfileLink();	}//		public function saveTableData(){ // save table and seat information		global $rts;			$table_data->table_id=$this->table_id;			$table_data->seat_id=$this->seat_id;			$table_data->in_turn=$this->in_turn;			$table_data->amount=$this->amount;			$table_data->turn_ts=$this->turn_ts;			$table_data->card_1=$this->card_1;			$table_data->card_2=$this->card_2;			$rts->write('user.table.data_'.$this->id,$table_data);	}//		public function saveBetData(){ // save bet information		global $rts;					$bet_data->bet=$this->bet;			$bet_data->bet_name=$this->bet_name;			$bet_data->amount=$this->amount;						$rts->write('user.bet.data_'.$this->id,$bet_data);			}//		public function saveData(){ // save other information		global $rts;						$this->generateProfileLink();						$data->last_action_ts=$this->last_action_ts;			$data->is_logged=$this->is_logged;						$data->email=$this->email;			$data->pass=$this->pass;			$data->pass_md5=$this->pass_md5;			$data->display_name=$this->display_name;			$data->avatar=$this->avatar;			$data->balance=$this->balance;			$data->points=$this->points;			$data->rank=$this->rank;			$data->profile_link=$this->profile_link;			//echo "saving $data->last_action_ts to user.data_$this->id\n";			$rts->write('user.data_'.$this->id,$data);			}//		public function calculateRank(){		global $db;		$db->connect();		$ranker=mysql_fetch_array(mysql_query("select count(*) from player where balance>".(int)$this->balance.""));		$this->rank=$ranker["0"]+1;	}		public function savePoints($points){		mysql_query	("update player set points='".$this->points."' where id='".$this->id."'");	}	// ---- Private ---- //	/* load table information , if no data create data		bool is_boot		return void	*/		private function updateSession(){		global $player;		if ($player->id != $this->id){return false;} // not same player , no session update		$tmp->display_name=$this->display_name;		$tmp->email=$this->email;		$tmp->avatar=$this->avatar;		$tmp->pass=$this->pass;		$tmp->balance=$this->balance;		$tmp->points=$this->points;		$tmp->rank=$this->rank;		$tmp->id=$this->id;		session_start();				$_SESSION["player"]=$tmp;		session_write_close();	}		private function generateProfileLink(){		$this->profile_link='<a href="javascript:;" class="player_name" onclick="game.displayProfile(\''.$this->id.'\');">'.$this->display_name.'</a>';	}		private function getUserIdByEmail($email=''){		if ($email=='') {return false;}		global $db;		$temp=$db->getRows("select * from player where email='$email'");		if (!isset($temp["0"]->id))return 0;		return $temp["0"]->id;	}}// end class player?>