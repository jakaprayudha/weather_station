<?php
header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] != 'GET') {
   http_response_code(405);
   echo json_encode(['message' => 'Method Not Allowed']);
   exit;
}
require '../database/connect.php';
$sql = "SELECT * FROM devices_log";
$result = $connect->query($sql);
$data = [];
if ($result->num_rows > 0) {
   while ($row = $result->fetch_assoc()) {
      $data[] = $row;
   }
   http_response_code(200);
   echo json_encode(array(
      'status' => 200,
      'message' => 'Get Dat Succes',
      'data' => $data
   ));
} else {
   http_response_code(404);
   echo json_encode(array(
      'status' => 404,
      'message' => 'Data Not Found'
   ));
}
