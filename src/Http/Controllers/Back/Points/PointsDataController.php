<?php

namespace InetStudio\Hashtags\Http\Controllers\Back\Points;

use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;
use InetStudio\Hashtags\Models\PointModel;
use InetStudio\Hashtags\Transformers\Back\PointTransformer;

/**
 * Class PointsDataController
 * @package InetStudio\Hashtags\Http\Controllers\Back\Points
 */
class PointsDataController extends Controller
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
        $items = PointModel::query();

        return DataTables::of($items)
            ->setTransformer(new PointTransformer)
            ->rawColumns(['name', 'actions'])
            ->make();
    }
}
