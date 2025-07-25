<?php
// session_manager.php
function secure_session_start() {
    $session_name = 'budgetify_session';
    $secure = false; // Set to true if using HTTPS
    $httponly = true;
    
    ini_set('session.use_only_cookies', 1);
    $cookieParams = session_get_cookie_params();
    session_set_cookie_params(
        $cookieParams["lifetime"],
        $cookieParams["path"],
        $cookieParams["domain"],
        $secure,
        $httponly
    );
    session_name($session_name);
    session_start();
    session_regenerate_id(true);
}

function login_check() {
    return isset($_SESSION['user_id'], $_SESSION['username'], $_SESSION['logged_in']);
}

function redirect_if_logged_in($location = 'homepage.php') {
    if (login_check()) {
        header("Location: $location");
        exit();
    }
}

function redirect_if_not_logged_in($location = 'login.html') {
    if (!login_check()) {
        header("Location: $location");
        exit();
    }
}
?>