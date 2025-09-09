<?php
header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
   http_response_code(405);
   echo json_encode(['message' => 'Method Not Allowed']);
   exit;
}
require '../database/connect.php';
// $data = json_decode(file_get_contents('php://input'), true);
if (isset($_POST['id_devices']) && isset($_POST['temp']) && isset($_POST['humd'])) {
   $device_id = $_POST['id_devices'];
   $temperature = $_POST['temp'];
   $humidity = $_POST['humd'];
   if ($temp >= 38) {
      $log_notes = "Panas";
   } else if ($temp >= 30) {
      $log_notes = "Cerah";
   } else {
      $log_notes = "Dingin";
   }
   $sql = "INSERT INTO devices_log (id_devices, temp, humd, log_notes) VALUES ('$device_id', '$temperature', '$humidity','$log_notes')";
   if ($connect->query($sql) === TRUE) {
      http_response_code(200);
      echo json_encode(array(
         'status' => 200,
         'message' => 'Data Inserted Successfully'
      ));
   } else {
      http_response_code(500);
      echo json_encode(array(
         'status' => 500,
         'message' => 'Data Not Inserted'
      ));
   }
}
