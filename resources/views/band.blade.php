@extends('layouts.app')

@section('title', '所屬樂團')

@section('style')
    <style>
        input[type="submit"]{
            background-color: #ffffff00;
            border: 1px solid #ffffff55;
            border-radius: 0.25rem;
            color: #ffffff;
        }
    </style>
@endsection

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card" style="background-color: rgb(52, 56, 61); color: rgb(255, 255, 255);">
                    <div class="card-header">{{ __('新增所屬樂團') }}</div>
    
                    <div class="card-body">
                        <form method="POST" action="{{ route('createband') }}">
                            @csrf
                            @if(session()->has('error-msg'))
                                <div class="alert alert-danger">
                                    {{ session()->get('error-msg') }}
                                </div>
                            @endif
                            <div class="form-group row">
                                <label for="email" class="col-md-3 col-form-label text-md-right">{{ __('樂團名稱') }}</label>
    
                                <div class="col-md-6" style="display:flex">
                                    <input style="width:100%;" id="bandname" type="text" class="form-control" name="bandname" required autocomplete="bandname" autofocus placeholder="(團長或負責人填寫)">
                                    <input style="margin-left: 10px;"type="submit" class="btn btn-primary" value="{{ __('新增') }}">
                                </div>
                            </div>

                        </form>
                    </div>
                    
                </div>

                <div class="card" style="margin-top: 30px; background-color: rgb(52, 56, 61); color: rgb(255, 255, 255);">
                    @if(session()->has('del-error-msg'))
                        <div class="alert alert-danger">
                            {{ session()->get('del-error-msg') }}
                        </div>
                    @endif
                    <div class="card-header">{{ __('所屬樂團列表') }}</div>
    
                    <div class="card-body">
                        <table class="table table-dark table-bordered">
                            <thead>
                              <tr>
                                <th scope="col" width="10%">#</th>
                                <th scope="col" width="70%">樂團名稱</th>
                                <th scope="col" width="20%">動作</th>
                              </tr>
                            </thead>
                            <tbody>
                                <?php $count = 1; ?>
                                @foreach ($bandlist as $item)
                                    <tr>
                                        <th>{{$count++}}</th>
                                        <td>{{$item->name}}</td>
                                        <td>
                                            {{ Form::open(array('route'=>'deleteband', 'method'=>'delete'))}}
                                            {{ Form::hidden('belongid', $item->belongto)}}
                                            {{ Form::hidden('bandname', $item->name)}}
                                            {{ Form::submit('移除')}}
                                            {{ Form::close()}}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                          </table>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
@endsection