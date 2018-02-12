@extends('admin::back.layouts.app')

@php
    $title = ($item->id) ? 'Редактирование поста' : 'Добавление поста';
@endphp

@section('title', $title)

@pushonce('styles:fancybox')
    <!-- FANCYBOX -->
    <link href="{!! asset('admin/css/plugins/fancybox/jquery.fancybox.min.css') !!}" rel="stylesheet">
@endpushonce

@pushonce('styles:posts_custom')
    <!-- CUSTOM STYLE -->
    <link href="{!! asset('admin/css/modules/hashtags/custom.css') !!}" rel="stylesheet">
@endpushonce

@section('content')

    @push('breadcrumbs')
        @include('admin.module.hashtags::back.partials.breadcrumbs.posts.form')
    @endpush

    <div class="row m-sm">
        <a class="btn btn-white" href="{{ route('back.hashtags.posts.index') }}">
            <i class="fa fa-arrow-left"></i> Вернуться назад
        </a>
    </div>

    <div class="row m-b-lg m-t-lg">
        <div class="col-md-6">
            <div class="profile-image">

                @include('admin.module.hashtags::back.partials.preview', [
                    'media' => $media,
                    'gallery' => 'post',
                    'id' => $item->id,
                ])

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

    <div class="wrapper wrapper-content">

        {!! Form::info() !!}

        {!! Form::open(['url' => (! $item->id) ? route('back.hashtags.posts.store') : route('back.hashtags.posts.update', [$item->id]), 'id' => 'mainForm', 'enctype' => 'multipart/form-data', 'class' => 'form-horizontal']) !!}

            @if ($item->id)
                {{ method_field('PUT') }}
            @endif

            {!! Form::hidden('post_id', (! $item->id) ? '' : $item->id) !!}

            <div class="row">
                <div class="col-lg-12">
                    <div class="panel-group float-e-margins" id="mainAccordion">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h5 class="panel-title">
                                    <a data-toggle="collapse" data-parent="#mainAccordion" href="#collapseMain" aria-expanded="true">Основная информация</a>
                                </h5>
                            </div>
                            <div id="collapseMain" class="panel-collapse collapse in" aria-expanded="true">
                                <div class="panel-body">

                                    {!! Form::dropdown('status_id', $item->status_id, [
                                        'label' => [
                                            'title' => 'Статус',
                                        ],
                                        'field' => [
                                            'class' => 'select2 form-control',
                                            'data-placeholder' => 'Выберите статус',
                                            'style' => 'width: 100%',
                                        ],
                                        'options' => [
                                            'values' => [null => ''] + \InetStudio\Hashtags\Models\StatusModel::select('id', 'name')->pluck('name', 'id')->toArray(),
                                        ],
                                    ]) !!}

                                    <div class="form-group" id="prizes">

                                        <label for="status_id" class="col-sm-2 control-label">Призы</label>

                                        <div class="col-sm-10">
                                            <div class="ibox float-e-margins">
                                                <div class="ibox-content no-borders">
                                                    <a href="#" class="btn btn-sm btn-primary btn-xs add-prize"><i class="fa fa-plus"></i> Добавить</a>
                                                    <ul class="prizes-list m-t small-list">
                                                        @foreach ($item->prizes as $prize)
                                                            @php
                                                                if ($prize->pivot->stage_id) {
                                                                    $type = 'stage';
                                                                } elseif ($prize->pivot->date) {
                                                                    $type = 'day';
                                                                } else {
                                                                    $type = 'winner';
                                                                }

                                                                switch ($type) {
                                                                    case 'stage':
                                                                        $prizeTitle = \InetStudio\Hashtags\Models\StageModel::find($prize->pivot->stage_id)->name.' / ';
                                                                        break;
                                                                    case 'day':
                                                                        $prizeTitle = \Carbon\Carbon::createFromFormat('Y-m-d', $prize->pivot->date)->format('d.m.Y').' / ';
                                                                        break;
                                                                    case 'winner':
                                                                        $prizeTitle = '';
                                                                        break;
                                                                }
                                                            @endphp
                                                            <li>
                                                                <span class="m-l-xs">{{ $prizeTitle }}{{ $prize->name }}</span>
                                                                <div class="btn-group pull-right">
                                                                    <a href="#" class="btn btn-xs btn-danger delete"><i class="fa fa-times"></i></a>
                                                                </div>
                                                                <input name="prize_type[]'" type="hidden" value="{{ $type }}">
                                                                <input name="date[]'" type="hidden" value="{{ ($type == 'day') ? \Carbon\Carbon::createFromFormat('Y-m-d', $prize->pivot->date)->format('d.m.Y') : 0 }}">
                                                                <input name="prize_id[]'" type="hidden" value="{{ $prize->id }}">
                                                                <input name="stage_id[]'" type="hidden" value="{{ ($type == 'stage') ? $prize->pivot->stage_id : 0 }}">
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {!! Form::buttons('', '', ['back' => 'back.hashtags.posts.index']) !!}

        {!! Form::close()!!}

    </div>

    <div class="prize-item-template">
        <li>
            <span class="m-l-xs"></span>
            <div class="btn-group pull-right">
                <a href="#" class="btn btn-xs btn-danger delete"><i class="fa fa-times"></i></a>
            </div>
            <input name="prize_type[]'" type="hidden" value="">
            <input name="date[]'" type="hidden" value="">
            <input name="prize_id[]'" type="hidden" value="">
            <input name="stage_id[]'" type="hidden" value="">
        </li>
    </div>

    @include('admin.module.hashtags::back.pages.posts.modals.prize')
@endsection

@pushonce('scripts:fancybox')
    <!-- FANCYBOX -->
    <script src="{!! asset('admin/js/plugins/fancybox/jquery.fancybox.min.js') !!}"></script>
@endpushonce

@pushonce('custom_scripts:posts_custom')
    <!-- CUSTOM SCRIPTS -->
    <script src="{!! asset('admin/js/modules/hashtags/custom.js') !!}"></script>
@endpushonce
