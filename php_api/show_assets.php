<?php
require_once "condb.php";

try {
    $stmt = $conn->prepare(
        "SELECT asset_id, asset_code, asset_name, purchase_date, price, image, created_at
         FROM assets ORDER BY asset_id ASC"
    );
    $stmt->execute();
    echo json_encode(["success" => true, "data" => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
?>
