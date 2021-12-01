<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

@include('partials._head')
<body>
    @include('sweetalert::alert')
    @yield('js-up')
    <div id="app">
        <canvas id="canvas" width="1920" height="1080" style="position:fixed; left: 0; top:0; z-index: -1;"></canvas>
        @include('partials._nav')

        <main>
            @yield('content')
        </main>
    </div>
    @yield('js-down')
    <script>
        matcher = window.matchMedia('(prefers-color-scheme: dark)');
        matcher.addListener(onUpdate);
        onUpdate();
        createBackground();
        function onUpdate() {
            let lightSchemeIcon = document.querySelector('link#white-icon');
            let darkSchemeIcon = document.querySelector('link#black-icon');

            if (matcher.matches) {
                darkSchemeIcon.remove();
                document.head.append(lightSchemeIcon);
            } else {
                document.head.append(darkSchemeIcon);
                lightSchemeIcon.remove();
            }
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
    </script>
</body>
</html>
