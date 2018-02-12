@pushonce('modals:post_add_prize')
    <div class="modal inmodal fade" id="modal_add_prize" tabindex="-1" role="dialog"  aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Закрыть</span></button>
                    <h4 class="modal-title">Добавление приза</h4>
                </div>
                <form class="form-horizontal">
                    <div class="modal-body">

                        {!! Form::dropdown('prize_type', '', [
                            'label' => [
                                'title' => 'Тип победителя',
                            ],
                            'field' => [
                                'class' => 'select2 form-control',
                                'data-placeholder' => 'Выберите тип',
                                'style' => 'width: 100%',
                                'id' => 'add_prize_type',
                            ],
                            'options' => [
                                'values' => [
                                    null => '',
                                    'winner' => 'Победитель',
                                    'stage' => 'Победитель этапа',
                                    'day' => 'Победитель дня',
                                ],
                            ],
                        ]) !!}

                        <div class="prize-type-fields" data-type="day">
                            {!! Form::datepicker('date', '', [
                                'label' => [
                                    'title' => 'Дата выигрыша',
                                ],
                                'field' => [
                                    'class' => 'datetimepicker form-control',
                                    'data-options' => '{"timepicker": false, "format": "d.m.Y"}',
                                ],
                            ]) !!}
                        </div>

                        <div class="prize-type-fields" data-type="stage">
                            {!! Form::dropdown('stage_id', '', [
                                'label' => [
                                    'title' => 'Этап',
                                ],
                                'field' => [
                                    'class' => 'select2 form-control',
                                    'data-placeholder' => 'Выберите этап',
                                    'style' => 'width: 100%',
                                    'id' => 'add_prize_stage',
                                ],
                                'options' => [
                                    'values' => [null => ''] + \InetStudio\Hashtags\Models\StageModel::select('id', 'name')->pluck('name', 'id')->toArray(),
                                ],
                            ]) !!}
                        </div>

                        {!! Form::dropdown('prize_id', '', [
                            'label' => [
                                'title' => 'Приз',
                            ],
                            'field' => [
                                'class' => 'select2 form-control',
                                'data-placeholder' => 'Выберите приз',
                                'style' => 'width: 100%',
                                'id' => 'add_prize_prize',
                            ],
                            'options' => [
                                'values' => [null => ''] + \InetStudio\Hashtags\Models\PrizeModel::select('id', 'name')->pluck('name', 'id')->toArray(),
                            ],
                        ]) !!}

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-white" data-dismiss="modal">Отмена</button>
                        <a class="btn btn-primary save">Добавить</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endpushonce
