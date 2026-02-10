<?php include __DIR__ . '/../layout/header.php'; ?>

<div class="max-w-4xl mx-auto mt-10 px-4">
    <h1 class="text-4xl font-bold text-center text-gray-800 mb-10">Últimas Noticias</h1>

    <div class="grid grid-cols-1 gap-8">
        <?php foreach ($posts as $post): ?>
            <article class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-200">
                
                <?php if (!empty($post['image'])): ?>
                    <div class="h-64 overflow-hidden">
                        <img src="uploads/<?= htmlspecialchars($post['image']) ?>" alt="Imagen de la noticia" class="w-full h-full object-cover">
                    </div>
                <?php endif; ?>

                <div class="p-6">
                    <h2 class="text-2xl font-bold text-gray-800 mb-3" style="word-wrap: break-word; word-break: break-word; overflow-wrap: break-word;">
                        <?= htmlspecialchars($post['title']) ?>
                    </h2>
                    
                    <p class="text-gray-600 leading-relaxed mb-4" style="word-wrap: break-word; word-break: break-word; overflow-wrap: break-word;">
                        <?= nl2br(htmlspecialchars(substr($post['content'], 0, 150))) ?>...
                    </p>

                    <div class="flex items-center justify-between mt-4">
                        <span class="text-sm text-gray-500">
                            Publicado el: <?= date('d/m/Y', strtotime($post['created_at'])) ?>
                        </span>
                        <a href="index.php?action=show_post&id=<?= $post['id'] ?>" class="text-blue-600 hover:underline font-semibold">Leer más →</a>
                    </div>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>