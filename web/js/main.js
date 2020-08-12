$('#article').readmore({
      maxHeight: 240,
      moreLink: '<a class="text-center" href="#"><ins>Подробнее &#8595</ins></a>',
      lessLink: '<a class="text-center" href="#"><ins>Скрыть &#8593</ins></a>'
    });

$(function () {
    $('.scenes').slick({
        // infinite: true,
        centerMode: true,
        dots: true,
        slidesToShow: 2,
        lazyLoad: 'ondemand',
        slidesToScroll: 2
    });
    $('.actors').slick({
        // infinite: true,
        // centerMode: true,
        dots: true,
        // speed: 300,
        lazyLoad: 'ondemand',
        slidesToShow: 5,
        // centerMode: true,
        // variableWidth: true,
        // centerMode: true,
        // variableWidth: true,
        slidesToScroll: 5,
        adaptiveHeight: true,
        responsive: [
            {
                breakpoint: 1024, // - от какой ширины изменять настройки(1024 и ниже)
                settings: {
                    // вносим изменения на ширине 1024 и ниже
                    slidesToShow: 4,
                    slidesToScroll: 4
                }
            },
            {
                breakpoint: 568, // - от какой ширины изменять настройки(1024 и ниже)
                settings: {
                    // вносим изменения на ширине 1024 и ниже
                    slidesToShow: 3,
                    slidesToScroll: 3
                }
            },
            {
                breakpoint: 480, // брекпоинтов может быть сколько угодно
                settings: {
                    slidesToShow: 2,
                    slidesToScroll: 2
                }
            }
        ]
    });
})

$('.basicAutoComplete').autoComplete();