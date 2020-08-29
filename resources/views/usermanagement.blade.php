@extends('layouts.app')

@section('title', '使用者管理')

@section('content')
    <div class="container">
        <table class="table table-dark table-bordered">
            <thead>
              <tr>
                <th scope="col" width="10%">#</th>
                <th scope="col" width="20%">使用者</th>
                <th scope="col" width="30%">Email</th>
                <th scope="col" width="30%">上次登入時間</th>
                <th scope="col" width="10%">管理者</th>
              </tr>
            </thead>
            <tbody>
            <?php $count = 1; ?>
            @foreach ($user as $item)
                <tr>
                    <td>{{$count++}}</td>
                    <td>{{$item->name}}</td>
                    <td>{{$item->email}}</td>
                    <td>{{$item->lastlogintime}}</td>
                    <td>
                        {{Form::open(array('route'=>'changeuseradmin'))}}
                        {{Form::hidden('userid', $item->id)}}
                        @if ($item->admin=='Y')
                            @if (Auth::id() == $item->id)
                            @else
                                {{Form::label('是')}}
                                {{Form::radio('isadmin', 'Y', true)}}
                                {{Form::label('否')}}
                                {{Form::radio('isadmin', 'N', false, array('onclick'=>'this.form.submit();'))}}
                            @endif
                        @else
                            {{Form::label('是')}}
                            {{Form::radio('isadmin', 'Y', false, array('onclick'=>'this.form.submit();'))}}
                            {{Form::label('否')}}
                            {{Form::radio('isadmin', 'N', true)}}
                        @endif
                        {{Form::close()}}
                    </td>
                </tr>
            @endforeach
            </tbody>
          </table>          
    </div>
@endsection