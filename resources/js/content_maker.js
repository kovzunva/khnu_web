// Присвоєння фукнцій на кнопки 
$(document).ready(function() {
    // Додавання ел
    $('#btn_p_alt_name_add').click(function() {
        AddElInsert('p_alt_name');
    });
    $('#btn_w_alt_name_add').click(function() {
        AddElInsert('w_alt_name');
    });
    $('#btn_anotation_add').click(function() {
        AddElInsertBig('anotation');
    });
    
    // Видалення ел
    $('.btn-remove-el-inserted').click(function() {
        DelElInserted('p_alt_name',this);
        DelElInserted('w_alt_name',this);
        DelElInserted('avtor_work',this);
        DelElInserted('work_cycle',this);
        DelElInserted('avtor',this);
        DelElInserted('designer',this);
        DelElInserted('illustrator',this);
        DelItemInserted('item',this);
        DelElInsertedBig('anotation',this);
    });
    $('.btn-remove-translator-inserted').click(function() {
        let el = "translator";
        let btt = this;

        $(btt).closest('.el-inserted').addClass('hide-post');
        let parentElement = $(btt.closest('.insert-item'));
    
        let regex = new RegExp(`^${el}.*\\[it_id\\]$`);
        let input = parentElement.find('input[name]').filter(function() {
            return regex.test($(this).attr('name'));
        });
        let currentName = input.attr('name');
        let newName = `del_${currentName}`;
        input.attr('name', newName);
    
        regex = new RegExp(`^${el}.*\\[tr_id\\]$`);
        input = parentElement.find('input[name]').filter(function() {
            return regex.test($(this).attr('name'));
        });
        currentName = input.attr('name');
        newName = `del_${currentName}`;
        input.attr('name', newName);
    });

    // Редагування ел
    $('.btn-edit-el-inserted').click(function() {
        EditElInsertedBig('anotation',this);
    });
    $('.btn-edit-item').click(function() {
        EditInsertedItem('item',this);
    });

    //Вставка в текстове поле
    $('#add_link_in_notes').click(function() {
        insertLink(this,'<a href="URL" target="_blank">Текст посилання</a>');
    });
    $('#add_link_in_links').click(function() {
        insertLink(this,'<a href="URL" target="_blank">Текст посилання</a>');
    });

    // КПЗ
    $('.btn-remove-el-inserted-kpz').click(function() {
        $(this).closest('.el-inserted').find('[name="rezult_id[]"]').attr('name','del_rezult_id[]');
        $(this).closest('.el-inserted').find('[name="question_id[]"]').attr('name','del_question_id[]');
        $(this).closest('.el-inserted').find('[name="answer_id[]"]').attr('name','del_answer_id[]');
    });
});

// кнопка для вставлення в текстове поле
function insertLink(btn,tag) {
    let buttonId = $(btn).attr('id');
    let textareaId = buttonId.replace('add_link_in_', '');
    let textarea = $("#" + textareaId);
    let linkText = prompt("Введіть текст посилання:", "");
    let linkUrl = prompt("Введіть URL посилання:", "http://");
    
    if (linkText && linkUrl) {
        let linkCode = tag.replace("Текст посилання", linkText).replace("URL", linkUrl);
        let currentText = textarea.val();
        let startPosition = textarea[0].selectionStart;
        let endPosition = textarea[0].selectionEnd;
        let newText = currentText.substring(0, startPosition) + linkCode + currentText.substring(endPosition);
        textarea.val(newText);
    }
}

// Прокрутка до блоку: Scroll
function ScrollTo(element){
    $(element).addClass("mt-minus-offset");
    element.scrollIntoView({ behavior: 'smooth', block: 'start' })
    $(element).removeClass("mt-minus-offset");
}

// Як називати елементи для додавання el-insert???
// кнопка для додавання: el_add
// контейнер: el_container
// додані елементи: el[]
// елементи для додавання: add_el[]
// елементи для видалення: del_el[]

// додавання el_insert
function AddElInsert(el){
    let $new_name = $('#'+el+'_add');
    if ($new_name.val()!=''){
        let $container = $('#'+el+'_container');
        // alert($container.attr('id'));
        let html = '<div class="el-insert input-group mb-1">'+
        '<input readonly type="text" class="form-control input-with-btt"'+
        'name="add_'+el+'[]" value="'+$new_name.val()+'">'+
        '<button type="button" class="btn btn-outline-secondary btt-with-input btn-remove-el-insert">Видалити</button>'+
        '</div>';
        $container.append(html);
        $new_name.val('');
    }
}
// додавання el_insert_big
function AddElInsertBig(el) {
    let $new_el = $('#'+el+'_add');
    if ($new_el.val()!=''){

        // Отримуємо посилання на контейнер, куди будемо додавати блоки
        let container = document.getElementById(el + '_container');

        // Створюємо блок textarea та кнопки "Видалити"
        let block = document.createElement('div');
        block.className = 'el-insert input-group-big mb-2';
        block.innerHTML = `
            <textarea rows="3" name="add_${el}[]">`+$new_el.val()+`</textarea>
            <div class='text-end'>
                <button type="button" class="btn btn-outline-secondary btt-with-input btn-remove-el-insert-big">
                    Видалити</button>
            </div>
        `;

        // Додаємо новий блок до контейнера
        container.appendChild(block);

        // Додаємо обробник подій для кнопки "Видалити"
        let deleteButton = block.querySelector('.btn-remove-el-insert-big');
        deleteButton.addEventListener('click', () => {
            container.removeChild(block); // Видаляємо блок
        });
        
        $new_el.val('');
    }
}

// Видалення блоку для el_insert
$(document).on('click', '.btn-remove-el-insert', function () {
    $(this).closest('.el-insert').remove();
});
// Видалення блоку для el_inserted
function DelElInserted(el,btt){
    $(btt).closest('.el-inserted').addClass('hide-post');
    $(btt).closest('.el-inserted').find('[name="'+el+'[]"]').attr('name','del_'+el+'[]');
}
// Видалення блоку для edition_item
function DelItemInserted(el,btt){
    $(btt).closest('.el-inserted').addClass('hide-post');
    let parentElement = $(btt.closest('.insert-item'));
    let regex = new RegExp(`^${el}.*\\[id\\]$`);
    let input = parentElement.find('input[name]').filter(function() {
        return regex.test($(this).attr('name'));
    });
    let currentName = input.attr('name');
    let newName = `del_${currentName}`;
    input.attr('name', newName);
}
// Редагування блоку для el_inserted_big
function DelElInsertedBig(el,btt){
    $(btt).closest('.el-inserted').addClass('hide-post');
    $(btt).closest('.el-inserted').find('[name="'+el+'_id[]"]').attr('name','del_'+el+'_id[]');
}

// Редагування блоку для el_inserted_big
function EditElInsertedBig(el,btt){
    $(btt).closest('.el-inserted').find('[name="'+el+'_id[]"]').attr('name','edit_'+el+'_id[]');
    $(btt).closest('.el-inserted').find('[name="'+el+'[]"]').removeAttr('readonly');
    $(btt).closest('.el-inserted').find('[name="'+el+'[]"]').attr('name','edit_'+el+'[]');
    $(btt).remove();
}
// Редагування блоку для edition_item
function EditInsertedItem(el,btt){
    let parentElement = btt.closest('.insert-item');

    if (parentElement) {
        let inputsToEdit = parentElement.querySelectorAll(`input[name^="${el}"]`);
        inputsToEdit.forEach(input => {
            input.removeAttribute('readonly');
            let currentName = input.getAttribute('name');
            let newName = `edit_${currentName}`;
            input.setAttribute('name', newName);
        });
    }
    $(btt).remove();
}



// Перевірка радіобатонів зображення
function CheckRadioButtons(){
    let imgPasses = document.getElementById('img_passes');
    let radioButtons = document.querySelectorAll('[name="is_main_img"]');
    let mainImgInput = imgPasses.querySelector('[name="main_img"]');
    if (radioButtons.length>0 && !Array.from(radioButtons).some(radioButton => radioButton.checked)) {
        radioButtons[0].checked = true;
        mainImgInput.value = 0;
    }
}

// ентер на кнопку у текстових полях
document.addEventListener('DOMContentLoaded', function() {
    let enterBtnInputs = document.querySelectorAll('.enter_btn');

    enterBtnInputs.forEach(input => {
        input.addEventListener('keydown', function(event) {
            if (event.key === 'Enter') {
                event.preventDefault();
                let btnId = 'btn_' + input.id;
                let EnterButton = document.getElementById(btnId);
                if (EnterButton) {
                    EnterButton.click(); // Викликаємо обробник кнопки
                }
            }
        });
    });
});

// Завантаження зображень
let img_index = 0;
function PrintNewImgEdit(imageURL, containerImg, imgPasses){
    let imgContainer = document.createElement('div');
    imgContainer.classList.add('edit-img-container');
    imgContainer.classList.add('container');

    let img = document.createElement('img');
    img.src = imageURL;
    imgContainer.appendChild(img);
    
    let btnContainer = document.createElement('div');
    btnContainer.classList.add('row');

    let containerCol1 = document.createElement('div');
    containerCol1.classList.add('col');
    containerCol1.classList.add('p-1');
    containerCol1.classList.add('d-flex');
    containerCol1.classList.add('align-items-center');
    containerCol1.classList.add('justify-content-center');

    let radioButton = document.createElement('input');
    radioButton.type = 'radio';
    radioButton.name = 'is_main_img';
    radioButton.id = 'is_new_main_img'+img_index;
    let radioButtonLabel = document.createElement('label');
    radioButtonLabel.textContent = 'Головна';
    radioButtonLabel.htmlFor = 'is_new_main_img'+img_index;
    img_index++;

    containerCol1.appendChild(radioButton);
    containerCol1.appendChild(radioButtonLabel);

    let containerCol2 = document.createElement('div');
    containerCol2.classList.add('col');
    containerCol2.classList.add('border-left');
    containerCol2.classList.add('d-flex');
    containerCol2.classList.add('p-none');

    let deleteButton = document.createElement('button');
    deleteButton.type = 'button';
    deleteButton.classList.add('edit-img-del-btn');
    deleteButton.addEventListener('click', function() {
        imgContainer.remove();
        imgPasses.removeChild(imgPathInput);
        CheckRadioButtons();
    });
    
    let deleteButtonText = document.createElement('i');
    deleteButtonText.classList.add('fa-solid');
    deleteButtonText.classList.add('fa-xmark');

    deleteButton.appendChild(deleteButtonText);
    containerCol2.appendChild(deleteButton);

    btnContainer.appendChild(containerCol1);
    btnContainer.appendChild(containerCol2);
    imgContainer.appendChild(btnContainer);
    containerImg.appendChild(imgContainer);

    let imgPathInput = document.createElement('input');
    imgPathInput.type = 'text';
    imgPathInput.value = imageURL;
    imgPathInput.name = 'imgs[]';
    imgPasses.appendChild(imgPathInput);
}
const fileInput = document.getElementById('file_img');
if (fileInput) {
  fileInput.addEventListener('change', function() {
    let containerImg = document.getElementById('container_img');
    let imgPasses = document.getElementById('img_passes');

    let imageFile = this.files[0];
    if (imageFile) {
      let fileReader = new FileReader();
      fileReader.onload = function(event) {
        let imageURL = event.target.result;
        PrintNewImgEdit(imageURL, containerImg, imgPasses);
      }
      fileReader.readAsDataURL(imageFile);
    }
  });
}
const urlInput = document.getElementById('url_img');
if (urlInput) {
  document.getElementById('btn_url_img').addEventListener('click', function() {
    let containerImg = document.getElementById('container_img');
    let imgPasses = document.getElementById('img_passes');

    let imageURL = urlInput.value.trim();

    if (imageURL !== '') {        
      PrintNewImgEdit(imageURL, containerImg, imgPasses);
      urlInput.value = '';
    }
  });
}

// Видалення зображень
const deleteButtons = document.querySelectorAll('.saved-img-del-btn');  
deleteButtons.forEach(button => {
    button.addEventListener('click', function (event) {
        event.preventDefault(); // Зупинити типову дію кнопки

        let imgPath = this.getAttribute('data-img');
        let imgPasses = document.getElementById('img_passes');
        let imgPathInput = document.createElement('input');
        imgPathInput.type = 'text';
        imgPathInput.value = imgPath;
        imgPathInput.name = 'del_imgs[]';
        imgPasses.appendChild(imgPathInput);

        let imgContainer = this.closest('.edit-img-container');
        imgContainer.remove();

        CheckRadioButtons();
    });
});

// Головна картинка
document.addEventListener('DOMContentLoaded', function() {
    let radioButtons = document.querySelectorAll('[name="is_main_img"]');

    radioButtons.forEach((radioButton, index) => {
        radioButton.addEventListener('click', () => {
            let imgPasses = document.getElementById('img_passes');
            let mainImgInput = imgPasses.querySelector('[name="main_img"]');
            mainImgInput.value = index;
        });
    });
});


// Автозаповнення при пошуку автора в редагуванні твору  
$(document).ready(function() {  
    $("#avtor_work").select2({
        ajax: {
            url: "/api/search/avtors",
            dataType: "json",
            delay: 250,
            processResults: function(data) {
            return {
                results: data.map(item => ({
                id: item.id,
                text: item.name+' (id = '+item.id+")"
            }))
            };
            },
            cache: true
        },
        placeholder: "Виберіть автора",
        minimumInputLength: 3,
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

    // кнопка видалення el-insert
    $("#avtor_work_container").on("click", ".btn-remove-el-insert", function() {
        $(this).closest(".el-insert").remove();
    });
    $("#avtor_work").on("select2:select", function(e) {
        let selectedItem = e.params.data;
        let isExisting = $("#avtor_work_container input[name='avtor_work[]'][value='" + selectedItem.id + "']").length > 0 ||
                      $("#avtor_work_container input[name='add_avtor_work[]'][value='" + selectedItem.id + "']").length > 0;
        if (!isExisting) {
            let newElement = $('<div class="el-insert input-group mb-1">' +
                '<input readonly type="text" class="hide" name="add_avtor_work[]" value="' + selectedItem.id + '">' +
                '<input readonly type="text" class="form-control input-with-btt" value="' + selectedItem.text + '">' +
                '<button type="button" class="btn btn-outline-secondary btt-with-input btn-remove-el-insert">Видалити</button>' +
                '</div>');
            $("#avtor_work_container").append(newElement);
        }
    });

});

// Автозаповнення при пошуку циклу в редагуванні твору  
$(document).ready(function() {  
    $("#work_cycle").select2({
        ajax: {
            url: "/api/search/cycles",
            dataType: "json",
            delay: 250,
            processResults: function(data) {
            return {
                results: data.map(item => ({
                id: item.id,
                text: item.name+' (id = '+item.id+")"
            }))
            };
            },
            cache: true
        },
        placeholder: "Виберіть цикл",
        minimumInputLength: 3,
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

    // кнопка видалення el-insert
    $("#work_cycle_container").on("click", ".btn-remove-el-insert", function() {
        $(this).closest(".el-insert").remove();
    });
    $("#work_cycle").on("select2:select", function(e) {
        let selected = e.params.data;
        let isExisting = $("#work_cycle_container input[name='work_cycle[]'][value='" + selected.id + "']").length > 0 ||
                      $("#work_cycle_container input[name='add_work_cycle[]'][value='" + selected.id + "']").length > 0;
        if (!isExisting) {
            let newElement = $('<div class="el-insert input-group mb-1">' +
                '<input readonly type="text" class="hide" name="add_work_cycle[]" value="' + selected.id + '">' +
                '<input readonly type="text" class="form-control input-with-btt" value="' + selected.text + '">' +
                '<button type="button" class="btn btn-outline-secondary btt-with-input btn-remove-el-insert">Видалити</button>' +
                '</div>');
            $("#work_cycle_container").append(newElement);
        }
    });

});

// Розгортання details, з яким працювали
$(document).ready(function() {
    if (typeof detailsElement !== 'undefined' && detailsElement) {
        detailsElement.open = true;
    }
});

// Автозаповнення при пошуку автора в редагуванні твору  
$(document).ready(function() {  
    $(".double-search").select2({
        ajax: {
            url: function() {
                var typeValue = $("form").find("input[name='type']").val();
                
                switch (typeValue) {
                    case "person":
                        return "/api/search/persons";
                    case "work":
                        return "/api/search/work-with-avtor";
                    case "edition":
                        return "/api/search/editions";
                    default:
                        return "/api/search/publishers";
                }
            },
            dataType: "json",
            delay: 250,
            processResults: function(data) {
                return {
                    results: data.map(item => ({
                        id: item.id,
                        text: item.name+' (id = '+item.id+")"
                    }))
                };
            },
            cache: true
        },
        placeholder: "Виберіть об'єкт дублю",
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

    $(".double-search").on("select2:select", function (e) {
        var data = e.params.data;
        var container = $(this).closest('.insert-group').find(".insert-container");
        var name = container.data('name');
        var html = '<input type="hidden" name="' + name + '" value="' + data.id + '">' +
                    '<input readonly type="text" value="' + data.text + '">';
        container.html(html);
    });
    
});

// планувала, але не використовується
// Вставка елемента
function InsertItem(data){
    var container = $(this).closest('.insert-group').find(".insert-container");
    var name = $(this).attr('name');
    var edit = $(this).attr('edit');
    var clear = $(this).attr('clear');
    var html = '<input type="hidden" name="' + name + '" value="' + data.id + '" class="required">' +
                '<input readonly type="text" value="' + data.text + '">';
    if (clear) container.empty();
    container.append(html);
}