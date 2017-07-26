<?php

use Illuminate\Database\Seeder;

class StatusesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('hashtags_statuses')->insert([
            'name' => 'Модерация',
            'alias' => 'moderation',
            'description' => 'Посты, ожидающие модерацию',
            'default' => 1,
            'check' => 1,
        ]);

        DB::table('hashtags_statuses')->insert([
            'name' => 'Одобрено',
            'alias' => 'approved',
            'description' => 'Одобренные посты',
            'main' => 1,
        ]);

        DB::table('hashtags_statuses')->insert([
            'name' => 'Отклонено',
            'alias' => 'rejected',
            'description' => 'Отклоненные посты',
        ]);

        DB::table('hashtags_statuses')->insert([
            'name' => 'Заблокировано',
            'alias' => 'blocked',
            'description' => 'Заблокированные посты',
            'block' => 1,
        ]);

        DB::table('hashtags_statuses')->insert([
            'name' => 'Удалено',
            'alias' => 'deleted',
            'description' => 'Удаленные посты',
            'delete' => 1,
        ]);
    }
}
