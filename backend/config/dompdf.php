<?php

return [

    'show_warnings' => false,

    'font_dir' => storage_path('fonts/'), // pasta onde pode armazenar fontes customizadas
    'font_cache' => storage_path('fonts/'),

    'temp_dir' => storage_path('app/dompdf/temp/'), // pasta temporária

    'chroot' => base_path(), // diretório raiz para Dompdf acessar arquivos locais

    'is_html5_parser_enabled' => true,

    'is_remote_enabled' => true,  // ESSENCIAL para carregar imagens via URL

    'default_font' => 'sans-serif',

    'dpi' => 96,

    'enable_php' => false,

    'enable_css_float' => true,

    'enable_javascript' => false,

    'debug_png' => false,

    'debug_keep_temp' => false,

    'debug_css' => false,

    'debug_layout' => false,

    'debug_layout_lines' => false,

    'debug_layout_blocks' => false,

    'debug_layout_inline' => false,

];
