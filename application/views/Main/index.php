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

$db_user_sql = "SELECT a.*,b.* FROM ctf_user a, ctf_team b WHERE a.no=? AND b.no=a.team_no";
$db_user_result = $this->db->query($db_user_sql, array($user_no));
$db_user_assoc=$db_user_result->row();

$db_myrank_sql = "SELECT *, FIND_IN_SET( score, ( SELECT GROUP_CONCAT( score ORDER BY score DESC ) FROM ctf_team ) ) AS rank FROM ctf_team WHERE no=? ORDER BY rank,last_auth,reg_timestamp LIMIT 1"; 
$db_myrank_result = $this->db->query($db_myrank_sql, array($db_user_assoc->team_no));
$db_myrank_assoc = $db_myrank_result->row();



@$page=$_GET['page'];
$showPage='';
switch($page){
    case 'notice': {
	$showPage='Notice/notice.php';
	break;
    }
    case 'challenge': {
	$showPage='Challenge/chal.php';
	break;
    }

    case 'auth':{
	$showPage='Auth/auth.php';
	break;
    }

    case 'rank':{
	$showPage='Rank/rank.php';
	break;
    }

    case 'logout':{
	$this->session->sess_destroy();
	header('Location: http://christmasctf.com');
	exit;
	break;
    }

    default:{
	$showPage='Notice/notice.php';
	break;
    }

}
?>
 <!DOCTYPE html>
<html>
  <title>ChristmasCTF 2015</title>
  <meta charset="utf-8" />
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css"> <!-- FontAwesome -->
  <link rel="stylesheet" href="http://fonts.googleapis.com/earlyaccess/notosanskr.css"> <!-- Noto Sans KR -->
  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script> 
  <script>
    $(document).ready(function(){
      $(".chal_title").click(function(){
        $(this).children('div').slideToggle("fast");
                                            
      });
      
      $(document).on('click', '.auth_smt', function(e) {
	e.preventDefault();
	$.post('http://christmasctf.com/auth', $('.auth_form').serialize(), function(data) {
		alert(data);
		location.reload();
	});
      });
      
      
    });
  </script>
  <body>
    <div id="wrapper">
      <div class="header">
        <div class="header_item">
          <span><?=$this->session->userdata('username')?> (<?=$this->session->userdata('teamname')?>)</span>
        </div>
        <div class="header_item">
          <span>Point : <?=$db_user_assoc->score?></span>
        </div>
        <div class="header_item">
          <span>RANK : <?=$db_myrank_assoc->rank?></span>
        </div>
				<div class="header_item">
          <span class="sponsorlink">Sponsed by : <a href="http://d2.naver.com"><font color="#FFF">NAVER</font> <font color="#30C27E">D2</font></a>, <font color="#EB72AB">Wing</font>, <a href="http://leaveret.kr">LeaveRet</a>
					</span>
        </div>
      </div>
      <div class="bottom">
        <div class="sidebar">
          <div class="sidebar_menu">
            <a href="?page=notice"><div class="menu_item">
              <div class="menu_icon">
                <i class="fa fa-commenting"></i>
              </div>
              <div class="menu_name">
                <span>NOTICE</span>
              </div>
            </div></a>
            <a href="?page=challenge"><div class="menu_item">
              <div class="menu_icon">
                <i class="fa fa-file-text"></i>
              </div>
              <div class="menu_name">
                <span>CHALLENGE</span>
              </div>
            </div></a>
            <a href="?page=auth"><div class="menu_item">
              <div class="menu_icon">
                <i class="fa fa-check"></i>
              </div>
              <div class="menu_name">
                <span>AUTH</span>
              </div>
            </div></a>
            <a href="?page=rank"><div class="menu_item">
              <div class="menu_icon">
                <i class="fa fa-sort-numeric-asc"></i>
              </div>
              <div class="menu_name">
                <span>RANK</span>
              </div>
            </div></a>
            <a href="?page=logout"><div class="menu_item">
              <div class="menu_icon">
                <i class="fa fa-sign-out"></i>
              </div>
              <div class="menu_name">
                <span>LOGOUT</span>
              </div>
            </div></a>
          </div>
          <div class="sidebar_footer">
            <span class="sidebar_contact">
              운영문의 : christmasCTF@gmail.com
            </span>
            <span class="sidebar_copyright">&copy; 2015 by <b>FrozenBeer</b> All rights reserved.</span>
          </div>
        </div>
        <div class="content">
		<?php
		$this->load->view($showPage);
		?>
        </div>
      </div>
    </div>
  </body>
</html>
