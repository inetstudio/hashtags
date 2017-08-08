@php
    $title = ($item->id) ? 'Редактирование статуса' : 'Добавление статуса';
@endphp

@extends('admin::layouts.app')

@section('title', $title)

@section('styles')
    <!-- iCheck -->
    <link href="{!! asset('admin/css/plugins/iCheck/custom.css') !!}" rel="stylesheet">
@endsection

@section('content')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-sm-12">
            <h2>
                {{ $title }}
            </h2>
            <ol class="breadcrumb">
                <li>
                    <a href="{{ url('/back/') }}">Главная</a>
                </li>
                <li>
                    <span>Посты по хэштегам</span>
                </li>
                <li>
                    <a href="{{ route('back.hashtags.statuses.index') }}">Статусы</a>
                </li>
                <li class="active">
                    <strong>
                        {{ $title }}
                    </strong>
                </li>
            </ol>
        </div>
    </div>

    <div class="wrapper wrapper-content">

        {!! Form::info() !!}

        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-content">

                        {!! Form::open(['url' => (!$item->id) ? route('back.hashtags.statuses.store') : route('back.hashtags.statuses.update', [$item->id]), 'id' => 'mainForm', 'enctype' => 'multipart/form-data', 'class' => 'form-horizontal']) !!}

                            @if ($item->id)
                                {{ method_field('PUT') }}
                            @endif

                            {!! Form::hidden('status_id', (!$item->id) ? '' : $item->id) !!}

                            <p>Общая информация</p>

                            {!! Form::string('name', $item->name, [
                                'label' => [
                                    'title' => 'Название',
                                ],
                            ]) !!}

                            {!! Form::string('alias', $item->alias, [
                                'label' => [
                                    'title' => 'Алиас',
                                ],
                            ]) !!}

                            {!! Form::wysiwyg('description', $item->description, [
                                'label' => [
                                    'title' => 'Описание',
                                ],
                                'field' => [
                                    'class' => 'tinymce',
                                ],
                            ]) !!}


                            {!! Form::checks('default', $item->default, [
                                'label' => [
                                    'title' => 'Статус по умолчанию',
                                ],
                                'checks' => [
                                    [
                                        'value' => 1,
                                    ],
                                ],
                            ]) !!}

                            {!! Form::checks('main', $item->main, [
                                'label' => [
                                    'title' => 'Основной статус',
                                ],
                                'checks' => [
                                    [
                                        'value' => 1,
                                    ],
                                ],
                            ]) !!}

                            {!! Form::checks('check', $item->check, [
                                'label' => [
                                    'title' => 'Проверка',
                                ],
                                'checks' => [
                                    [
                                        'value' => 1,
                                    ],
                                ],
                            ]) !!}

                            {!! Form::checks('delete', $item->delete, [
                                'label' => [
                                    'title' => 'Удалено',
                                ],
                                'checks' => [
                                    [
                                        'value' => 1,
                                    ],
                                ],
                            ]) !!}

                            {!! Form::checks('block', $item->block, [
                                'label' => [
                                    'title' => 'Блокировать',
                                ],
                                'checks' => [
                                    [
                                        'value' => 1,
                                    ],
                                ],
                            ]) !!}

                            {!! Form::buttons('', '', ['back' => 'back.hashtags.statuses.index']) !!}

                        {!! Form::close()!!}

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <!-- iCheck -->
    <script src="{!! asset('admin/js/plugins/iCheck/icheck.min.js') !!}"></script>

    <!-- TINYMCE -->
    <script src="{!! asset('admin/js/plugins/tinymce/tinymce.min.js') !!}"></script>
@endsection
