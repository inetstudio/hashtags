@extends('admin::back.layouts.app')

@php
    $title = 'Теги';
@endphp

@section('title', $title)

@section('content')

    @push('breadcrumbs')
        @include('admin.module.hashtags::back.partials.breadcrumbs.tags.index')
    @endpush

    <div class="wrapper wrapper-content">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <a href="{{ route('back.hashtags.tags.create') }}" class="btn btn-sm btn-primary btn-lg">Добавить</a>
                    </div>
                    <div class="ibox-content">
                        <div class="table-responsive">
                            {{ $table->table(['class' => 'table table-striped table-bordered table-hover dataTable']) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@pushonce('scripts:datatables_tags_index')
    {!! $table->scripts() !!}
@endpushonce
