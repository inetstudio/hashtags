<?php

return [

    /*
     * Расширение файла конфигурации app/config/filesystems.php
     * добавляет локальный диск для выгрузок.
     */

    'hashtags_downloads' => [
        'driver' => 'local',
        'root' => storage_path('app/public/hashtags/downloads/'),
        'url' => env('APP_URL').'/storage/hashtags/downloads/',
        'visibility' => 'public',
    ],

];
