@extends('layouts.app')

@section('title', '練團表')

@section('style')
    <style>
        #schedule-container{
            width: 100%;
            height: 100%;
        }
        table{
            text-align: center;
        }
        input[type="submit"], .add-button, .delete-button{
            background-color: #ffffff00;
            border: 1px solid #ffffff55;
            border-radius: 0.25rem;
            color: #ffffff;
        }

    </style>
@endsection

@section('content')
    <?php
        $week = array("星期一", "星期二", "星期三", "星期四", "星期五", "星期六", "星期日");
        $schedule_arr = array();
        foreach($schedule as $item){
            $time = strtotime($item->starttime);
            $schedule_arr[$time] = $item->title;
        }
        $user_arr = array();
        foreach($user as $item){
            $user_arr[$item->id] = $item->name;
        }
    ?>
    <div class="container" id="schedule-container">
        <table class="table table-dark table-bordered">
            <?php 
                // echo "user each day: ";
                // print_r($usereachdayCount); 
                // echo "<br>";
                // echo "use order count";
                // print_r($userordercount->count);
                // echo "<br>";
                // echo "band order count";
                // print_r($bandordercount);
                // echo "<br>";
                // echo "band each day: ";
                // print_r($bandseachdaycanorder);
                // echo "<br>";
                // echo "each band each day count";
                // print_r($bandeachdayCount);
                $thisweek = strtotime("this week");
            ?>
            <thead>
              <tr>
                <th scope="col" width="10%"></th>
                @for ($i = 0; $i < 7; $i++)
                    <th scope="col">{{$week[$i]}} ({{date('m/d', $thisweek)}}) </th>
                    <?php $thisweek += 86400; ?>
                @endfor
                {{-- <th scope="col">星期一</th>
                <th scope="col">星期二</th>
                <th scope="col">星期三</th>
                <th scope="col">星期四</th>
                <th scope="col">星期五</th>
                <th scope="col">星期六</th>
                <th scope="col">星期日</th> --}}
              </tr>
            </thead>
            <tbody>
                @for ($i = 9; $i <= 24; $i++)
                    <?php
                        $start = '';
                        $end = '';
                    ?>
                    @if ($i-1 < 10)
                        <?php
                            $hour = $i-1;
                            $start = "0$hour:00";
                        ?>
                    @else
                        <?php
                            $hour = $i-1;
                            $start = "$hour:00";
                        ?>
                    @endif
                    <tr>
                        <td>{{$start}}</td>
                        <?php 
                            $thisweek = strtotime("this week");
                            $oneday = 86400;
                        ?>
                        @for ($j = 0; $j < 7; $j++)
                            <?php
                                $day = date('Y-m-d', $thisweek);
                                $daytime = $day . " " . $start;
                            ?>
                            <td>
                                @guest
                                    @if (array_key_exists(strtotime($daytime), $schedule_arr))
                                        <?php 
                                            $name = $schedule_arr[strtotime($daytime)];
                                        ?>
                                        {{$name}}
                                    @else
                                        
                                    @endif
                                @else
                                    
                                    @if (array_key_exists(strtotime($daytime), $schedule_arr)) 
                                    {{-- 預約中 --}}
                                        <?php $name = $schedule_arr[strtotime($daytime)]; ?>
                                        @if ($name == Auth::user()->name || App\User::belongBand($name))
                                            @if ( strtotime(date('Y-m-d H:00:00')) >= strtotime($daytime))
                                                {{ $name }}
                                            @else
                                                {{ Form::open(array('method'=>'delete','route' => 'deleteschedule')) }}
                                                {{ Form::hidden('date', "$daytime")}}
                                                {{ Form::hidden('userid', Auth::user()->id)}}
                                                {{ Form::hidden('title', $name)}}
                                                {{ Form::submit("$name x", array('class'=>'delete-button btn btn-danger')) }}
                                                {{ Form::close() }}
                                            @endif
                                        @else
                                            {{ $name }}
                                        @endif
                                    @else
                                    {{-- 尚未預約 --}}
                                        @if (App\User::belongBandCount() > 0)
                                            {{-- 有樂團的使用者 --}}
                                            @if (($usercanorder || $bandcanorder))
                                                @if ($usereachdayCount[$j] >= 2 && ($bandseachdaycanorder[$j] == 0 && $bandseachdaycanorder[$j]!= ''))
                                                {{-- @if ($usereachdayCount[$j] >= 2 && ($bandeachdayCount[$j] >= 2)) --}}

                                                @else
                                                    @if ( strtotime(date('Y-m-d H:00:00')) >= strtotime($daytime))

                                                    @else
                                                        {{-- @if (($bandseachdaycanorder[$j] == 1) || $bandseachdaycanorder[$j] == '') --}}
                                                        @if($usercanorder || $bandcanorder)
                                                            <button type="button" class="add-button btn btn-primary"data-toggle="modal" data-target="#exampleModal{{strtotime($daytime)}}">+</button>
                                                            <!-- Modal -->
                                                            <div class="modal fade" id="exampleModal{{strtotime($daytime)}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                                <div class="modal-dialog modal-dialog-centered" role="document">
                                                                    <div class="modal-content">
                                                                    <div class="modal-body">
                                                                        <table class="table table-dark table-bordered">
                                                                            <thead>
                                                                                <tr>
                                                                                    <th scope="col">選擇預約單位</th>
                                                                                </tr>
                                                                            </thead>
                                                                            <tbody>
                                                                                @if ($usereachdayCount[$j] < 2)
                                                                                    @if($userordercount->count < 4)
                                                                                        <tr>
                                                                                            <td>
                                                                                                {{Form::open(array('route'=>'createschedule'))}}
                                                                                                {{Form::hidden('date', $daytime)}}
                                                                                                {{Form::hidden('title', auth()->user()->name)}}
                                                                                                {{Form::submit(auth()->user()->name, array('class'=>'btn-block btn btn-primary'))}}
                                                                                                {{Form::close()}}
                                                                                            </td>
                                                                                        </tr>
                                                                                    @else
                                                                                    @endif
                                                                                @endif
                                                                                @foreach ($bandlist as $item)
                                                                                    @if ($bandordercount[$item->name] < 4)
                                                                                        @if ($bandeachdayCount[$item->name][$j] < 2)
                                                                                            <tr>
                                                                                                <td>
                                                                                                    {{Form::open(array('route'=>'createschedule'))}}
                                                                                                    {{Form::hidden('date', "$daytime")}}
                                                                                                    {{Form::hidden('title', $item->name)}}
                                                                                                    {{Form::submit($item->name, array('class'=>'btn-block btn btn-primary'))}}
                                                                                                    {{Form::close()}}
                                                                                                </td>
                                                                                            </tr>
                                                                                        @else 
                                                                                            
                                                                                        @endif
                                                                                    @endif
                                                                                @endforeach
                                                                            </tbody>
                                                                        </table>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-primary" data-dismiss="modal">關閉</button>
                                                                    </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        {{-- @else
                                                            @if ($usereachdayCount[$j] < 2)
                                                                {{ Form::open(array('route' => 'createschedule')) }}
                                                                {{ Form::hidden('date', "$daytime")}}
                                                                {{ Form::hidden('title', auth()->user()->name)}}
                                                                {{ Form::submit('+') }}
                                                                {{ Form::close() }}
                                                            @else
                                                            @endif --}}
                                                        @endif
                                                    @endif
                                                @endif
                                            @endif
                                        @else
                                            {{-- 沒有樂團的使用者 --}}
                                            @if ($usereachdayCount[$j] >= 2)
                                                {{-- 今天無法預約     --}}
                                            @else
                                                @if ( strtotime(date('Y-m-d H:00:00')) >= strtotime($daytime))
                                                @else
                                                    @if ($usereachdayCount[$j] < 2)
                                                        @if ($usercanorder)
                                                            {{ Form::open(array('route' => 'createschedule')) }}
                                                            {{ Form::hidden('date', "$daytime")}}
                                                            {{ Form::hidden('title', auth()->user()->name)}}
                                                            {{ Form::submit("+", array('class'=>'add-button btn btn-primary')) }}
                                                            {{ Form::close() }}
                                                        @else
                                                        {{-- 預約已滿4小時 --}}
                                                        @endif
                                                    @else
                                                    @endif
                                                @endif
                                            @endif
                                        @endif
                                    @endif
                                
                                @endguest
                            </td>
                            <?php $thisweek += $oneday; ?>
                        @endfor
                    </tr>
                @endfor
            </tbody>
          </table>
    </div>
@endsection