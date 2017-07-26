<?php

namespace Inetstudio\Hashtags\Transformers;

use Emojione\Emojione as Emoji;
use League\Fractal\TransformerAbstract;
use InetStudio\Hashtags\Models\TagModel;
use InetStudio\Hashtags\Models\PostModel;

class PostTransformer extends TransformerAbstract
{

    private $status;

    public function __construct($status)
    {
        $this->status = $status;
    }

    public function transform(PostModel $post)
    {
        $status = $this->status;

        $postData = \Cache::remember('posts_'.$post->id.'_'.$post->status_id, 1440, function() use ($post, $status) {
            $caption = $post->social->caption;

            if ($status->check) {
                preg_match_all("/(#[а-яА-Яa-zA-Z0-9]+)/u", $caption, $matches);
                foreach ($matches[0] as $hashtag) {
                    $query = substr($hashtag, 1);
                    $tag = TagModel::select(['id', 'name'])->where('name', $query)->first();

                    if (!empty($tag)) {
                        $caption = str_replace($hashtag, '<a class="submit-post" href="#" data-id="' . $post->id . '" data-tag-id="' . $tag->id . '" data-tag-name="' . $tag->name . '" data-toggle="modal" data-target="#submit">' . $hashtag . '</a>', $caption);
                    }
                }
            }

            return [
                'id' => $post->id,
                'trashed' => $post->trashed(),
                'hash' => $post->hash,
                'status' => $post->status->alias,
                'network' => $post->social->social_name,
                'media' => [
                    'type' => $post->social->type,
                    'preview' => asset($post->social->getFirstMedia('images')->getUrl('admin_index_thumb')),
                    'source' => ($post->social->type == 'video') ? asset($post->social->getFirstMediaUrl('videos')) : asset($post->social->getFirstMediaUrl('images')),
                    'caption' => Emoji::shortnameToUnicode($caption),
                ],
                'user' => [
                    'name' => $post->social->user->user_nickname,
                    'link' => $post->social->user->user_url,
                ],
                'post' => [
                    'link' => $post->social->post_url,
                ],
            ];
        });

        return [
            'id' => $postData['id'],
            'media' => view('admin.module.hashtags::pages.posts.datatables.media', ['item' => $postData])->render(),
            'info' => view('admin.module.hashtags::pages.posts.datatables.info', ['item' => $postData])->render(),
            'submit' => (! $status->main) ? view('admin.module.hashtags::pages.posts.datatables.submit', ['item' => $postData])->render() : '',
            'statuses' => view('admin.module.hashtags::pages.posts.datatables.statuses', ['item' => $postData])->render(),
            'actions' => view('admin.module.hashtags::pages.posts.datatables.actions', ['item' => $postData])->render(),
        ];
    }
}
