<?php include __DIR__ . '/../layout/header.php'; ?>

<div class="max-w-6xl mx-auto mt-10 p-6">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Gestionar Usuarios</h1>
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
    <div class="bg-white shadow-md rounded my-6 overflow-x-auto">
        <table class="min-w-full w-full table-auto">
            <thead>
                <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                    <th class="py-3 px-6 text-left">ID</th>
                    <th class="py-3 px-6 text-left">Usuario</th>
                    <th class="py-3 px-6 text-left">Email</th>
                    <th class="py-3 px-6 text-center">Rol</th>
                    <th class="py-3 px-6 text-center">Acciones</th>
                </tr>
            </thead>
            <tbody class="text-gray-600 text-sm font-light">
                <?php foreach ($users as $user): ?>
                <tr class="border-b border-gray-200 hover:bg-gray-100">
                    <td class="py-3 px-6 text-left font-bold"><?= $user['id'] ?></td>
                    <td class="py-3 px-6 text-left"><?= htmlspecialchars($user['username']) ?></td>
                    <td class="py-3 px-6 text-left"><?= htmlspecialchars($user['email']) ?></td>
                    <td class="py-3 px-6 text-center">
                        <?php if($user['role'] === 'admin'): ?>
                            <span class="bg-purple-200 text-purple-600 py-1 px-3 rounded-full text-xs">Admin</span>
                        <?php else: ?>
                            <span class="bg-gray-200 text-gray-600 py-1 px-3 rounded-full text-xs">User</span>
                        <?php endif; ?>
                    </td>
                    <td class="py-3 px-6 text-center">
                        <?php if($user['id'] != $_SESSION['user_id']):  ?>
                            <a href="index.php?action=admin_delete_user&id=<?= $user['id'] ?>&csrf_token=<?= htmlspecialchars($_SESSION['csrf_token']) ?>" class="text-red-500 hover:text-red-700 font-bold" onclick="return confirm('¿Seguro que quieres eliminar a este usuario?');">
                                Borrar 🗑️
                            </a>
                        <?php else: ?>
                            <span class="text-gray-400 text-xs">Tú</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        <a href="index.php?action=admin_dashboard" class="text-gray-500 underline">← Volver al Panel</a>
    </div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>