<?php

namespace InetStudio\Hashtags\Http\Controllers\Back\Posts;

use Chumper\Zipper\Facades\Zipper;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use InetStudio\Hashtags\Models\PostModel;
use InetStudio\Hashtags\Models\StatusModel;
use InetStudio\Hashtags\Contracts\Http\Controllers\Back\Posts\PostsUtilityControllerContract;

/**
 * Class PostsUtilityController.
 */
class PostsUtilityController extends Controller implements PostsUtilityControllerContract
{
    /**
     * Скачиваем архив работ.
     *
     * @param $status
     * @param $id
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function download($status, $id = null)
    {
        if (! is_null($status)) {
            $status = StatusModel::where('alias', '=', $status)->first();

            if (! $status) {
                abort(404);
            }
        }

        $select = PostModel::with(['social' => function ($query) {
            $query->with('user');
        }])->where('status_id', '=', $status->id)->withTrashed();

        if (! is_null($id) && $id > 0) {
            $select->where('id', '=', $id);
        }

        //$posts = $select->search(request()->get('search'))->get();
        $posts = $select->get();

        $excelFname = time();
        Excel::create($excelFname, function ($excel) use ($posts) {
            $excel->sheet('Посты', function ($sheet) use ($posts) {
                $sheet->row(1, [
                    'ID', 'Социальная сеть', 'Пользователь', 'Ссылка на профиль', 'Ссылка на пост', 'Ссылка на медиа', 'Имя файла', 'Время поста', 'Время последнего изменения',
                ]);

                foreach ($posts as $index => $post) {
                    $images = ($post->social->hasMedia('images')) ? $post->social->getFirstMedia('images') : null;
                    $videos = ($post->social->hasMedia('videos')) ? $post->social->getFirstMedia('videos') : null;

                    $fileUrl = (! ($images || $videos)) ? '' : (($post->social->type == 'video') ? asset($post->social->getFirstMediaUrl('videos')) : asset($post->social->getFirstMediaUrl('images')));
                    $fileName = (! ($images || $videos)) ? '' : (($post->social->type == 'video') ? $videos->file_name : $images->file_name);

                    $sheet->appendRow([
                        $post->hash, $post->social->social_name, $post->social->user->user_nickname, $post->social->user->user_url, $post->social->post_url, $fileUrl, $fileName, $post->social->post_time, $post->updated_at,
                    ]);
                }
            });
        })->store('xlsx', public_path('storage/hashtags/downloads'));

        $fname = 'storage/hashtags/downloads/'.time().'.zip';
        $zip = Zipper::make($fname);
        $zip->add(public_path('storage/hashtags/downloads/'.$excelFname.'.xlsx'));

        foreach ($posts as $post) {
            $images = ($post->social->hasMedia('images')) ? $post->social->getFirstMedia('images') : null;
            $videos = ($post->social->hasMedia('videos')) ? $post->social->getFirstMedia('videos') : null;

            $filePath = (! ($images || $videos)) ? '' : (($post->social->type == 'video') ? $post->social->getFirstMediaPath('videos') : $post->social->getFirstMediaPath('images'));

            if ($filePath) {
                $zip->folder('posts')->add($filePath);
            }
        }
        $zip->close();

        return response()->download(public_path($fname));
    }
}
