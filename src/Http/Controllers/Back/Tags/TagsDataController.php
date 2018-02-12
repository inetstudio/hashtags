<?php

namespace InetStudio\Hashtags\Http\Controllers\Back\Tags;

use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;
use InetStudio\Hashtags\Models\TagModel;
use InetStudio\Hashtags\Transformers\Back\TagTransformer;

/**
 * Class TagsDataController
 * @package InetStudio\Hashtags\Http\Controllers\Back\Tags
 */
class TagsDataController extends Controller
{
    /**
     * DataTables ServerSide.
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function data()
    {
        $items = TagModel::query();

        return Datatables::of($items)
            ->setTransformer(new TagTransformer)
            ->rawColumns(['actions'])
            ->make();
    }
}
