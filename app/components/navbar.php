<header>
    <nav class="bg-white shadow-md">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <ul>
                    <li>
                        <a href="mypage.php" class="text-xl font-semibold text-gray-800 hover:text-gray-600 transition duration-300 ease-in-out">しゃべクリップ</a>
                    </li>
                </ul>
                <ul class="flex items-center space-x-4">
                    <li>
                        ようこそ、<?php echo htmlspecialchars($user['name']); ?>さん  
                    </li>
                    <li>
                        <a href="create.php" class="bg-blue-500 hover:bg-blue-700 text-white py-2 px-4 rounded">動画作成</a>
                    <li>
                        <a href="logout.php" class="bg-red-500 hover:bg-red-700 text-white py-2 px-4 rounded">ログアウト</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</header>