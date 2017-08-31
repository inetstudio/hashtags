@extends('admin::layouts.app')

@php
    $title = ($item->id) ? 'Редактирование баллов' : 'Добавление баллов';
@endphp

@section('title', $title)

@section('content')

    @push('breadcrumbs')
        @include('admin.module.hashtags::partials.breadcrumbs')
        <li>
            <a href="{{ route('back.hashtags.points.index') }}">Баллы</a>
        </li>
    @endpush

    <div class="wrapper wrapper-content">

        {!! Form::info() !!}

        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-content">

                        {!! Form::open(['url' => (!$item->id) ? route('back.hashtags.points.store') : route('back.hashtags.points.update', [$item->id]), 'id' => 'mainForm', 'enctype' => 'multipart/form-data', 'class' => 'form-horizontal']) !!}

                            @if ($item->id)
                                {{ method_field('PUT') }}
                            @endif

                            {!! Form::hidden('points_id', (!$item->id) ? '' : $item->id) !!}

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

                            {!! Form::string('numeric', $item->numeric, [
                                'label' => [
                                    'title' => 'Количество баллов',
                                ],
                            ]) !!}

                            {!! Form::checks('show', $item->show, [
                                'label' => [
                                    'title' => 'Отображать в галереях',
                                ],
                                'checks' => [
                                    [
                                        'value' => 1,
                                    ],
                                ],
                            ]) !!}

                            {!! Form::buttons('', '', ['back' => 'back.hashtags.points.index']) !!}

                        {!! Form::close()!!}

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection