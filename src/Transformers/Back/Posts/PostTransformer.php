<?php

namespace InetStudio\Hashtags\Transformers\Back\Posts;

use Emojione\Emojione as Emoji;
use Illuminate\Support\Facades\Cache;
use League\Fractal\TransformerAbstract;
use InetStudio\Hashtags\Models\TagModel;
use InetStudio\Hashtags\Models\PostModel;
use League\Fractal\Resource\Collection as FractalCollection;
use InetStudio\Hashtags\Contracts\Transformers\Back\Posts\PostTransformerContract;

/**
 * Class PostTransformer.
 */
class PostTransformer extends TransformerAbstract implements PostTransformerContract
{
    /**
     * Подготовка данных для отображения в таблице.
     *
     * @param PostModel $post
     *
     * @return array
     *
     * @throws \Throwable
     */
    public function transform(PostModel $post): array
    {
        $cacheKey = 'posts_'.md5($post->id.'_'.$post->status_id);

        $postData = Cache::remember($cacheKey, 1440, function () use ($post) {
            $caption = $post->social->caption;
            $caption = Emoji::shortnameToUnicode($caption);

            if (! $post->status->classifiers->contains('alias', 'main')) {
                preg_match_all('/(#[а-яА-Яa-zA-Z0-9]+)/u', $caption, $matches);
                foreach ($matches[0] as $hashtag) {
                    $query = substr($hashtag, 1);
                    $tag = TagModel::select(['id', 'name'])->where('name', $query)->first();

                    if ($tag) {
                        $tagData = [
                            'id' => $tag->id,
                            'type' => get_class(new TagModel()),
                            'title' => $tag->name,
                        ];

                        $caption = str_replace($hashtag, '<a class="submit-post" href="#" data-id="'.$post->id.'" data-tag=\''.json_encode($tagData).'\' data-toggle="modal" data-target="#submit">'.$hashtag.'</a>', $caption);
                    }
                }
            }

            $images = ($post->social->hasMedia('images')) ? $post->social->getFirstMedia('images') : null;
            $videos = ($post->social->hasMedia('videos')) ? $post->social->getFirstMedia('videos') : null;

            return [
                'id' => $post->id,
                'trashed' => $post->trashed(),
                'hash' => $post->hash,
                'status' => $post->status->alias,
                'network' => $post->social->social_name,
                'media' => [
                    'type' => $post->social->type,
                    'preview' => ($images) ? asset($images->getUrl('preview_admin_index')) : '',
                    'source' => (! ($images || $videos)) ? '' : (($post->social->type == 'video') ? asset($post->social->getFirstMediaUrl('videos')) : asset($post->social->getFirstMediaUrl('images'))),
                    'caption' => $caption,
                    'placeholder' => 'holder.js/320x320?auto=yes&font=FontAwesome&text=&#xf1c5;',
                ],
                'user' => [
                    'name' => $post->social->user->user_nickname,
                    'link' => $post->social->user->user_url,
                ],
                'post' => [
                    'link' => $post->social->post_url,
                    'created_at' => $post->social->post_time,
                ],
                'prizes' => $post->prizes,
            ];
        });

        return [
            'id' => $postData['id'],
            'media' => view('admin.module.hashtags::back.partials.datatables.posts.media', [
                'item' => $postData,
            ])->render(),
            'info' => view('admin.module.hashtags::back.partials.datatables.posts.info', [
                'item' => $postData,
            ])->render(),
            'date' => $postData['post']['created_at']->format('d.m.Y H:i'),
            'orderDate' => (string) $postData['post']['created_at'],
            'prizes' => view('admin.module.hashtags::back.partials.datatables.posts.prizes', [
                'item' => $postData,
            ])->render(),
            'submit' => (! $post->status->classifiers->contains('alias', 'main')) ? view('admin.module.hashtags::back.partials.datatables.posts.submit', [
                'item' => $postData,
            ])->render() : '',
            'statuses' => view('admin.module.hashtags::back.partials.datatables.posts.statuses', [
                'item' => $postData,
            ])->render(),
            'actions' => view('admin.module.hashtags::back.partials.datatables.posts.actions', [
                'item' => $postData,
            ])->render(),
        ];
    }

    /**
     * Обработка коллекции статей.
     *
     * @param $items
     *
     * @return FractalCollection
     */
    public function transformCollection($items): FractalCollection
    {
        return new FractalCollection($items, $this);
    }
}
