<?php

namespace InetStudio\Hashtags\Http\Controllers\Back\Prizes;

use App\Http\Controllers\Controller;
use InetStudio\Hashtags\Contracts\Services\Back\Prizes\PrizesDataTableServiceContract;
use InetStudio\Hashtags\Contracts\Http\Controllers\Back\Prizes\PrizesDataControllerContract;

/**
 * Class PrizesDataController.
 */
class PrizesDataController extends Controller implements PrizesDataControllerContract
{
    /**
     * Получаем данные для отображения в таблице.
     *
     * @param PrizesDataTableServiceContract $dataTableService
     *
     * @return mixed
     */
    public function data(PrizesDataTableServiceContract $dataTableService)
    {
        return $dataTableService->ajax();
    }
}
