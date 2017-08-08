@php
    $title = ($item->id) ? 'Редактирование поста' : 'Добавление поста';
@endphp

@extends('admin::layouts.app')

@section('title', $title)

@section('styles')
    <!-- SELECT2 -->
    <link href="{!! asset('admin/css/plugins/select2/select2.min.css') !!}" rel="stylesheet">

    <!-- FANCYBOX -->
    <link href="{!! asset('admin/css/plugins/fancybox/jquery.fancybox.min.css') !!}" rel="stylesheet">

    <!-- DATETIMEPICKER -->
    <link href="{!! asset('admin/css/plugins/datetimepicker/jquery.datetimepicker.css') !!}" rel="stylesheet">

    <!-- CUSTOM STYLE -->
    <link href="{!! asset('admin/css/modules/hashtags/custom.css') !!}" rel="stylesheet">
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
                    <a href="{{ route('back.hashtags.posts.index') }}">Посты</a>
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

        <div class="row m-b-lg m-t-lg">
            <div class="col-md-6">

                <div class="profile-image">
                    @if ($item->social->hasMedia('videos'))
                        <a href="#fancy-{{ $item['id'] }}" class="fancybox-video-link">
                            <img src="{{ asset($item->social->getFirstMedia('images')->getUrl('admin_form_thumb')) }}" class=" m-b-md" alt="profile">
                        </a>
                        <div id="fancy-{{ $item['id'] }}" class="fancybox-video">
                            <video controls width="100%" height="auto">
                                <source src="{{ asset($item->social->getFirstMediaUrl('videos')) }}" type="video/mp4">
                            </video>
                        </div>
                    @else
                        <a data-fancybox="profile" href="{{ asset($item->social->getFirstMediaUrl('images')) }}">
                            <img src="{{ asset($item->social->getFirstMedia('images')->getUrl('admin_form_thumb')) }}" class=" m-b-md" alt="profile">
                        </a>
                    @endif
                </div>
                <div class="profile-info">
                    <div class="">
                        <div>
                            <h2 class="no-margins">
                                {{ $item->social->user->userNickname }}
                            </h2>
                            <h4><a href="{{ $item->social->user->userURL }}" target="_blank">Открыть профиль</a></h4>
                            <h4><a href="{{ $item->social->postURL }}" target="_blank">Перейти к посту</a></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-content">

                        {!! Form::open(['url' => (!$item->id) ? route('back.hashtags.posts.store') : route('back.hashtags.posts.update', [$item->id]), 'id' => 'mainForm', 'enctype' => 'multipart/form-data', 'class' => 'form-horizontal']) !!}

                            @if ($item->id)
                                {{ method_field('PUT') }}
                            @endif

                            {!! Form::hidden('post_id', (!$item->id) ? '' : $item->id) !!}

                            <p>Общая информация</p>

                            {!! Form::dropdown('status_id', $item->status_id, [
                                'label' => [
                                    'title' => 'Статус',
                                ],
                                'field' => [
                                    'class' => 'select2 form-control',
                                    'data-placeholder' => 'Выберите статус',
                                    'style' => 'width: 100%',
                                ],
                                'options' => [null => ''] + \InetStudio\Hashtags\Models\StatusModel::select('id', 'name')->pluck('name', 'id')->toArray(),
                            ]) !!}

                            {!! Form::dropdown('stage_id', $item->stage_id, [
                                'label' => [
                                    'title' => 'Этап',
                                ],
                                'field' => [
                                    'class' => 'select2 form-control',
                                    'data-placeholder' => 'Выберите этап',
                                    'data-allow-clear' => 'true',
                                    'style' => 'width: 100%',
                                ],
                                'options' => [null => ''] + \InetStudio\Hashtags\Models\StageModel::select('id', 'name')->pluck('name', 'id')->toArray(),
                            ]) !!}

                            {!! Form::dropdown('prize_id', $item->prize_id, [
                                'label' => [
                                    'title' => 'Приз',
                                ],
                                'field' => [
                                    'class' => 'select2 form-control',
                                    'data-placeholder' => 'Выберите приз',
                                    'data-allow-clear' => 'true',
                                    'style' => 'width: 100%',
                                ],
                                'options' => [null => ''] + \InetStudio\Hashtags\Models\PrizeModel::select('id', 'name')->pluck('name', 'id')->toArray(),
                            ]) !!}

                            {!! Form::buttons('', '', ['back' => 'back.hashtags.posts.index']) !!}

                        {!! Form::close()!!}

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <!-- SELECT2 -->
    <script src="{!! asset('admin/js/plugins/select2/select2.full.min.js') !!}"></script>

    <!-- FANCYBOX -->
    <script src="{!! asset('admin/js/plugins/fancybox/jquery.fancybox.min.js') !!}"></script>

    <!-- DATETIMEPICKER -->
    <script src="{!! asset('admin/js/plugins/datetimepicker/jquery.datetimepicker.full.min.js') !!}"></script>

    <!-- CUSTOM SCRIPTS -->
    <script src="{!! asset('admin/js/modules/hashtags/custom.js') !!}"></script>
@endsection
