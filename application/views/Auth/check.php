<?php

$authKey=@$_POST['key'];
$pKey=$this->session->userdata('pKey');


if($pKey==false){
	echo "세션 값이 올바르지 않습니다. 새로 로그인한 후 다시 시도해주세요.";
	return;
}

if($authKey=='') {
	//echo "인증값을 입력해주세요.";
	echo "<script>alert('인증 값을 입력해주세요.');</script>";
	return;
}

$db_sql = "SELECT * FROM ctf_user WHERE pKey=?";
$db_result = $this->db->query($db_sql, array($pKey));
if($db_result->num_rows() <= 0) {
	echo "세션 값이 올바르지 않습니다. 새로 로그인한 후 다시 시도해주세요.";
	return;
}
$db_assoc = $db_result->row();
$userNo=$db_assoc->no;
$teamNo=$db_assoc->team_no;

$db_auth_sql = "SELECT * FROM ctf_challenges WHERE authKey=?";
$db_auth_result = $this->db->query($db_auth_sql, array(hash('sha256', $authKey)));
if($db_auth_result->num_rows() <= 0) {
	$db_log_sql = "INSERT INTO ctf_auth_log (user_no, team_no, input_key) VALUES (?, ?, ?)";
	$db_log_result = $this->db->query($db_log_sql, array($userNo, $teamNo, $authKey));
	//echo "올바르지않은 인증값입니다.";
	echo "올바르지 않은 인증 값입니다.";
	return;
}

$db_auth_assoc=$db_auth_result->row();
$gotPoint = $db_auth_assoc->points;
$challengeNo = $db_auth_assoc->no;

//-- is Already Auth?
$db_auth_sql = "SELECT * FROM ctf_auth_log WHERE team_no=? AND challenges_no=? AND result='Y'";
$db_auth_result = $this->db->query($db_auth_sql, array($teamNo, $challengeNo));
if($db_auth_result->num_rows() > 0) {
	echo "이미 인증한 문제입니다.";
	return;
}

$curTime=strtotime(date('Y-m-d H:i:s'));
$finTime=strtotime('2015-12-25 23:59:59');
if($curTime <= $finTime){
    //-- Update SolvedCount
    $db_solved_sql = "UPDATE ctf_challenges SET solved_count=solved_count+1 WHERE no=?";
    $db_solved_result=$this->db->query($db_solved_sql, array($challengeNo));

}
//-- Insert  auth Log
$db_log_sql = "INSERT INTO ctf_auth_log (user_no, team_no, challenges_no, input_key, result) VALUES (?, ?, ?, ?, 'Y')";
$db_log_result = $this->db->query($db_log_sql, array($userNo, $teamNo, $challengeNo, $authKey));


usleep(100000);

if($curTime <= $finTime){
//-- Update Team Score And Info
$db_score_sql = "UPDATE ctf_team SET score=score+?, last_auth=CURRENT_TIMESTAMP WHERE no=?";
$db_score_result=$this->db->query($db_score_sql, array($gotPoint, $teamNo));
}

if($curTime <= $finTime){
echo "Congratulations! 정답입니다.";
} else {
echo "Congratulations! 정답입니다. 하지만 이미 대회가 종료되었으므로 점수는 반영되지 않습니다.";
}
return;
?>
