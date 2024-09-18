// редагування коментаря
document.addEventListener('DOMContentLoaded', function() {
    let editButtons = document.querySelectorAll('.edit-comment-btn');
    editButtons.forEach(function(button) {
        button.addEventListener('click', function() {
            let commentId = button.getAttribute('data-comment-id');
            let commentDiv = document.querySelector('.comment-item[data-comment-id="' + commentId + '"]');
            let comment = commentDiv.querySelector('.comment-text');
            let editForm = commentDiv.querySelector('.edit-comment-form');
            
            // Переключаємо відображення між коментарем та формою редагування
            comment.style.display = 'none';
            editForm.style.display = 'block';
        });
    });
});

// відповідь на коментар
document.addEventListener('DOMContentLoaded', function() {
    let answerButtons = document.querySelectorAll('.answer-comment-btn');
    answerButtons.forEach(function(button) {
        button.addEventListener('click', function() {
            let commentId = button.getAttribute('data-comment-id');
            let commentDiv = document.querySelector('.comment-item[data-comment-id="' + commentId + '"]');
            let answerForm = commentDiv.querySelector('.answer-comment-form');
            
            answerForm.style.display = 'block';
        });
    });
});

// вподобайка
let likeButtons = document.querySelectorAll('.like-btn');
likeButtons.forEach(likeButton => {
    likeButton.addEventListener('click', () => {
        let likeGroup = likeButton.closest('.like-group');

        let item_typeInput = likeGroup.querySelector('input[name="item_type"]');
        let itemIdInput = likeGroup.querySelector('input[name="item_id"]');
        let likesCountSpan = likeGroup.querySelector('.likes-count');

        // Отримуємо значення з цих інпутів
        let item_type = item_typeInput.value;
        let item_id = itemIdInput.value;

        // Створюємо об'єкт з параметрами
        let requestData = {
            item_type: item_type,
            item_id: item_id,
        };
        // Відправка AJAX-запиту на сервер для додавання лайку
        fetch(`/like`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(requestData),
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Вподобайку успішно додано
                let likesCountValue = Number(likesCountSpan.innerHTML);

                if (data.liked) {
                    likeButton.classList.remove('fa-regular');
                    likeButton.classList.add('fa-solid');
                    likesCountSpan.innerHTML = likesCountValue + 1;
                    likesCountSpan.closest('.count-span').title = "Зняти вподобайку";
                }   
                else {
                    likeButton.classList.remove('fa-solid');
                    likeButton.classList.add('fa-regular');
                    likesCountSpan.innerHTML = likesCountValue - 1;
                    likesCountSpan.closest('.count-span').title = "Вподобати";
                }             
            } 
        })
        .catch(error => {
            console.error('Помилка при виконанні AJAX-запиту:', error);
        });
    })
});

// Підсвітка коментаря, на який дали  відповідь
$(document).ready(function() {
    $('.to-answer-link').on('click', function(e) {
        e.preventDefault();
        let href = $(this).attr('href');
        let targetComment = $(href);

        ScrollTo(targetComment[0]);
        
        // Додайте клас анімації для бликання
        targetComment.addClass('blink-animation');
        
        // Видаліть клас анімації після завершення анімації
        setTimeout(function() {
            targetComment.removeClass('blink-animation');
        }, 1000); // Змініть цей час на той, який вам підходить
    });
});

// Тематика форуму
$(document).ready(function() {
    $('.tematic-option').click(function() {
        let tematicId = $(this).data('value');
        $('.tematic-block').hide();
        $(`#tematic${tematicId}`).show();
    });

    $("input.forum_item").each(function() {
        let tematicSearch = $(this).data('tematic-search');
        let itemName = $(this).data('item-name');

        $(this).select2({
            ajax: {
                url: "/api/search//" + tematicSearch,
                dataType: "json",
                delay: 250,
                processResults: function(data) {
                    return {
                        results: data.map(item => ({
                            id: item.id,
                            text: item.name + ' (id = ' + item.id + ")"
                        }))
                    };
                },
                cache: true
            },
            placeholder: itemName,
            minimumInputLength: 3,
            allowClear: true,
            language: {
                searching: function() {
                    return "Пошук триває...";
                },
                noResults: function() {
                    return "Нема результатів";
                },
                inputTooShort: function(args) {
                    var message = "Мінімум символів для пошуку: " + args.minimum;
                    return message;
                },
            }
        });

        $(this).on("select2:select", function(e) {
            let selectedItem = e.params.data;
            let tematicBlock = $(this).closest('.tematic-block');
            let forumItemContainer = tematicBlock.find('.forum-item-container');
        
            forumItemContainer.empty();
        
            let NameInput = $('<input readonly type="text" value="' + selectedItem.text + '">');
            let IdInput = $('<input type="hidden" name="item_id" value="' + selectedItem.id + '">');
        
            forumItemContainer.append(NameInput);
            forumItemContainer.append(IdInput);
        });
    });    
    
});

// Сповіщення
$(document).ready(function() {
    $('.notification-btn').on('click', function(event) {
        let notificationId = $(this).data('notification-id');

        if (!$(this).hasClass('read')) {
            event.preventDefault();
            $.ajax({
                url: '/notifications/mark-as-read/' + notificationId,
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(data) {
                    window.location.href = $(this).attr('href');
                }.bind(this),
                error: function(error) {
                    console.log('Помилка сповіщення: ' + error);
                }
            });
        }
    });
});

// Рейтинг
$(document).ready(function () {
    // виведення рейтингу
    let ratingContainers = $(".rating-container[data-rating]");
    ratingContainers.each(function () {
        let rating = parseInt($(this).data("rating"));
        SetRating($(this), rating);
    });

    // підсвітка при наведенні
    let toRateContainers = $(".rating-container[data-work-id]");
    toRateContainers.on("mouseenter", '.left-half-full, .right-half-full', function () {
        let currentRating = $(this).data("rating");
        let toRateContainer = $(this).closest(".rating-container");

        toRateContainer.find(".left-half-full, .right-half-full").each(function () {
            let rating = $(this).data("rating");
            if (rating <= currentRating) {
                $(this).css("opacity", 1);
            }
            else {
                $(this).css("opacity", 0);
            }
        });
    });

    // підсвітка при відведенні (встановлений рейтинг)
    toRateContainers.on("mouseleave", '.left-half-full, .right-half-full', function () {
        let toRateContainer = $(this).closest(".rating-container");
        let rating = toRateContainer.data("rating");
        SetRating(toRateContainer, rating);
    });

    // виставлення рейтингу
    toRateContainers.on("click", '.left-half, .right-half', function () {
        var rating = $(this).data('rating');
        let toRateContainer = $(this).closest(".rating-container");
        let w_id = toRateContainer.data('work-id');

        $.ajax({
            type: 'POST',
            url: '/works/' + w_id + '/rate',
            data: {
                value: rating 
            },
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function (data) {
                if (data.error) showToast(data.error,IconTypes.ERROR);
                else {
                    SetRating(toRateContainer,rating);
                    $("#average_rating").text(data.average_rating);
                    toRateContainer.data('rating', rating);
                    toRateContainer.addClass('has-rating');
                    showToast(data.success,IconTypes.SUCCESS)
                }
            },
            error: function(data){
                showToast("Виникла помилка",IconTypes.ERROR)
            }
        });
    });

    // скасування рейтингу
    $('#btn-cancel-rate').on('click', function() {
        let toRateContainer = $(this).closest(".rating-container");
        let w_id = toRateContainer.data('work-id');

        $.ajax({
            type: 'POST',
            url: '/works/' + w_id + '/cancel-rate',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function (data) {
                if (data.error) {
                    showToast(data.error, IconTypes.ERROR);
                } else {
                    showToast(data.success, IconTypes.SUCCESS);
                    $(".rating-container").data('rating',null)
                    SetRating(toRateContainer,0);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                showToast("Виникла помилка.", IconTypes.ERROR);
            }
        });
    });

    var timeout;
    $('.rating-container').hover(
        function() {
            var $this = $(this);
            timeout = setTimeout(function() {
                $this.addClass('show-cancel-rate');
            }, 500); // Затримка в 500 мс перед показом блоку
        },
        function() {
            clearTimeout(timeout);
            $(this).removeClass('show-cancel-rate');
        }
    );
});
// виведення рейтингу
function SetRating(toRateBlock, rating) {
    let halfHearts = toRateBlock.find(".left-half-full, .right-half-full");

    halfHearts.each(function () {
        let currentRating = $(this).data("rating");
        if (currentRating <= rating) {
            $(this).css("opacity", 1);
        }
        else {
            $(this).css("opacity", 0);
        }
    });

    if (rating>0) $("#btn-cancel-rate-group").show();
    else $("#btn-cancel-rate-group").hide();
}

// виставлення дати прочитання
$(document).ready(function() {
    $('#input-date-read').on('keypress', function(event) {
        if (event.which === 13) {
            $('#btn-date-read').click();
        }
    });

    $('#btn-date-read').on('click', function() {
        let group = $(this).closest(".active-group");
        let dateRead = group.find(".input-date").val();
        let w_id = $(this).data('work-id');

        $.ajax({
            type: 'POST',
            url: '/works/' + w_id + '/date-read',
            data: {
                date_read: dateRead
            },
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function (data) {
                if (data.error) {
                    showToast(data.error, IconTypes.ERROR);
                } else {
                    showToast(data.success, IconTypes.SUCCESS);
                    if (dateRead) $("#btn-cancel-date-read").show();
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                showToast("Виникла помилка.", IconTypes.ERROR);
            }
        });
    });

    // скасування дати прочитання
    $('#btn-cancel-date-read').on('click', function() {
        let w_id = $(this).data('work-id');

        $.ajax({
            type: 'POST',
            url: '/works/' + w_id + '/cancel-date-read',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function (data) {
                if (data.error) {
                    showToast(data.error, IconTypes.ERROR);
                } else {
                    showToast(data.success, IconTypes.SUCCESS);
                    $('#input-date-read').val('');
                    $("#btn-cancel-date-read").hide();
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                showToast("Виникла помилка.", IconTypes.ERROR);
            }
        });
    });
});

// Розгортання блоку
$(document).ready(function() {
    $(".to-expand").each(function() {
        const $toExpand = $(this);
        const $expandButton = $toExpand.closest(".row").find(".expand-button");
        const maxHeight = 100;
        let isExpanded = false;

        if ($toExpand.height() + 1 >= maxHeight) {
            $expandButton.show();
        }

        $expandButton.click(function() {
            if (isExpanded) {
                $toExpand.removeClass("expanded");
                $expandButton.css('transform', 'none');
            } else {
                $toExpand.addClass("expanded");
                $expandButton.css('transform', 'scaleY(-1)');
                // Отримуємо вертикальну позицію блоку
                // ScrollTo($toExpand[0]);
            }
            isExpanded = !isExpanded;
        });
        
    });
});

$(document).ready(function() {
    // Показати повний текст і приховати скорочений для всіх кнопок "Показати більше"
    $(document).on('click', '.show-more', function() {
        var truncatedText = $(this).closest('.truncated-text');
        truncatedText.find('.short-text').hide();      // Ховаємо скорочений текст
        truncatedText.find('.full-text').show();       // Показуємо повний текст
    });

    // Показати скорочений текст і приховати повний для всіх кнопок "Показати менше"
    $(document).on('click', '.show-less', function() {
        var truncatedText = $(this).closest('.truncated-text');
        truncatedText.find('.full-text').hide();       // Ховаємо повний текст
        truncatedText.find('.short-text').show();      // Показуємо скорочений текст
    });
});




// Класифікатор
// Взаємовиключення чекбоксів в yes-no-group
$(document).ready(function () {
    $('.no-yes-group input[type="checkbox"]').on('change', function () {
        const checkboxes = $(this).closest('.no-yes-group').find('input[type="checkbox"]');
        checkboxes.not(this).prop('checked', false);
    });
});
// Перевірка, чи виводити опції
function checkOptions() {
    // проходимось по всіх батьківських опціях
    $('[data-change]').each(function() {
        const optId = $(this).data('change');
        const checkbox = $(`#opt${optId}`);
        
        // залежні опції
        const relatedOptions = $(`[data-change-id="${optId}"]`);

        // Перевірка, чи чекбокс вибраний
        if (!checkbox.prop('checked')) {
            // Якщо чекбокс не вибраний, ховаємо залежні опції та робимо всі чекбокси всередині них невибраними
            relatedOptions.hide();
            relatedOptions.find('input[type="checkbox"]').prop('checked', false);
        } else {
            // Якщо чекбокс вибраний, показуємо залежні опції
            relatedOptions.show();
        }
    });

}
// Згортання/розгортання підопцій
$(document).ready(function () {
    checkOptions();

    $('[data-change] input[type="checkbox"], [data-change] input[type="radio"]').on('change', function () {
        checkOptions();
    });
});

// Автозаповнення при пошуку по сайту  
$(document).ready(function() {  
    let dataTable = $("#search").data("table");
    $("#search").select2({
        ajax: {
            url: "/api/search/" + dataTable,
            dataType: "json",
            delay: 250,
            processResults: function(data) {
            return {
                results: data.map(item => ({
                id: item.id,
                text: item.name,
            }))
            };
            },
            cache: true
        },
        placeholder: "Пошук",
        minimumInputLength: 4,
        allowClear: true,
        language: {
            searching: function () {
            return "Пошук триває...";
            },
            noResults: function () {
            return "Нема результатів";
            },
            inputTooShort: function (args) {
            let message = "Мінімум символів для пошуку: "+args.minimum;
            return message;
            },
        }
    });

    $("#search").on("select2:select", function(e) {
        let selectedItem = e.params.data;
        window.location.href = dataTable+'/'+selectedItem.id;
    });

});

// Відправка форми при зміні значення
$(document).ready(function() {
    $(".change-to-submit").change(function() {
        let form = $(this).closest("form");
        form.submit();
    });   
});

// Радіобатони сортування
$(document).ready(function() {
    $(".radio-sort").on("click", function() {
        // Переключаємо стан вибраності при кліку
        $(this).prop("checked", !$(this).prop("checked"));

        // Знімаємо вибір з іншої радіокнопки
        $(".radio-sort").not(this).prop("checked", true);

        let form = $(this).closest("form");
        form.submit();
    });
});

// Фільтрування кнопки
$(document).ready(function() {
    $("#filter_btn").on("click", function() {
        var $filterContainer = $("#filter_container");

        if ($filterContainer.is(":visible")) {
            // Якщо блок видимий, відправити форму
            var $parentForm = $(this).closest("form");
            $parentForm.submit();
        } else {
            // Якщо блок прихований, розгорнути його
            $filterContainer.show();
        }
    });

    // При кліку на кнопку згортання
    $("#filter_hide_btn").on("click", function() {
        $("#filter_container").hide();
    });

    // При кліку на кнопку скидання опцій
    $("#filter_clear_btn").on("click", function() {
        var $form = $(this).closest("form");

        // Зробити всі чекбокси в блоку невибраними
        $form.find("input[type='checkbox']:checked").each(function() {
            $(this).click();
        });

        // Очистити текстові поля
        $form.find("input[type='text']").val('');

        // Встановити перше значення в списку
        $form.find(".base-select").each(function() {
            var $selectBox = $(this).find(".select-box");
            var $options = $(this).find(".options");
            var $firstOption = $options.find("li:first");

            // Встановити перше значення для випадаючого списку
            $selectBox.find(".selected-option").text($firstOption.text());

            // Встановити значення в input[type='hidden']
            var selectedValue = $firstOption.attr("data-value");
            $(this).find("input[type='hidden']").val(selectedValue);
        });
    });
});

// Розтягування на фокусі
$(document).ready(function() {
    $('.resizeble').focus(function() {
        var rows = $(this).data('focus-rows');
        $(this).attr('rows', rows);
        ScrollTo(this);
    });

    $('.resizeble').on('blur', function(event) {
        var $textarea = $(this);
        var parentForm = $textarea.closest('form');
        if (!parentForm.is(event.relatedTarget) && !parentForm.has(event.relatedTarget).length) {
            console.log(parentForm.is(event.relatedTarget)+" | "+parentForm.has(event.relatedTarget).length)
            var initialRows = $textarea.data('start-rows');
            $textarea.attr('rows', initialRows);
        }
    });
});

// Автозаповнення при пошуку книги для полиці  
$(document).ready(function() {    
    let shelf_id = $("#search_works_for_shelf").data("shelf");
    $("#search_works_for_shelf").select2({
        ajax: {
            url: "/api/search/works",
            dataType: "json",
            delay: 250,
            processResults: function(data) {
            return {
                results: data.map(item => ({
                id: item.id,
                text: item.name,
            }))
            };
            },
            cache: true
        },
        placeholder: "Додайте книгу на полицю",
        minimumInputLength: 4,
        allowClear: true,
        language: {
            searching: function () {
            return "Пошук триває...";
            },
            noResults: function () {
            return "Нема результатів";
            },
            inputTooShort: function (args) {
            let message = "Мінімум символів для пошуку: "+args.minimum;
            return message;
            },
        }
    });

    $("#search_works_for_shelf").on("select2:select", function(e) {
        let selectedItem = e.params.data;
        $(this).val(selectedItem.id).trigger('change');
        $(this).closest('form').submit();
    });
});

// Прокрутка до блоку: Scroll
function ScrollTo(element, offset = false, scrollToElement = true) {
    if (offset) {
        $(element).addClass("mt-minus-offset");
    }
    if (scrollToElement) {
        var scrollToParent = $(element).closest('.scroll-to')[0];
        if (scrollToParent) {
            scrollToParent.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
        else {
            element.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }
    else {
        element.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
    if (offset) {
        $(element).removeClass("mt-minus-offset");
    }
}


// Кнопки вікривання/приховання меню
$(document).ready(function() {
    $('.show-menu-btn').click(function() {
        var targetBlock = $(this).data('show-id');
        var $block = $('#' + targetBlock);
        var $overlay = $('#overlay');
        var body = $("body");
        
        if (!$block.is(':visible')) {
            $block.show();
            $overlay.show();
            body.addClass("no-scroll");
        }
        else {
            $block.hide();
            $overlay.hide();
            body.removeClass("no-scroll");
        }
    });

    $('#overlay').click(function() {
        $('.overlay-menu').hide();
        $(this).hide();
    });
});

// спойлери
$(document).ready(function() {
    $('.with-spoilers').each(function() {
        let content = $(this).html();
        content = content.replace(/\[spoiler\](.*?)\[\/spoiler\]/g, '<span class="spoiler">$1</span>');
        $(this).html(content);
    });
    $('.spoiler').click(function() {
        $(this).toggleClass('active');
    });
    
    $('.spoiler-add-btn').click(function() {
        var textarea = $(this).closest('.active-group').find('textarea');
        var spoilerText = '[spoiler][/spoiler]';
        
        var selectedText = textarea.val().substring(textarea[0].selectionStart, textarea[0].selectionEnd);
        if (selectedText !== '') {
            var newText = textarea.val().substring(0, textarea[0].selectionStart) +
                          '[spoiler]' + selectedText + '[/spoiler]' +
                          textarea.val().substring(textarea[0].selectionEnd);
            textarea.val(newText);
        }
        else {
            var cursorPos = textarea[0].selectionStart;
            var newText = textarea.val().substring(0, cursorPos) +
                          spoilerText +
                          textarea.val().substring(cursorPos);
            textarea.val(newText);
        }
    });
});

// спливні сповіщення
function showToast(message, iconClass) {
    var $toast = $('#toast');
    var $message = $toast.find('.message');
    var $icon = $toast.find('.icon-box');

    $message.text(message);
    $icon.removeClass().addClass('icon-box ' + iconClass);

    $toast.css({
        opacity: 0,
        bottom: '0',
        visibility: 'visible'
    });

    $toast.animate({
        opacity: 1,
        bottom: '30px'
    }, 400);

    var timeoutId = setTimeout(function() {
        $toast.animate({
            opacity: 0,
            bottom: '0'
        }, 400, function() {
            $toast.css('visibility', 'hidden');
        });
    }, 2500);

    $toast.hover(
        function() {
            clearTimeout(timeoutId);
        },
        function() {
            timeoutId = setTimeout(function() {
                $toast.animate({
                    opacity: 0,
                    bottom: '0'
                }, 400, function() {
                    $toast.css('visibility', 'hidden');
                });
            }, 2500);
        }
    );
}
const IconTypes = {
    SUCCESS: 'success',
    ERROR: 'error',
    INFO: 'info',
    WARNING: 'warning'
};


// slider
$(document).ready(function () {
    var totalLinks = $('.slider-links li').length;
    var visibleLinks = 5; // Кількість видимих посилань
    var currentIndex = 0;

    // Оновлюємо видимі посилання
    function updateSlider() {
        $('.slider-links li').each(function (index) {
            var position = (index - currentIndex + totalLinks) % totalLinks;

            if (position >= 0 && position < visibleLinks) {
                $(this).css({
                    'display': 'inline-block', // Показуємо
                    'opacity': 1 - (position * 0.2), // Регулюємо прозорість
                    'filter': 'brightness(' + (1 - position * 0.1) + ')'
                });
            } else {
                $(this).css({
                    'display': 'none' // Ховаємо інші
                });
            }
        });
    }

    // Логіка для кнопки "Вперед"
    $('.next').click(function () {
        currentIndex = (currentIndex + 1) % totalLinks; // Кругове прогортання вперед
        updateSlider();
    });

    // Логіка для кнопки "Назад"
    $('.prev').click(function () {
        currentIndex = (currentIndex - 1 + totalLinks) % totalLinks; // Кругове прогортання назад
        updateSlider();
    });

    // Ініціалізація слайдера
    updateSlider();
});
