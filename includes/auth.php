<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function isLoggedIn()
{
    if (isset($_SESSION["user_id"])) {
        return true;
    }

    return false;
}

function isAdmin()
{
    if (isLoggedIn() && $_SESSION["role"] == "admin") {
        return true;
    }

    return false;
}

function isEmployee()
{
    if (isLoggedIn() && $_SESSION["role"] == "employee") {
        return true;
    }

    return false;
}

function requireLogin()
{
    if (!isLoggedIn()) {
        header("Location: ../login.php");
        exit();
    }
}

function requireAdmin()
{
    requireLogin();

    if (!isAdmin()) {
        header("Location: ../login.php");
        exit();
    }
}

function requireEmployee()
{
    requireLogin();

    if (!isEmployee()) {
        header("Location: ../login.php");
        exit();
    }
}

function logout()
{
    $_SESSION = [];
    session_destroy();
}
