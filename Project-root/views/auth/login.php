<?php include __DIR__ . '/../layout/header.php'; ?>

<div class="max-w-md mx-auto bg-white p-8 rounded shadow-md mt-10">
    <h2 class="text-2xl font-bold mb-6 text-center text-gray-800">Iniciar Sesión</h2>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            <?= htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($error)): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form action="index.php?action=login" method="POST">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
        <div class="mb-4">
            <label class="block text-gray-700 font-bold mb-2">Email</label>
            <input type="email" name="email" required 
                   class="w-full border p-2 rounded focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>

        <div class="mb-6">
            <label class="block text-gray-700 font-bold mb-2">Contraseña</label>
            <input type="password" name="password" required 
                   class="w-full border p-2 rounded focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>

        <button type="submit" class="w-full bg-blue-500 text-white font-bold py-2 px-4 rounded hover:bg-blue-600">
            Entrar
        </button>
    </form>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>