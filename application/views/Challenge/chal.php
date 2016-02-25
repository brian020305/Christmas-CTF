<?php

if($this->session->userdata('pKey') == false) {
        $this->session->sess_destroy();
        header('Location: http://christmasctf.com');
        return;
}

if($this->session->userdata('logged_in') == false) {
        $this->session->sess_destroy();
        header('Location: http://christmasctf.com');
        return;
}
$pKey = $this->session->userdata('pKey');
$db_sql = "SELECT * FROM ctf_user WHERE pKey=?";
$db_result = $this->db->query($db_sql, array($pKey));
if($db_result->num_rows() <= 0) {
        $this->session->sess_destroy();
        header('Location: http://christmasctf.com');
        return;
}
$db_assoc = $db_result->row();

$user_no=$db_assoc->no;
$team_no=$db_assoc->team_no;


$db_challenge_sql = "SELECT * FROM ctf_challenges WHERE opend='Y'";
$db_challenge_result = $this->db->query($db_challenge_sql);


$clear_list = array();
$db_clear_sql = "SELECT * FROM ctf_auth_log WHERE team_no=? AND result='Y'";
$db_clear_result = $this->db->query($db_clear_sql, array($team_no));
if($db_clear_result->num_rows() > 0) {
        foreach($db_clear_result->result() as $row) {
                $clear_list[]=$row->challenges_no;
        }
}


if($db_challenge_result->num_rows() <= 0) {
?>
<div class="chal">
<div class="chal_div">
<div class="chal_title">
<span><i class="fa fa-angle-down"></i>Wait for 2015-12-25 00:00:00</span>
<div class="chal_content">
안돼안돼 돌아가 안보여줄꺼야
</div>
</div>
</div>
</div>

<?php
}
?>
<!-- CHALLENGE 시작 --> 
<div class="chal">
<?
foreach($db_challenge_result->result() as $row) {
	if(in_array($row->no, $clear_list)) {
?>
            <div class="chal_div" solved="true">
              <div class="chal_title">
                <span><i class="fa fa-angle-down"></i>[<?=$row->category?>-<?=$row->points?>] - <?=$row->title?> (Solved)</span>
                <div class="chal_content">
		    Solved : <font color="#7F021D"><?=$row->solved_count?></font> Team(s)<br /><br />
                   <?=$row->content?><br>
                </div>
              </div>
            </div>

<?php
	} else {
?>
            <div class="chal_div">
              <div class="chal_title">
		<span><i class="fa fa-angle-down"></i>[<?=$row->category?>-<?=$row->points?>] - <?=$row->title?></span>
                <div class="chal_content">
		    Solved : <font color="#7F021D"><?=$row->solved_count?></font> Team(s)<br /><br />
                   <?=$row->content?>
                </div>
              </div>
            </div>



<?php
	}
}
?>
</div>
<!-- CHALLENGE 끝 -->
