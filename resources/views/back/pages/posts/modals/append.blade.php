@pushonce('modals:post_append')
    <div class="modal inmodal fade" id="add" tabindex="-1" role="dialog"  aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Закрыть</span></button>
                    <h4 class="modal-title">Добавление поста</h4>
                </div>
                <form method="post" action="{{ route('back.hashtags.posts.append') }}" class="form-horizontal">

                    {{ csrf_field() }}

                    <div class="modal-body">

                        {!! Form::dropdown('social_network', '', [
                            'label' => [
                                'title' => 'Социальная сеть',
                            ],
                            'field' => [
                                'class' => 'select2 form-control',
                                'data-placeholder' => 'Выберите социальную сеть',
                                'style' => 'width: 100%',
                            ],
                            'options' => [
                                'values' => [
                                    null => '',
                                    'instagram' => 'Instagram',
                                    'vkontakte' => 'Вконтакте',
                                ],
                            ],
                        ]) !!}

                        {!! Form::string('post_link', '', [
                            'label' => [
                                'title' => 'Ссылка на пост',
                            ],
                        ]) !!}

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-white" data-dismiss="modal">Отмена</button>
                        <button class="btn btn-primary" type="submit">Добавить</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endpushonce
