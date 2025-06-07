<?php
$id = validate($_GET['id']);

$sql = "DELETE FROM `users` WHERE id = $id";
$result = mysqli_query($conn, $sql);

if($result){
    redirect('app.php?to=administration', 'Success', 'User deleted.');
}
else {
    echo "Failed to delete user:" . mysqli_error($conn);
}
?>