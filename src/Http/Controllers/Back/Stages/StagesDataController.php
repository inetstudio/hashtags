<?php

namespace InetStudio\Hashtags\Http\Controllers\Back\Stages;

use App\Http\Controllers\Controller;
use InetStudio\Hashtags\Contracts\Services\Back\Stages\StagesDataTableServiceContract;
use InetStudio\Hashtags\Contracts\Http\Controllers\Back\Stages\StagesDataControllerContract;

/**
 * Class StagesDataController.
 */
class StagesDataController extends Controller implements StagesDataControllerContract
{
    /**
     * Получаем данные для отображения в таблице.
     *
     * @param StagesDataTableServiceContract $dataTableService
     *
     * @return mixed
     */
    public function data(StagesDataTableServiceContract $dataTableService)
    {
        return $dataTableService->ajax();
    }
}
