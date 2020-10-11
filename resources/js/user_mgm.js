import $ from 'jquery';
import toastr from 'toastr';

toastr.options = {
    "positionClass": "toast-top-center",
    "timeOut": "1000",
}

var $updateUserPermissionInput = $('.update-user-permission-input');
var $csrfToken = $('meta[name ="csrf-token"]').attr('content');

function updateUserPermission(isAdmin, user_id) {
    console.log(isAdmin);
    $.ajax({
        url: '/updateUserPermission',
        method: 'post',
        data: {
            _token: $csrfToken,
            isAdmin: isAdmin,
            user_id: user_id
        },
        dataType: 'json',
        success: function (resp) {
            if (resp.success) {
                toastr.success('更新成功');
            } else {
                toastr.error('系統發生錯誤');
            }
        },
        error: function () {
            toastr.error('系統發生錯誤');
        }
    });
}

function bindEvents() {
    $updateUserPermissionInput.on('click', function () {
        var isAdmin = this.checked === true ? 'Y' : 'N';
        updateUserPermission(isAdmin, $(this).attr('user-id'));
    });
}

function init() {
    bindEvents();
}

init();