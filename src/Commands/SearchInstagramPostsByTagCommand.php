<?php

namespace InetStudio\Hashtags\Commands;

use Ramsey\Uuid\Uuid;
use Illuminate\Console\Command;
use InetStudio\Instagram\Models\InstagramPostModel;
use InetStudio\Hashtags\Models\PostModel;
use InetStudio\Hashtags\Models\StatusModel;
use Spatie\MediaLibrary\Exceptions\FileCannotBeAdded\UnreachableUrl;

class SearchInstagramPostsByTagCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hashtags:instagram';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Search instagram posts by tag';

    private $postTypes = [];

    /**
     * Create a new command instance.
     *
     * SearchInstagramPostsByTagCommand constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $configTypes = config('hashtags.types');

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

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $startTime = config('hashtags.start');
        $endTime = config('hashtags.end');
        $tags = config('hashtags.tags');

        $blockStatus = StatusModel::where('block', true)->first();

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
            $postsArr = \InstagramPost::getPostsByTag($tagArr, $startTime, $endTime, $contestIGPostIds, $this->postTypes);

            foreach ($postsArr as $post) {
                if (isset($post['id'])) {
                    if (! in_array(InstagramPostModel::NETWORK.'_'.$post['user']['pk'], $userIds)) {
                        $this->createContestPost($post);
                    }
                }
            }
        }
    }

    /**
     * Создаем пост.
     *
     * @param $post
     * @return null
     */
    private function createContestPost($post)
    {
        $igPost = \InstagramPost::createPost($post['id']);
        if (! isset($igPost)) {
            return;
        }

        $igUser = \InstagramUser::createUser($post['user']['pk']);
        if (! isset($igUser)) {
            return;
        }

        try {
            if (! isset($post['image_versions2']['candidates'][0]['url'])) {
                return;
            }
            $igPost->addMediaFromUrl($post['image_versions2']['candidates'][0]['url'])->toMediaCollection('images', 'instagram_posts');
        } catch (UnreachableUrl $error) {
            return;
        }

        try {
            if (! isset($post['user']['profile_pic_url'])) {
                return;
            }
            $igUser->addMediaFromUrl($post['user']['profile_pic_url'])->toMediaCollection('images', 'instagram_users');
        } catch (UnreachableUrl $error) {
            return;
        }

        if ($post['media_type'] == 2) {
            try {
                if (! isset($post['video_versions'][0]['url'])) {
                    return;
                }
                $igPost->addMediaFromUrl($post['video_versions'][0]['url'])->toMediaCollection('videos', 'instagram_posts');
            } catch (UnreachableUrl $error) {
                return;
            }
        }

        $uuid = Uuid::uuid4();

        $statuses = StatusModel::where('default', true)->get();
        if ($statuses->count() != 1) {
            $statuses = StatusModel::orderBy('id', 'asc')->get();
        }

        $status = $statuses->first();

        PostModel::create([
            'hash' => $uuid->toString(),
            'status_id' => isset($status->id) ? $status->id : 0,
            'social_id' => $igPost->id,
            'social_type' => get_class($igPost),
        ]);
    }
}
