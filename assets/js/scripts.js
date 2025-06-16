// script.js
document.addEventListener('DOMContentLoaded', () => {
    const carousel = document.getElementById('slideCarousel');
    const col3Image = document.getElementById('col3Image');

    function updateCol3Image() {
        const activeItem = carousel.querySelector('.carousel-item.active');
        const imgUrl = activeItem?.getAttribute('data-img') || '';
        col3Image.style.backgroundImage = `url('${imgUrl}')`;
    }

    if (carousel) {
        carousel.addEventListener('slide.bs.carousel', function () {
            setTimeout(updateCol3Image, 100);
        });
        updateCol3Image();
    }
});
