@php
    $title = ($item->id) ? 'Редактирование баллов' : 'Добавление баллов';
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
                    <a href="{{ route('back.hashtags.points.index') }}">Баллы</a>
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

                        {!! Form::open(['url' => (!$item->id) ? route('back.hashtags.points.store') : route('back.hashtags.points.update', [$item->id]), 'id' => 'mainForm', 'enctype' => 'multipart/form-data', 'class' => 'form-horizontal']) !!}

                            @if ($item->id)
                                {{ method_field('PUT') }}
                            @endif

                            {!! Form::hidden('points_id', (!$item->id) ? "" : $item->id) !!}

                            <p>Общая информация</p>

                            {!! Form::string('name', $item->name, [
                                'label' => [
                                    'title' => 'Название',
                                    'class' => 'col-sm-2 control-label',
                                ],
                                'field' => [
                                    'class' => 'form-control',
                                ],
                            ]) !!}

                            {!! Form::string('alias', $item->alias, [
                                'label' => [
                                    'title' => 'Алиас',
                                    'class' => 'col-sm-2 control-label',
                                ],
                                'field' => [
                                    'class' => 'form-control',
                                ],
                            ]) !!}

                            {!! Form::string('numeric', $item->numeric, [
                                'label' => [
                                    'title' => 'Количество баллов',
                                    'class' => 'col-sm-2 control-label',
                                ],
                                'field' => [
                                    'class' => 'form-control',
                                ],
                            ]) !!}

                            <div class="form-group @if ($errors->has('show')){!! "has-error" !!}@endif">
                                <label for="show" class="col-sm-2 control-label">Отображать в галереях</label>
                                <div class="col-sm-10">
                                    <div class="i-checks"><label> <input type="checkbox" value="1" name="show" @if (! $item->id || $item->show) checked="" @endif id="show"> <i></i> </label></div>
                                    @foreach ($errors->get('show') as $message)
                                        <span class="help-block m-b-none">{{ $message }}</span>
                                    @endforeach
                                </div>
                            </div>

                            <div class="hr-line-dashed"></div>

                            {!! Form::buttons('', '', ['back' => 'back.hashtags.points.index']) !!}

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
@endsection
