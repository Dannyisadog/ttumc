<template>
    <div id="home-page">
        <!-- <div class="carousel-wrapper">
            <carousel :images="carouselImages"></carousel>
        </div> -->
        <div class="row-1">
            <a href="/schedule" class="row-1-text-wrapper">
                <div class="text">
                    <div class="text-content">
                        <div>練團室</div>
                        <div>預約系統</div>
                    </div>
                </div>
            </a>
            <div class="row-1-img-wrapper">
                <div class="img">
                    <div class="img-mask">
                        <div class="rule-title">練團室預約規則</div>
                        <div class="rule-content">
                            <ul>
                                <li>每日最多預約兩個時段</li>
                                <li>每週最多預約四個時段</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row-2">
            <div class="row-2-text-wrapper">
                <div class="text">
                    <div class="text-content">
                        <div>社課</div>
                    </div>
                </div>
            </div>
            <div class="row-2-img-wrapper">
                <div class="img img-1">
                    <div class="img-mask-hover">
                        <div class="weekday">星期一</div>
                        <div class="course-time">
                            <div class="course">爵士鼓</div>
                            <div class="time">12:00 ~ 13:00</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row-2-img-wrapper">
                <div class="img img-2">
                    <div class="img-mask-hover">
                        <div class="weekday">星期二</div>
                        <div class="course-time">
                            <div class="course">吉他</div>
                            <div class="time">18:00 ~ 19:00</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row-3">
            <div class="row-3-img-wrapper">
                <div class="img img-1">
                    <div class="img-mask-hover">
                        <div class="weekday">星期三</div>
                        <div class="course-time">
                            <div class="course">貝斯</div>
                            <div class="time">18:00 ~ 19:00</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row-3-img-wrapper">
                <div class="img img-2">
                    <div class="img-mask-hover">
                        <div class="weekday">星期四</div>
                        <div class="course-time">
                            <div class="course">主唱</div>
                            <div class="time">12:00 ~ 13:00</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row-3-img-wrapper">
                <div class="img img-3">
                    <div class="img-mask-hover">
                        <div class="weekday">星期五</div>
                        <div class="course-time">
                            <div class="course">來就對了</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import carousel from './carousel.vue';

export default {
    data() {
        return {
            carouselImages: [
                '/images/banner/banner2.jpg',
                '/images/banner/banner3.jpg',
                '/images/banner/banner4.jpg',
            ]
        }
    },
    components: {
        'carousel': carousel,
    }
}
</script>