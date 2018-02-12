<?php

namespace InetStudio\Hashtags\Console\Commands;

use Illuminate\Console\Command;
use InetStudio\Hashtags\Models\PostModel;
use InetStudio\Hashtags\Models\StatusModel;
use InetStudio\Vkontakte\Models\VkontaktePostModel;
use InetStudio\Hashtags\Contracts\Services\Back\ContestPostsServiceContract;
use InetStudio\Vkontakte\Contracts\Services\Back\VkontaktePostsServiceContract;
use InetStudio\Vkontakte\Contracts\Services\Back\VkontakteUsersServiceContract;

class SearchVkontaktePostsByTagCommand extends Command
{
    /**
     * Имя команды.
     *
     * @var string
     */
    protected $signature = 'inetstudio:hashtags:vkontakte';

    /**
     * Описание команды.
     *
     * @var string
     */
    protected $description = 'Search vkontakte posts by tag';

    private $postTypes = [];

    private $services = [];

    /**
     * SearchVkontaktePostsByTagCommand constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $configTypes = config('hashtags.types');

        if ($configTypes) {
            foreach ($configTypes as $configType) {
                switch ($configType) {
                    case 'all':
                        $this->postTypes = ['video', 'photo', 'link'];
                        break;
                    default:
                        $this->postTypes[] = $configType;
                        break;
                }
            }
        }

        $this->services['VkontaktePosts'] = app()->make(VkontaktePostsServiceContract::class);
        $this->services['VkontakteUsers'] = app()->make(VkontakteUsersServiceContract::class);
        $this->services['ContestPosts'] = app()->make(ContestPostsServiceContract::class);
    }

    /**
     * Запуск команды.
     *
     * @return void
     */
    public function handle()
    {
        $startTime = config('hashtags.start');
        $endTime = config('hashtags.end');
        $tags = config('hashtags.tags');

        $blockStatus = StatusModel::whereHas('classifiers', function ($classifiersQuery) {
            $classifiersQuery->where('classifiers.alias', 'block');
        })->first();

        if ($blockStatus) {
            $blockedPosts = PostModel::with('social')->where('status_id', $blockStatus->id)->get();
            $userIds = [];

            foreach ($blockedPosts as $blockedPost) {
                $userIds[] = $blockedPost->social->social_name.'_'.$blockedPost->social->user->user_id;
            }

            $userIds = array_unique($userIds);
        } else {
            $userIds = [];
        }

        $contestVKPostIds = VkontaktePostModel::select('post_id')->pluck('post_id')->toArray();

        foreach ($tags as $tagArr) {
            $postsArr = $this->services['VkontaktePosts']->getPostsByTag($tagArr, $startTime, $endTime, $contestVKPostIds, $this->postTypes);

            foreach ($postsArr as $post) {
                if (isset($post['id'])) {
                    if (! in_array(VkontaktePostModel::NETWORK.'_'.$post['from_id'], $userIds)) {
                        $this->services['ContestPosts']->createPostFromVkontakte($post);
                    }
                }
            }
        }
    }
}
