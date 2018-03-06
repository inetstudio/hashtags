<?php

namespace InetStudio\Hashtags\Services\Back\Posts;

use Ramsey\Uuid\Uuid;
use InetStudio\Hashtags\Models\TagModel;
use InetStudio\Hashtags\Models\PostModel;
use InetStudio\Hashtags\Models\PointModel;
use InetStudio\Hashtags\Models\StatusModel;
use Spatie\MediaLibrary\Exceptions\FileCannotBeAdded\UnreachableUrl;
use InetStudio\Hashtags\Contracts\Services\Back\Posts\ContestPostsServiceContract;

/**
 * Class ContestPostsService.
 */
class ContestPostsService implements ContestPostsServiceContract
{
    private $services = [];

    /**
     * ContestPostsService constructor.
     */
    public function __construct()
    {
        $this->services['InstagramPosts'] = app()->make('InetStudio\Instagram\Contracts\Services\Back\InstagramPostsServiceContract');
        $this->services['InstagramUsers'] = app()->make('InetStudio\Instagram\Contracts\Services\Back\InstagramUsersServiceContract');
        $this->services['VkontaktePosts'] = app()->make('InetStudio\Vkontakte\Contracts\Services\Back\VkontaktePostsServiceContract');
        $this->services['VkontakteUsers'] = app()->make('InetStudio\Vkontakte\Contracts\Services\Back\VkontakteUsersServiceContract');
    }

    /**
     * Создаем пост на основе поста Instagram.
     *
     * @param $post
     *
     * @return PostModel|null
     */
    public function createPostFromInstagram($post): ?PostModel
    {
        if (! $post) {
            return null;
        }

        $igPost = $this->services['InstagramPosts']->createPost($post['id']);
        if (! isset($igPost)) {
            return null;
        }

        $igUser = $this->services['InstagramUsers']->createUser($post['user']['pk']);
        if (! isset($igUser)) {
            return null;
        }

        try {
            if (! isset($post['image_versions2']['candidates'][0]['url'])) {
                return null;
            }
            $igPost->addMediaFromUrl($post['image_versions2']['candidates'][0]['url'])->toMediaCollection('images', 'instagram_posts');
        } catch (UnreachableUrl $error) {
            return null;
        }

        try {
            if (! isset($post['user']['profile_pic_url'])) {
                return null;
            }
            $igUser->addMediaFromUrl($post['user']['profile_pic_url'])->toMediaCollection('images', 'instagram_users');
        } catch (UnreachableUrl $error) {
            return null;
        }

        if ($post['media_type'] == 2) {
            try {
                if (! isset($post['video_versions'][0]['url'])) {
                    return null;
                }
                $igPost->addMediaFromUrl($post['video_versions'][0]['url'])->toMediaCollection('videos', 'instagram_posts');
            } catch (UnreachableUrl $error) {
                return null;
            }
        }

        return $this->createPost($igPost);
    }

    /**
     * Создаем пост на основе поста Vkontakte.
     *
     * @param $post
     *
     * @return PostModel|null
     */
    public function createPostFromVkontakte($post): ?PostModel
    {
        if (starts_with($post['from_id'], '-')) {
            return null;
        }

        $vkPost = $this->services['VkontaktePosts']->createPost($post['from_id'].'_'.$post['id']);
        if (! isset($vkPost)) {
            return null;
        }

        $vkUser = $this->services['VkontakteUsers']->createUser($post['from_id']);
        if (! isset($vkUser)) {
            return null;
        }

        if (isset($post['attachments'][0])) {
            $url = $this->getVKPostPhotoAttachmentURL($post['attachments'][0]);

            if ($url) {
                try {
                    $vkPost->addMediaFromUrl($url)->toMediaCollection('images', 'vkontakte_posts');
                } catch (UnreachableUrl $error) {
                    return null;
                }
            }
        }

        $url = $this->getVKUserPhotoURL($vkUser);

        if ($url) {
            try {
                $vkUser->addMediaFromUrl($url)->toMediaCollection('images', 'vkontakte_users');
            } catch (UnreachableUrl $error) {
                return null;
            }
        }

        return $this->createPost($vkPost);
    }

    /**
     * Создаем пост на основе модели социального поста.
     *
     * @param $socialPost
     *
     * @return PostModel
     */
    private function createPost($socialPost): PostModel
    {
        $uuid = Uuid::uuid4();

        $defaultStatus = StatusModel::whereHas('classifiers', function ($classifiersQuery) {
            $classifiersQuery->where('classifiers.alias', 'default');
        })->first();

        if (! $defaultStatus) {
            $defaultStatus = StatusModel::orderBy('id', 'asc')->first();
        }

        return PostModel::create([
            'hash' => $uuid->toString(),
            'status_id' => isset($defaultStatus->id) ? $defaultStatus->id : 0,
            'social_id' => $socialPost->id,
            'social_type' => get_class($socialPost),
        ]);
    }

    /**
     * Ищем в посте изображение максимального размера.
     *
     * @param $attachment
     *
     * @return string|null
     */
    private function getVKPostPhotoAttachmentURL($attachment): ?string
    {
        $fields = [
            'photo' => [
                'photo_2560',
                'photo_1280',
                'photo_807',
                'photo_604',
                'photo_130',
                'photo_75',
            ],
            'video' => [
                'photo_800',
                'photo_640',
                'photo_320',
                'photo_130',
            ],
            'link' => [
                'photo_604',
                'photo_130',
                'photo_75',
            ],
        ];

        $attachmentType = $attachment['type'];

        foreach ($fields[$attachmentType] as $field) {
            switch ($attachmentType) {
                case 'link':
                    if (isset($attachment[$attachmentType]['photo'][$field])) {
                        return $attachment[$attachmentType]['photo'][$field];
                    }
                    break;
                case 'photo':
                    if (isset($attachment[$attachmentType][$field])) {
                        return $attachment[$attachmentType][$field];
                    }
                    break;
            }
        }

        return null;
    }

    /**
     * Ишем фото пользователя максимального размера.
     *
     * @param $user
     *
     * @return string|null
     */
    private function getVKUserPhotoURL($user): ?string
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

        return null;
    }

    /**
     * Переводим пост в нужный статус.
     *
     * @param $request
     * @param PostModel $item
     * @param StatusModel $status
     *
     * @return PostModel
     *
     * @throws \Exception
     */
    public function moveToStatus($request, PostModel $item, StatusModel $status): PostModel
    {
        if ($item->status_id == $status->id) {
            return $item;
        }

        event(app()->makeWith('InetStudio\Hashtags\Events\Posts\ModifyPostEvent', ['object' => $item]));

        if ($status->classifiers->contains('alias', 'main')) {
            if ($request->filled('tag_data')) {
                $tagData = json_decode($request->get('tag_data'), true);

                $tag = TagModel::find($tagData['id']);
            }

            if ($request->filled('points_id')) {
                $points = PointModel::find(trim($request->get('points_id')));

                if ($points && isset($tag)) {
                    $tag->points()->attach($points, ['post_id' => $item->id]);
                    $item->tags()->attach($tag, ['point_id' => $points->id]);
                    $item->points()->attach($points, ['tag_id' => $tag->id]);
                } elseif ($points) {
                    $item->points()->attach($points, ['tag_id' => 0]);
                }
            } elseif (isset($tag)) {
                $item->tags()->attach($tag);
            }
        } else {
            foreach ($item->tags as $tag) {
                $tag->points()->wherePivot('post_id', $item->id)->detach();
                $item->points()->wherePivot('tag_id', $tag->id)->detach();
            }
            $item->tags()->detach();

            if ($status->classifiers->contains('alias', 'block')) {
                $userSocialPosts = $item->social->user->posts;
                $userPosts = PostModel::whereIn('social_id', $userSocialPosts->pluck('id')->toArray())->where('social_type', get_class($userSocialPosts->first()))->get();

                foreach ($userPosts as $userPost) {
                    foreach ($userPost->tags as $tag) {
                        $tag->points()->wherePivot('post_id', $userPost->id)->detach();
                        $item->points()->wherePivot('tag_id', $tag->id)->detach();
                    }
                    $userPost->tags()->detach();
                    $userPost->update([
                        'status_id' => $status->id,
                    ]);
                }
            }
        }

        $item->update([
            'status_id' => $status->id,
        ]);

        if ($item->trashed() && ! $status->classifiers->contains('alias', 'delete')) {
            $item->restore();
        } elseif ($status->classifiers->contains('alias', 'delete')) {
            $item->delete();
        }

        event(app()->makeWith('InetStudio\Hashtags\Events\Posts\ModifyPostEvent', ['object' => $item]));

        return $item;
    }
}
