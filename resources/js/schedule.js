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
    },
    updated() {
        createBackground();
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
                // toastr.info(resp.msg);
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
                // toastr.success("預約成功");
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
            // toastr.success("預約成功");
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

function createBackground() {
    var canvas = document.getElementById("canvas"),
    ctx = canvas.getContext("2d");
    canvas.style.display = "block";
    var stars = [], // Array that contains the stars
    FPS = 24, // Frames per second
    x = 200, // Number of stars
    mouse = {
        x: 0,
        y: 0
    }; // mouse location

    // Push stars to array

    for (var i = 0; i < x; i++) {
    stars.push({
        x: Math.random() * canvas.width,
        y: Math.random() * canvas.height,
        radius: Math.random() * 1 + 1,
        vx: Math.floor(Math.random() * 50) - 25,
        vy: Math.floor(Math.random() * 50) - 25
    });
    }

    // Draw the scene

    function draw() {
    ctx.clearRect(0, 0, canvas.width, canvas.height);

    ctx.globalCompositeOperation = "destination-over";

    for (var i = 0, x = stars.length; i < x; i++) {
        var s = stars[i];

        ctx.fillStyle = "#fff";
        ctx.beginPath();
        ctx.arc(s.x, s.y, s.radius, 0, 2 * Math.PI);
        ctx.fill();
        ctx.fillStyle = "black";
        ctx.stroke();
    }

    ctx.beginPath();
    for (let i = 0, x = stars.length; i < x; i++) {
        let starI = stars[i];
        ctx.moveTo(starI.x, starI.y);
        if (distance(mouse, starI) < 150) ctx.lineTo(mouse.x, mouse.y);
        for (let j = 0, x = stars.length; j < x; j++) {
        let starII = stars[j];
        if (distance(starI, starII) < 150) {
            //ctx.globalAlpha = (1 / 150 * distance(starI, starII).toFixed(1));
            ctx.lineTo(starII.x, starII.y);
        }
        }
    }
    ctx.lineWidth = 0.05;
    ctx.strokeStyle = "lightblue";
    ctx.stroke();
    }

    function distance(point1, point2) {
    var xs = 0;
    var ys = 0;

    xs = point2.x - point1.x;
    xs = xs * xs;

    ys = point2.y - point1.y;
    ys = ys * ys;

    return Math.sqrt(xs + ys);
    }

    // Update star locations

    function update() {
    for (var i = 0, x = stars.length; i < x; i++) {
        var s = stars[i];

        s.x += s.vx / FPS;
        s.y += s.vy / FPS;

        if (s.x < 0 || s.x > canvas.width) s.vx = -s.vx;
        if (s.y < 0 || s.y > canvas.height) s.vy = -s.vy;
    }
    }

    canvas.addEventListener("mousemove", function (e) {
    mouse.x = e.clientX;
    mouse.y = e.clientY;
    });

    // Update and draw

    function tick() {
    draw();
    update();
    requestAnimationFrame(tick);
    }

    tick();
}

function init() {
    getSchedules();
    bindEvents();
}

init();