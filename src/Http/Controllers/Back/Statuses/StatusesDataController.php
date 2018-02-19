<?php

namespace InetStudio\Hashtags\Http\Controllers\Back\Statuses;

use App\Http\Controllers\Controller;
use InetStudio\Hashtags\Contracts\Services\Back\Statuses\StatusesDataTableServiceContract;
use InetStudio\Hashtags\Contracts\Http\Controllers\Back\Statuses\StatusesDataControllerContract;

/**
 * Class StatusesDataController.
 */
class StatusesDataController extends Controller implements StatusesDataControllerContract
{
    /**
     * Получаем данные для отображения в таблице.
     *
     * @param StatusesDataTableServiceContract $dataTableService
     *
     * @return mixed
     */
    public function data(StatusesDataTableServiceContract $dataTableService)
    {
        return $dataTableService->ajax();
    }
}
