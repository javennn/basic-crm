<?php require_once('config/function.php');?>
<?php 
$user = isset($_SESSION['loggedInUser']['user_username']) ? $_SESSION['loggedInUser']['user_username'] : 'Unknown User';

if(isset($_SESSION['auth'])) {
    
    logoutSession();

    $type = 'Failed';
    $message = 'has logged out.';
    logActivity($type, $user, $message);

    redirect('.././index.php', 'Success', 'Logged out succesfully.');
}
?>