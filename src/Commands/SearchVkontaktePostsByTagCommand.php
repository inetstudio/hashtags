<?php

namespace InetStudio\Hashtags\Commands;

use Ramsey\Uuid\Uuid;
use Illuminate\Console\Command;
use InetStudio\Vkontakte\Models\VkontaktePostModel;
use InetStudio\Hashtags\Models\PostModel;
use InetStudio\Hashtags\Models\StatusModel;
use Spatie\MediaLibrary\Exceptions\FileCannotBeAdded\UnreachableUrl;

class SearchVkontaktePostsByTagCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hashtags:vkontakte';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Search vkontakte posts by tag';

    private $postTypes = [];

    /**
     * Create a new command instance.
     *
     * SearchVkontaktePostsByTagCommand constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $configTypes = config('hashtags.types');

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

        dd($userIds);

        $contestVKPostIds = VkontaktePostModel::select('post_id')->pluck('post_id')->toArray();

        foreach ($tags as $tagArr) {
            $postsArr = \VkontaktePost::getPostsByTag($tagArr, $startTime, $endTime, $contestVKPostIds, $this->postTypes);

            foreach ($postsArr as $post) {
                if (isset($post['id'])) {
                    if (! in_array(VkontaktePostModel::NETWORK.'_'.$post['from_id'], $userIds)) {
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
        if (starts_with($post['from_id'], '-')) {
            return;
        }

        $vkPost = \VkontaktePost::createPost($post['from_id'].'_'.$post['id']);
        if (! isset($vkPost)) {
            return;
        }

        $vkUser = \VkontakteUser::createUser($post['from_id']);
        if (! isset($vkUser)) {
            return;
        }

        try {
            $url = $this->getPostPhotoAttachmentURL($post['attachments'][0]);
            if (! isset($url)) {
                return;
            }
            $vkPost->addMediaFromUrl($url)->toMediaCollection('images', 'vkontakte_posts');
        } catch (UnreachableUrl $error) {
            return;
        }

        try {
            $url = $this->getUserPhotoURL($vkUser);
            if (! isset($url)) {
                return;
            }
            $vkUser->addMediaFromUrl($url)->toMediaCollection('images', 'vkontakte_users');
        } catch (UnreachableUrl $error) {
            return;
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
            'social_id' => $vkPost->id,
            'social_type' => get_class($vkPost),
        ]);
    }

    /**
     * Ищем в посте изображение максимального размера.
     *
     * @param $attachment
     * @return mixed
     */
    private function getPostPhotoAttachmentURL($attachment)
    {
        $fields = [
            'photo' => [
                'src_xbig',
                'src_big',
                'src_small',
                'src',
            ],
            'link' => [
                'image_big',
                'image_src',
            ],
        ];

        $attachmentType = $attachment['type'];

        foreach ($fields[$attachmentType] as $field) {
            if (isset($attachment[$attachmentType][$field])) {
                return $attachment[$attachmentType][$field];
            }
        }
    }

    /**
     * Ишем фото пользователя максимального размера.
     *
     * @param $user
     * @return mixed
     */
    private function getUserPhotoURL($user)
    {
        $fields = [
            'photo_max_orig',
            'photo_max',
            'photo_400_orig',
            'photo_200_orig',
            'photo_200',
            'photo_100',
        ];

        foreach ($fields as $field) {
            if (isset($user->$field)) {
                return $user->$field;
            }
        }
    }
}
