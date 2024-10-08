<?php
// store.php

require 'db.php';
require_once 'csrf_token.php';
require_once 'auth.php';

$user_id = getCurrentUserId();

// ユーザーがログインしていない場合はエラーを返す
if (!$user_id) {
    http_response_code(401);
    echo json_encode(['error' => 'ユーザーがログインしていません']);
    exit;
}

// CSRFトークンの検証
if (!isset($_POST['csrf_token']) || !verify_csrf_token($_POST['csrf_token'])) {
    http_response_code(403);
    echo json_encode(['error' => 'CSRF検証に失敗しました']);
    exit;
}

// POSTデータを取得
$src = $_POST['src'];
$transcripts = json_decode($_POST['transcripts'], true);

// 現在時刻を取得
$current_time = date('Y-m-d H:i:s');

// SQLクエリを準備（user_idカラムを追加）
$sql = "INSERT INTO videos (user_id, src, clips_origin, clips, created_at, updated_at) VALUES (:user_id, :src, :clips_origin, :clips, :created_at, :updated_at)";
$stmt = $pdo->prepare($sql);
$params = [
    ':user_id' => $user_id,
    ':src' => $src,
    ':clips_origin' => json_encode($transcripts),
    ':clips' => json_encode($transcripts),
    ':created_at' => $current_time,
    ':updated_at' => $current_time
];

// クエリを実行
if ($stmt->execute($params)) {
    $lastInsertId = $pdo->lastInsertId();
    $response = [
        'id' => $lastInsertId,
        'user_id' => $user_id,
        'src' => $src,
        'clips_origin' => $transcripts,
        'clips' => $transcripts,
        'created_at' => $current_time,
        'updated_at' => $current_time
    ];
    echo json_encode($response);
} else {
    http_response_code(500);
    echo json_encode(['error' => $stmt->errorInfo()[2]]);
}
?>