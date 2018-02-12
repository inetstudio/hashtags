<?php

namespace InetStudio\Hashtags\Http\Controllers\Back\Posts;

use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;
use InetStudio\Hashtags\Models\PostModel;
use InetStudio\Hashtags\Models\StatusModel;
use InetStudio\Hashtags\Transformers\Back\PostTransformer;

/**
 * Class PostsDataController
 * @package InetStudio\Hashtags\Http\Controllers\Back\Posts
 */
class PostsDataController extends Controller
{
    /**
     * DataTables ServerSide.
     *
     * @param Request $request
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function data(Request $request)
    {
        $statusId = $request->get('status_id');
        $status = StatusModel::find($statusId);

        if (! $status) {
            abort(404);
        }

        $items = PostModel::with([
                'status' => function ($statusQuery) {
                    $statusQuery->select(['id', 'alias']);
                },
                'social' => function ($socialQuery) {
                    $socialQuery->with([
                        'media' => function ($mediaQuery) {
                            $mediaQuery->select(['id', 'model_id', 'model_type', 'collection_name', 'file_name', 'disk']);
                        },
                        'user',
                    ])
                        ->select([
                            'id', 'type',
                        ]);
                },
            ])
            ->select(['id', 'hash', 'social_id', 'social_type', 'status_id'])
            ->where('status_id', $statusId)
            ->withTrashed();

        if ($request->filled('search.value')) {
            $items = $items->get();
            $search = $request->input('search.value');
        }

        $datatables = DataTables::of($items)
            ->setTransformer(new PostTransformer())
            ->rawColumns(['media', 'info', 'submit', 'statuses', 'actions']);

        if (isset($search)) {
            $datatables->filter(function ($engine) use ($search) {
                $collection = $engine->collection;

                $engine->collection = $collection->filter(function ($item) use ($search) {
                    return strpos($item['info'], $search) !== false;
                });
            });
        }

        return $datatables->make();
    }
}
