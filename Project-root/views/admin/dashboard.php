<?php include __DIR__ . '/../layout/header.php'; ?>

<div class="max-w-4xl mx-auto mt-10 p-6">
    <h1 class="text-3xl font-bold mb-8 text-gray-800 border-b pb-4">🛠️ Panel de Administración</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        
        <a href="index.php?action=admin_posts" class="block p-6 bg-white border border-gray-200 rounded-lg shadow-md hover:bg-blue-50 transition">
            <h2 class="text-xl font-bold text-blue-600 mb-2">📰 Posts / Noticias</h2>
            <p class="text-gray-600">Crear, editar y borrar publicaciones.</p>
        </a>

        <a href="index.php?action=admin_users" class="block p-6 bg-white border border-gray-200 rounded-lg shadow-md hover:bg-green-50 transition">
            <h2 class="text-xl font-bold text-green-600 mb-2">👥 Usuarios</h2>
            <p class="text-gray-600">Ver usuarios y cambiar roles.</p>
        </a>

        <a href="index.php?action=admin_comments" class="block p-6 bg-white border border-gray-200 rounded-lg shadow-md hover:bg-purple-50 transition">
            <h2 class="text-xl font-bold text-purple-600 mb-2">💬 Comentarios</h2>
            <p class="text-gray-600">Moderar y eliminar comentarios.</p>
        </a>

    </div>

    <div class="mt-8 text-center">
        <a href="index.php?action=posts" class="text-gray-500 hover:text-gray-800 underline">← Volver al Blog público</a>
    </div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>