var csrfToken = $('meta[name="csrf-token"]').attr("content");
var lastPage = $('meta[name="pageCount"]').attr("content");
var block = true;

var currentObj = {
    s_type : 0,
    s_year : 0,
    s_genre : 0,
    s_country : 0,
    s_page : 1
};

// window.history.pushState(currentObj, "Title");

if (history.state != null) {
    currentObj = history.state;
}

if (currentObj.s_type != 0 || currentObj.s_year != 0 || currentObj.s_genre != 0 || currentObj.s_country != 0 || currentObj.s_page > 1) {
    setFormFilter();
    setFilter();
} else {
    updatePagination();
}

$(window).scroll(function () {
    if ($(window).height() + $(window).scrollTop() + 100 >= $(document).height() && block) {
        currentObj.s_page++;
        loadNewPage();
        updatePagination();
    }
});

$('#set-filter').on('click', setStateAndFilter);
$('#del-filter').on('click', delStateAndFilter);

function delStateAndFilter() {
    $('#s-type').val(0);
    $('#s-year').val(0);
    $('#s-genre').val(0);
    $('#s-country').val(0);
    $('.selectpicker').selectpicker('refresh');

    currentObj = {
        s_type : 0,
        s_year : 0,
        s_genre : 0,
        s_country : 0,
        s_page : 1
    };

    lastPage = $('meta[name="pageCount"]').attr("content");

    $(".lp-message").empty();
    $(".row-figure").empty();

    loadNewPage();
    updatePagination();
}

function setFormFilter() {
    $('#s-type').val(currentObj.s_type);
    $('#s-year').val(currentObj.s_year);
    $('#s-genre').val(currentObj.s_genre);
    $('#s-country').val(currentObj.s_country);
}

function setStateAndFilter() {
    currentObj = {
        s_type : $('#s-type').val(),
        s_year : $('#s-year').val(),
        s_genre : $('#s-genre').val(),
        s_country : $('#s-country').val(),
        s_page : 1
    };

    window.history.pushState(currentObj, "Title");

    setFilter();
}

function setFilter() {
    $(".lp-message").empty();
    $(".row-figure").empty();

    if (block) {
        block = false;
        $.ajax({
            url: '/filmy/page-count',
            type: 'post',
            data: {
                s_type : currentObj.s_type,
                s_year : currentObj.s_year,
                s_genre : currentObj.s_genre,
                s_country : currentObj.s_country,
                _csrf : csrfToken
            },
            success: function (data) {
                lastPage = data;
                block = true;
                loadNewPage();
                updatePagination();
            }
        });
    }
    startLoading();
}

function loadNewPage() {
    if (currentObj.s_page > lastPage) {
        currentObj.s_page = lastPage;
        $(".lp-message").empty();
        $(".lp-message").append('<div class="alert alert-primary" role="alert">Вы просмотрели все записи</div>');
        return null;
    }

    if (block) {
        console.log('lnp');
        block = false;

        $.ajax({
            url: '/filmy/page',
            type: 'post',
            data: {
                page: currentObj.s_page,
                s_type: currentObj.s_type,
                s_year: currentObj.s_year,
                s_genre: currentObj.s_genre,
                s_country: currentObj.s_country,
                _csrf: csrfToken
            },
            success: function (data) {
                stopLoading();
                window.history.pushState(currentObj, "Title");
                $(".row-figure").append(data);
                block = true;
            }
        });
    }

    startLoading();
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
    console.log('pl');
    $(".lp-message").empty();
    currentObj.s_page = parseInt($(this).attr("data-page"));
    window.history.pushState(currentObj, "Title");
    $(".row-figure").empty();
    loadNewPage();
    updatePagination();
}

function updatePagination() {
    var current = currentObj.s_page;
    var last = lastPage;

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
