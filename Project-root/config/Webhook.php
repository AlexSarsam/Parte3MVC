<?php

// Configuracion del webhook para notificaciones externas (n8n, etc.)
define('WEBHOOK_URL', getenv('WEBHOOK_URL') ?: '');
define('WEBHOOK_SECRET', getenv('WEBHOOK_SECRET') ?: '');
define('APP_BASE_URL', getenv('APP_BASE_URL') ?: 'http://localhost:8000');
