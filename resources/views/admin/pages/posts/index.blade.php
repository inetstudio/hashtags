@extends('admin::layouts.app')

@php
    $title = 'Конкурсные посты';
@endphp

@section('title', $title)

@section('styles')
    <!-- FANCYBOX -->
    <link href="{!! asset('admin/css/plugins/fancybox/jquery.fancybox.min.css') !!}" rel="stylesheet">

    <!-- SELECT2 -->
    <link href="{!! asset('admin/css/plugins/select2/select2.min.css') !!}" rel="stylesheet">

    <!-- DATATABLES -->
    <link href="{!! asset('admin/css/plugins/datatables/datatables.min.css') !!}" rel="stylesheet">

    <!-- CUSTOM STYLE -->
    <link href="{!! asset('admin/css/modules/hashtags/custom.css') !!}" rel="stylesheet">
@endsection

@section('content')

    @include('admin.module.hashtags::partials.breadcrumb_index', ['title' => $title])

    <div class="wrapper wrapper-content">
        <div class="text-right m-b-md view-toggle">
            <a class="btn btn-sm btn-white" href="#" data-target="sort"><i class="fa fa-th-large"></i> Сортировка</a>
            <a class="btn btn-sm btn-white" href="#" data-target="moderate"><i class="fa fa-edit"></i> Модерация</a>
        </div>

        <div class="row view" data-mode="sort">
            <div class="col-lg-12">
                <div class="mail-box">
                    <div class="mail-attachment">
                        <div class="attachment order-list" data-sort-url="{{ route('back.hashtags.posts.sort') }}">
                            @foreach($sortItems as $item)
                                <div class="file-box" data-post-id="{{ $item['id'] }}">
                                    <div class="file">
                                        <a href="#">
                                            <span class="corner"></span>
                                            <div class="image">
                                                @if ($item['media']['type'] == 'video')
                                                    <a data-fancybox="order" href="#fancy-order-{{ $item['id'] }}" class="fancybox-video-link">
                                                        <img data-src="{{ $item['media']['preview'] }}" class=" m-b-md img-responsive" alt="profile">
                                                    </a>
                                                    <div id="fancy-order-{{ $item['id'] }}" class="fancybox-video">
                                                        <video controls width="100%" height="auto">
                                                            <source src="{{ $item['media']['source'] }}" type="video/mp4">
                                                        </video>
                                                    </div>
                                                @else
                                                    <a data-fancybox="order" href="{{ $item['media']['source'] }}">
                                                        <img data-src="{{ $item['media']['preview'] }}" class=" m-b-md img-responsive">
                                                    </a>
                                                @endif
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

        <div class="row view active" data-mode="moderate">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-content">
                        <div class="tabs-container">
                            <ul class="nav nav-tabs">
                                @foreach ($statuses as $alias => $status)
                                    <li class="@if ($currentStatus == $alias) {{ "active" }} @endif"><a href="{{ route('back.hashtags.posts.index', $alias) }}"> {{ $status }}</a></li>
                                @endforeach
                            </ul>
                            <div class="tab-content">
                                <div id="{{ $currentStatus }}" class="tab-pane active">
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

    @include('admin.module.hashtags::pages.posts.popups.submit')

@endsection

@section('scripts')
    <!-- AUTOCOMPLETE -->
    <script src="{!! asset('admin/js/plugins/autocomplete/jquery.autocomplete.min.js') !!}"></script>

    <!-- CLIPBOARD -->
    <script src="{!! asset('admin/js/plugins/clipboard/clipboard.min.js') !!}"></script>

    <!-- DATATABLES -->
    <script src="{!! asset('admin/js/plugins/datatables/datatables.min.js') !!}"></script>

    <!-- FANCYBOX -->
    <script src="{!! asset('admin/js/plugins/fancybox/jquery.fancybox.min.js') !!}"></script>

    <!-- SELECT2 -->
    <script src="{!! asset('admin/js/plugins/select2/select2.full.min.js') !!}"></script>

    <!-- SORTABLE -->
    <script src="{!! asset('admin/js/plugins/sortable/sortable.min.js') !!}"></script>

    <!-- Custom Admin Scripts -->
    <script src="{!! asset('admin/js/modules/hashtags/custom.js') !!}"></script>

    {!! $table->scripts() !!}
@endsection
