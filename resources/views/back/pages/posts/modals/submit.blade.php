@php
    $tagsCount = \InetStudio\Hashtags\Models\TagModel::count();
    $pointsCount = \InetStudio\Hashtags\Models\PointModel::count();

    $mainStatus = \InetStudio\Hashtags\Models\StatusModel::whereHas('classifiers', function ($classifiersQuery) {
        $classifiersQuery->where('classifiers.alias', 'main');
    })->first();
@endphp

@pushonce('modals:post_submit')
    <div class="modal inmodal fade" id="submit" tabindex="-1" role="dialog"  aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Закрыть</span></button>
                    <h4 class="modal-title">Подтверждение поста</h4>

                    @if ($tagsCount > 0 && $pointsCount > 0)
                        <small class="font-bold">Выберите тег и количество баллов</small>
                    @endif

                </div>
                <form method="post" data-action="{{ route('back.hashtags.posts.moderate', ['_id_', $mainStatus->alias]) }}" class="form-horizontal">

                    {{ csrf_field() }}

                    @if ($tagsCount > 0 || $pointsCount > 0)
                        <div class="modal-body">
                            @if ($tagsCount > 0)

                                {!! Form::hidden('tag_data', '', [
                                    'class' => 'choose-data',
                                    'id' => 'tag_data',
                                ]) !!}

                                {!! Form::string('tag', '', [
                                    'label' => [
                                        'title' => 'Тег',
                                    ],
                                    'field' => [
                                        'class' => 'form-control autocomplete',
                                        'data-search' => route('back.hashtags.tags.getSuggestions'),
                                        'data-target' => '#tag_data',
                                        'placeholder' => 'Ведите название тега',
                                    ],
                                ]) !!}

                            @endif

                            @if ($pointsCount > 0)

                                {!! Form::dropdown('points_id', '', [
                                    'label' => [
                                        'title' => 'Баллы',
                                    ],
                                    'field' => [
                                        'class' => 'select2 form-control',
                                        'data-placeholder' => 'Выберите тип поста',
                                        'data-allow-clear' => 'true',
                                        'style' => 'width: 100%',
                                    ],
                                    'options' => [
                                        'values' => [null => ''] + \InetStudio\Hashtags\Models\PointModel::select('id', 'name')->pluck('name', 'id')->toArray(),
                                    ],
                                ]) !!}

                            @endif
                        </div>
                    @endif

                    <div class="modal-footer">
                        <button type="button" class="btn btn-white" data-dismiss="modal">Отмена</button>
                        <button class="btn btn-primary" type="submit">Подтвердить</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endpushonce
