<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Blog MVC</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-800">

    <nav class="bg-white border-b p-4 mb-8 flex justify-between items-center shadow-sm">
        
        <a href="index.php?action=posts" class="font-bold text-xl text-blue-600">
            BLOG MVC
        </a>

        <div class="space-x-4 text-sm font-medium">
            <a href="index.php?action=posts" class="hover:text-blue-500">INICIO</a>
            <a href="index.php?action=rag_ask" class="hover:text-blue-500">BUSCAR</a>

            <?php if (isset($_SESSION['user_id'])): ?>
                <span class="text-gray-400">|</span>
                
                <?php if ($_SESSION['role'] === 'admin'): ?>
                    <a href="index.php?action=admin_dashboard" class="text-purple-600 hover:text-purple-800">Panel Admin</a>
                <?php endif; ?>
                
                <span class="bg-green-100 text-green-700 px-3 py-1 rounded">
                    👤 <?= htmlspecialchars($_SESSION['username'] ?? 'User') ?>
                </span>
                <a href="index.php?action=logout" class="text-red-500 hover:text-red-700 ml-2">Salir</a>
            <?php else: ?>
                <span class="text-gray-400">|</span>
                <a href="index.php?action=login" class="hover:text-blue-500 bg-gray-100 px-3 py-1 rounded">Iniciar sesión</a>
                <a href="index.php?action=register" class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700">Registrarse</a>
            <?php endif; ?>
        </div>
    </nav>

    <main class="container mx-auto px-4">