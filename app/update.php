<?php
// update.php

require 'db.php';

// POSTデータを取得
$id = $_POST['id'];
$src = $_POST['src'] ?? null;
$clips = isset($_POST['clips']) ? json_decode($_POST['clips'], true) : null;
$title = $_POST['title'] ?? null;
$subtitle = $_POST['subtitle'] ?? null;

// SQLクエリを準備
$updateFields = [];
$params = [':id' => $id];

if ($src !== null) {
    $updateFields[] = "src = :src";
    $params[':src'] = $src;
}

if ($clips !== null) {
    $updateFields[] = "clips = :clips";
    $params[':clips'] = json_encode($clips);
}

if ($title !== null) {
    $updateFields[] = "title = :title";
    $params[':title'] = $title;
}

if ($subtitle !== null) {
    $updateFields[] = "subtitle = :subtitle";
    $params[':subtitle'] = $subtitle;
}

$updateFields[] = "updated_at = NOW()";

if (empty($updateFields)) {
    http_response_code(400);
    echo json_encode(['error' => '更新するフィールドがありません']);
    exit;
}

$sql = "UPDATE videos SET " . implode(', ', $updateFields) . " WHERE id = :id";

// クエリを実行
try {
    $stmt = $pdo->prepare($sql);
    if ($stmt->execute($params)) {
        $response = [
            'id' => $id,
            'updated_at' => date('Y-m-d H:i:s')
        ];
        if ($src !== null) $response['src'] = $src;
        if ($clips !== null) $response['clips'] = $clips;
        if ($title !== null) $response['title'] = $title;
        if ($subtitle !== null) $response['subtitle'] = $subtitle;
        echo json_encode($response);
    } else {
        throw new Exception($stmt->errorInfo()[2]);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>