<?php
header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] != 'GET') {
   http_response_code(405);
   echo json_encode(['message' => 'Method Not Allowed']);
   exit;
}
require '../database/connect.php';
// $data = json_decode(file_get_contents('php://input'), true);
if (isset($_GET['number_devices']) && isset($_GET['temp']) && isset($_GET['humd'])) {
   $number_devices = $_GET['number_devices'];
   $checkdevice = mysqli_query($connect, "SELECT id_devices, number_devices FROM devices WHERE number_devices = '$number_devices'");
   $datacheck = mysqli_fetch_array($checkdevice);
   if ($datacheck == NULL) {
      http_response_code(404);
      echo json_encode(array(
         'status' => 404,
         'message' => 'Devices Not Found'
      ));
      exit;
   } else {
      $temperature = $_GET['temp'];
      $humidity = $_GET['humd'];
      if ($temp >= 38) {
         $log_notes = "Panas";
      } else if ($temp >= 30) {
         $log_notes = "Cerah";
      } else {
         $log_notes = "Dingin";
      }
      $device_id = $datacheck['id_devices'];
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
}
