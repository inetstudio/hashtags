<?php

namespace InetStudio\Hashtags\Http\Controllers\Back\Prizes;

use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;
use InetStudio\Hashtags\Models\PrizeModel;
use InetStudio\Hashtags\Transformers\Back\PrizeTransformer;

/**
 * Class PrizesDataController
 * @package InetStudio\Hashtags\Http\Controllers\Back\Prizes
 */
class PrizesDataController extends Controller
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
        $items = PrizeModel::query();

        return DataTables::of($items)
            ->setTransformer(new PrizeTransformer)
            ->rawColumns(['actions'])
            ->make();
    }
}
