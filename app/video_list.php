<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Uploaded Videos</title>
</head>
<body>
    <h1>Uploaded Videos</h1>
    <?php
    $directory = 'uploads/';
    $videos = array_diff(scandir($directory), array('..', '.'));

    if (count($videos) > 0) {
        foreach ($videos as $video) {
            $videoPath = $directory . $video;
            echo "<div>";
            echo "<h2>$video</h2>";
            echo "<video width='320' height='240' controls>";
            echo "<source src='$videoPath' type='video/webm'>";
            echo "Your browser does not support the video tag.";
            echo "</video>";
            echo "</div>";
        }
    } else {
        echo "<p>No videos found</p>";
    }
    ?>
</body>
</html>