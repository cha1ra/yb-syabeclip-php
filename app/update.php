<?php
// update.php

require 'db.php';

// POSTデータを取得
$id = $_POST['id'];
$src = $_POST['src'];
$transcripts = json_decode($_POST['transcripts'], true);

// SQLクエリを準備
$sql = "UPDATE videos SET src = :src, clips = :clips, updated_at = NOW() WHERE id = :id";
$stmt = $pdo->prepare($sql);
$params = [
    ':id' => $id,
    ':src' => $src,
    ':clips' => json_encode($transcripts)
];

// クエリを実行
if ($stmt->execute($params)) {
    $response = [
        'id' => $id,
        'src' => $src,
        'clips' => $transcripts,
        'updated_at' => date('Y-m-d H:i:s')
    ];
    echo json_encode($response);
} else {
    http_response_code(500);
    echo json_encode(['error' => $stmt->errorInfo()[2]]);
}
?>