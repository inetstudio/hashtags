<?php

namespace InetStudio\Hashtags\Http\Controllers\Back\Points;

use App\Http\Controllers\Controller;
use InetStudio\Hashtags\Contracts\Services\Back\Points\PointsDataTableServiceContract;
use InetStudio\Hashtags\Contracts\Http\Controllers\Back\Points\PointsDataControllerContract;

/**
 * Class PointsDataController.
 */
class PointsDataController extends Controller implements PointsDataControllerContract
{
    /**
     * Получаем данные для отображения в таблице.
     *
     * @param PointsDataTableServiceContract $dataTableService
     *
     * @return mixed
     */
    public function data(PointsDataTableServiceContract $dataTableService)
    {
        return $dataTableService->ajax();
    }
}
