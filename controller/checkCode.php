<?php
session_start();

if (!isset($_POST['code'])) {
    echo "no_code";
    exit();
}

$userCode = trim($_POST['code']);
$realCode = $_SESSION['code_verif'] ?? "";

if ($userCode == $realCode) {
    echo "correct";
} else {
    echo "wrong";
}
