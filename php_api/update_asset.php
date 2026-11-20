<?php
require_once "condb.php";

$contentType = $_SERVER['CONTENT_TYPE'] ?? '';
if (stripos($contentType, 'application/json') !== false) {
    $input = json_decode(file_get_contents("php://input"), true) ?? [];
} else {
    $input = $_POST;
}

$asset_id = intval($input['asset_id'] ?? 0);
$asset_code = trim($input['asset_code'] ?? '');
$asset_name = trim($input['asset_name'] ?? '');
$purchase_date = trim($input['purchase_date'] ?? '') ?: null;
$price = isset($input['price']) && $input['price'] !== '' ? floatval($input['price']) : 0.00;
$image_name = trim($input['image'] ?? '');

if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = __DIR__ . "/uploads/";
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    $ext = strtolower(pathinfo($_FILES['image_file']['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    if (in_array($ext, $allowed)) {
        $image_name = time() . "_" . preg_replace('/[^a-zA-Z0-9._-]/', '_', basename($_FILES['image_file']['name']));
        move_uploaded_file($_FILES['image_file']['tmp_name'], $uploadDir . $image_name);
    }
}

if ($asset_id <= 0 || $asset_code === '' || $asset_name === '') {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "กรุณากรอกข้อมูลให้ครบถ้วน"]);
    exit();
}

try {
    $stmt = $conn->prepare(
        "UPDATE assets
         SET asset_code = :asset_code,
             asset_name = :asset_name,
             purchase_date = :purchase_date,
             price = :price,
             image = :image
         WHERE asset_id = :asset_id"
    );
    $stmt->execute([
        ':asset_code' => $asset_code,
        ':asset_name' => $asset_name,
        ':purchase_date' => $purchase_date,
        ':price' => $price,
        ':image' => $image_name,
        ':asset_id' => $asset_id,
    ]);
    echo json_encode(["success" => true, "message" => "แก้ไขข้อมูลอุปกรณ์เรียบร้อยแล้ว"]);
} catch (PDOException $e) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
?>
