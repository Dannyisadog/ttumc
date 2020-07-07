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
    ?>
    <div class="container" id="schedule-container">
        <table class="table table-dark table-bordered">
            <?php 
                $thisweek = strtotime("this week");
            ?>
            <thead>
              <tr>
                <th scope="col" width="10%"></th>
                @for ($i=0; $i<7; $i++)
                    <th scope="col">{{$week[$i]}} ({{date('m/d', $thisweek)}}) </th>
                    <?php $thisweek += 86400; ?>
                @endfor
              </tr>
            </thead>
            <tbody>
                @for ($i=0; $i<16; $i++)
                    <tr>
                    @for ($j=0; $j<8; $j++)
                        @if ($j == 0)
                            <td>
                                {{ sprintf("%02d:00", $i+8) }}
                            </td>
                        @else
                            <?php $datetime = date("Y-m-d H:i:s", strtotime('monday this week') + ($i+8)*3600 + ($j-1)*86400); ?>
                            @if (Auth::check())
                                @if (isset($schedule_map[$datetime]))
                                    <td>
                                        @if ($schedule_map[$datetime]["user_id"] == Auth::user()->id)
                                            {{Form::open(array('method'=>'delete','route' => 'deleteschedule'))}}
                                            {{Form::hidden('schedule_id', $schedule_map[$datetime]["schedule_id"])}}
                                            {{Form::submit($schedule_map[$datetime]["user_name"] . "x", array('class'=>'btn btn-danger'))}}
                                            {{Form::close()}}
                                        @else
                                            {{$schedule_map[$datetime]["user_name"]}}
                                        @endif
                                    </td>
                                @else
                                    <td>
                                        @if ($user::getDateOrderCount($datetime) < 2 && $user::getWeekOrderCount() < 4 && strtotime($datetime) > strtotime(date('Y-m-d H:i:s')))
                                            {{Form::open(array('route'=>'createschedule'))}}
                                            {{Form::hidden('date', $datetime)}}
                                            {{Form::submit("預約", array('class'=>'btn btn-primary'))}}
                                            {{Form::close()}}
                                        @endif
                                    </td>
                                @endif
                            @else
                                @if (isset($schedule_map[$datetime]))
                                    <td>
                                        {{$schedule_map[$datetime]["user_name"]}}
                                    </td>
                                @else
                                    <td></td>
                                @endif
                            @endif
                        @endif
                    @endfor
                    </tr
                @endfor
            </tbody>
          </table>
    </div>
@endsection