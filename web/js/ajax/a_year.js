var csrfToken = $('meta[name="csrf-token"]').attr("content");
var lastPage = $('meta[name="pageCount"]').attr("content");
var pageYear = $('meta[name="pageYear"]').attr("content");
var block = true;

var currentObj = {
    s_page : 1
};

$('.loader').fadeOut();
// window.history.pushState(currentObj, "Title");

if (history.state != null) {
    currentObj = history.state;
}

if (currentObj.s_page > 1) {
    $(".row-figure").empty();
    loadNewPage();
    updatePagination();
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
            url: '/filmy/page-year',
            type: 'post',
            data: {
                page: currentObj.s_page,
                s_year: pageYear,
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
