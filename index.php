<?php require('./server/index.php');?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="shortcut icon" href="sg.ico" type="image/x-icon"/>
    <link rel="stylesheet" href="./assets/index-styles2.css">
    
    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    
    <!-- Fontawesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <title> Login </title>
</head>
<body>
    <div class="main-container">
        <div class="login-container-header">
            <?php if(isset($_SESSION['status']) && $_SESSION['status'] != '') { promptMessageMain(); }?>
        </div>
        <div class="login-container">
            <div class="loader-main <?php echo $showLoader ? 'show' : ''; ?>">
                <div class="loader-submain">
                <div class="logo-container">
                    <!-- <img src="../assets/img/sample logo.png" width="10%" alt="sample logo"> -->
                    <label class="logo">basic-crm app</label>
                    <div class="splitter"></div>
                    <label class="ver">v1.18.2025</label>
                </div>

                <svg class="pl" viewBox="0 0 128 128" width="128px" height="128px" xmlns="http://www.w3.org/2000/svg">
                    <defs>
                        <linearGradient id="pl-grad" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stop-color="#f15951"></stop>
                            <stop offset="100%" stop-color="#BD443E"></stop>
                        </linearGradient>
                    </defs>
                    <circle class="pl__ring" r="56" cx="64" cy="64" fill="none" stroke="hsla(0,10%,10%,0.1)" stroke-width="16" stroke-linecap="round"></circle>
                    <path class="pl__worm" d="M92,15.492S78.194,4.967,66.743,16.887c-17.231,17.938-28.26,96.974-28.26,96.974L119.85,59.892l-99-31.588,57.528,89.832L97.8,19.349,13.636,88.51l89.012,16.015S81.908,38.332,66.1,22.337C50.114,6.156,36,15.492,36,15.492a56,56,0,1,0,56,0Z" fill="none" stroke="url(#pl-grad)" stroke-width="16" stroke-linecap="round" stroke-linejoin="round" stroke-dasharray="44 1111" stroke-dashoffset="10"></path>
                </svg>

                <label id="loaderLabelSub" class="loader-label-sub">❌ Maximum attempts exceeded, <br> <span>your account has been <b>lock</b>.</span></label>
                <label class="loader-label">Please wait here until the form <br> re-enables.</label>
                </div>
            </div>

            <div class="login-container-sub <?php echo $showLoader ? 'hide' : ''; ?>">
                <div class="logo-container">
                    <!-- <img src="../assets/img/sample logo.png" width="10%" alt="sample logo"> -->
                    <label class="logo">basic-crm app</label>
                    <div class="splitter"></div>
                    <label class="ver">v1.18.2025</label>
                </div>

                <div class="loginform-container">
                    <form method="POST" class="loginform-container-form">
                        <label class="b">Username</label>
                        <input class="logininput-fieldform" type="text" name="username_input" placeholder="Enter username...">

                        <label class="b">Password</label>
                        <input type="password" class="logininput-fieldform" id="userPassword" name="password_input" placeholder="Enter password...">

                        <div class="show-password-container">
                            <input type="checkbox" class="logininput-showpassword" name="show_password_checkbox" onclick="togglePasswordVisibility()">
                            <label class="nb">Show password</label>
                        </div>

                        <input type="submit" name="login" class="login-btn" value="Login">
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php include('./components/footer.php');?>