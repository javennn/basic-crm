<?php require_once('./components/config/function.php');?>

<?php
// Established a maximum number of attempts can be made by the user.
$maxAttempts = 3;

// Defined reset time in seconds.
$resetTime = 30;

// Defined the added penalty if after the reset time, the counter resets to 0, and the user failed again to input his password for 3 times.
$resetTimePenalty = 30;

// Initialized the login attempts counter session variable if it's not already set.
if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
}

// Check if the last attempt time is set and if it's time to reset the attempts counter.
if (isset($_SESSION['last_attempt_time'])) {
    $lastAttemptTime = $_SESSION['last_attempt_time'];
    $currentTime = time();

    // Check if reset time has passed
    if (($currentTime - $lastAttemptTime) > $resetTime) {
        // Reset login attempts counter
        $_SESSION['login_attempts'] = 0;
        unset($_SESSION['last_attempt_time']);
    }
}

if (isset($_SESSION['auth'])) {
    redirect('app.php?to=customers', 'Informational', 'You are already logged in.');
}

// Determine if loader should be shown
$showLoader = ($_SESSION['login_attempts'] >= $maxAttempts) && (isset($_SESSION['last_attempt_time']) && (time() - $_SESSION['last_attempt_time']) <= $resetTime);

if (isset($_POST['login'])) {
    $username = validate($_POST['username_input']);

    $sqlCheckUsers = "SELECT * FROM `users` WHERE `user_username` = '$username' LIMIT 1";
    $resultCheckUsers = mysqli_query($conn, $sqlCheckUsers);

    if($username == '' || $resultCheckUsers == true) {
        $unknownUser  = 'Unknown user';
    }

    $password = $_POST['password_input'];

    $convertedUsername = convert($username);

    $sanitizedUsername = filter_var($convertedUsername, FILTER_SANITIZE_STRING);

    if ($sanitizedUsername != '' && $password != '') {
        if ($_SESSION['login_attempts'] < $maxAttempts) {
            $sql = "SELECT * FROM `users` WHERE `user_username` = '$sanitizedUsername' LIMIT 1";
            $result = mysqli_query($conn, $sql);

            // Debugging to check if SQL query is correct.
            if (!$result) {
                die("Query failed: " . mysqli_error($conn));
            }

            if (mysqli_num_rows($result) == 1) {
                $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
                $hashedPassword = $row['user_password_hash'];

                // If inputted password from the login is matched with the hashed password of it then.
                if (password_verify($password, $hashedPassword)) {
                    // Proceed with login
                
                    // Reset the login attempt counter
                    $_SESSION['login_attempts'] = 0;
                
                    if ($row['user_islock'] == 1) {
                        $type = 'Failed';
                        $activity = 'tried to login with a locked account.';
                        logActivity($type, $row['user_username'], $activity);

                        redirect('index.php', 'Failed', 'User is locked.');
                    } 
                    else {
                        $_SESSION['auth'] = true;
                        $_SESSION['loggedInUserRole'] = $row['user_role'];
                        $_SESSION['loggedInUser'] = [
                            'id' => $row['id'], // Add user ID to session
                            'user_username' => $row['user_username'],
                            'user_password' => $row['user_password'],
                        ];
                
                        $_SESSION['username'] = $row['user_username'];

                        $type = 'Success';
                        $activity = 'has logged in.';
                        logActivity($type, $row['user_username'], $activity);

                        // Determine redirect URL based on role
                        $redirectToAdmin = 'app.php?to=administration';
                        $redirectToUser = 'app.php?to=customers';

                        // Redirect to the loader
                        redirectToLoader($row['user_role'], $redirectToAdmin, $redirectToUser);
                    }
                }
                else {
                    // Password is incorrect
                    // Increment the attempt counter
                    $_SESSION['login_attempts']++;
                
                    $remainingAttempts = $maxAttempts - $_SESSION['login_attempts'];
                    
                    $type = 'Failed';
                    $activity = 'has failed to login, and only have ' . $remainingAttempts . ' attempts remaining.';
                    logActivity($type, $sanitizedUsername, $activity);
                
                    if ($remainingAttempts > 0) {
                        redirect('index.php', 'Informational', 'Password is incorrect, ' . $remainingAttempts . ' attempts remaining.');
                    } else {
                        // Set the last attempt time
                        $_SESSION['last_attempt_time'] = time();
                        $userStatus = 1; // Lock user account

                        // Update the user's lock status in the database
                        $sqlLock = "UPDATE `users` SET `user_islock` = $userStatus WHERE `user_username` = '$sanitizedUsername'";
                        mysqli_query($conn, $sqlLock);

                        $type = 'Failed';
                        $activity = 'has been locked, due to many failed login attempts.';
                        logActivity($type, $sanitizedUsername, $activity);

                        redirectNoPrompt('index.php');
                    }
                }
            } 
            else {
                // User not found

                $type = 'Warning';
                $activity = 'tried to login with an unknown account.';
                logActivity($type, $sanitizedUsername, $activity);
            
                redirect('index.php', 'Informational', 'User not found.');
            }
        } 
        else {
            // Set the last attempt time
            $_SESSION['last_attempt_time'] = time();
            $userStatus = 1; // Lock user account

            // Update the user's lock status in the database
            $sqlLock = "UPDATE `users` SET `user_islock` = $userStatus WHERE `user_username` = '$sanitizedUsername'";
            mysqli_query($conn, $sqlLock);

            $type = 'Failed';
            $activity = 'has been locked, due to many failed login attempts.';
            logActivity($sanitizedUsername, $activity);
        
            redirectNoPrompt('index.php');
        }
    } 
    else {
        $type = 'Warning';
        $activity = 'tried to login with an empty user and password.';
        logActivity($type, $unknownUser, $activity);

        // Username or password is empty
        redirect('index.php', 'Informational', 'Please enter username and password.');
    }
}
?>