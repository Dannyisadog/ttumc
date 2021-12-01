<template>
    <div id="home-page">
        <!-- <div class="carousel-wrapper">
            <carousel :images="carouselImages"></carousel>
        </div> -->
        <div class="row-1">
            <a href="/schedule" class="row-1-text-wrapper">
                <div class="text">
                    <div class="text-content">
                        <div>Reserve</div>
                    </div>
                </div>
            </a>
            <div class="row-1-img-wrapper">
                <div class="img">
                    <div class="img-mask">
                        <div class="rule-title">rules</div>
                        <div class="rule-content">
                            <ul>
                                <li>2  sessions / day</li>
                                <li>4  sessions / week</li>
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
                        <div>Course</div>
                    </div>
                </div>
            </div>
            <div class="row-2-img-wrapper">
                <div class="img img-1">
                    <div class="img-mask-hover">
                        <div class="course-time">
                            <div class="course">Drum</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row-2-img-wrapper">
                <div class="img img-2">
                    <div class="img-mask-hover">
                        <div class="course-time">
                            <div class="course">Guitar</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row-3">
            <div class="row-3-img-wrapper">
                <div class="img img-1">
                    <div class="img-mask-hover">
                        <div class="course-time">
                            <div class="course">Bass</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row-3-img-wrapper">
                <div class="img img-2">
                    <div class="img-mask-hover">
                        <div class="course-time">
                            <div class="course">Vocal</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row-3-img-wrapper">
                <div class="img img-3">
                    <div class="img-mask-hover">
                        <div class="course-time">
                            <div class="course">Synthesizer</div>
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