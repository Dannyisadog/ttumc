@extends('layouts.app')

@section('title', '練團表')

@section('style')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css">
    <style>
        #schedule-container-pc{
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
    <div class="container" id="schedule-container-pc">
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
                <tr v-for="hour_schedule in schedules">
                    <td v-for="schedule in hour_schedule" class="table-item">
                        <button v-if="schedule.is_owner && !schedule.expired" @click="deleteSchedule(schedule.schedule_id)" class="btn btn-delete-schedule">${schedule.title} x</button>
                        <button v-else-if="schedule.can_order" @click="checkSchedule(schedule.dateTime)" class="btn btn-primary btn-order">+</button>
                        <div v-else>
                            ${ schedule.title }
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="container" id="schedule-container-mw">
        <div id="weekday-selector-container">
            <a href="./?selector=1" id="mw-monday-selector" class="weekday-selector btn btn-primary btn-selector <?php if ($selector==1 ) echo 'active-week-selector';?>">ㄧ</a>
            <a href="./?selector=2" id="mw-tuesday-selector" class="weekday-selector btn btn-primary btn-selector <?php if ($selector==2 ) echo 'active-week-selector';?>">二</a>
            <a href="./?selector=3" id="mw-wednesday-selector" class="weekday-selector btn btn-primary btn-selector <?php if ($selector==3 ) echo 'active-week-selector';?>">三</a>
            <a href="./?selector=4" id="mw-thursday-selector" class="weekday-selector btn btn-primary btn-selector <?php if ($selector==4 ) echo 'active-week-selector';?>">四</a>
            <a href="./?selector=5" id="mw-friday-selector" class="weekday-selector btn btn-primary btn-selector <?php if ($selector==5 ) echo 'active-week-selector';?>">五</a>
            <a href="./?selector=6" id="mw-saturday-selector" class="weekday-selector btn btn-primary btn-selector <?php if ($selector==6 ) echo 'active-week-selector';?>">六</a>
            <a href="./?selector=7" id="mw-sunday-selector" class="weekday-selector btn btn-primary btn-selector <?php if ($selector==7 ) echo 'active-week-selector';?>">日</a>
        </div>
        <div id="schedule-order-title-container">
            <div class="schedule-order-title-line"></div>
            <div id="schedule-order-title-mw">
                {{$selector_weekday_map[$selector]}}
            </div>
            <div class="schedule-order-title-line"></div>
        </div>
        <div id="order-table-mw">
            <table class="table table-dark table-bordered">
                <thead>
                    <tr>
                        <th width="50%" class="order-title-mw">時段</th>
                        <th width="50%" class="order-title-mw">預約</th>
                    </tr>
                </thead>
                <tbody>
                    @for ($i=8; $i<24; $i++)
                        <tr>
                        @for ($j=0; $j<2; $j++)
                            @if ($j == 0)
                                <td height="60px" class="order-item-mw">
                                    {{ sprintf("%02d:00", $i) }}
                                </td>
                            @else
                               <?php $datetime = date("Y-m-d H:i:s", strtotime('monday this week') + ($i)*3600 + ($selector-1)*86400); ?>
                                @if (Auth::check())
                                    @if (isset($schedule_map[$datetime]))
                                        <td height="60px" class="order-item-mw">
                                            <div class="table-item">
                                            @if (in_array(Auth::user()->id, $schedule_map[$datetime]["user_ids"]) && strtotime($datetime) > strtotime(date('Y-m-d H:i:s')))
                                                {{Form::open(array('method'=>'delete','route' => 'deleteschedule'))}}
                                                {{Form::hidden('schedule_id', $schedule_map[$datetime]["schedule_id"])}}
                                                {{Form::submit($schedule_map[$datetime]["order_title"] . "x", array('class'=>'btn btn-danger'))}}
                                                {{Form::close()}}
                                            @else
                                                {{$schedule_map[$datetime]["order_title"]}}
                                            @endif
                                            </div>
                                        </td>
                                    @else
                                        <td height="60px" class="order-item-mw">
                                            <div class="order-item">
                                            @if ($week_can_order && $date_can_order_map[date('Y-m-d', strtotime($datetime))] && strtotime($datetime) > strtotime(date('Y-m-d H:i:s')))
                                                <button class="btn btn-primary btn-order" onclick="orderCheck('{{$datetime}}')">+</button>
                                            @endif
                                            </div>
                                        </td>
                                    @endif
                                @else
                                    @if (isset($schedule_map[$datetime]))
                                        <td height="60px" class="order-item-mw">
                                            <div class="table-item">
                                            {{$schedule_map[$datetime]["order_title"]}}
                                            </div>
                                        </td>
                                    @else
                                        <td height="60px">
                                            
                                        </td>
                                    @endif
                                @endif
                            @endif
                        @endfor
                        </tr>
                    @endfor
                </tbody>
            </table>
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="can-multi-order-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content" style="background-color: #212529;">
                <div class="modal-body">
                    <table class="table table-dark table-bordered">
                        <thead>
                            <tr>
                                <th scope="col">選擇預約身份</th>
                            </tr>
                        </thead>
                        <tbody class="order-identities-body">     
                                                                                                                                                                                           <tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal">關閉</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js-down')
<script src="{{ asset('js/schedule.js') }}" defer></script>
@endsection