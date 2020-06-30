@extends('layouts.app')

@section('title', '使用者管理')

@section('content')
    <div class="container">
        <table class="table table-dark table-bordered">
            <thead>
              <tr>
                <th scope="col" width="10%">#</th>
                <th scope="col" width="70%">團名</th>
                <th scope="col" width="20%">擁有人</th>
              </tr>
            </thead>
            <tbody>
            <?php $count = 1; ?>
            @foreach ($allband as $item)
                <tr>
                    <td>{{$count++}}</td>
                    <td>{{$item->bandname}}</td>
                    <td>{{$item->username}}</td>
                </tr>
            @endforeach
            </tbody>
          </table>          
    </div>
@endsection