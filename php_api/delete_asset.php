<?php
require_once "condb.php";

$input = json_decode(file_get_contents("php://input"), true);
$asset_id = intval($input['asset_id'] ?? ($_GET['asset_id'] ?? 0));

if ($asset_id <= 0) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "ไม่พบรหัสอุปกรณ์ที่ต้องการลบ"]);
    exit();
}

try {
    $stmt = $conn->prepare("DELETE FROM assets WHERE asset_id = :asset_id");
    $stmt->execute([':asset_id' => $asset_id]);
    echo json_encode(["success" => true, "message" => "ลบข้อมูลอุปกรณ์เรียบร้อยแล้ว"]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
?>
