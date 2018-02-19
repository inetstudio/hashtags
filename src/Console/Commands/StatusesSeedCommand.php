<?php

namespace InetStudio\Hashtags\Console\Commands;

use Illuminate\Console\Command;
use InetStudio\Hashtags\Models\StatusModel;
use InetStudio\Classifiers\Models\ClassifierModel;

/**
 * Class StatusesSeedCommand.
 */
class StatusesSeedCommand extends Command
{
    /**
     * Имя команды.
     *
     * @var string
     */
    protected $name = 'inetstudio:hashtags:statuses:seed';

    /**
     * Описание команды.
     *
     * @var string
     */
    protected $description = 'Seed statuses';

    /**
     * Запуск команды.
     *
     * @return void
     */
    public function handle(): void
    {
        $statuses = [
            [
                'name' => 'Модерация',
                'alias' => 'moderation',
                'description' => 'Посты, ожидающие модерацию',
                'types' => [
                    'default' => 'Статус по умолчанию',
                    'check' => 'Проверка',
                ],
            ],
            [
                'name' => 'Одобрено',
                'alias' => 'approved',
                'description' => 'Одобренные посты',
                'types' => [
                    'main' => 'Основной статус',
                ],
            ],
            [
                'name' => 'Отклонено',
                'alias' => 'rejected',
                'description' => 'Отклоненные посты',
            ],
            [
                'name' => 'Заблокировано',
                'alias' => 'blocked',
                'description' => 'Заблокированные посты',
                'types' => [
                    'block' => 'Блокировать',
                ],
            ],
            [
                'name' => 'Удалено',
                'alias' => 'deleted',
                'description' => 'Удаленные посты',
                'types' => [
                    'delete' => 'Удалено',
                ],
            ],
        ];

        foreach ($statuses as $status) {
            $statusObj = StatusModel::updateOrCreate([
                'name' => $status['name'],
                'alias' => $status['alias'],
                'description' => $status['description'],
            ]);

            $classifiers = [];
            if (isset($status['types'])) {
                foreach ($status['types'] as $alias => $value) {
                    $classifier = ClassifierModel::updateOrCreate([
                        'type' => 'Тип статуса',
                        'value' => $value,
                    ], [
                        'alias' => $alias,
                    ]);

                    $classifiers[] = $classifier;
                }
            }

            $statusObj->syncClassifiers($classifiers);
        }

        $this->info('Statuses seeding complete.');
    }
}
