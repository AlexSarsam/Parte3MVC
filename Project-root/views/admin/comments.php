<?php include __DIR__ . '/../layout/header.php'; ?>

<div class="max-w-6xl mx-auto mt-10 p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">Gestión de Comentarios</h1>
        <a href="index.php?action=admin_dashboard" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">
            ← Volver al Dashboard
        </a>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            <?= htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Usuario</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Post</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Comentario</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Imagen</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (empty($comments)): ?>
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                            No hay comentarios registrados
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($comments as $comment): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm"><?= htmlspecialchars($comment['id']) ?></td>
                            <td class="px-6 py-4 text-sm font-medium"><?= htmlspecialchars($comment['username']) ?></td>
                            <td class="px-6 py-4 text-sm">
                                <a href="index.php?action=show_post&id=<?= $comment['post_id'] ?>" 
                                   class="text-blue-600 hover:underline" target="_blank">
                                    <?= htmlspecialchars(substr($comment['post_title'], 0, 30)) ?>...
                                </a>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <?= htmlspecialchars(substr($comment['content'], 0, 50)) ?>...
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <?php if (!empty($comment['image'])): ?>
                                    <img src="uploads/<?= htmlspecialchars($comment['image']) ?>" 
                                         alt="Imagen" class="w-12 h-12 object-cover rounded">
                                <?php else: ?>
                                    <span class="text-gray-400">Sin imagen</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                <?= date('d/m/Y H:i', strtotime($comment['created_at'])) ?>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                          <a href="index.php?action=admin_delete_comment&id=<?= $comment['id'] ?>&csrf_token=<?= htmlspecialchars($_SESSION['csrf_token']) ?>" 
                                   class="text-red-600 hover:text-red-900"
                                   onclick="return confirm('¿Estás seguro de eliminar este comentario?')">
                                    Eliminar
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="mt-6 text-sm text-gray-600">
        Total de comentarios: <strong><?= count($comments) ?></strong>
    </div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>
