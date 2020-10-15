import toastr from 'toastr';

toastr.options = {
    "positionClass": "toast-top-center",
    "timeOut": "1000",
}

var scheduleVue = new Vue({
    el: '#app',
    delimiters: ['${', '}'],
    data: {
        schedules: []
    },
    methods: {
        checkSchedule: function (dateTime) {
            orderCheck(dateTime);
        },
        orderSchedule: function (identity, dateTime) {
            order(identity, dateTime);
        },
        deleteSchedule: function (schedule_id) {
            deleteSchedule(schedule_id);
        }
    }
});

function getSchedules() {
    $.ajax({
        url: '/getSchedules',
        method: 'get',
        dataType: 'json',
        success: function (resp) {
            if (resp.success) {
                scheduleVue.schedules = resp.data;
                console.log(scheduleVue);
            }
        },
        error: function () {
            toastr.error("系統發生錯誤");
        }
    });
}

function deleteSchedule(schedule_id) {
    $.ajax({
        url: '/deleteSchedule',
        method: 'delete',
        data: {
            schedule_id: schedule_id
        },
        dataType: 'json',
        success: function (resp) {
            if (resp.success) {
                toastr.info(resp.msg);
            } else {
                toastr.error(resp.msg);
            }
        },
        error: function () {
            toastr.error("系統發生錯誤");
        },
        complete: function () {
            getSchedules();
        }
    });
}

function bindEvents() {

}

window.addEventListener("load", function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $('#can-multi-order-modal').on('hidden.bs.modal', function (e) {
        $('.order-identities-body').empty();
    })
});
function orderCheck(datetime) {
    $.ajax({
        url: './schedule/order_check',
        method: 'post',
        data: {
            "datetime": datetime,
        },
        dataType: 'json',
        success: function (resp) {
            if (resp.status === true && resp.can_multi_order === false) {
                toastr.success("預約成功");
                getSchedules();
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
            toastr.success("預約成功");
        },
        error: function (xhr) {
            toastr.error("預約失敗");
        },
        complete: function () {
            $('#can-multi-order-modal').modal('hide');
            getSchedules();
        }
    });
}

function appendIdentitiesToModal(identities, datetime) {
    var modalBody = document.querySelector("#order-modal-body");
    modalBody.innerHTML = '';
    identities.forEach(identity => {
        var tr = document.createElement("div");

        tr.setAttribute('class', 'order-identity-item');

        var td = document.createElement("div");

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

function init() {
    getSchedules();
    bindEvents();
}

init();