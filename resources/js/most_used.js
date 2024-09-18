// import 'bootstrap/dist/js/bootstrap.min.js';

import $ from 'jquery';
window.jQuery = $;
window.$ = $;

import select2 from 'select2';
select2();

// Перевірки при надсиланні форми
$(document).ready(function() {
  $(".validate-form").each(function() {
      $(this).submit(function(event) {
          var hasError = false;
          $(".form-error").remove();

          // Обов'язкові поля
          $(this).find(".required").each(function() {
            var input = $(this);
            if (input.val().trim() === "") {
                FormError(input, 'required');
                hasError = true;
            }

            // Видалення помилки при зміні значення поля
            input.on('input', function() {
                removeFormError(input); // Видалити помилку, якщо значення поля змінюється
            });
        });

        // Поля з датою
        $(this).find(".input-date").each(function() {
            var input = $(this);
            var value = input.val().trim();
            var datePattern = /^\d{2}\.\d{2}\.\d{4}$/;

            if (!datePattern.test(value) && value !== '') {
                FormError(input, 'input-date');
                hasError = true;
            } else {
                var parts = value.split(".");
                var day = parseInt(parts[0], 10);
                var month = parseInt(parts[1], 10);

                if (day > 31 || month > 12) {
                    FormError(input, 'input-date');
                    hasError = true;
                }
            }

            // Видалення помилки при зміні значення поля
            input.on('input', function() {
                removeFormError(input); // Видалити помилку, якщо значення поля змінюється
            });
        });

          // Вибрати хоч один чекбокс
          $(".at-least-one").each(function() {
            var checkboxes = $(this).find('input[type="checkbox"]');
            var hasChecked = checkboxes.is(":checked");

            if (!hasChecked) {
                FormError($(this),'at-least-one');
                hasError = true;
            }

            // Видалення помилки при зміні стану чекбоксів
            checkboxes.on('change', function() {
                removeFormError($(this).closest(".at-least-one")); // Видалити помилку, якщо чекбокси змінили стан
            });
          });

          // Блоки, які не можуть бути пусті
          $(".no-empty").each(function() {
            var $block = $(this);
            var hasVisibleChild = false;
            $(this).children().each(function() {
                var hasHiddenClass = $(this).hasClass('hide') || $(this).hasClass('hide-post');
                var isHiddenAttributeSet = $(this).attr('hidden');

                if (!hasHiddenClass && !isHiddenAttributeSet) {
                    hasVisibleChild = true;
                    return false; // Виходимо з циклу, якщо знайдено видимий елемент
                }
            });

            if (!hasVisibleChild) {
                FormError($(this),'no-empty');
                hasError = true;
            }

            // Видалення помилки при зміні вмісту блоку
            $block.on('DOMSubtreeModified', function() {
                removeFormError($block);
            });
          });

          // Інші перевірки тут

          if (hasError) {
              var firstError = $(this).find(".form-error").first();
              ScrollTo(firstError.get(0));
              event.preventDefault();
          }
      });
  });
});
// Виведення помилки валідації форми
function FormError(input, attr){
  var errorMessageText = input.data(attr) || input.data('error') || "Тут щось негаразд";
  var errorMessage = $("<div class='form-error'></div>").text(errorMessageText);
  errorMessage.insertBefore(input);
}
function removeFormError(element){
  element.parent("div").find(".form-error").remove();
}

// Селект (випадний список)
$(document).ready(function() {
    $(".base-select").on("click", ".selected-option", function(event) {
      event.stopPropagation();
      var $baseSelect = $(this).closest(".base-select");
      $baseSelect.find(".options").toggleClass("hide");
    });
  
    $(".base-select").on("click", ".options li", function() {
        var $baseSelect = $(this).closest(".base-select");
        var selectedValue = $(this).data("value");
        $baseSelect.find("input[type='hidden']").val(selectedValue);
        $baseSelect.find(".selected-option").text($(this).text());
        $baseSelect.find(".options").addClass("hide");
            
        // фільтрування при зміні опції
        if ($(this).hasClass("change-to-submit")) {
            var $parentForm = $baseSelect.closest("form");
            if ($parentForm.length > 0) {
                $parentForm.submit();
            }
        }
    });

    $(document).on('click', function(event) {
      if (!$(event.target).closest('.options').length) {
          $('.options').addClass('hide');
      }
    });
});

// Випадний список (не для вибору)
$(document).ready(function() {
  $(".open-select").on("click", ".open-select-title", function(event) {
    event.stopPropagation();
    var $select = $(this).closest(".open-select");
    $select.find(".options").toggleClass("hide");
  });
});

// Кнопка з випадним списком
$(document).ready(function() {
  $('.custom-dropdown-btn').click(function(event) {    
      event.stopPropagation();
      let button = $(this);
      let dropdownMenu = button.closest('.options-btn').find('.custom-dropdown-menu');
      
      if (dropdownMenu && (button.is(event.target) || button.has(event.target).length > 0)) {
          toggleDropdown(dropdownMenu);
          event.preventDefault();
      }
      else {
          dropdownMenu.removeClass('show');
      }
  });

  $('.dropdown-item').click(function(event) {
      let option = $(this);
      let dropdownMenu = option.closest('.custom-dropdown-menu');
      toggleDropdown(dropdownMenu);
  });

  $(document).on('click', function(event) {
    if (!$(event.target).closest('.custom-dropdown-menu').length) {
        $('.custom-dropdown-menu').removeClass('show');
    }
  });
});

function toggleDropdown(dropdownMenu) {
    dropdownMenu.toggleClass('show');
}

// Введення дати
document.addEventListener('input', function(event) {
    if (event.target.classList.contains('input-date')) {
      const input = event.target;
      const value = input.value.replace(/\D/g, '');
      
      if (value.length >= 2 && value.length < 4) {
        input.value = value.slice(0, 2) + '.' + value.slice(2);
      }
      else if (value.length >= 4) {
        input.value = value.slice(0, 2) + '.' + value.slice(2, 4) + '.' + value.slice(4, 8);
      }
      else {
        input.value = value;
      }
      
    }
  });
document.addEventListener('keydown', function(event) {
if (event.target.classList.contains('input-date')) {
    if (event.key === 'Backspace' || event.key === 'Delete') {
    event.target.value = '';
    }
}
});

// Лише числа
const numberInputs = document.querySelectorAll('.number');
numberInputs.forEach(input => {
  input.addEventListener('input', function() {
    this.value = this.value.replace(/[^0-9]/g, '');
  });
});

// Лише числа з комою
const numberInputsDot = document.querySelectorAll('.number-dot');
numberInputsDot.forEach(input => {
    input.addEventListener('input', function() {
        let value = input.value;
        
        // Замінюємо всі символи, крім цифр і крапки, на порожній рядок
        value = value.replace(/[^0-9.]/g, '');

        // Перевіряємо, чи є більше однієї крапки
        if (value.split('.').length > 2) {
            const parts = value.split('.');
            value = parts[0] + '.' + parts.slice(1).join('');
        }

        // Перевіряємо, чи введено більше однієї крапки
        if (value.includes('.') && (value.match(/\./g) || []).length > 1) {
            value = value.substring(0, value.lastIndexOf('.'));
        }

        // Оновлюємо значення поля вводу
        input.value = value;
    });
});

// Кнопки-посилання з підтвердженням
$(document).ready(function() {
  $('.confirm-link').click(function(event) {
      let confirmationMessage = $(this).data('message');
      let confirmation = confirm(confirmationMessage);

      if (!confirmation) {
          event.preventDefault();
      }
  });
});
// Кнопки-посилання з введенням повідомлення
$(document).ready(function() {
  $(".input-link").click(function(e) {
      e.preventDefault();

      let message = $(this).data('message');
      let input = $(this).data('input')? $(this).data('input'):"";
      let userInput = prompt(message, input);

      if (userInput !== null && userInput.trim() !== "") {
          let link = $(this).attr('href');
          window.location.href = link + "?input=" + encodeURIComponent(userInput);
      }
  });
});


// картинка
function PrintNewImgEdit(imageURL){
  edit_img.src = imageURL;
  img_pass.value = imageURL;
}  
const fileInput = document.getElementById('file_img_1');
if (fileInput) {
fileInput.addEventListener('change', function() {

const imageFile = this.files[0];
if (imageFile) {
  const fileReader = new FileReader();
  fileReader.onload = function(event) {
  const imageURL = event.target.result;
  PrintNewImgEdit(imageURL);
  }
  fileReader.readAsDataURL(imageFile);
}
});
}
const urlInput = document.getElementById('url_img_1');
if (urlInput) {
  document.getElementById('btn_url_img_1').addEventListener('click', function() {
    const imageURL = urlInput.value.trim();

    if (imageURL !== '') {        
      PrintNewImgEdit(imageURL);
      urlInput.value = '';
    }
  });
}

// Кнопки перемикання меню
$(document).ready(function () {
  // Головне меню
  $('#main_menu_btn').on('click', function () {
    if ($('#main_menu').is(":visible")) {
      $('#main_menu').hide();
      $('#this_menu').show();
    }
    else {
      $('#main_menu').show();
      $('#this_menu').hide();
    }
    $('#profile_menu').hide();
  });

  // Профільне меню
  $('#profile_menu_btn').on('click', function () {
    if (!$('#profile_menu_btn').data('user')) {
        window.location.href = '/login';
        return;
    }

    if ($('#profile_menu').is(":visible")) {
        $('#profile_menu').hide();
        $('#this_menu').show();
    } else {
        $('#profile_menu').show();
        $('#this_menu').hide();
    }
    $('#main_menu').hide();
});

});

// Боковий блок, якщо пустий
$(document).ready(function () {
  if ($('#this_menu').text().trim() === '') {
    let mainMenuContent = $('#main_menu').html();
    $('#this_menu').html(mainMenuContent);
  }
});

// Оверлей під меню
$('.overlay-menu').on('show', function() {
  $('#overlay').show();
});

// Прокрутка до блоку: Scroll
function ScrollTo(element, offset = 50) {
  var originalMarginTop = $(element).css('margin-top');
  $(element).css('margin-top', `-${offset}px`);
  element.scrollIntoView({ behavior: 'smooth', block: 'start' });
  setTimeout(function() {
      $(element).css('margin-top', originalMarginTop);
  }, 100);
}


// Неактивна кнопка, якщо пусте поле
$(document).ready(function() {
  // Функція для перевірки поля вводу
  function checkInput() {
      $('.input-group').each(function() {
          var $input = $(this).find('.input-with-btt');
          var $button = $(this).find('.btt-with-input');
          if ($input.val().trim() === '') {
              $button.prop('disabled', true);
          } else {
              $button.prop('disabled', false);
          }
      });
  }

  // Виклик функції під час завантаження сторінки
  checkInput();

  // Додаємо подію input для полів вводу
  $('.input-with-btt').on('input', function() {
      checkInput();
  });
});

// Сповіщення при загрузці сторінки
$(document).ready(function() {
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

  if (typeof session_error !== 'undefined') {
      showToast(session_error, IconTypes.ERROR);
  }

  if (typeof session_success !== 'undefined') {
      showToast(session_success, IconTypes.SUCCESS);
  }
});
const IconTypes = {
  SUCCESS: 'success',
  ERROR: 'error',
  INFO: 'info',
  WARNING: 'warning'
};

// спливні підказки
document.addEventListener('DOMContentLoaded', function() {
  tippy(':not(.light-tippy)[title]', {                    
          allowHTML: true,
          delay: [100, 200],
          theme: 'custom',
          maxWidth: 200,
          content(reference) {
            const title = reference.getAttribute('title');
            reference.removeAttribute('title');
            return title;
        },
      });
});
document.addEventListener('DOMContentLoaded', function() {
  tippy('.light-tippy', {                    
          allowHTML: true,
          delay: [100, 200],
          theme: 'light',
          maxWidth: 200,
          content(reference) {
            const title = reference.getAttribute('title');
            reference.removeAttribute('title');
            return title;
        },
      });
});

// Кнопки вікривання/приховання вмісту
$(document).ready(function() {
    $('.show-btn').click(function() {
        var targetBlock = $(this).data('show-id');
        var $block = $('#' + targetBlock);
        if (!$block.is(':visible')) {
            $block.show();
            ScrollTo($block[0]);
        }
        else {
            $block.hide();
        }
    });    

    $('.hide-btn').click(function() {
        var targetBlock = $(this).data('hide-id');
        $('#' + targetBlock).hide();
    });

    $(".show-content-btn").click(function() {
        var target = $(this).data('target');
        $(".to-show-content").hide();
        $("#" + target).show();

        $(".show-content-btn").not(this).removeClass('selected-btn');
        $(this).addClass('selected-btn');
    });
});

// зміна url
$(document).ready(function() {
  $('.change-link').on('click', function(e) {
      var changeLink = $(this).data('change-link');
      var currentUrl = new URL(window.location.href);

      if (changeLink.startsWith('#')) {
          // Це посилання на блок
          currentUrl.hash = changeLink;
      } else if (changeLink.includes('=')) {
          // Це гет параметр
          var param = changeLink.split('=')[0];
          var value = changeLink.split('=')[1];
          currentUrl.searchParams.set(param, value);
      }

      window.history.pushState({}, '', currentUrl);
      console.log('URL оновлено:', currentUrl.toString());
  });
});

// початковий скрол до елемента
$(document).ready(function() {

  if (typeof session_scroll_to !== 'undefined') {
    ScrollTo($("#"+session_scroll_to)[0],20);
  }

  const urlParams = new URLSearchParams(window.location.search);  
  const scrollTo = urlParams.get('scroll-to');
  
  if (scrollTo) {
    const targetElement = $("#" + scrollTo);
    if (targetElement.length) {
      ScrollTo(targetElement[0], 20);
    }
  }
});