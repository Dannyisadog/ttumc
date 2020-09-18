@extends('layouts.app')

@section('title', '使用者管理')

@section('content')
    <div class="container">
        <table class="table table-dark table-bordered">
            <thead>
              <tr>
                <th scope="col" width="10%" class="band-index-title">#</th>
                <th scope="col" width="40%" class="band-name-title">團名</th>
                <th scope="col" width="20%" class="band-lead-title">團長</th>
                <th scope="col" width="10%" class="band-action-title">動作</th>
              </tr>
            </thead>
            <tbody>
            <?php $count = 1; ?>
            @foreach ($bands as $band)
                <tr class="band-rows" height="62px">
                    <td class="band-index">{{$count++}}</td>
                    <td class="band-name">{{$band->name}}</td>
                    <td class="band-lead">{{$band->leader->name}}</td>
                    <td class="band-action">
                      @if (isset($band_join_map) && !$band_join_map[$band->id])
                        {{Form::open(array('method'=>'post','route' => 'joinBand'))}}
                        {{Form::hidden('user_id', Auth::user()->id)}}
                        {{Form::hidden('band_id', $band->id)}}
                        {{Form::submit("加入", array('class'=>'btn btn-primary btn-order'))}}
                        {{Form::close()}}
                      @else
                        已加入
                      @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
          </table>          
    </div>
@endsection