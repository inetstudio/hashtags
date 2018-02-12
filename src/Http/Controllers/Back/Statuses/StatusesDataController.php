<?php

namespace InetStudio\Hashtags\Http\Controllers\Back\Statuses;

use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;
use InetStudio\Hashtags\Models\StatusModel;
use InetStudio\Hashtags\Transformers\Back\StatusTransformer;

/**
 * Class StatusesDataController
 * @package InetStudio\Hashtags\Http\Controllers\Back\Statuses
 */
class StatusesDataController extends Controller
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
        $items = StatusModel::query();

        return Datatables::of($items)
            ->setTransformer(new StatusTransformer)
            ->rawColumns(['name', 'actions'])
            ->make();
    }
}
