<?php include __DIR__ . '/../layout/header.php'; ?>

<div class="max-w-3xl mx-auto mt-10 p-6 bg-white rounded-lg shadow-md">
	<h1 class="text-2xl font-bold text-gray-800 mb-4">Busqueda semantica de posts</h1>
	<p class="text-gray-600 mb-6">Escribe palabras clave para encontrar noticias relacionadas.</p>

	<?php if (isset($_SESSION['error'])): ?>
		<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
			<?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
		</div>
	<?php endif; ?>

	<form action="index.php?action=rag_answer" method="POST">
		<input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
		<label class="block text-gray-700 font-semibold mb-2" for="q">Palabras clave</label>
		<input id="q" name="q" type="text" required
			   class="w-full border p-3 rounded mb-4 focus:outline-none focus:ring-2 focus:ring-blue-500"
			   placeholder="ej: tecnologia, salud, educacion">
		<button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
			Buscar
		</button>
	</form>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>
