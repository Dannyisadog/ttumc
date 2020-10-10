import toastr from 'toastr';

toastr.options = {
    "positionClass": "toast-top-center",
    "timeOut": "1000",
}

function getSchedules() {
    $.ajax({
        url: '/getSchedules',
        method: 'get',
        dataType: 'json',
        success: function (resp) {
            console.log(resp);
        },
        error: function () {
            toastr.error("系統發生錯誤");
        }
    });
}

function bindEvents() {

}

function init() {
    getSchedules();
    bindEvents();
}

init();