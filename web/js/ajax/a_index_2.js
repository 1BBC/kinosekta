var s_type = 0;
var s_year = 0;
var s_genre = 0;
var s_country = 0;
var currentState = 1;
console.log(history.state);

if (history.state != null) {
    s_type = history.state.s_type;
    s_type = history.state.s_year;
    s_type = history.state.s_genre;
    s_type = history.state.s_country;
    currentState = history.state.page;
    $('#s-type').val(s_type);
    $('#s-year').val(s_year);
    $('#s-genre').val(s_genre);
    $('#s-country').val(s_country);
}

var block = true;
var csrfToken = $('meta[name="csrf-token"]').attr("content");

var lastPage = $('meta[name="pageCount"]').attr("content");



$('#s-btn-true').on('click', find);
$('#s-btn-false').on('click', destroyFind);
$('button[class=page-link]').on('click', pageLink);

if (history.state != null && (s_type != 0 || s_year != 0 || s_genre != 0 || s_country != 0 || currentState > 0)) {
    getPageCount();
    loadStartPage(currentState);
    updatePagination(1, lastPage);
} else {
    updatePagination(1, lastPage);
}

$(window).scroll(function () {
    if (history.state != null) {
        old_page = history.state.page;
    } else {
        old_page = null;
    }

    if (Number.isInteger(old_page)) {
        new_page = (old_page + 1);
    } else {
        new_page = 2;
    }

    loadNewPage(new_page);
});



function find() {
    $(".row-figure").empty();
    $(".lp-message").empty();
    s_type = $('#s-type').val();
    s_year = $('#s-year').val();
    s_genre = $('#s-genre').val();
    s_country = $('#s-country').val();

    getPageCount();
    loadStartPage(1);
}

function destroyFind() {
    $(".row-figure").empty();
    $(".lp-message").empty();
    s_type = 0;
    s_year = 0;
    s_genre = 0;
    s_country = 0;

    $('#s-type').val(0);
    $('#s-year').val(0);
    $('#s-genre').val(0);
    $('#s-country').val(0);
    $('.selectpicker').selectpicker('refresh');

    var stateObj = new Object();
    stateObj.s_type = s_type;
    stateObj.s_year = s_year;
    stateObj.s_genre = s_genre;
    stateObj.s_country = s_country;
    stateObj.page = 1;
    window.history.pushState(stateObj, "Title");

    getPageCount();
    loadStartPage(1);
}
function loadNewPage(new_page) {
    if (new_page > lastPage) {
        $(".lp-message").empty();
        $(".lp-message").append('<div class="alert alert-primary" role="alert">Вы просмотрели все записи</div>');
        return null;
    }

    if ($(window).height() + $(window).scrollTop() + 100 >= $(document).height() && block) {
        block = false;

        $.ajax({
            url: '/filmy/page',
            type: 'post',
            data: {
                page : new_page,
                s_type : s_type,
                s_year : s_year,
                s_genre : s_genre,
                s_country : s_country,
                _csrf : csrfToken
            },
            success: function (data) {
                stopLoading();
                updatePagination(new_page, lastPage);
                var stateObj = new Object();
                stateObj.s_type = s_type;
                stateObj.s_year = s_year;
                stateObj.s_genre = s_genre;
                stateObj.s_country = s_country;
                stateObj.page = new_page;
                window.history.pushState(stateObj, "Title");
                $(".row-figure").append(data);
                block = true;
            }
        });
        startLoading();
    }
}

function loadStartPage(new_page)
{
    $.ajax({
        url: '/filmy/page',
        type: 'post',
        data: {
            page : new_page,
            s_type : s_type,
            s_year : s_year,
            s_genre : s_genre,
            s_country : s_country,
            _csrf : csrfToken
        },
        success: function (data) {
            stopLoading();
            var stateObj = new Object();
            stateObj.s_type = s_type;
            stateObj.s_year = s_year;
            stateObj.s_genre = s_genre;
            stateObj.s_country = s_country;
            stateObj.page = new_page;
            console.log(stateObj);
            updatePagination(new_page, lastPage);
            window.history.pushState(stateObj, "Title");
            $(".row-figure").empty();
            $(".row-figure").append(data);
            block = true;
        }
    });
    startLoading();
}

function getPageCount(new_page)
{
    $.ajax({
        url: '/filmy/page-count',
        type: 'post',
        data: {
            page : new_page,
            s_type : s_type,
            s_year : s_year,
            s_genre : s_genre,
            s_country : s_country,
            _csrf : csrfToken
        },
        success: function (data) {
            lastPage = data;
            console.log('getPageCount' + data);
            // console.log('lastPage' + lastPage);
        }
    });
}

//старт анимация ajax
function startLoading() {
    $('.loader').fadeIn(300);
}
//конец анимации ajax
function stopLoading() {
    $('.loader').fadeOut();
}

function pageLink() {
    $(".lp-message").empty();
    var new_page = parseInt($(this).attr("data-page"));
    var stateObj = new Object();
    stateObj.s_type = s_type;
    stateObj.s_year = s_year;
    stateObj.s_genre = s_genre;
    stateObj.s_country = s_country;
    stateObj.page = new_page;
    window.history.pushState(stateObj, "Title");
    $(".row-figure").empty();
    loadStartPage(new_page);
}

function updatePagination(current, last) {
    console.log('lp' + last);
    var pagination = $(".pagination");
    pagination.empty();

    if (current == 1) {
        pagination.append($("<li class=\"page-item disabled\"><button class=\"page-link\">❮</button></li>"));
    } else {
        pagination.append($("<li class=\"page-item\"><button class=\"page-link\" data-page=\""+ (current-1) + "\">❮</button></li>"));

        if (current > 3) {
            pagination.append($("<li class=\"page-item\"><button class=\"page-link\" data-page=\"1\">1..</button></li>"));
            pagination.append($("<li class=\"page-item\"><button class=\"page-link\" data-page=\""+ (current-1) + "\">" + (current-1) + "</button></li>"));
        } else {
            pagination.append($("<li class=\"page-item\"><button class=\"page-link\" data-page=\"1\">1</button></li>"));
            if (current == 3) {
                pagination.append($("<li class=\"page-item\"><button class=\"page-link\" data-page=\"2\">2</button></li>"));
            }
        }
    }

    pagination.append($("<li class=\"page-item active\"><button class=\"page-link\" data-page=\""+ current + "\">" + current + "</button></li>"));
    var lastForFor = current+5;
    if (lastForFor >= last) {
        lastForFor = last;
    }


    for (var i = current+1; i <= lastForFor; i++) {
        pagination.append($("<li class=\"page-item\"><button class=\"page-link\" data-page=\""+ i + "\">" + i + "</button></li>"));
    }

    if (lastForFor < last) {
        if (lastForFor == last-1) {
            pagination.append($("<li class=\"page-item\"><button class=\"page-link\" data-page=\""+ last + "\">" + last + "</button></li>"));
        } else {
            pagination.append($("<li class=\"page-item\"><button class=\"page-link\" data-page=\""+ last + "\">.." + last + "</button></li>"));
        }

        pagination.append($("<li class=\"page-item\"><button class=\"page-link\"data-page=\""+ (current + 1) + "\">❯</button></li>"));
    } else {
        pagination.append($("<li class=\"page-item disabled\"><button class=\"page-link\">❯</button></li>"));
    }

    $('button[class=page-link]').on('click', pageLink);
}

function getUrlParams(url = location.search){
    var regex = /[?&]([^=#]+)=([^&#]*)/g, params = {}, match;
    while(match = regex.exec(url)) {
        params[match[1]] = match[2];
    }
    return params;
}
