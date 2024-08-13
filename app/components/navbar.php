<?php
require_once 'db.php';

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
$stmt->execute([':id' => $_SESSION['user_id']]);
$user = $stmt->fetch();
?>
<header>
    <nav class="bg-white shadow-md">
        <div class="mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <a href="mypage.php" class="text-xl font-semibold text-gray-800 hover:text-gray-600 transition duration-300 ease-in-out">
                    <img src="../assets/images/logo-text.png" alt="しゃべクリップ" class="w-56">
                </a>
                
                <!-- ハンバーガーメニューボタン（モバイル用） -->
                <button id="menu-toggle" class="md:hidden">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>

                <!-- メニュー項目 -->
                <ul id="menu-items" class="hidden md:flex md:items-center md:space-x-4">
                    <li class="px-4 md:px-0 py-4 md:py-0">
                        ようこそ、<?php echo htmlspecialchars($user['name']); ?>さん  
                    </li>
                    <li class="md:hidden">
                        <a href="create.php" class="block py-2 px-4 text-gray-800 hover:bg-gray-200 transition">動画作成</a>
                    </li>
                    <li class="md:hidden">
                        <a href="logout.php" class="block py-2 px-4 text-gray-800 hover:bg-gray-200 transition">ログアウト</a>
                    </li>
                    <li class="hidden md:block">
                        <a href="create.php" class="bg-amber-500 hover:bg-amber-700 transition text-white py-2 px-4 rounded">動画作成</a>
                    </li>
                    <li class="hidden md:block">
                        <a href="logout.php" class="bg-gray-700 hover:bg-gray-900 transition text-white py-2 px-4 rounded">ログアウト</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</header>

<script>
document.getElementById('menu-toggle').addEventListener('click', function() {
    var menuItems = document.getElementById('menu-items');
    menuItems.classList.toggle('hidden');
    menuItems.classList.toggle('flex');
    menuItems.classList.toggle('flex-col');
    menuItems.classList.toggle('absolute');
    menuItems.classList.toggle('top-16');
    menuItems.classList.toggle('right-0');
    menuItems.classList.toggle('bg-white');
    menuItems.classList.toggle('w-full');
    menuItems.classList.toggle('shadow-md');
});
</script>