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
    <div class="container" style="margin-top: 30px;">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card" style="background-color: #222222; color: rgb(255, 255, 255);">
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
                                <div class="col-md-8" style="display:flex; align-items: center">
                                    <div style="width:70px;">樂團名稱</div>
                                    <input id="bandname" style="width: 300px; " type="text" class="form-control" name="bandname" required autocomplete="bandname" autofocus >
                                    <input style="margin-left: 10px; width: 80px; height: 40px;"type="submit" class="btn btn-primary btn-blue" value="{{ __('新增') }}">
                                </div>
                            </div>

                        </form>
                    </div>
                    
                </div>

                <div class="card" style="margin-top: 30px; background-color: #222222; color: rgb(255, 255, 255);">
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
                              </tr>
                            </thead>
                            <tbody>
                                <?php $count = 1; ?>
                                @foreach ($bands as $band)
                                    <tr>
                                        <th>{{$count++}}</th>
                                        <td>{{$band->name}}</td>
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