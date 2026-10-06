<?php
// number for the "Requests" menu badge
include '../config.php';
header('Content-Type: application/json');
echo json_encode(['requests' => pending_requests()]);
