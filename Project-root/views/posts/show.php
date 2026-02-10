<?php include __DIR__ . '/../layout/header.php'; ?>

<div class="max-w-4xl mx-auto mt-10 px-4">
    <article class="bg-white rounded-lg shadow-md p-8">
        <h1 class="text-4xl font-bold text-gray-800 mb-4" style="word-wrap: break-word; word-break: break-word; overflow-wrap: break-word;"><?php echo htmlspecialchars($post['title']); ?></h1>

        <div class="text-sm text-gray-500 mb-6">
            <span>Publicado el: <?= date('d/m/Y H:i', strtotime($post['created_at'])) ?></span>
            <span class="mx-2">|</span>
            <span>Noticia #<?php echo $post['id']; ?></span>
        </div>

        <?php if (!empty($post['image'])): ?>
            <div class="mb-6">
                <img src="uploads/<?= htmlspecialchars($post['image']) ?>" alt="<?= htmlspecialchars($post['title']) ?>" class="w-full rounded-lg">
            </div>
        <?php endif; ?>

        <div class="text-gray-700 leading-relaxed text-lg" style="word-wrap: break-word; word-break: break-word; overflow-wrap: break-word;">
            <?php echo nl2br(htmlspecialchars($post['content'])); ?>
        </div>

        <div class="mt-8 pt-6 border-t">
            <a href="index.php?action=posts" class="text-blue-600 hover:underline font-semibold">
                ← Volver al listado
            </a>
        </div>
    </article>

    <!-- Posts Relacionados -->
    <?php if (!empty($relatedPosts)): ?>
    <div class="bg-white rounded-lg shadow-md p-8 mt-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">📰 Noticias Relacionadas</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <?php foreach ($relatedPosts as $related): ?>
                <a href="index.php?action=show_post&id=<?= (int)$related['id'] ?>" class="block bg-gray-50 rounded-lg p-4 hover:bg-blue-50 transition border border-gray-200 hover:border-blue-300">
                    <?php if (!empty($related['image'])): ?>
                        <img src="uploads/<?= htmlspecialchars($related['image']) ?>" alt="<?= htmlspecialchars($related['title']) ?>" class="w-full h-32 object-cover rounded mb-3">
                    <?php endif; ?>
                    <h3 class="font-semibold text-gray-800 mb-1 line-clamp-2"><?= htmlspecialchars($related['title']) ?></h3>
                    <p class="text-sm text-gray-500"><?= date('d/m/Y', strtotime($related['created_at'])) ?></p>
                    <p class="text-sm text-gray-600 mt-1 line-clamp-2"><?= htmlspecialchars(mb_strimwidth(strip_tags($related['content']), 0, 100, '...')) ?></p>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Sección de Comentarios -->
    <div class="bg-white rounded-lg shadow-md p-8 mt-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Comentarios (<?= count($comments) ?>)</h2>

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

        <!-- Formulario para agregar comentario -->
        <?php if (isset($_SESSION['user_id'])): ?>
            <form action="index.php?action=add_comment" method="POST" enctype="multipart/form-data" class="mb-8">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                <div class="mb-4">
                    <label for="content" class="block text-gray-700 font-semibold mb-2">Agregar un comentario:</label>
                    <textarea name="content" id="content" rows="4" 
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                              placeholder="Escribe tu comentario aquí..." required></textarea>
                </div>
                <div class="mb-4">
                    <label for="comment_image" class="block text-gray-700 font-semibold mb-2">Imagen (opcional):</label>
                    <input type="file" name="image" id="comment_image" accept="image/*" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                           onchange="previewCommentImage(event)">
                    <div id="commentImagePreview" class="mt-2 hidden">
                        <img id="commentPreview" src="" alt="Preview" class="max-w-xs rounded shadow-md">
                    </div>
                </div>
                <button type="submit" 
                        class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                    Publicar comentario
                </button>
            </form>
        <?php else: ?>
            <div class="bg-blue-50 border-l-4 border-blue-500 p-6 rounded mb-6">
                <div class="flex items-start">
                    <svg class="w-6 h-6 text-blue-500 mr-3 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <p class="font-semibold text-gray-800 mb-2">¿Quieres comentar en este post?</p>
                        <p class="text-gray-600 mb-3">Debes iniciar sesión para poder dejar comentarios e imágenes.</p>
                        <div class="flex gap-3">
                            <a href="index.php?action=login" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 inline-block font-semibold">
                                Iniciar sesión
                            </a>
                            <a href="index.php?action=register" class="bg-gray-200 text-gray-700 px-4 py-2 rounded hover:bg-gray-300 inline-block font-semibold">
                                Registrarse
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Lista de comentarios -->
        <div class="space-y-4">
            <?php if (empty($comments)): ?>
                <p class="text-gray-500 italic">Aún no hay comentarios. ¡Sé el primero en comentar!</p>
            <?php else: ?>
                <?php foreach ($comments as $comment): ?>
                    <div class="border-l-4 border-blue-500 bg-gray-50 p-4 rounded">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold mr-3">
                                    <?= strtoupper(substr($comment['username'], 0, 1)) ?>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-800"><?= htmlspecialchars($comment['username']) ?></p>
                                    <p class="text-sm text-gray-500"><?= date('d/m/Y H:i', strtotime($comment['created_at'])) ?></p>
                                </div>
                            </div>
                        </div>
                        <p class="text-gray-700 ml-13 mb-3"><?= nl2br(htmlspecialchars($comment['content'])) ?></p>
                        <?php if (!empty($comment['image'])): ?>
                            <div class="ml-13 mt-3">
                                <img src="uploads/<?= htmlspecialchars($comment['image']) ?>" 
                                     alt="Imagen del comentario" 
                                     class="max-w-md rounded shadow-md cursor-pointer hover:opacity-90 transition"
                                     onclick="window.open('uploads/<?= htmlspecialchars($comment['image']) ?>', '_blank')">
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function previewCommentImage(event) {
    const preview = document.getElementById('commentPreview');
    const previewDiv = document.getElementById('commentImagePreview');
    const file = event.target.files[0];
    
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            previewDiv.classList.remove('hidden');
        }
        reader.readAsDataURL(file);
    } else {
        previewDiv.classList.add('hidden');
    }
}
</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>