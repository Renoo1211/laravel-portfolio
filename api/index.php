<?php

// Vercel: filesystem hanya-baca kecuali /tmp, jadi cache & view Laravel diarahkan ke sana.
foreach (['framework/views', 'framework/cache', 'framework/sessions', 'logs'] as $dir) {
    if (! is_dir("/tmp/storage/$dir")) {
        mkdir("/tmp/storage/$dir", 0755, true);
    }
}

require __DIR__.'/../public/index.php';
