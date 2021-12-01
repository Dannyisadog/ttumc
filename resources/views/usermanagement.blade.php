@extends('layouts.app')

@section('title', '使用者管理')

@section('style')
<style>

.switch {
  position: relative;
  display: inline-block;
  width: 55px;
  height: 28px;
}

.switch input { 
  opacity: 0;
  width: 0;
  height: 0;
}

.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #ccc;
  -webkit-transition: .4s;
  transition: .4s;
}

.slider:before {
  position: absolute;
  content: "";
  height: 22px;
  width: 22px;
  left: 4px;
  bottom: 3px;
  background-color: white;
  -webkit-transition: .4s;
  transition: .4s;
}

input:checked + .slider {
  background-color: #50ca6e;
}

input:focus + .slider {
  box-shadow: 0 0 1px #50ca6e;
}

input:checked + .slider:before {
  -webkit-transform: translateX(26px);
  -ms-transform: translateX(26px);
  transform: translateX(26px);
}

/* Rounded sliders */
.slider.round {
  border-radius: 34px;
}

.slider.round:before {
  border-radius: 50%;
}

td {
  padding: 10px 20px !important;
}
</style>
@endsection

@section('content')
    <div class="container" style="margin-top: 30px;">
        <table class="table table-dark table-bordered">
            <thead>
              <tr>
                <th scope="col" width="10%">#</th>
                <th scope="col" width="20%">使用者</th>
                <th scope="col" width="30%">Email</th>
                <th scope="col" width="30%">上次登入時間</th>
                <th scope="col" width="10%" class="text-center">管理者</th>
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
                    <td class="table-item-center">
                        @if (Auth::user()->id != $item->id)
                        <label class="switch">
                            <input type="checkbox" class="update-user-permission-input" user-id="{{$item->id}}" <?=$item->admin=='Y' ? 'checked' : ''?>>
                            <span class="slider round"></span>
                        </label>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
          </table>          
    </div>
@endsection

@section('js-down')
<script src="{{ asset('js/user_mgm.js') }}" defer></script>
@endsection