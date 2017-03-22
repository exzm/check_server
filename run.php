<?php

require_once "CheckServer.class.php";
require_once "config.php";

$check = new CheckServer();
$check->setLoadAverage(MAX_LOAD_AVERAGE);
$check->setMinFreeDisk(MIN_FREE_DISK);

$result = $check->run();
if ($result) {
    $to = EMAIL;
    $subject = "Уведомление о проблемах на cервере " . SERVER_NAME;
    $message = '<h1>Уведомление о проблемах на cервере ' . SERVER_NAME . '</h1>';
    $message .= '<ul>';
    foreach ($result AS $problem) {
        $message .= "<li>{$problem}</li>";
    }
    $message .= '</ul>';
    mail($to, $subject, $message, 'Content-type: text/html;');
}

