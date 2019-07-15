<?php
/**
 * 
 * /quiz/api/join.php
 *
 * @version 1.0.0
 * @author M.katsube <katsubemakito@gmail.com>
 * @copyright 2019 M.katsube
 */

require_once('util.php');

$uname = getQuery('uname');
if( $uname === null ){
	putResult(false, 'uname is required');	// ???O?￠?u??G???[
	exit(0);
}

$count = countUser();
if( $count >= MAX_PLAYER ){
	putResult(false, 'This room is full');  // ???o?G???[
	exit(0);
}

$uid = uniqid();

$str = implode("\t", [$uid, $uname]) ."\n";  // $uid<?^?u>$uname<?u?s>
saveFile(DATA_ROOM, $str);

putResult(true, ['uid'=> $uid]);