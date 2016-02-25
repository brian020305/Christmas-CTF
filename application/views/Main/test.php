<?php
//var_dump($this->session->all_userdata());
setcookie('flag','goodbyerootnix',strtotime('2015-12-26 00:00:00'),'/','.christmasctf.com');
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

$db_notice_sql = "SELECT * FROM ctf_notice ORDER BY no DESC";
$db_notice_result = $this->db->query($db_notice_sql);

$db_rank_sql = "SELECT *, FIND_IN_SET( score, (
SELECT GROUP_CONCAT( score
ORDER BY score DESC )
FROM ctf_team )
) AS rank
FROM ctf_team ORDER BY rank,last_auth,reg_timestamp LIMIT 10";
$db_rank_result = $this->db->query($db_rank_sql);

$db_user_sql = "SELECT a.*,b.* FROM ctf_user a, ctf_team b WHERE a.no=? AND b.no=a.team_no";
$db_user_result = $this->db->query($db_user_sql, array($user_no));
$db_user_assoc=$db_user_result->row();


$db_myrank_sql = "SELECT *, FIND_IN_SET( score, ( SELECT GROUP_CONCAT( score ORDER BY score DESC ) FROM ctf_team ) ) AS rank FROM ctf_team WHERE no=? ORDER BY rank,last_auth,reg_timestamp LIMIT 1";
$db_myrank_result = $this->db->query($db_myrank_sql, array($db_user_assoc->team_no));
$db_myrank_assoc = $db_myrank_result->row();

$db_teamcount_sql = "SELECT count(*)cnt FROM ctf_team";
$db_teamcount_result = $this->db->query($db_teamcount_sql);
$db_teamcount_assoc = $db_teamcount_result->row();
$teamcount=$db_teamcount_assoc->cnt;

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


?>

<!DOCTYPE html>
<html lang="ko">
<head>
<title>Chirstmas CTF</title>
<meta http-equiv="X-UA-Compatible" content="IE=8">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
<meta property="og:description" content="2015.12.25. Christmas CTF" />  
<meta property="og:title" content="크리스마스CTF"/>
<meta property="og:url" content="http://christmasctf.com"/>
<meta property="og:site_name" content="ChirstmasCTF"/>
<meta property="og:image" content="http://christmasctf.com/assets/11226906_733418636789536_1122222814244090993_n.jpg"/>
<meta property="og:type"   content="website" />
<meta name = "format-detection" content = "telephone=no">
<link rel="shortcut icon" type="image/x-icon" href="favicon.ico" />
    
<link href="assets/normalize.css" rel="stylesheet">

<style>
body,html{margin:0;padding:0;font-family: "Apple SD Gothic Neo", "Nanum Barun Gothic", "Malgun Gothic", "돋움", Dotum, sans-serif;background-color: #ffffff;margin: 0 auto}
#background_1 { background-color:#000;width:100%;height:100%;text-align:center}
</style>
</head>


<body>
<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/ko_KR/sdk.js#xfbml=1&version=v2.0";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>

<div id="background_1">
<span style="color:#fff;font-size:20px">이 페이지를 보고 계신다면 로그인에 성공한겁니다 :D</span><br>
<span style="color:#fff;font-size:20px">Powered by FrozenBeer</span><br><br>
<div class="fb-like" data-href="http://christmasctf.com" data-layout="button_count" data-action="like" data-show-faces="true" data-share="true"></div><br><br>
<img src="assets/11226906_733418636789536_1122222814244090993_n.jpg" style="height:850px;margin:0 auto;">
<iframe width="0" height="0" src="//www.youtube.com/embed/zL5JzNLamLc?rel=0&amp;controls=0&amp;showinfo=0&amp;autoplay=1&amp;loop=1&amp;playlist=    zL5JzNLamLc" frameborder="0" allowfullscreen></iframe><br><br><br><br><br>
<span style="color:#212121;font-size:1px;">You have to know that Do not Use Navxx Cleaner to Clean Cookie. Cookie is very Important!</span>
</div>

</body>

</html>
