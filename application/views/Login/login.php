<?php

if($this->session->userdata('pKey') == true) {
//        $this->session->sess_destroy();
        header('Location: http://christmasctf.com/main');
        return;
}

if($this->session->userdata('logged_in') == true) {
//        $this->session->sess_destroy();
        header('Location: http://christmasctf.com/main');
        return;
}

@$loginType = $_POST['submitType'];
if($loginType=='login') {
	@$userId=trim($_POST['id']);
	@$userPw=trim($_POST['pw']);
	$userPw=hash('sha256', $userPw);
	$sql = "SELECT * FROM ctf_user WHERE user_id=? LIMIT 1";
	$result = $this->db->query($sql, array($userId));

	if($result->num_rows() <= 0) {
		$result=array();
                $result['result']=0;
                $result['msg']='아이디 또는 비밀번호가 올바르지 않습니다.'; 
                echo("<script>alert('".$result['msg']."');history.back();</script>");
                return;
	}
	$row=$result->row();

	if($row->user_pw != $userPw) {
		$result=array();
		$result['result']=0;
		$result['msg']='아이디 또는 비밀번호가 올바르지 않습니다.';
		echo("<script>alert('".$result['msg']."');history.back();</script>");
		return;
	}

	$teamSql="SELECT * FROM ctf_team WHERE no=?";
	$team_result = $this->db->query($teamSql, array($row->team_no));
	$team_row = $team_result->row();

	$data = array();
        $data['username'] = $userId;
        $data['team_no'] = $row->team_no;
	$data['teamname']=$team_row->team_name;
        $data['pKey'] = $row->pKey;
        $data['logged_in'] = true;
        $this->session->set_userdata($data);

	header('Location: http://christmasctf.com/main');
//	header('Location: http://smishing.rootnix.cn');
	return;
} else if($loginType=='register') {
	@$isLeader=$_POST['gubun']; // new, join
	@$userId=trim($_POST['id']);
	@$userPw=hash('sha256',trim($_POST['pw']));
	@$teamName=trim($_POST['teamname']);
	@$email=$_POST['email'];

	$result=array();

	if($userId=='' || $userPw=='') {
		$result['result']=0;
		$result['msg']='모든 정보를 입력해주세요.';
		echo("<script>alert('".$result['msg']."');history.back();</script>");
		return;
	}

	$db_sql = "SELECT * FROM ctf_user WHERE user_id=?";
	$db_result = $this->db->query($db_sql, array($userId));
	if($db_result->num_rows() > 0) {
		$result['result']=0;
		$result['msg']='이미 가입된 ID 입니다.';
		echo("<script>alert('".$result['msg']."');history.back();</script>");
		return;
	}

	if($isLeader=='new') {
		if($email != '') {
			$db_sql = "SELECT * FROM ctf_user WHERE user_email=?";
			$db_result = $this->db->query($db_sql, array($email));
			if($db_result->num_rows() > 0) {
				$result['result']=0;
				$result['msg']='이미 가입된 Email 입니다.';
				echo("<script>alert('".$result['msg']."');history.back();</script>");
				return;	
			}
		}

		$db_sql = "SELECT * FROM ctf_team WHERE team_name=?";
		$db_result = $this->db->query($db_sql, array($teamName));
		if($db_result->num_rows() > 0 ){
			$result['result']=0;
			$result['msg']='이미 생성된 팀 이름입니다.';
			echo("<script>alert('".$result['msg']."');history.back();</script>");
			return;
		}

		$db_insert_sql = "INSERT INTO ctf_team SET team_name=?";
		$db_insert_result = $this->db->query($db_insert_sql, array($teamName));
		if(!$db_insert_result) {
			$result['result']=0;
			$result['msg']='가입중 오류발생. 잠시 후 다시 시도 해주세요.';
			echo("<script>alert('".$result['msg']."');history.back();</script>");
			return;
		}

		$teamNo = $this->db->insert_id();

		$pKey=hash('sha256', $this->ctfutil->get_random_string(50));

		$db_register_sql = "INSERT INTO ctf_user (user_id, user_pw, user_email, team_no, pKey) VALUES (?, ?, ?, ?, ?)";
		$db_register_result=$this->db->query($db_register_sql, array($userId, $userPw, $email, $teamNo, $pKey));

		if(!$db_register_result) {
			$result['result']=0;
			$result['msg']='가입중 오류발생. 잠시 후 다시 시도 해주세요.';
			echo("<script>alert('".$result['msg']."');history.back();</script>");
			return;
		}

		$userNo=$this->db->insert_id();
		$db_update_sql = "UPDATE ctf_team SET team_leader_no=? WHERE no=?";
		$db_update_result = $this->db->query($db_update_sql, array($userNo, $teamNo));

		$result['result']=1;
		$result['msg']='가입이 완료되었습니다. 가입한 가입정보로 로그인해주세요.';
//		echo("<script>alert('".$result['msg']."');history.back();</script>");
//		header('Location: http://christmasctf.com/main');
		echo("<script>alert('".$result['msg']."');location.href='http://christmasctf.com';</script>");
		return;
	} else if($isLeader=='join') {
		$db_sql = "SELECT * FROM ctf_user WHERE user_email=?";
		$db_result = $this->db->query($db_sql, array($email));
		if($db_result->num_rows() <= 0) {
			$result['result']=0;
			$result['msg']='존재하지 않는 팀장 이메일입니다.'.$email;
			echo("<script>alert('".$result['msg']."');history.back();</script>");
			return;
		}

		$db_row=$db_result->row();
		$teamNo = $db_row->team_no;

		$db_count_sql = "SELECT COUNT(1)cnt FROM ctf_user WHERE team_no=?";
		$db_count_result = $this->db->query($db_count_sql, array($teamNo));
		$db_count_row = $db_count_result->row();
		if($db_count_row->cnt >= 4) {
			$result['result']=0;
			$result['msg']='이미 정원이 초과된 팀입니다.';
			echo("<script>alert('".$result['msg']."');history.back();</script>");
			return;
		}

		$pKey = hash('sha256', $this->ctfutil->get_random_string(50));

		$db_register_sql = "INSERT INTO ctf_user (user_id, user_pw, team_no, pKey) VALUES (?, ?, ?, ?)";
		$db_register_result = $this->db->query($db_register_sql, array($userId, $userPw, $teamNo, $pKey));
		$result['result']=1;
		$result['msg']='가입이 완료되었습니다. 가입한 가입정보로 로그인해주세요.';
//		header('Location: http://christmasctf.com/main');
		echo("<script>alert('".$result['msg']."');location.href='http://christmasctf.com';</script>");
		return;
	}

} else {
	$result=array();
	$result['result']=-1;
	$result['msg']='잘못된 접근입니다^^...';
}
?>
<!DOCTYPE html>
<html>
  <head>
    <title>ChristmasCTF 2015 | Login</title>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script> <!-- jQuery 1.11.3 for IE Support -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css"><!-- Font Awesome! -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/earlyaccess/notosanskr.css"> <!-- Noto Sans KR -->
    <link rel="stylesheet" href="./assets/css/reset.css">
    <link rel="stylesheet" href="./assets/css/login.css">
    <!-- Meta Tag -->
    <meta http-equiv="content-type" content="text/html; charset=utf-8">
    <meta charset="utf-8">
    <meta name="subject" content="Login">
    <meta name="title" content="ChristmasCTF Login">
    <meta name="author" content="FrozenBeer">
    <meta name="description" content="ChristmasCTF Login" />
    <meta name="keywords" content="ChristmasCTF FrozenBeer">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
  </head>
  <body>

		<script>
			$(document).ready(function(){
				//Layout Init
				winheight = $(window).height();
				$(".box").css("top",(winheight / 2) - 315 +"px");
				
				//Login Function.
				$("#login_icn").click(function(){
					$(".login_req_form").submit();
				});
				$(document).keypress(function(e) {
						if(e.which == 13) {
							$(".login_req_form").submit();
						}
				});
				
				//RegisterFunction
				$(".new_account").click(function(){
					$("#login").hide(300);
					$("#register").fadeIn(300);
				});
				
				//TeamLeader Function
				$(".gubun").click(function(){
					//팀장일경우
					if($(".gubun:checked").val() == "new"){
						$(".join").hide(300);
						$("#teamname_input").html("팀명");
						$("#inpt_teamname").attr("placeholder","팀명");
						$('#inpt_teamname').attr("name", "teamname");
						$('#leader').attr('name', 'email');
						$(".team_view").show(300);
						$(".teamleader").show(300);
					}
					else{
						$(".teamleader").hide(300);
						$("#teamname_input").html("팀장이메일");
						$("#inpt_teamname").attr("placeholder","팀장이메일 입력");
						$("#inpt_teamname").attr("name", "email");
						$("#leader").attr("name", "leader_email");
						$(".team_view").show(300);
						$(".join").show(300);
					}
				});
				
				//TeamLeader New Team Privacy Agreement function
				$("#leaeder_sbmt").click(function(){
						//agree_policy
					if($(".agree_policy").is(":checked") == true){
						$(".register_form").submit();
					}else{
						alert("개인정보수집및 이용방침에 동의하지 않으면 서비스를 이용하실 수 없습니다.")
					}
				});
				
			});
			$(window).resize(function(){
				winheight = $(window).height();
				$(".box").css("top",(winheight / 2) - 315 +"px");
			});
		</script>
		<div id="wrapper">
			<div class="box">
				<div class="box_inner" id="login">
					<span class="title">ChristmasCTF 2015</span>
					<span class="desc">2015.12.25 00:00 ~ 23:59:59 (24 Hrs)</span>
					<span class="desc"><a href="https://www.facebook.com/christmasctf" style="color: #fff;">https://www.facebook.com/christmasctf</a></span>
					<div class="login_form">
						<form class="login_req_form" action="./login" method="POST">
							<div class="login_form_top">
								<input class="input_id" type="text" placeholder="ID" name="id">
							</div>
							<div class="login_form_bottom">
								<input clas="input_pw" type="password" placeholder="PW" name="pw">
								<i class="fa fa-arrow-circle-o-right" id="login_icn"></i>
							</div>
							<input type="hidden" name="submitType" value="login"/>
						</form>
						<span class="login_helper">로그인은 우측 화살표를 클릭하시거나 엔터키로 하실 수 있습니다.</span>
					</div>
					<span class="new_account">Create New Account</span>
					<span class="help">운영문의 : christmasctf@gmail.com</span>
					<div class="sponsor">
						<div class="spon_item">
							<img src="assets/img/naverd2.png">
						</div>
						<div class="spon_item">
							<img src="assets/img/wing.png">
						</div>
						<div class="spon_item">
							<img src="assets/img/leaveret.jpeg">
						</div>
					</div>
					<span class="copyright">&copy; 2015 by FrozenBeer All rights reserved.</span>
				</div>
				<div class="box_inner" id="register">
					<span class="register_title">REGISTER</span>
					<div class="regi_form">
						<form class="register_form" action="./login" method="post"><!-- action 부분 수정하셔야 합니닷 -->
							<div class="team">
								<!-- POST["gubun"] 에 new 혹은 join 날라갑니다 (사용자가 체크 한 대로 ) -->
								<input type="radio" class="gubun" value="new" name="gubun">New Team
								<input type="radio" class="gubun" value="join" name="gubun">Join Team
							</div>
							<div class="team_view"><!-- aa -->
								<span class="reg_title" id="teamname_input">팀명</span>
								<input id="inpt_teamname" class="reg_input" type="text" name="teamname">
								<span class="reg_title">ID</span>
								<input id="inpt_id" class="reg_input" type="text" name="id" placeholder="ID">
								<span class="reg_title">PW</span>
								<input id="inpt_pw" class="reg_input" type="password" name="pw" placeholder="PW">
								<!--팀 생성(팀장)일 경우 노출-->
								<div class="teamleader">
									<span class="reg_title">연락처(이메일)</span>
									<input class="reg_input" type="text" name="email" placeholder="TeamID" id="leader">

								<div class="privacy">
									<p>
										개인정보수집및 이용방침<br />
										총칙<br />
										i)&nbsp;&nbsp; Christmas CTF 주최측은 귀하의 개인정보보호를 매우 중요시하며, 행정자치부에서 정한 『개인정보보호법』의 각 조항의 필요성에 대해 이해하고 이를 준수하기 위하여 최선의 노력을 하고 있으며 또한 『정보통신망이용촉진등에관한법률』상의 개인정보보호 규정 및 정보통신부가 제정한 『개인정보보호지침』을 준수합니다.<br />
										ii)&nbsp;&nbsp; Christmas CTF 주최측은 개인정보취급방침을 공개함으로써 귀하께서 필요한 경우 언제든지 보실 수 있도록 조치하고 있습니다.<br />
										iii)&nbsp;&nbsp; Christmas CTF 주최측은 개인정보취급방침의 지속적인 개선을 위하여 개인정보취급방침을 개정하는데 필요한 절차를 정하고 있고, 이를 개정하는 경우 귀하께서 쉽게 알아보실 수 있도록 지침을 정하여 관리하고 있습니다.<br />
										수집하는 개인정보와 항목<br />
										-&nbsp;&nbsp; 필수항목: 로그인ID, 비밀번호, 이름, E-mail, 휴대전화번호<br />
										개인정보의 수집목적 및 이용목적<br />
										i)&nbsp;&nbsp; Christmas CTF 주최측은 다음과 같은 목적을 위하여 개인정보를 수집하고 있습니다.<br />
										A.&nbsp;&nbsp; Christmas CTF 대회 진행을 위한 참가자의 본인식별 및 본인의사 확인 등<br />
										B.&nbsp;&nbsp; Christmas CTF 대회 참가에 필요한 공지사항 전달 및 참가자에 필요한 숙지사항 전달<br />
										C.&nbsp;&nbsp; Christmas CTF 대회에서 우수한 성적의 참가자에게 상금 혹은 상품 전달 시 연락 및 본인확인<br />
										ii)&nbsp;&nbsp; 이용자의 기본적 인권 침해의 우려가 있는 민감한 개인정보(인종 및 민족, 사상 및 신조, 출신지 및 본적지, 정치적 성향 및 범죄기록, 건강상태 및 성생활 등)는 수집하지 않습니다.<br />
										개인정보의 보유 / 이용기간<br />
										-&nbsp;&nbsp; 홈페이지 회원가입 및 관리와 관련한 개인정보는 수집, 이용에 관한 동의일로부터 3달간 위 이용목적을 위하여 보유. 이용됩니다. (보유근거: 대회 참가 및 상품수령에 관한 약관)<br />
										-&nbsp;&nbsp; 이용자의 개인정보는 개인정보의 보유기간이 경과된 경우 보유기간의 종료일로부터 7일 이내에, 개인정보의 처리 목적 달성, 본 대회의 종료 등 그 개인정보가 불필요하게 되었을 때 개인정보의 처리가 불필요한 것으로 인정되는 날로부터 7일 이내에 그 개인정보를 파기합니다.<br />
										-&nbsp;&nbsp; Christmas CTF 주최측은 『개인정보보호법』 제4조(정보주체의 권리) 제4항의 개인정보의 처리 정지, 정정, 삭제 및 파기를 요구할 권리에 따라 개인정보 주체가 처리 정리, 정정, 삭제 및 파기를 요청한 경우 처리 신청자의 권리에 따라 지체 없이 요청을 처리합니다.<br />
										개인정보 수집에 대한 동의<br />
										-&nbsp;&nbsp; Christmas CTF 주최측은 귀하께서 Christmas CTF의 개인정보취급방침 또는 이용약관의 내용에 대해 [동의]버튼 또는 [동의하지 않음]버튼을 클릭할 수 있는 절차를 마련하며, [동의]버튼을 클릭하면 개인정보 수집에 대해 동의한 것으로 간주합니다.<br />
										쿠키에 의한 개인정보 수집<br />
										-&nbsp;&nbsp; Christmas CTF 주최측은 귀하에 대한 정보를 저장하고 수시로 접근하는 ‘쿠키(cookie)’를 사용합니다. 쿠키는 웹사이트가 귀하의 인터넷 브라우저로 전송하는 정보입니다. 로그인 이후 대회 문제를 푸는데 불편함이 없도록 쿠키를 사용합니다. 쿠키는 귀하의 컴퓨터를 식별할 수는 있지만 귀하를 개인적으로 식별하지는 않습니다. 쿠키 사용에 대해 허락하거나 거부할 수 있는 선택권이 있으며 귀하의 웹 브라우저의 옵션을 조정함으로써 쿠키를 받아들이거나 거부할 수 있는 선택권을 가질 수 있습니다.<br />
										목적 외 사용 및 제3자에 대한 제공<br />
										i)&nbsp;&nbsp; Christmas CTF는 귀하의 개인정보를 [개인정보의 수집목적 및 이용목적]에서 고지한 범위 내에서 사용하며, 동 범위를 초과하여 이용하거나 타인 또는 타기업 및 기관에 제공하지 않습니다.<br />
										ii)&nbsp;&nbsp; 그러나 후원자 및 후원기업 등에서 상품 전달 및 본인확인 용도로 귀하의 개인정보를 요청할 경우 Christmas CTF 주최측은 귀하의 개인정보를 전달하는 것이 명백하게 개인정보 주체에게 유리한 권리로 판단하며 귀하의 개인정보를 제3자(후원자 및 후원기업)에게 제공 할 수 있습니다. 이 때 귀하가 입력한 E-mail 주소 및 휴대전화번호를 통하여 제3자 제공에 대한 사항을 귀하에게 알리며 개인정보주체는 이를 즉시 거부할 수 있는 권리가 있습니다.<br />
										개인정보의 열람및 정정<br />
										i)&nbsp;&nbsp; 귀하는 언제든지 홈페이지에 가입했던 개인정보에 대한 내용의 열람을 요청하거나 정정할 수 있는 권리가 있습니다. 개인정보 열람 및 정정을 하고자 할 경우에는 홈페이지의 Christmas CTF 주최측 E-mail을 통해 연락하시면 바로 조치하겠습니다.<br />
										ii)&nbsp;&nbsp; 다만, 대회 특성상 대회가 시작된 이후에 대회 참가자의 정보나 인원, 이름 및 연락처 등의 정보를 수정하는 것은 공정한 대회 운영 및 Christmas CTF 주최측에서 추구하는 대회운영의 이점에 대한 내용에 반하는 것으로 Christmas CTF 주최측에서 개인정보주체의 의사에 반하여 이를 주관적으로 판단하고 거부할 수 있습니다.<br />
										개인정보 수집, 이용, 제공에 대한 동의철회<br />
										-&nbsp;&nbsp; 회원가입 등을 통해 개인정보의 수집, 이용, 제공에 대해 귀하께서 동의하신 내용을 귀하께서는 언제든지 철회하실 수 있습니다. 동의 철회는 홈페이지의 Christmas CTF 주최측 E-mail을 통해 연락하시면 즉시 개인정보의 삭제 등 필요한 조치를 하겠습니다.<br />
										개인정보의 보유기간 및 이용기간<br />
										i)&nbsp;&nbsp; 귀하의 개인정보는 다음과 같이 개인정보의 수집목적 또는 제공받은 목적이 달성되면 파기됩니다.<br />
										A.&nbsp;&nbsp; 회원가입정보의 경우, 회원가입 탈퇴를 신청하거나 가입자의 부당한 행위로 제명된 때<br />
										B.&nbsp;&nbsp; 입상자 및 성적우수자의 경우, 상금 또는 서비스가 인도되거나 제공된 때(단, 상법 등 법령의 규정에 의하여 보존할 필요성이 있는 경우에는 예외로 합니다.)<br />
										C.&nbsp;&nbsp; 위 보유기간 및 이용기간에도 불구하고 계속 보유하여야 할 필요가 있을 경우에는 별도로 귀하의 동의를 받습니다.<br />
										개인정보보호를 위한 기술적 대책<br />
										-&nbsp;&nbsp; Christmas CTF 주최측은 귀하의 개인정보를 취급함에 있어 개인정보가 분실, 도난, 누출, 변조 또는 훼손되지 않도록 안정성 확보를 위하여 다음과 같은 기술적 대책을 강구하고 있습니다.<br />
										i)&nbsp;&nbsp; 귀하의 개인정보는 비밀번호에 의해 보호됩니다.<br />
										ii)&nbsp;&nbsp; Christmas CTF 주최측은 방화벽, 백신, 방화벽, 침입탐지시스템 등을 이용하여 악성코드나 악의적 해킹에 의한 피해를 방지하기 위한 조치를 취하고 있습니다.<br />
										iii)&nbsp;&nbsp; Christmas CTF 주최측은 네트워크 구간에서 적절한 암호 알고리즘을 이용하여 네트워크상의 개인정보를 안전하게 전송할 수 있도록 조치하고 있습니다.<br />
										iv)&nbsp;&nbsp; Christmas CTF 대회 전, 대회 중에는 담당자와 운영진 측이 24시간 패킷을 모니터링하며 대회 운영 시스템 및 데이터베이스에 대한 침입을 감시하고 있습니다. <br />
										개인정보의 위탁처리<br />
										-&nbsp;&nbsp; Christmas CTF측은 귀하의 개인정보를 외부에 위탁하여 처리하지 않습니다.<br />
										의견수렴 및 불만처리<br />
										-&nbsp;&nbsp; Christmas CTF 주최측에게 개인정보보호와 관련하여 의견 및 불만을 제기할 경우, 홈페이지에 공지된 대회 주최측의 E-mail로 문의하시면 신속하게 답변하겠습니다.<br />
										개인정보 관리책임자<br />
										-&nbsp;&nbsp; Christmas CTF 주최측은 개인정보에 대한 의견수렴 및 불만처리를 담당하는 개인정보 관리책임자를 지정하고 있습니다. 개인정보 관리 책임자는 다음과 같습니다.<br />
										1&nbsp;&nbsp; 성 명 :&nbsp;&nbsp;송수호<br />
										2&nbsp;&nbsp; 전화번호 : 070-7014-6325<br />
										3&nbsp;&nbsp; E-mail : ChristmasCTF@gmail.com<br />
										아동의 개인정보보호<br />
										i)&nbsp;&nbsp; Christmas CTF 주최측은 만 14세 미만 아동의 개인정보를 수집할 수 없습니다.<br />
										ii)&nbsp;&nbsp; 14세 미만 아동이 본 약관을 이해하고 동의하고자 하는 경우 법정대리인의 동의가 필요합니다. 이 경우 주최측에게 E-mail을 통해 연락해주시기 바랍니다.<br />
										iii)&nbsp;&nbsp; 만 14세미만 아동의 법정대리인은 아동의 개인정보의 열람, 정정, 동의철회를 요청할 수 있으며, 이러한 요청이 있을 경우 Christmas CTF 주최측은 지체 없이 필요한 조치를 취합니다.<br />
									</p>
								</div>
								<div class="agreement">
									<input type="checkbox" class="agree_policy">개인정보수집및 이용방침에 동의합니다.
								</div>
								<span value="Register" class="submit_reg" id="leaeder_sbmt">REGISTER</span>
							</div>
							<!-- 팀장 노출 끝 -->
							<!-- 팀원Join -->
							<div class="join">
								<input type="submit" value="REGISTER" class="submit_reg">
							</div>
							<!-- Join끝 -->
							<input type="hidden" name="submitType" value="register"/>
						</form>
					</div>		
				</div>
			</div>
		</div>
  </body>
</html>
