function setAjaxData(object = null) {
    var data = {
        'sysLangId': VrConfig.sysLangId,
    };
    data[VrConfig.csrfTokenName] = $('meta[name="X-CSRF-TOKEN"]').attr('content');
    if (object != null) {
        Object.assign(data, object);
    }
    return data;
}

function setSerializedData(serializedData) {
    serializedData.push({name: 'sysLangId', value: VrConfig.sysLangId});
    serializedData.push({name: VrConfig.csrfTokenName, value: $('meta[name="X-CSRF-TOKEN"]').attr('content')});
    return serializedData;
}

// Passive event listeners
jQuery.event.special.touchstart = {
    setup: function (_, ns, handle) {
        this.addEventListener("touchstart", handle, {passive: !ns.includes("noPreventDefault")});
    }
};
jQuery.event.special.touchmove = {
    setup: function (_, ns, handle) {
        this.addEventListener("touchmove", handle, {passive: !ns.includes("noPreventDefault")});
    }
};
//validation
(function () {
    'use strict'
    var forms = document.querySelectorAll('.needs-validation')
    Array.prototype.slice.call(forms)
        .forEach(function (form) {
            form.addEventListener('submit', function (event) {
                if (!form.checkValidity()) {
                    event.preventDefault()
                    event.stopPropagation()
                }
                form.classList.add('was-validated')
            }, false)
        })
})();

//tooltips
var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl)
});

//lazyload background images
document.addEventListener('lazybeforeunveil', function (e) {
    var bg = e.target.getAttribute('data-bg');
    if (bg) {
        e.target.style.backgroundImage = 'url(' + bg + ')';
    }
});

//run on page load
$(document).ready(function () {
    var data = {};
    if ($('#postDetailsPage').length > 0) {
        var postId = $('#postDetailsPage').attr('data-id');
        data = {
            'isPostPage': true,
            'postId': postId
        };
    }
    $.ajax({
        type: 'POST',
        url: VrConfig.baseURL + '/Ajax/runOnPageLoad',
        data: setAjaxData(data),
        success: function (response) {
            var obj = JSON.parse(response);
            if (obj.result == 1) {
                if (obj.isPostPage) {
                    document.getElementById("postNextPrevContainer").innerHTML = obj.htmlContent;
                }
            }
        }
    });
});

//mobile memu
$(document).on('click', '.mobile-menu-button', function () {
    if ($("#navMobile").hasClass('nav-mobile-open')) {
        $("#navMobile").removeClass('nav-mobile-open');
        $('#overlay_bg').hide();
    } else {
        $("#navMobile").addClass('nav-mobile-open');
        $('#overlay_bg').show();
    }
});
$(document).on('click', '#overlay_bg', function () {
    $("#navMobile").removeClass('nav-mobile-open');
    $('#overlay_bg').hide();
});
//close menu
$('.close-menu-click').click(function () {
    $('#navMobile').removeClass('nav-mobile-open');
    $('#overlay_bg').hide();
});

$(document).ready(function () {
    $('form.needs-validation').attr('novalidate', 'novalidate');
    $(".show-on-page-load").css("visibility", "visible");

    $('.nav-main .nav-item-category').hover(function () {
        var categoryId = $(this).attr('data-category-id');
        $('.mega-menu').css('display', 'none');
        $('.mega-menu .link-sub-category').removeClass('active');
        $('.mega-menu .menu-category-items').removeClass('active');
        $('.mega-menu .link-sub-category-all').addClass('active');
        $('.mega-menu .menu-right .filter-all').addClass('active');
        $('.mega-menu-' + categoryId).css('display', 'flex');
    }, function () {
        $('.mega-menu').css('display', 'none');
    });
    $('.mega-menu').hover(function () {
        $(this).css('display', 'flex');
        var categoryId = $(this).attr('data-category-id');
        $('.nav-main .nav-item-category-' + categoryId).addClass('active');
    }, function () {
        $('.mega-menu').css('display', 'none');
        $('.nav-main .nav-item-category').removeClass('active');
    });
    $('.mega-menu .link-sub-category').hover(function () {
        var filter = $(this).attr('data-category-filter');
        $('.mega-menu .link-sub-category').removeClass('active');
        $(this).addClass('active');
        $('.mega-menu .menu-category-items').removeClass('active');
        $('.mega-menu .menu-right .filter-' + filter).addClass('active');
    }, function () {
    });

    $('.mobile-search-button').click(function () {
        $('.mobile-search-form').slideToggle(300);
    });

    //main slider
    $('#main-slider').slick({
        autoplay: true,
        autoplaySpeed: 4900,
        slidesToShow: 1,
        slidesToScroll: 1,
        infinite: true,
        speed: 200,
        rtl: VrConfig.rtl,
        cssEase: 'linear',
        lazyLoad: 'progressive',
        prevArrow: $('#main-slider-nav .prev'),
        nextArrow: $('#main-slider-nav .next'),
    });

    $('#post-detail-slider').slick({
        autoplay: false,
        autoplaySpeed: 4900,
        slidesToShow: 1,
        slidesToScroll: 1,
        infinite: false,
        speed: 200,
        rtl: VrConfig.rtl,
        adaptiveHeight: true,
        lazyLoad: 'progressive',
        prevArrow: $('#post-detail-slider-nav .prev'),
        nextArrow: $('#post-detail-slider-nav .next'),
    });

    $('.newsticker li').delay(500).fadeIn(100);
    $('.newsticker').newsTicker({
        row_height: 30,
        max_rows: 1,
        speed: 400,
        direction: 'up',
        duration: 4000,
        autostart: 1,
        pauseOnHover: 0,
        prevButton: $('#nav_newsticker .prev'),
        nextButton: $('#nav_newsticker .next')
    });

    if (VrConfig.categorySliderIds.length > 0) {
        for (var i = 0; i < VrConfig.categorySliderIds.length; i++) {
            var sliderId = VrConfig.categorySliderIds[i];
            $('#category_slider_' + sliderId).slick({
                autoplay: true,
                autoplaySpeed: 4900,
                infinite: true,
                speed: 200,
                rtl: VrConfig.rtl,
                cssEase: 'linear',
                prevArrow: $('#category_slider_nav_' + sliderId + ' .prev'),
                nextArrow: $('#category_slider_nav_' + sliderId + ' .next'),
                slidesToShow: 4,
                slidesToScroll: 1,
                responsive: [
                    {breakpoint: 992, settings: {slidesToShow: 3, slidesToScroll: 1}},
                    {breakpoint: 768, settings: {slidesToShow: 2, slidesToScroll: 1}},
                    {breakpoint: 576, settings: {slidesToShow: 1, slidesToScroll: 1}}
                ]
            });
        }
    }
    $(window).scroll(function () {
        if ($(this).scrollTop() > 100) {
            $('.scrollup').fadeIn()
        } else {
            $('.scrollup').fadeOut()
        }
    });
    $(".scrollup").click(function () {
        $('html, body').animate({scrollTop: 0}, 700);
        return false
    });
});

//search
$(".search-icon").click(function () {
    if ($(".search-form").hasClass("open")) {
        $(".search-form").removeClass("open");
    } else {
        $(".search-form").addClass("open");
    }
});
//login
$(document).ready(function () {
    $("#form-login").submit(function (event) {
        event.preventDefault();
        var form = $(this);
        var serializedData = form.serializeArray();
        serializedData = setSerializedData(serializedData);
        $.ajax({
            url: VrConfig.baseURL + '/Auth/loginPost',
            type: 'POST',
            data: serializedData,
            success: function (response) {
                var obj = JSON.parse(response);
                if (obj.result == 1) {
                    location.reload();
                } else if (obj.result == 0) {
                    document.getElementById("result-login").innerHTML = obj.error_message;
                }
            }
        });
    });

    $(".form-newsletter").submit(function (event) {
        event.preventDefault();
        var formId = $(this).attr('id');
        var input = '#' + formId + " .newsletter-input";
        var email = $(input).val().trim();
        if (email == '') {
            $(input).addClass('is-invalid');
            return false;
        } else {
            $(input).removeClass('is-invalid');
        }
        var serializedData = $(this).serializeArray();
        serializedData = setSerializedData(serializedData);
        $.ajax({
            type: 'POST',
            url: VrConfig.baseURL + '/add-newsletter-post',
            data: serializedData,
            success: function (response) {
                var obj = JSON.parse(response);
                if (obj.result == 1) {
                    if (obj.isSuccess) {
                        Swal.fire({text: obj.message, icon: 'success', confirmButtonText: VrConfig.textOk});
                        $(input).val('');
                    } else {
                        Swal.fire({text: obj.message, icon: 'warning', confirmButtonText: VrConfig.textOk});
                    }
                }
            }
        });
    });
});

//load more posts
var pageNumLoadMorePosts = 1;

function loadMorePosts(langId, type) {
    pageNumLoadMorePosts++;
    var data = {
        'lang_id': langId,
        'page': pageNumLoadMorePosts,
        'type': type,
        'view': '_post_item',
        'q': getUrlParameter('q')
    };
    $(".btn-load-more").prop("disabled", true);
    $('.btn-load-more svg').hide();
    $('.btn-load-more .spinner-load-more').show();
    $.ajax({
        type: 'POST',
        url: VrConfig.baseURL + '/Ajax/loadMorePosts',
        data: setAjaxData(data),
        success: function (response) {
            var obj = JSON.parse(response);
            if (obj.result == 1) {
                setTimeout(function () {
                    $("#postsLoadMoreContent").append(obj.htmlContent);
                    $(".btn-load-more").prop("disabled", false);
                    $('.btn-load-more svg').show();
                    $('.btn-load-more .spinner-load-more').hide();
                    if (!obj.hasMore) {
                        $(".btn-load-more").hide();
                    }
                }, 200);
            } else {
                setTimeout(function () {
                    $(".btn-load-more").hide();
                    $('.btn-load-more svg').show();
                    $('.btn-load-more .spinner-load-more').hide();
                }, 200);
            }
        }
    });
}

function getUrlParameter(param) {
    const queryString = window.location.search;
    const urlParams = new URLSearchParams(queryString);
    return urlParams.get(param);
}

//view poll results
function viewPollResults(a) {
    $("#poll_" + a + " .question").hide();
    $("#poll_" + a + " .result").show()
}

//view poll option
function viewPollOptions(a) {
    $("#poll_" + a + " .result").hide();
    $("#poll_" + a + " .question").show()
}

//add reaction
let reactionAjaxRequest = false;

function addReaction(postId, reaction) {
    if (reactionAjaxRequest) {
        return false;
    }
    reactionAjaxRequest = true;
    var data = {
        'post_id': postId,
        'reaction': reaction
    };
    $.ajax({
        type: 'POST',
        url: VrConfig.baseURL + '/Ajax/addReaction',
        data: setAjaxData(data),
        success: function (response) {
            var obj = JSON.parse(response);
            if (obj.result == 1) {
                document.getElementById("reactions_result").innerHTML = obj.htmlContent
            }
        },
        complete: function () {
            reactionAjaxRequest = false;
        }
    });
}

//vote poll
$(document).ready(function () {
    $(".poll-form").submit(function (event) {
        event.preventDefault();
        var formId = $(this).attr("data-form-id");
        var form = $(this);
        var serializedData = form.serializeArray();
        serializedData = setSerializedData(serializedData);
        $.ajax({
            url: VrConfig.baseURL + '/Ajax/addPollVote',
            type: 'POST',
            data: serializedData,
            success: function (response) {
                var obj = JSON.parse(response);
                if (obj.result == 1) {
                    if (obj.htmlContent == 'required') {
                        $("#poll-required-message-" + formId).show();
                        $("#poll-error-message-" + formId).hide();
                    } else if (obj.htmlContent == 'voted') {
                        $("#poll-required-message-" + formId).hide();
                        $("#poll-error-message-" + formId).show();
                    } else {
                        document.getElementById("poll-results-" + formId).innerHTML = obj.htmlContent;
                        $("#poll_" + formId + " .result").show();
                        $("#poll_" + formId + " .question").hide()
                    }
                }
            }
        });
    });

    $("#add_comment").submit(function (event) {
        event.preventDefault();
        var formValues = $(this).serializeArray();
        var data = {};
        var submit = true;
        $(formValues).each(function (i, field) {
            if ($.trim(field.value).length < 1) {
                $("#add_comment [name='" + field.name + "']").addClass("is-invalid");
                submit = false;
            } else {
                $("#add_comment [name='" + field.name + "']").removeClass("is-invalid");
                data[field.name] = field.value;
            }
        });
        data['limit'] = $('#post_comment_limit').val();
        if (VrConfig.isRecaptchaEnabled == true) {
            if (typeof data['g-recaptcha-response'] === 'undefined') {
                $('.g-recaptcha').addClass("is-recaptcha-invalid");
                submit = false;
            } else {
                $('.g-recaptcha').removeClass("is-recaptcha-invalid");
            }
        }
        if (submit == true) {
            $('.g-recaptcha').removeClass("is-recaptcha-invalid");
            $.ajax({
                type: 'POST',
                url: VrConfig.baseURL + '/Ajax/addCommentPost',
                data: setAjaxData(data),
                success: function (response) {
                    var obj = JSON.parse(response);
                    if (obj.type == 'message') {
                        document.getElementById("message-comment-result").innerHTML = obj.htmlContent;
                    } else {
                        document.getElementById("comment-result").innerHTML = obj.htmlContent;
                    }
                    if (VrConfig.isRecaptchaEnabled == true) {
                        grecaptcha.reset();
                    }
                    $("#add_comment")[0].reset();
                }
            });
        }
    });

    $("#add_comment_registered").submit(function (event) {
        event.preventDefault();
        var formValues = $(this).serializeArray();
        var data = {
            'limit': $('#post_comment_limit').val()
        };
        var submit = true;
        $(formValues).each(function (i, field) {
            if ($.trim(field.value).length < 1) {
                $("#add_comment_registered [name='" + field.name + "']").addClass("is-invalid");
                submit = false;
            } else {
                $("#add_comment_registered [name='" + field.name + "']").removeClass("is-invalid");
                data[field.name] = field.value;
            }
        });
        data['limit'] = $('#post_comment_limit').val();
        if (submit == true) {
            $.ajax({
                type: 'POST',
                url: VrConfig.baseURL + '/Ajax/addCommentPost',
                data: setAjaxData(data),
                success: function (response) {
                    var obj = JSON.parse(response);
                    if (obj.type == 'message') {
                        document.getElementById("message-comment-result").innerHTML = obj.htmlContent;
                    } else {
                        document.getElementById("comment-result").innerHTML = obj.htmlContent;
                    }
                    $("#add_comment_registered")[0].reset();
                }
            });
        }
    });
});

//add subcomment
$(document).on('click', '.btn-subcomment', function () {
    var commentId = $(this).attr("data-comment-id");
    var data = {};
    data['limit'] = $('#post_comment_limit').val();
    var formId = "#add_subcomment_" + commentId;
    $(formId).ajaxSubmit({
        beforeSubmit: function () {
            var formValues = $("#add_subcomment_" + commentId).serializeArray();
            var submit = true;
            $(formValues).each(function (i, field) {
                if ($.trim(field.value).length < 1) {
                    $(formId + " [name='" + field.name + "']").addClass("is-invalid");
                    submit = false;
                } else {
                    $(formId + " [name='" + field.name + "']").removeClass("is-invalid");
                    data[field.name] = field.value;
                }
            });
            if (VrConfig.isRecaptchaEnabled == true) {
                if (typeof data['g-recaptcha-response'] === 'undefined') {
                    $(formId + ' .g-recaptcha').addClass("is-recaptcha-invalid");
                    submit = false;
                } else {
                    $(formId + ' .g-recaptcha').removeClass("is-recaptcha-invalid");
                }
            }
            if (submit == false) {
                return false;
            }
        },
        type: 'POST',
        url: VrConfig.baseURL + '/Ajax/addCommentPost',
        data: setAjaxData(data),
        success: function (response) {
            if (VrConfig.isRecaptchaEnabled == true) {
                grecaptcha.reset();
            }
            var obj = JSON.parse(response);
            if (obj.type == 'message') {
                document.getElementById("message-subcomment-result-" + commentId).innerHTML = obj.htmlContent;
            } else {
                document.getElementById("comment-result").innerHTML = obj.htmlContent;
            }
            $('.visible-sub-comment form').empty();
        }
    })
});

//add registered subcomment
$(document).on('click', '.btn-subcomment-registered', function () {
    var commentId = $(this).attr("data-comment-id");
    var data = {};
    $("#add_subcomment_registered_" + commentId).ajaxSubmit({
        beforeSubmit: function () {
            var form = $("#add_subcomment_registered_" + commentId).serializeArray();
            var comment = $.trim(form[0].value);
            if (comment.length < 1) {
                $(".form-comment-text").addClass("is-invalid");
                return false;
            } else {
                $(".form-comment-text").removeClass("is-invalid");
            }
        },
        type: 'POST',
        url: VrConfig.baseURL + '/Ajax/addCommentPost',
        data: setAjaxData(data),
        success: function (response) {
            var obj = JSON.parse(response);
            if (obj.type == 'message') {
                document.getElementById("message-subcomment-result-" + commentId).innerHTML = obj.htmlContent;
            } else {
                document.getElementById("comment-result").innerHTML = obj.htmlContent;
            }
            $('.visible-sub-comment form').empty();
        }
    })
});


//show comment box
$(document).on('click', '.comment-meta .btn-reply', function () {
    $('.comment-meta .btn-reply').prop('disabled', true);
    var commentId = $(this).attr('data-parent');
    if ($('#sub_comment_form_' + commentId).html().length > 0) {
        $('#sub_comment_form_' + commentId).empty();
        $('.comment-meta .btn-reply').prop('disabled', false);
    } else {
        $('.visible-sub-comment').empty();
        var limit = parseInt($("#post_comment_limit").val());
        var data = {
            'comment_id': commentId,
            'limit': limit
        };
        $.ajax({
            type: 'POST',
            url: VrConfig.baseURL + '/Ajax/loadSubcommentBox',
            data: setAjaxData(data),
            success: function (response) {
                var obj = JSON.parse(response);
                if (obj.result == 1) {
                    $('#sub_comment_form_' + commentId).append(obj.htmlContent);
                }
                $('.comment-meta .btn-reply').prop('disabled', false);
            }
        });
    }
});

//like comment
$(document).on('click', '.btn-comment-like', function () {
    if ($(this).hasClass('comment-liked')) {
        $(this).removeClass('comment-liked');
    } else {
        $(this).addClass('comment-liked');
    }
    var commentId = $(this).attr("data-comment-id");
    var data = {
        'comment_id': commentId
    };
    $.ajax({
        type: 'POST',
        url: VrConfig.baseURL + '/Ajax/likeCommentPost',
        data: setAjaxData(data),
        success: function (response) {
            var obj = JSON.parse(response);
            if (obj.result == 1) {
                document.getElementById("lbl_comment_like_count_" + commentId).innerHTML = obj.likeCount;
            }
        }
    });
});

//load more comments
function loadMoreComments(postId) {
    var limit = parseInt($("#post_comment_limit").val());
    var data = {
        'post_id': postId,
        'limit': limit
    };
    $.ajax({
        type: 'POST',
        url: VrConfig.baseURL + '/Ajax/loadMoreComments',
        data: setAjaxData(data),
        success: function (response) {
            var obj = JSON.parse(response);
            if (obj.result == 1) {
                setTimeout(function () {
                    $("#post_comment_limit").val(limit + 5);
                    document.getElementById("comment-result").innerHTML = obj.htmlContent
                }, 500);
            }
        }
    });
}

//add remove reading list
function addRemoveReadingListItem(postId) {
    $(".tooltip").hide();
    var data = {
        'post_id': postId,
    };
    $.ajax({
        type: 'POST',
        url: VrConfig.baseURL + '/Ajax/addRemoveReadingListItem',
        data: setAjaxData(data),
        success: function (response) {
            location.reload();
        }
    });
}

$(document).on('click', '.btn-load-more', function () {
    $('.btn-load-more svg').hide();
    $('.btn-load-more .spinner-load-more').show();
});

//delete comment
function deleteComment(commentId, postId, message) {
    Swal.fire({
        text: message,
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: VrConfig.textYes,
        cancelButtonText: VrConfig.textCancel,
    }).then((result) => {
        if (result.isConfirmed) {
            var limit = parseInt($("#post_comment_limit").val());
            var data = {
                'id': commentId,
                'post_id': postId,
                'limit': limit
            };
            $.ajax({
                type: 'POST',
                url: VrConfig.baseURL + '/Ajax/deleteCommentPost',
                data: setAjaxData(data),
                success: function (response) {
                    var obj = JSON.parse(response);
                    if (obj.result == 1) {
                        document.getElementById("comment-result").innerHTML = obj.htmlContent;
                    }
                }
            });
        }
    });
}

//close cookies warning
function closeCookiesWarning() {
    $('.cookies-warning').hide();
    $.ajax({
        type: 'POST',
        url: VrConfig.baseURL + '/close-cookies-warning-post',
        data: setAjaxData({}),
        success: function (response) {
        }
    });
}

//show image preview
function showImagePreview(input, showAsBackground) {
    var divId = $(input).attr('data-img-id');
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            if (showAsBackground) {
                $('#' + divId).css('background-image', 'url(' + e.target.result + ')');
            } else {
                $('#' + divId).attr('src', e.target.result);
            }
        }
        reader.readAsDataURL(input.files[0]);
    }
}

$(document).on('click', '.table-of-contents .ul-main li a', function (event) {
    if (this.hash !== "") {
        event.preventDefault();
        var hash = this.hash;
        $('html, body').animate({
            scrollTop: $(hash).offset().top
        }, 500, function () {
            window.location.hash = hash;
        });
    }
});

//print
$("#print_post").on("click", function () {
    $(".post-content .post-title, .post-content .post-image, .post-content .post-text").printThis({importCSS: true,})
});

//on ajax stop
$(document).ajaxStop(function () {
    function b(c) {
        $("#poll_" + c + " .question").hide();
        $("#poll_" + c + " .result").show()
    }

    function a(c) {
        $("#poll_" + c + " .result").hide();
        $("#poll_" + c + " .question").show()
    }
});