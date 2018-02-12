<?php

namespace InetStudio\Hashtags\Console\Commands;

use Illuminate\Console\Command;
use InetStudio\Hashtags\Models\PostModel;
use InetStudio\Hashtags\Models\StatusModel;
use InetStudio\Instagram\Models\InstagramPostModel;
use InetStudio\Hashtags\Contracts\Services\Back\ContestPostsServiceContract;
use InetStudio\Instagram\Contracts\Services\Back\InstagramPostsServiceContract;
use InetStudio\Instagram\Contracts\Services\Back\InstagramUsersServiceContract;

class SearchInstagramPostsByTagCommand extends Command
{
    /**
     * Имя команды.
     *
     * @var string
     */
    protected $signature = 'inetstudio:hashtags:instagram';

    /**
     * Описание команды.
     *
     * @var string
     */
    protected $description = 'Search instagram posts by tag';

    private $postTypes = [];

    private $services = [];

    /**
     * SearchInstagramPostsByTagCommand constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $configTypes = config('hashtags.types');

        if ($configTypes) {
            foreach ($configTypes as $configType) {
                switch ($configType) {
                    case 'all':
                        $this->postTypes = [1, 2];
                        break;
                    case 'photo':
                        $this->postTypes[] = 1;
                        break;
                    case 'video':
                        $this->postTypes[] = 2;
                        break;
                }
            }
        }

        $this->services['InstagramPosts'] = app()->make(InstagramPostsServiceContract::class);
        $this->services['InstagramUsers'] = app()->make(InstagramUsersServiceContract::class);
        $this->services['ContestPosts'] = app()->make(ContestPostsServiceContract::class);
    }

    /**
     * Запуск команды.
     *
     * @return void
     */
    public function handle(): void
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

        $contestIGPostIds = InstagramPostModel::select('pk')->pluck('pk')->toArray();

        foreach ($tags as $tagArr) {
            $postsArr = $this->services['InstagramPosts']->getPostsByTag($tagArr, $startTime, $endTime, $contestIGPostIds, $this->postTypes);

            foreach ($postsArr as $post) {
                if (isset($post['id'])) {
                    if (! in_array(InstagramPostModel::NETWORK.'_'.$post['user']['pk'], $userIds)) {
                        $this->services['ContestPosts']->createPostFromInstagram($post);
                    }
                }
            }
        }
    }
}
