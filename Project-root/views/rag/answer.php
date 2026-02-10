<?php include __DIR__ . '/../layout/header.php'; ?>

<div class="max-w-4xl mx-auto mt-10 p-6">
	<h1 class="text-2xl font-bold text-gray-800 mb-4">Resultados de busqueda</h1>

	<div class="mb-6">
		<a href="index.php?action=rag_ask" class="text-blue-600 hover:underline">Nueva busqueda</a>
	</div>

	<?php if (empty($results)): ?>
		<div class="bg-yellow-50 border border-yellow-200 text-yellow-800 p-4 rounded">
			No se encontraron posts para tu consulta.
		</div>
	<?php else: ?>
		<div class="space-y-4">
			<?php foreach ($results as $item): ?>
				<article class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
					<h2 class="text-xl font-semibold text-gray-800 mb-2">
						<?= htmlspecialchars($item['title']) ?>
					</h2>
					<p class="text-gray-600 mb-3">
						<?= nl2br(htmlspecialchars(substr($item['content'], 0, 180))) ?>...
					</p>
					<a href="index.php?action=show_post&id=<?= $item['id'] ?>" class="text-blue-600 hover:underline">
						Ver post
					</a>
				</article>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>
