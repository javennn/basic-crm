<?php
if (isset($_SESSION['auth']) && isset($_SESSION['loggedInUserRole']) && $_SESSION['loggedInUserRole'] != 'Admin') {
    logoutSession();
    redirect('././index.php', 'Informational', 'Access denied, you were logged out forcefully.');
}

// Fetch data

$sqlUsers = "SELECT * FROM `users`";
$resultUsers = mysqli_query($conn, $sqlUsers);

$numberOfUsers = mysqli_num_rows($resultUsers);
$totalUsers = 3;

if ($numberOfUsers) {
    echo "<script>
        const numberOfUsers = $numberOfUsers;
        const totalUsers = $totalUsers;
    </script>";
}


?>

<?php
// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Add user
    if (isset($_POST['form_type']) && $_POST['form_type'] === 'add_user') {
        $username = validate($_POST['username-input']);
        $password = $_POST['password-input'];

        // Get the last four characters of the password
        $lastFourPassword = substr($password, -4);

        $userRole = validate($_POST['user-role-select']);

        // Hash the password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $addUserSql = "INSERT INTO `users` (
            `user_username`, 
            `user_password`, 
            `user_password_hash`,
            `user_last_four_password`, 
            `user_role`
        ) VALUES (
            '$username', 
            '$password', 
            '$hashedPassword', 
            '$lastFourPassword',
            '$userRole'
        )";

        $addUserResult = mysqli_query($conn, $addUserSql);

        if ($addUserResult) {
            $userId = mysqli_insert_id($conn);
            $taskTableName = "user_$userId"; 
            $createTableSql = "CREATE TABLE `$taskTableName` (
                `id` INT(100) AUTO_INCREMENT PRIMARY KEY,
                `task_category` VARCHAR(100) NOT NULL,
                `task_trfnumber` VARCHAR(100) NOT NULL,
                `task_trfdate` DATE NULL,
                `task_datetime` VARCHAR(100) NOT NULL,
                `task_preferred_datetime` VARCHAR(100) NOT NULL,
                `task_requestor` VARCHAR(100) NOT NULL,
                `task_department` VARCHAR(100) NOT NULL,
                `task_site` VARCHAR(100) NOT NULL,
                `task_request` TEXT NOT NULL,
                `task_resolution` LONGTEXT NOT NULL,
                `task_status` TINYINT(1) NOT NULL,
                `task_resolveddatetime` VARCHAR(100) NOT NULL
            )";

            $createTableResult = mysqli_query($conn, $createTableSql);
                
            if ($createTableResult) {
                redirect('app.php?to=administration', 'Success', 'User added successfully!');
            } else {
                echo 'Failed to create task table: ' . mysqli_error($conn);
            }
        } else {
            echo 'Failed to add user: ' . mysqli_error($conn);
        }
    }
    // Update user
    else if(isset($_POST['form_type']) && $_POST['form_type'] === 'update_user') {
        $id = validate($_POST['id']);
        $username = validate($_POST['username-input']);
        $password = $_POST['password-input'];

        // Get the last four characters of the new password
        $lastFourPassword = substr($password, -4);

        $userRole = validate($_POST['user-role-select']);
    
        // Hash the new password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
        $updateUserSql = "UPDATE `users` SET 
        `user_username`='$username', 
        `user_password`='$password', 
        `user_password_hash`='$hashedPassword', 
        `user_last_four_password`='$lastFourPassword', 
        `user_role`='$userRole' 
        WHERE id=$id";
    
        echo $updateUserSql; // Debugging line to print SQL query
    
        $updateUserResult = mysqli_query($conn, $updateUserSql);
    
        if($updateUserResult) {
            // Check if the ID being updated matches the ID of the current logged-in user
            if ($id == $_SESSION['loggedInUser']['id']) {
                // Update the session variable only for the current logged-in user
                $_SESSION['loggedInUser']['user_username'] = $username;
                // Also update the session variable for the username in the navbar
                $_SESSION['username'] = $username;
            }
    
            redirect('app.php?to=administration', 'Success', 'User updated successfully.');
        } else {
            echo 'Failed to update user: ' . mysqli_error($conn);
        }
    }
    // Lock or unlock user
    else if(isset($_POST['form_type']) && $_POST['form_type'] === 'toggle_user'){
        $id = validate($_POST['id']);

        // Get the current ban status of the user
        $checkStatusSql = "SELECT `user_islock` FROM `users` WHERE `id`='$id'";
        $statusResult = mysqli_query($conn, $checkStatusSql);

        if ($statusResult && $row = mysqli_fetch_assoc($statusResult)) {
            $isLocked = $row['user_islock'];

            // Determine the new status
            $newStatus = $isLocked ? 0 : 1;
            $statusAction = $isLocked ? 'unlocked' : 'locked';

            // Prepare SQL query to update the user status
            $updateStatusSql = "UPDATE `users` SET `user_islock`='$newStatus' WHERE `id`='$id'";
            $updateStatusResult = mysqli_query($conn, $updateStatusSql);

            if ($updateStatusResult) {
                redirect('app.php?to=administration', 'Success', "User $statusAction successfully.");
            } else {
                echo 'Failed to update user status: ' . mysqli_error($conn);
            }
        } else {
            echo 'Failed to fetch user status: ' . mysqli_error($conn);
        }
    }
}
?>

<!-- Logs -->
<?php
$sqlLogs = "SELECT * FROM `user_activity_logs` ORDER BY id DESC";
$resultLogs = mysqli_query($conn, $sqlLogs);
?>

<!-- Logs filtering -->
<?php
    $filterDate = isset($_GET['filter_date']) ? $_GET['filter_date'] : '';

    if (!empty($filterDate)) {
        // Escape the filter date to prevent SQL injection
        $escapedFilterDate = validate($filterDate);

        // Apply filter condition
        $sql = "SELECT * FROM user_activity_logs WHERE DATE_FORMAT(timestamp, '%Y-%m-%d') = '$escapedFilterDate' ORDER BY id DESC";
    } else {
        // Retrieve all logs if no filter is applied
        $sql = "SELECT * FROM user_activity_logs ORDER BY id DESC";
    }

    $result = mysqli_query($conn, $sql);

    if (!$result) {
        echo "SQL Error: " . $conn->error;
        exit;
    }

    $logsArray = [];
    while ($row = $result->fetch_assoc()) {
        $date = new DateTime($row['timestamp']);
        $formattedDate = $date->format('D, m-d-Y, h:i A');
        $logsArray[] = [
            'id' => $row['id'],
            'date' => $formattedDate,
            'user' => $row['user'],
            'activity' => $row['activity']
        ];
    }
?>
