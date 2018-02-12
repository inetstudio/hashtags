<?php

namespace InetStudio\Hashtags\Http\Controllers\Back\Stages;

use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;
use InetStudio\Hashtags\Models\StageModel;
use InetStudio\Hashtags\Transformers\Back\StageTransformer;

/**
 * Class StagesDataController
 * @package InetStudio\Hashtags\Http\Controllers\Back\Stages
 */
class StagesDataController extends Controller
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
        $items = StageModel::query();

        return DataTables::of($items)
            ->setTransformer(new StageTransformer)
            ->rawColumns(['actions'])
            ->make();
    }
}
