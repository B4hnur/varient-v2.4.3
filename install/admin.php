<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once 'functions.php';

$dbArray = [
    'db_host' => $_POST['db_host'],
    'db_user' => $_POST['db_user'],
    'db_password' => $_POST['db_password'],
    'db_name' => $_POST['db_name']
];

$licenseCode = $_POST["license_code"];
$purchaseCode = $_POST["purchase_code"];
if (!isset($licenseCode) || !isset($purchaseCode)) {
    header("Location: index.php");
    exit();
}

$timezone = '';
if (isset($_POST["btn_admin"])) {

    $dbArray = [
        'db_host' => $_POST['db_host'],
        'db_user' => $_POST['db_user'],
        'db_password' => $_POST['db_password'],
        'db_name' => $_POST['db_name']
    ];
    $licenseCode = $_POST["license_code"];
    $purchaseCode = $_POST["purchase_code"];

    $timezone = trim($_POST['timezone']);
    $baseUrl = trim($_POST['base_url']);
    $adminUsername = trim($_POST['admin_username']);
    $adminEmail = trim($_POST['admin_email']);
    $adminPassword = trim($_POST['admin_password']);

    $adminPasswordHash = password_hash($adminPassword, PASSWORD_DEFAULT);
    $slug = str_slug($adminUsername);

    /* Connect */
    $connection = mysqli_connect($dbArray['db_host'], $dbArray['db_user'], $dbArray['db_password'], $dbArray['db_name']);
    $connection->query("SET CHARACTER SET utf8mb4");
    $connection->query("SET NAMES utf8mb4");

    /* check connection */
    if (mysqli_connect_errno()) {
        $error = 0;
    } else {
        $token = uniqid("", TRUE);
        $token = str_replace(".", "-", $token) . "-" . rand(10000000, 99999999);

        $stmt = $connection->prepare("INSERT INTO users (`id`, `username`, `slug`, `email`, `email_status`, `token`, `password`, `role_id`, `user_type`, `status`, `show_email_on_profile`, `show_rss_feeds`) 
VALUES (1, ?, ?, ?, 1, ?, ?, 1, 'registered', 1, 1, 1)");
        $stmt->bind_param("sssss", $adminUsername, $slug, $adminEmail, $token, $adminPasswordHash);
        $stmt->execute();

        $stmt= $connection->prepare("UPDATE general_settings SET timezone= ? WHERE id= 1");
        $stmt->bind_param("s", $timezone);
        $stmt->execute();

        /* close connection */
        mysqli_close($connection);
        /*write env file*/
        $env = "#--------------------------------------------------------------------
# ENVIRONMENT
#--------------------------------------------------------------------
CI_ENVIRONMENT = production

#--------------------------------------------------------------------
# APP
#--------------------------------------------------------------------
app.baseURL = " . trim($baseUrl) . "

#--------------------------------------------------------------------
# LICENSE
#--------------------------------------------------------------------
PURCHASE_CODE = " . trim($purchaseCode) . "
LICENSE_KEY = " . trim($licenseCode) . "

#--------------------------------------------------------------------
# COOKIE
#--------------------------------------------------------------------  
cookie.prefix = 'vr_'";
        $handle = fopen("../.env", "w");
        fwrite($handle, trim($env));
        fclose($handle);

        $redir = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") ? "https" : "http");
        $redir .= "://" . $_SERVER['HTTP_HOST'];
        $redir .= str_replace(basename($_SERVER['SCRIPT_NAME']), "", $_SERVER['SCRIPT_NAME']);
        $redir = str_replace('install/', '', $redir);
        header("refresh:5;url=" . $redir);
        $success = 1;
    }
} ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Varient - Installer</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="../assets/admin/plugins/bootstrap/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css?family=Poppins:400,500,700" rel="stylesheet">
    <!-- Font-awesome CSS -->
    <link href="../assets/admin/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet"/>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="container">
    <div class="row">
        <div class="col-md-8 col-sm-12 col-md-offset-2">
            <div class="row">
                <div class="col-sm-12 logo-cnt">
                    <h1>Varient</h1>
                    <p>Welcome to the Installer</p>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="install-box">
                        <div class="steps">
                            <div class="step-progress">
                                <div class="step-progress-line" data-now-value="100" data-number-of-steps="5" style="width: 100%;"></div>
                            </div>
                            <div class="step">
                                <div class="step-icon"><i class="fa fa-code"></i></div>
                                <p>Start</p>
                            </div>
                            <div class="step">
                                <div class="step-icon"><i class="fa fa-gear"></i></div>
                                <p>System Requirements</p>
                            </div>
                            <div class="step">
                                <div class="step-icon"><i class="fa fa-folder-open"></i></div>
                                <p>Folder Permissions</p>
                            </div>
                            <div class="step">
                                <div class="step-icon"><i class="fa fa-database"></i></div>
                                <p>Database</p>
                            </div>
                            <div class="step active">
                                <div class="step-icon"><i class="fa fa-user"></i></div>
                                <p>Settings</p>
                            </div>
                        </div>
                        <div class="messages">
                            <?php if (isset($error)) { ?>
                                <div class="alert alert-danger">
                                    <strong>Connect failed! Please check your database credentials.</strong>
                                </div>
                            <?php } ?>
                            <?php if (isset($success)) { ?>
                                <div class="alert alert-success">
                                    <strong>Completing installation... Please wait!</strong>
                                </div>
                            <?php } ?>
                        </div>
                        <?php if (isset($success)) { ?>
                            <div class="col-sm-12">
                                <div class="row">
                                    <div class="spinner">
                                        <div class="rect1"></div>
                                        <div class="rect2"></div>
                                        <div class="rect3"></div>
                                        <div class="rect4"></div>
                                        <div class="rect5"></div>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                        <div class="step-contents">
                            <div class="tab-1">
                                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                                    <div class="tab-content">
                                        <div class="tab_1">
                                            <h1 class="step-title">Settings</h1>
                                            <?php $root = (isset($_SERVER['HTTPS']) ? "https://" : "http://") . $_SERVER['HTTP_HOST'];
                                            $root .= str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);
                                            $root = str_replace('install/', '', $root); ?>
                                            <div class="form-group">
                                                <label>Site URL (Examples: <span style='font-family: "Helvetica Neue", Helvetica, Arial, sans-serif'>https://abc.com, https://abc.com/blog, https://test.abc.com</span>)</label><br>
                                                <input type="text" class="form-control form-input" name="base_url" placeholder="Base URL" value="<?php echo @$root; ?>" required>
                                                <small class="text-danger">(If your site does not have SSL, you must enter your site URL with "http". Example: http://abc.com)</small>
                                            </div>
                                            <div class="form-group">
                                                <label for="email">Timezone</label>
                                                <select name="timezone" class="form-control" required style="min-height: 44px;">
                                                    <option value="">Select Your Timezone</option>
                                                    <?php $timezones = timezone_identifiers_list();
                                                    if (!empty($timezones)):
                                                        foreach ($timezones as $item):?>
                                                            <option value="<?php echo $item; ?>" <?= $timezone == $item ? 'selected' : ''; ?>><?php echo $item; ?></option>
                                                        <?php endforeach;
                                                    endif; ?>
                                                </select>
                                                <br>
                                            </div>
                                            <h1 class="step-title">Admin Account</h1>
                                            <div class="form-group">
                                                <label for="email">Username</label>
                                                <input type="text" class="form-control form-input" name="admin_username" placeholder="Username" value="<?php echo @$adminUsername; ?>" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="email">Email</label>
                                                <input type="email" class="form-control form-input" name="admin_email" placeholder="Email" value="<?php echo @$adminEmail; ?>" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="email">Password</label>
                                                <input type="text" class="form-control form-input" name="admin_password" placeholder="Password" value="<?php echo @$adminPassword; ?>" required>
                                            </div>
                                            <input type="hidden" name="db_host" value="<?= htmlspecialchars($dbArray['db_host']) ?>">
                                            <input type="hidden" name="db_user" value="<?= htmlspecialchars($dbArray['db_user']) ?>">
                                            <input type="hidden" name="db_password" value="<?= htmlspecialchars($dbArray['db_password']) ?>">
                                            <input type="hidden" name="db_name" value="<?= htmlspecialchars($dbArray['db_name']) ?>">
                                            <input type="hidden" name="license_code" value="<?= htmlspecialchars($licenseCode); ?>">
                                            <input type="hidden" name="purchase_code" value="<?= htmlspecialchars($purchaseCode); ?>">
                                        </div>
                                    </div>
                                    <div class="buttons">
                                        <a href="database.php?license_code=<?= htmlspecialchars($licenseCode); ?>&purchase_code=<?= htmlspecialchars($purchaseCode); ?>" class="btn btn-success btn-custom pull-left">Prev</a>
                                        <button type="submit" name="btn_admin" class="btn btn-success btn-custom pull-right">Finish</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>