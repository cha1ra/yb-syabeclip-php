<?php
require_once 'csrf_token.php';

// CSRFトークンの検証
if (!isset($_POST['csrf_token']) || !verify_csrf_token($_POST['csrf_token'])) {
    http_response_code(403);
    echo json_encode(['error' => 'CSRF検証に失敗しました']);
    exit;
}

// 保存先ディレクトリを指定
$target_dir = "uploads/";
if (!file_exists($target_dir)) {
    mkdir($target_dir, 0777, true);
}

// ファイル名を取得
$target_file = $target_dir . basename($_FILES["videoChunk"]["name"]);
$uploadOk = 1;

// ファイルの種類を確認
$fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
if ($fileType != "webm") {
    echo "Sorry, only WEBM files are allowed.";
    error_log("File type not allowed: " . $fileType, 3, "upload_errors.log");
    $uploadOk = 0;
}

// ファイルを保存
if ($uploadOk == 1) {
    if (file_exists($target_file)) {
        // 既存のファイルにチャンクを追加
        $file = fopen($target_file, 'ab');
        $chunk = file_get_contents($_FILES["videoChunk"]["tmp_name"]);
        fwrite($file, $chunk);
        fclose($file);
        echo "The chunk has been appended to the existing file.";
    } else {
        // 新しいファイルとして保存
        if (move_uploaded_file($_FILES["videoChunk"]["tmp_name"], $target_file)) {
            echo "The file " . htmlspecialchars(basename($_FILES["videoChunk"]["name"])) . " has been uploaded.";
        } else {
            $error = $_FILES["videoChunk"]["error"];
            echo "Sorry, there was an error uploading your file. Error code: $error";
            error_log("Failed to move uploaded file: $error", 3, "upload_errors.log");
        }
    }
} else {
    error_log("Upload not OK: " . $_FILES["videoChunk"]["error"], 3, "upload_errors.log");
}
?>