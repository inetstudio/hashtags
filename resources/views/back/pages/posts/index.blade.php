@extends('admin::back.layouts.app')

@php
    $title = 'Посты';
@endphp

@section('title', $title)

@pushonce('styles:fancybox')
    <!-- FANCYBOX -->
    <link href="{!! asset('admin/css/plugins/fancybox/jquery.fancybox.min.css') !!}" rel="stylesheet">
@endpushonce

@pushonce('styles:datatables')
    <!-- DATATABLES -->
    <link href="{!! asset('admin/css/plugins/datatables/datatables.min.css') !!}" rel="stylesheet">
@endpushonce

@pushonce('styles:posts_custom')
    <!-- CUSTOM STYLE -->
    <link href="{!! asset('admin/css/modules/hashtags/custom.css') !!}" rel="stylesheet">
@endpushonce

@section('content')

    @push('breadcrumbs')
        @include('admin.module.hashtags::back.partials.breadcrumbs.posts.index')
    @endpush

    <div class="wrapper wrapper-content">

        {!! Form::hidden('currentStatus', $currentStatus->id, [
            'id' => 'currentStatus',
        ]) !!}

        <div class="text-right m-b-md view-toggle">
            <a class="btn btn-sm btn-white" href="#" data-target="sort"><i class="fa fa-th-large"></i> Сортировка</a>
            <a class="btn btn-sm btn-white" href="#" data-target="moderate"><i class="fa fa-edit"></i> Модерация</a>
        </div>

        <div class="row view" data-mode="sort">
            <div class="col-lg-12">
                <div class="mail-box">
                    <div class="mail-attachment">
                        <div class="attachment order-list" data-sort-url="{{ route('back.hashtags.posts.sort') }}" id="sorting">

                            @foreach($sortItems as $item)
                                <div class="file-box" data-post-id="{{ $item['id'] }}">
                                    <div class="file">
                                        <a href="#">
                                            <span class="corner"></span>
                                            <div class="image">

                                                @include('admin.module.hashtags::back.partials.preview', [
                                                    'media' => $item['media'],
                                                    'gallery' => 'sort',
                                                    'id' => $item['id'],
                                                ])

                                            </div>
                                            <div class="file-name">
                                                <span class="post-drag"><i class="fa fa-arrows"></i></span>
                                                <a href="{{ $item['user']['link'] }}" target="_blank">{{ $item['user']['name'] }}</a>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            @endforeach

                            <div class="clearfix"></div>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>

        <div class="row view active" data-mode="moderate" id="moderation">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#add"><i class="fa fa-plus"></i> Добавить</button>
                        <a class="btn btn-sm btn-default" href="{{ route('back.hashtags.posts.download', [$currentStatus->alias]) }}"><i class="fa fa-download"></i> Скачать</a>
                    </div>
                    <div class="ibox-content">
                        <div class="tabs-container">
                            <ul class="nav nav-tabs">

                                @foreach ($statuses as $alias => $status)
                                    <li class="{{ ($currentStatus->alias == $alias) ? 'active' : '' }}"><a href="{{ route('back.hashtags.posts.index', $alias) }}"> {{ $status }}</a></li>
                                @endforeach

                            </ul>
                            <div class="tab-content">
                                <div id="{{ $currentStatus->alias }}" class="tab-pane active">
                                    <div class="panel-body">
                                        <div class="table-responsive">
                                            {{ $table->table(['class' => 'table table-striped table-bordered table-hover dataTable']) }}
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

    @include('admin.module.hashtags::back.pages.posts.modals.submit')
    @include('admin.module.hashtags::back.pages.posts.modals.append')

@endsection

@pushonce('scripts:autocomplete')
    <!-- AUTOCOMPLETE -->
    <script src="{!! asset('admin/js/plugins/autocomplete/jquery.autocomplete.min.js') !!}"></script>
@endpushonce

@pushonce('scripts:clipboard')
    <!-- CLIPBOARD -->
    <script src="{!! asset('admin/js/plugins/clipboard/clipboard.min.js') !!}"></script>
@endpushonce

@pushonce('scripts:fancybox')
    <!-- FANCYBOX -->
    <script src="{!! asset('admin/js/plugins/fancybox/jquery.fancybox.min.js') !!}"></script>
@endpushonce

@pushonce('scripts:sortable')
    <!-- SORTABLE -->
    <script src="{!! asset('admin/js/plugins/sortable/sortable.min.js') !!}"></script>
@endpushonce

@pushonce('scripts:datatables')
    <!-- DATATABLES -->
    <script src="{!! asset('admin/js/plugins/datatables/datatables.min.js') !!}"></script>
@endpushonce

@pushonce('scripts:datatables_posts_index')
    {!! $table->scripts() !!}
@endpushonce

@pushonce('custom_scripts:posts_custom')
    <!-- Custom Admin Scripts -->
    <script src="{!! asset('admin/js/modules/hashtags/custom.js') !!}"></script>
@endpushonce
