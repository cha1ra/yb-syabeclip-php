<?php
require_once 'db.php';
require_once 'auth.php';
require_once 'csrf_token.php';

requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    $csrf_token = filter_input(INPUT_GET, 'csrf_token', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    if (!$id || !verify_csrf_token($csrf_token)) {
        http_response_code(403);
        echo json_encode(['error' => 'Invalid request']);
        exit;
    }

    try {
        $pdo->beginTransaction();

        // 動画情報を取得
        $stmt = $pdo->prepare("SELECT src FROM videos WHERE id = :id AND user_id = :user_id");
        $stmt->execute([':id' => $id, ':user_id' => $_SESSION['user_id']]);
        $video = $stmt->fetch();

        if (!$video) {
            throw new Exception('Video not found or you do not have permission to delete it.');
        }

        // データベースから動画レコードを削除
        $stmt = $pdo->prepare("DELETE FROM videos WHERE id = :id AND user_id = :user_id");
        $stmt->execute([':id' => $id, ':user_id' => $_SESSION['user_id']]);

        // 関連する動画ファイルを削除
        $filePath = 'uploads/' . $video['src'];
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        $pdo->commit();
        exit(header('Location: mypage.php'));
    } catch (Exception $e) {
        $pdo->rollBack();
        http_response_code(500);
        exit(json_encode(['error' => $e->getMessage()]));
    }
} else {
    http_response_code(405);
    exit(json_encode(['error' => 'Method not allowed']));
}