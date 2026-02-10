<?php include __DIR__ . '/../layout/header.php'; ?>
<div class="max-w-4xl mx-auto mt-10 p-6">
    <h1 class="text-2xl font-bold mb-4">Nueva Noticia</h1>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <form action="index.php?action=admin_store_post" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
        <label class="block mb-2 font-bold">Título</label>
        <input type="text" name="title" class="w-full border p-2 mb-4 rounded" required>

        <label class="block mb-2 font-bold">Estado</label>
        <select name="status" class="w-full border p-2 mb-4 rounded" required>
            <option value="published">Publicado</option>
            <option value="draft">Borrador</option>
        </select>
        
        <label class="block mb-2 font-bold">Imagen</label>
        <input type="file" name="image" id="image" accept="image/*" class="w-full border p-2 mb-2 rounded" onchange="previewImage(event)">
        <div id="imagePreview" class="mb-4 hidden">
            <img id="preview" src="" alt="Preview" class="max-w-md rounded shadow-md">
        </div>
            
        <label class="block mb-2 font-bold">Contenido</label>
        <textarea name="content" class="w-full border p-2 mb-4 rounded" rows="10" required style="white-space: pre-wrap;"></textarea>
        
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Guardar</button>
        <a href="index.php?action=admin_posts" class="ml-2 bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 inline-block">Cancelar</a>
    </form>
</div>

<script>
function previewImage(event) {
    const preview = document.getElementById('preview');
    const previewDiv = document.getElementById('imagePreview');
    const file = event.target.files[0];
    
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            previewDiv.classList.remove('hidden');
        }
        reader.readAsDataURL(file);
    }
}
</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>