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
                @for ($i=8; $i<24; $i++)
                    <tr>
                    @for ($j=0; $j<8; $j++)
                        @if ($j == 0)
                            <td>
                                <div class="table-item">
                                    {{ sprintf("%02d:00", $i) }}
                                </div>
                            </td>
                        @else
                            <?php $datetime = date("Y-m-d H:i:s", strtotime('monday this week') + ($i)*3600 + ($j-1)*86400); ?>
                            @if (Auth::check())
                                @if (isset($schedule_map[$datetime]))
                                    <td>
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
                                    <td>
                                        <div class="table-item">
                                        @if ($week_can_order && $date_can_order_map[date('Y-m-d', strtotime($datetime))] && strtotime($datetime) > strtotime(date('Y-m-d H:i:s')))
                                            <button class="btn btn-primary btn-order" onclick="order_check('{{$datetime}}')">+</button>
                                            {{-- <button type="button" class="add-button btn btn-primary"data-toggle="modal" data-target="#exampleModal1594512000">+</button> --}}
                                        @endif
                                        </div>
                                    </td>
                                @endif
                            @else
                                @if (isset($schedule_map[$datetime]))
                                    <td>
                                        <div class="table-item">
                                        {{$schedule_map[$datetime]["order_title"]}}
                                        </div>
                                    </td>
                                @else
                                    <td></td>
                                @endif
                            @endif
                        @endif
                    @endfor
                    </tr>
                @endfor
            </tbody>
        </table>
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
    </div>
@endsection

@section('js-down')
    <script>
        window.addEventListener("load", function (){
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $('#can-multi-order-modal').on('hidden.bs.modal', function (e) {
                $('.order-identities-body').empty();
            })
        });
        function order_check(datetime) {
            $.ajax({
                url: './schedule/order_check',
                method: 'post',
                data: {
                    "datetime": datetime,
                },
                dataType: 'json',
                success: function(resp) {
                    if (resp.status === true && resp.can_multi_order === false) {
                        swal.fire(resp.msg, "", "success");
                        location.reload();
                    }
                    if (resp.status === true && resp.can_multi_order === true) {
                        let identities = resp.identities;
                        appendIdentitiesToModal(identities, datetime);
                        $('#can-multi-order-modal').modal('show');
                    }
                },
                error: function (xhr) {
                    alert("error");
                }
            });
        }

        function order(identity, datetime) {
            $.ajax({
                url: './schedule/order',
                method: 'post',
                data: {
                    "identity": identity,
                    "datetime": datetime
                },
                dataType: 'json',
                success: function (resp) {
                    swal.fire("預約成功", "", "success");
                    location.reload();
                },
                error: function (xhr) {
                    swal.fire("預約失敗", "", "error");
                }
            });
        }

        function appendIdentitiesToModal(identities, datetime) {
            var modalBody = document.querySelector(".order-identities-body");

            identities.forEach(identity => {
                var tr = document.createElement("tr");

                var td = document.createElement("td");

                var button = document.createElement("button");
                button.classList.add("btn");
                button.classList.add("btn-primary");
                button.classList.add("btn-block");
                button.classList.add("btn-order");

                button.addEventListener("click", function () {
                    return order(identity, datetime);
                });

                var buttonText = document.createTextNode(identity.order_title);

                button.appendChild(buttonText);

                td.appendChild(button);

                tr.appendChild(td);

                modalBody.appendChild(tr);
            });
        }
    </script>
@endsection