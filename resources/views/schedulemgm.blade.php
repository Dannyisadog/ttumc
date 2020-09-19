@extends('layouts.app')

@section('title', '練團表')

@section('style')
    <style>
        #schedule-container{
            width: 100%;
            height: 100%;
            /* border: 2px solid #ffffff80;
            border-radius: 1.125rem; */
        }
        table{
            text-align: center;
        }
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
        <div style="display:flex; align-items:center">
            <div style="margin-right:10px;">
                {{ Form::open(array('method' => 'put' ,'route' => 'pauseCourse')) }}
                {{ Form::submit("暫停社課")}}
                {{ Form::close() }}
            </div>
            <div> 
                {{ Form::open(array('method' => 'put' ,'route' => 'resumeCourse')) }}
                {{ Form::submit("恢復社課")}}
                {{ Form::close() }}
            </div>
            <div style="color: white; margin-left: 10px">
                社課狀態：<?= $course_status ?>
            </div>
        </div>
        <div  id="schedule-container" style="margin-top:10px;">
            <table class="table table-dark table-bordered">
                <thead>
                <tr>
                    <th scope="col" width="10%"></th>
                    <th scope="col">星期一</th>
                    <th scope="col">星期二</th>
                    <th scope="col">星期三</th>
                    <th scope="col">星期四</th>
                    <th scope="col">星期五</th>
                    <th scope="col">星期六</th>
                    <th scope="col">星期日</th>
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
                            @for ($j = 1; $j <= 7; $j++)
                                <?php
                                    $daytime = $j . "-" . $start;
                                ?>
                                <td>
                                    @guest
                                        @if (array_key_exists(strtotime($daytime), $courses))
                                            <?php 
                                                $id = $courses[strtotime($daytime)]; 
                                                $name = $user_arr[$id];
                                            ?>
                                            {{$name}}
                                        @endif
                                    @else
                                        @if (array_key_exists($daytime, $courses))
                                            <?php $course = $courses[$daytime]; ?>
                                            {{ Form::open(array('method'=>'delete','route' => 'deleteCourse')) }}
                                            {{ Form::hidden('time', "$daytime")}}
                                            {{ Form::submit("$course->title x") }}
                                            {{ Form::close() }}
                                        @else
                                            {{ Form::open(array('route' => 'createCourse')) }}
                                            {{ Form::hidden('time', "$daytime")}}
                                            {{ Form::text('course', "", array('size'=>'10', 'required'))}}
                                            {{ Form::submit('+') }}
                                            {{ Form::close() }}
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
    </div>
@endsection