<?php

return [

    /*
     * Настройки таблиц
     */

    'datatables' => [
        'ajax' => [
            'points' => [
                'url' => 'back.hashtags.points.data',
                'type' => 'POST',
            ],
            'posts' => [
                'url' => 'back.hashtags.posts.data',
                'type' => 'POST',
                'data' => 'function(data) {  
                    data.status_id = $("#currentStatus").val();
                }',
            ],
            'prizes' => [
                'url' => 'back.hashtags.prizes.data',
                'type' => 'POST',
            ],
            'stages' => [
                'url' => 'back.hashtags.stages.data',
                'type' => 'POST',
            ],
            'statuses' => [
                'url' => 'back.hashtags.statuses.data',
                'type' => 'POST',
            ],
            'tags' => [
                'url' => 'back.hashtags.tags.data',
                'type' => 'POST',
            ],
        ],
        'table' => [
            'default' => [
                'paging' => true,
                'pagingType' => 'full_numbers',
                'searching' => true,
                'info' => false,
                'searchDelay' => 350,
                'language' => [
                    'url' => '/admin/js/plugins/datatables/locales/russian.json',
                ],
            ],
        ],
        'columns' => [
            'points' => [
                ['data' => 'name', 'name' => 'name', 'title' => 'Название'],
                ['data' => 'alias', 'name' => 'alias', 'title' => 'Алиас'],
                ['data' => 'numeric', 'name' => 'numeric', 'title' => 'Количество баллов'],
                ['data' => 'created_at', 'name' => 'created_at', 'title' => 'Дата создания'],
                ['data' => 'updated_at', 'name' => 'updated_at', 'title' => 'Дата обновления'],
                ['data' => 'actions', 'name' => 'actions', 'title' => 'Действия', 'orderable' => false, 'searchable' => false],
            ],
            'posts' => [
                ['data' => 'id', 'name' => 'id', 'title' => 'ID', 'orderable' => true],
                ['data' => 'media', 'name' => 'media', 'title' => 'Медиа', 'orderable' => false, 'searchable' => false],
                ['data' => 'info', 'name' => 'info', 'title' => 'Инфо', 'orderable' => false, 'searchable' => true],
                ['data' => 'date', 'name' => 'date', 'title' => 'Дата создания', 'orderable' => true, 'searchable' => true, 'orderData' => 4],
                ['data' => 'orderDate', 'name' => 'orderDate', 'title' => 'Дата создания (сортировка)', 'orderable' => true, 'visible' => false],
                ['data' => 'prizes', 'name' => 'prizes', 'title' => 'Призы', 'orderable' => false, 'searchable' => true],
                ['data' => 'submit', 'name' => 'submit', 'title' => 'Подтверждение', 'orderable' => false, 'searchable' => false],
                ['data' => 'statuses', 'name' => 'statuses', 'title' => 'Модерация', 'orderable' => false, 'searchable' => false],
                ['data' => 'actions', 'name' => 'actions', 'title' => 'Действия', 'orderable' => false, 'searchable' => false],
            ],
            'prizes' => [
                ['data' => 'name', 'name' => 'name', 'title' => 'Название'],
                ['data' => 'alias', 'name' => 'alias', 'title' => 'Алиас'],
                ['data' => 'created_at', 'name' => 'created_at', 'title' => 'Дата создания'],
                ['data' => 'updated_at', 'name' => 'updated_at', 'title' => 'Дата обновления'],
                ['data' => 'actions', 'name' => 'actions', 'title' => 'Действия', 'orderable' => false, 'searchable' => false],
            ],
            'stages' => [
                ['data' => 'name', 'name' => 'name', 'title' => 'Название'],
                ['data' => 'alias', 'name' => 'alias', 'title' => 'Алиас'],
                ['data' => 'created_at', 'name' => 'created_at', 'title' => 'Дата создания'],
                ['data' => 'updated_at', 'name' => 'updated_at', 'title' => 'Дата обновления'],
                ['data' => 'actions', 'name' => 'actions', 'title' => 'Действия', 'orderable' => false, 'searchable' => false],
            ],
            'statuses' => [
                ['data' => 'name', 'name' => 'name', 'title' => 'Название'],
                ['data' => 'alias', 'name' => 'alias', 'title' => 'Алиас'],
                ['data' => 'created_at', 'name' => 'created_at', 'title' => 'Дата создания'],
                ['data' => 'updated_at', 'name' => 'updated_at', 'title' => 'Дата обновления'],
                ['data' => 'actions', 'name' => 'actions', 'title' => 'Действия', 'orderable' => false, 'searchable' => false],
            ],
            'tags' => [
                ['data' => 'name', 'name' => 'name', 'title' => 'Название'],
                ['data' => 'created_at', 'name' => 'created_at', 'title' => 'Дата создания'],
                ['data' => 'updated_at', 'name' => 'updated_at', 'title' => 'Дата обновления'],
                ['data' => 'actions', 'name' => 'actions', 'title' => 'Действия', 'orderable' => false, 'searchable' => false],
            ],
        ],
    ],

    /*
     * Теги, по которым происходит поиск постов в социальных сетях.
     */

    'tags' => [
        [
            'тег1',
            'тег2',
        ],
    ],

    /*
     * Начало периода, за который происходит поиск работ
     */

    'start' => '01.04.2017 00:00',

    /*
     * Окончание периода, за который происходит поиск работ
     */

    'end' => '01.04.2017 23:59',

    /*
     * Тип постов, которые участвуют в конкурсе (all|photo|video|link)
     */

    'types' => [
        'all',
    ],

    'gallery_preview_images' => 'front_gallery',
    'winners_preview_images' => 'front_winners',

];
