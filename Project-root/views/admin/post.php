<?php 
// Asegura ruta correcta al layout (views/layout)
include __DIR__ . '/../layout/header.php'; 
?>

<div class="max-w-6xl mx-auto mt-10 p-6">
    
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Panel de Noticias</h1>
        
        <a href="index.php?action=admin_create_post" class="bg-blue-600 text-white px-5 py-2 rounded-lg hover:bg-blue-700 shadow-md transition duration-200 flex items-center gap-2">
            <span>+</span> Redactar Nueva Noticia
        </a>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 shadow-sm rounded-r" role="alert">
            <p class="font-bold">¡Éxito!</p>
            <p><?= htmlspecialchars($_SESSION['success']); ?></p>
        </div>
        <?php unset($_SESSION['success']); // Borramos el mensaje tras mostrarlo ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 shadow-sm rounded-r" role="alert">
            <p class="font-bold">Error</p>
            <p><?= htmlspecialchars($_SESSION['error']); ?></p>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <div class="bg-white shadow-md rounded-lg overflow-x-auto">
        <table class="min-w-full leading-normal table-fixed">
            <thead>
                <tr class="bg-gray-100 text-gray-600 uppercase text-sm leading-normal">
                    <th class="py-3 px-4 text-left w-16">ID</th>
                    <th class="py-3 px-4 text-left">Título</th>
                    <th class="py-3 px-4 text-center w-32">Estado</th>
                    <th class="py-3 px-4 text-center w-40">Acciones</th>
                </tr>
            </thead>
            <tbody class="text-gray-600 text-sm font-light">
                <?php if (!empty($posts)): ?>
                    <?php foreach ($posts as $post): ?>
                    <tr class="border-b border-gray-200 hover:bg-gray-50 transition duration-150">
                        
                        <td class="py-3 px-4 text-left whitespace-nowrap font-medium">
                            #<?= (int)$post['id'] ?>
                        </td>
                        
                        <td class="py-3 px-4 text-left truncate max-w-xs">
                            <a href="index.php?action=show_post&id=<?= (int)$post['id'] ?>" class="font-medium text-gray-800 hover:text-blue-600 transition">
                                <?= htmlspecialchars(mb_strimwidth($post['title'], 0, 80, '...')) ?>
                            </a>
                            <?php if(isset($post['created_at'])): ?>
                                <div class="text-xs text-gray-400 mt-1"><?= date('d/m/Y', strtotime($post['created_at'])) ?></div>
                            <?php endif; ?>
                        </td>

                        <td class="py-3 px-4 text-center">
                            <?php if (($post['status'] ?? 'published') === 'published'): ?>
                                <span class="bg-green-200 text-green-700 py-1 px-3 rounded-full text-xs font-bold uppercase tracking-wide">
                                    Publicado
                                </span>
                            <?php else: ?>
                                <span class="bg-yellow-200 text-yellow-700 py-1 px-3 rounded-full text-xs font-bold uppercase tracking-wide">
                                    Borrador
                                </span>
                            <?php endif; ?>
                        </td>

                        <td class="py-3 px-4 text-center">
                            <div class="flex item-center justify-center space-x-4">
                                <a href="index.php?action=show_post&id=<?= (int)$post['id'] ?>" class="text-gray-500 hover:text-gray-800" title="Ver">
                                    👁️
                                </a>
                                <a href="index.php?action=admin_edit_post&id=<?= (int)$post['id'] ?>" class="text-blue-500 hover:text-blue-700" title="Editar">
                                    ✏️
                                </a>
                                <a href="index.php?action=admin_delete_post&id=<?= (int)$post['id'] ?>&csrf_token=<?= htmlspecialchars($_SESSION['csrf_token']) ?>" 
                                   class="text-red-500 hover:text-red-700" 
                                   onclick="return confirm('¿Estás seguro de que quieres borrar esta noticia?');" title="Borrar">
                                    🗑️
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="py-6 px-6 text-center text-gray-500">
                            No hay noticias publicadas todavía. ¡Crea la primera!
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="mt-6 text-right">
        <a href="index.php" class="text-sm text-gray-500 hover:underline">Volver al Inicio</a>
    </div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>