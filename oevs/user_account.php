<?php
session_start();
require_once 'dbcon.php';

if ($_SERVER['REQUEST_METHOD']==='POST'){
  $school=trim($_POST['SchoolID']??'');
  $pass=trim($_POST['Password']??'');

  $stmt=$conn->prepare("SELECT VoterID, FirstName, LastName, Email, Password, Verified FROM voters WHERE SchoolID=? LIMIT 1");
  $stmt->bind_param('s',$school);
  $stmt->execute();
  $res=$stmt->get_result();
  if($row=$res->fetch_assoc()){
    if($pass!==$row['Password']){
      $_SESSION['login_error']='Incorrect password.';
      header('Location: login.php');exit;
    }
    if($row['Verified']!=='Verified'){
      $_SESSION['pending_email']=$row['Email'];
      $_SESSION['login_error']='Please verify your email first.';
      header('Location: verify_otp.php');exit;
    }
    $_SESSION['voter_id']=$row['VoterID'];
    $_SESSION['full_name']=$row['FirstName'].' '.$row['LastName'];
    $_SESSION['email']=$row['Email'];
    header('Location: index.php');exit;
  } else {
    $_SESSION['login_error']='Account not found.';
    header('Location: login.php');exit;
  }
}
