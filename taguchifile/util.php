<?php
/**
 * 
 *
 * @version 1.0.0
 * @author M.katsube <katsubemakito@gmail.com>
 * @copyright 2019 M.katsube
 */

define('MAX_PLAYER', 1);

define('DATA_ROOM','room.txt');		

define('COL_Q', 0);	
define('COL_A', 1);	

//-------------------------------------------------------------------
// ??????
//-------------------------------------------------------------------
// - countUser()
// - getUserList()
// - existUser($uid)
// - getBattleID()
// - getQuestion()
// - getAnswer($quiz_num)
// - getAnswerUser()
// - putResult($status, $data=null)
// - getQuery($name, $empty=null)
// - saveFile($path, $value, $mode='a')
// - debugQueryPrint()
//-------------------------------------------------------------------

/**
 * @return [integer]
 */
function countUser(){
	if( ! is_file(DATA_ROOM) ){
		return(0);
	}

	$fp = fopen(DATA_ROOM, 'r');
	$count = 0;
	while( fgets($fp) !== false ){
		$count++;
	}
	fclose($fp);
	return($count);
}

function getUserList(){
	$member = [];		

	$fp = fopen(DATA_ROOM, 'r');
	flock($fp, LOCK_SH);
	while( ($buff = fgets($fp)) !== false ){
		list($uid, $uname) = explode("\t", $buff);
		$member[$uid] = trim($uname);		
	}
	flock($fp, LOCK_UN);
	fclose($fp);

	return($member);
}

function existUser($uid){
	$member = getUserList();
	return( array_key_exists($uid, $member) );
}

function getBattleID(){
	$battle_id = null;

	if( is_file(DATA_ID) ){
		$fp = fopen(DATA_ID, 'r');	
		flock($fp, LOCK_SH);				
		$battle_id = fgets($fp);
		flock($fp, LOCK_UN);				
		fclose($fp);
	}
	else{
		$battle_id = uniqid("BID");		

		$fp = fopen(DATA_ID, 'w');		
		flock($fp, LOCK_EX);			
		fwrite($fp, $battle_id);
		flock($fp, LOCK_UN);			
		fclose($fp);
	}

	return($battle_id);
}

function getQuestion(){
	$quiz_num = null;

	if( is_file(DATA_QUESTION) ){
		$fp = fopen(DATA_QUESTION, 'r');
		flock($fp, LOCK_SH);
		$quiz_num = fgets($fp);
		flock($fp, LOCK_UN);
		fclose($fp);
	}
	else{
		global $question;
		$quiz_num = rand(0, count($question)-1);	

		$fp = fopen(DATA_QUESTION, 'w');
		flock($fp, LOCK_EX);			
		fwrite($fp, $quiz_num);
		flock($fp, LOCK_UN);			
		fclose($fp);
	}

	return( (integer) $quiz_num );
}

/**
 * 
 *
 * @param [integer] $quiz_num
 * @return [string]
 */
function getAnswer($quiz_num=null){
	global $question;
	if($quiz_num === null){
		$quiz_num = getQuestion();
	}

	return(
		$question[$quiz_num][COL_A]
	);
}

/**
 *
 * @return void
 */
function getAnswerUser(){
	$member = getUserList();
	$list = [];

	$fp = fopen(DATA_ANSWER, 'r');
	flock($fp, LOCK_SH);
	while( ($buff = fgets($fp)) !== false ){
		list($uid, $answer, $correct) = explode("\t", trim($buff));
		$list[$uid] = [
			  'answer'  => $answer
			, 'uname'   => $member[$uid]
			, 'correct' => ($correct === '1')?  true:false
		];
	}
	flock($fp, LOCK_UN);
	fclose($fp);

	return($list);
}


/**
 * 
 *
 * @param [boolean] $status 
 * @param [mixed]   $value l
 * @return void
 */
function putResult($status, $data=null){
	$value = [
		'status' => $status,
		'value'  => $data
	];

	if( isset($_GET['_debug']) && $_GET['_debug'] === "on" ){
		header('Content-type: text/plain');
		echo json_encode($value, JSON_PRETTY_PRINT);	// ?l??a?c???￠?`??o?I
		debugQueryPrint();								// ?N?G???[?d?\?|
	}
	else{
		header('Content-type: application/json');
		echo json_encode($value);
	}
}

function getQuery($name, $empty=null){
	if( isset($_GET[$name]) && !empty($_GET[$name]) ){
		return($_GET[$name]);
	}
	else{
		return($empty);
	}
}


function saveFile($path, $value, $mode='a'){
	$fp = fopen($path, $mode);
	flock($fp, LOCK_EX);		
	fwrite($fp, $value);	
	flock($fp, LOCK_UN);		
	fclose($fp);			
}

function debugQueryPrint(){
	printf("\n\n");
	printf("----------------------\n");
	printf(' $_GET'."\n");
	printf("----------------------\n");
	print_r($_GET);

	printf("\n\n");
	printf("----------------------\n");
	printf(' $_COOKIE'."\n");
	printf("----------------------\n");
	print_r($_COOKIE);
}