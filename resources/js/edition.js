// Порядковий номер елемента вмісту видання
function GetLastItemNumber(){
    let blocks = $(".el-insert, .el-inserted");
    let lastItemNumber = 0;
    blocks.each(function() {
    let block = $(this);
    let itemNumbers = block.find("span.item-number");
    if (itemNumbers.length > 0) {
        let lastItemText = itemNumbers.last().text();
        let lastItem = parseInt(lastItemText.match(/\d+/)[0]);
        if (lastItem > lastItemNumber) {
        lastItemNumber = lastItem;
        }
    }
    });
    return lastItemNumber;
} 

// Автозаповнення при пошуку автора в редагуванні видання  
$(document).ready(function() {  
    $("#avtor_edition").select2({
        ajax: {
            url: "/api/search/avtors",
            dataType: "json",
            delay: 250,
            processResults: function(data) {
            return {
                results: data.map(item => ({
                id: item.id,
                text: item.name+' (id = '+item.id+")",
                item_name: item.name
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
    $("#avtor_container").on("click", ".btn-remove-el-insert", function() {
        $(this).closest(".el-insert").remove();
    });
    $("#avtor_edition").on("select2:select", function(e) {
        let selectedItem = e.params.data;
        AddAvtorPost(selectedItem.id,selectedItem.text,selectedItem.item_name);
    });

});
function AddAvtorPost(id,text,item_name,work=null,about=null){
    if (item_name && item_name!="undefined"){
        let isExisting = $("#avtor_container input[name='add_avtor_id[]'][value='" + id + "']").length > 0 ||
        $("#avtor_container input[name='avtor[]'][value='" + id + "']").length > 0;
        if (!isExisting) {
            let newElement = $('<div class="el-insert input-group mb-1">' +
            '<input readonly type="text" class="hide" name="add_avtor_id[]" value="' + id + '">' +
            '<input readonly type="text" class="form-control input-with-btt" value="' + text + '">' +
            '<input type="text" class="form-control" name="add_avtor[]" value="' + item_name + '">' +
            '<button type="button" class="btt-with-input btn-remove-el-insert">Видалити</button>' +
            '</div>');
            $("#avtor_container").append(newElement);
        }
    }
}

// Автозаповнення при пошуку видавництва в редагуванні видання  
$(document).ready(function() {  
    $("#publisher_edition").select2({
        ajax: {
            url: "/api/search/publishers",
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
        placeholder: "Виберіть видавництво",
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

    $("#publisher_edition").on("select2:select", function(e) {
        let selectedPublisher = e.params.data;
        AddPublisherPost(selectedPublisher.id,selectedPublisher.text);
    });
});
function AddPublisherPost(id,text){
    $("#publisher_edition_container").empty();
    let publisherNameInput = $('<input readonly type="text" value="' + text + '">');
    let publisherIdInput = $('<input type="hidden" name="publisher_id" value="' + id + '">');
    
    $("#publisher_edition_container").append(publisherNameInput);
    $("#publisher_edition_container").append(publisherIdInput);
}

// Автозаповнення при пошуку дизайнера в редагуванні видання  
$(document).ready(function() {  
    $("#designer_edition").select2({
        ajax: {
            url: "/api/search/designers",
            dataType: "json",
            delay: 250,
            processResults: function(data) {
            return {
                results: data.map(item => ({
                id: item.id,
                text: item.name+' (id = '+item.id+")",
                item_name: item.name
            }))
            };
            },
            cache: true
        },
        placeholder: "Виберіть дизайнера",
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
    $("#designer_container").on("click", ".btn-remove-el-insert", function() {
        $(this).closest(".el-insert").remove();
    });
    $("#designer_edition").on("select2:select", function(e) {
        let selectedItem = e.params.data;
        let isExisting = $("#designer_container input[name='add_designer_id[]'][value='" + selectedItem.id + "']").length > 0 ||
                      $("#designer_container input[name='designer[]'][value='" + selectedItem.id + "']").length > 0;
        if (!isExisting) {
            let newElement = $('<div class="el-insert input-group mb-1">' +
                '<input readonly type="text" class="hide" name="add_designer_id[]" value="' + selectedItem.id + '">' +
                '<input readonly type="text" class="form-control input-with-btt" value="' + selectedItem.text + '">' +
                '<input type="text" class="form-control input-with-btt" name="add_designer[]" value="' + selectedItem.item_name + '">' +
                '<button type="button" class="btt-with-input btn-remove-el-insert">Видалити</button>' +
                '</div>');
            $("#designer_container").append(newElement);
        }
    });

});

// Автозаповнення при пошуку ілюстратора в редагуванні видання  
$(document).ready(function() {  
    $("#illustrator_edition").select2({
        ajax: {
            url: "/api/search/illustrators",
            dataType: "json",
            delay: 250,
            processResults: function(data) {
            return {
                results: data.map(item => ({
                id: item.id,
                text: item.name+' (id = '+item.id+")",
                item_name: item.name
            }))
            };
            },
            cache: true
        },
        placeholder: "Виберіть ілюстратора",
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
    $("#illustrator_container").on("click", ".btn-remove-el-insert", function() {
        $(this).closest(".el-insert").remove();
    });
    $("#illustrator_edition").on("select2:select", function(e) {
        let selectedItem = e.params.data;
        let isExisting = $("#illustrator_container input[name='add_illustrator_id[]'][value='" + selectedItem.id + "']").length > 0 ||
                      $("#illustrator_container input[name='illustrator[]'][value='" + selectedItem.id + "']").length > 0;
        if (!isExisting) {
            let newElement = $('<div class="el-insert input-group mb-1">' +
                '<input readonly type="text" class="hide" name="add_illustrator_id[]" value="' + selectedItem.id + '">' +
                '<input readonly type="text" class="form-control input-with-btt" value="' + selectedItem.text + '">' +
                '<input type="text" class="form-control input-with-btt" name="add_illustrator[]" value="' + selectedItem.item_name + '">' +
                '<button type="button" class="btt-with-input btn-remove-el-insert">Видалити</button>' +
                '</div>');
            $("#illustrator_container").append(newElement);
        }
    });

});

// Автозаповнення при пошуку твору в редагуванні видання  
$(document).ready(function() {  
    $("#edition_item").select2({
        ajax: {
            url: "/api/search/works",
            dataType: "json",
            delay: 250,
            processResults: function(data) {
            return {
                results: data.map(item => ({
                id: item.id,
                text: item.avtors+' «'+item.name+'» (id = '+item.id+")",
                item_name: item.avtors+' «'+item.name+'»'
            }))
            };
            },
            cache: true
        },
        placeholder: "Виберіть твір",
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
    $("#item_container").on("click", ".btn-remove-el-insert", function() {
        $(this).closest(".el-insert").remove();
    });

    $("#edition_item").on("select2:select", function(e) {
        let selectedItem = e.params.data;
        AddWorkPost(selectedItem.id,selectedItem.text,selectedItem.item_name);
    });
});
function AddWorkPost(id,text,item_name,avtor=null,translator=null){
    let itemNumber = GetLastItemNumber()+1;
    let newElement = $(`
    <div class="el-insert insert-item mb-3" data-item-number="`+itemNumber+`">
        <li>Елемент № <span class='item-number'>`+itemNumber+`</span></li>
        <input readonly type="hidden" name="add_item[`+itemNumber+`][w_id]" value="`+id+`">

        <div class="mb-2 mt-2">
            <label for="" class="form-label">Назва в базі</label>
            <input readonly type="text" class="form-control" value="`+text+`">
        </div>

        <div class="mb-2">
            <label for="" class="form-label">Назва в виданні</label>
            <input type="text" class="form-control" name="add_item[`+itemNumber+`][name]" value="`+avtor+` «`+item_name+`»">
        </div>

        <div class="mb-2">
            <label for="" class="form-label">Додайте перекладача</label>
            <input type="text" class="form-control add_item_translator_add" value="">
            <div class="mt-2 translator_container">`+
            ((translator)? 
            `<div class="el-insert input-group mb-1">
                <input readonly type="hidden" name="add_item[`+itemNumber+`][translator][`+translator.id+`][tr_id]" 
                value="`+translator.id+`">
                <input readonly type="text" class="form-control input-with-btt" value="`+translator.name+` (id = `+translator.id+`)">
                <input type="text" class="form-control input-with-btt" 
                name="add_item[`+itemNumber+`][translator][`+translator.id+`][name]" value="`+translator.name+`">
                <button type="button" class="btt-with-input btn-remove-el-insert">Видалити</button>
            </div>`
        :``)+`</div>
        </div>

        <div class="mb-2">
            <label for="" class="form-label">Сторінки</label>
            <input type="text" class="form-control" name="add_item[`+itemNumber+`][pages]" value="">
        </div>

        <div class="mb-2">
            <label for="" class="form-label">Рівень </label>
            <input type="text" class="number" name="add_item[`+itemNumber+`][level]" value="1">
        </div>

        <div class="mb-2">
            <label for="" class="form-label">Позиція</label>
            <input type="text" class="number" name="add_item[`+itemNumber+`][number]" value="`+itemNumber+`">
        </div>

        <div class='text-end'>
            <button type="button" class="btn-remove-el-insert">Видалити</button>
        </div>
    </div>`);

    $("#item_container").append(newElement);

    // Автозаповнення при пошуку перекладача в елементі видання, який додається    
    newElement.find(".add_item_translator_add").select2({
        ajax: {
            url: "/api/search/translators",
            dataType: "json",
            delay: 250,
            processResults: function(data) {
                return {
                    results: data.map(item => ({
                        id: item.id,
                        text: item.name + ' (id = ' + item.id + ")",
                        item_name: item.name
                    }))
                };
            },
            cache: true
        },
        placeholder: "Виберіть перекладача",
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
                let message = "Мінімум символів для пошуку: " + args.minimum;
                return message;
            },
        }
    });

    newElement.find(".el-insert").on("click", ".btn-remove-el-insert", function() {
        $(this).closest(".el-insert").remove();
    });

    newElement.find(".add_item_translator_add").on("select2:select", function(e) {
        let selectedItem = e.params.data
        AddTranslatorPost(selectedItem.id,selectedItem.text,selectedItem.item_name,itemNumber);
    });
}
function AddTranslatorPost(id,text,item_name,itemNumber){
    $(this).closest(".insert-item").find(".translator_container").append(`            
        <div class="el-insert input-group mb-1">
            <input readonly type="hidden" name="add_item[`+itemNumber+`][translator][`+id+`][tr_id]" 
            value="`+id+`">
            <input readonly type="text" class="form-control input-with-btt" value="`+text + `">
            <input type="text" class="form-control input-with-btt" 
            name="add_item[`+itemNumber+`][translator][`+id+`][name]" value="`+item_name+`">
            <button type="button" class="btt-with-input btn-remove-el-insert">Видалити</button>
        </div>
        `);
}

// Автозаповнення при пошуку перекладача в редагуванні вмісту видання  
$(document).ready(function() {
    $(".insert-item").each(function() {
        let elInsert = $(this);

        elInsert.find(".item_translator_add").select2({
            ajax: {
                url: "/api/search/translators",
                dataType: "json",
                delay: 250,
                processResults: function(data) {
                    return {
                        results: data.map(item => ({
                            id: item.id,
                            text: item.name + ' (id = ' + item.id + ")",
                            item_name: item.name
                        }))
                    };
                },
                cache: true
            },
            placeholder: "Виберіть перекладача",
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
                    let message = "Мінімум символів для пошуку: " + args.minimum;
                    return message;
                },
            }
        });

        elInsert.find(".el-insert").on("click", ".btn-remove-el-insert", function() {
            $(this).closest(".el-insert").remove();
        });

        elInsert.find(".item_translator_add").on("select2:select", function(e) {
            let selectedItem = e.params.data;
            let itemId = $(this).closest('.insert-item').data('item-id');
            if (selectedItem.item_name){
            $(this).closest(".insert-item").find(".translator_container").append(`            
                <div class="el-insert input-group mb-1">
                    <input readonly type="hidden" name="add_translator[`+itemId+`_`+selectedItem.id+`][it_id]" value="`+itemId+`">
                    <input readonly type="hidden" name="add_translator[`+itemId+`_`+selectedItem.id+`][tr_id]" value="`+selectedItem.id+`">
                    <input readonly type="text" class="form-control input-with-btt" value="`+selectedItem.text + `">
                    <input type="text" class="form-control input-with-btt" 
                    name="add_translator[`+itemId+`_`+selectedItem.id+`][name]" value="`+selectedItem.item_name+`">
                    <button type="button" class="btt-with-input btn-remove-el-insert">Видалити</button>
                </div>
            `);
            }
        });
    });
});

// Пошук по випадному списку
function searchAndSetOption(listId, searchText) {
    const list = document.querySelector(`#${listId}`);
    if (!list || !searchText) {
        return false;
    }

    const options = list.querySelectorAll('li[data-value]');
    for (const option of options) {
        if (option.textContent.trim().toLowerCase() === searchText.toLowerCase()) {
            const selectedOption = list.querySelector('.selected-option');
            const hiddenInput = list.querySelector('input[type="hidden"]');            
                        
            selectedOption.textContent = option.textContent;
            hiddenInput.value = option.getAttribute('data-value');
            
            return true;
        }
    }
    
    return false;
}

// Індикатор завантаження
function ShowLoading(btn){
    $('#'+btn).addClass('with-spinner');
}
function HideLoading(btn){
    $('#'+btn).removeClass('with-spinner');
}

//
// Імпорт
$(document).ready(function() {
    $("#btn_url_import").click(function() {
        let url = $("#url_import").val();
        $("#error_import").hide();
        // $("#loading-indicator").show();
        ShowLoading('btn_url_import');
        clearFieldsAndContainers();  
        
        $.ajax({
            type: "POST",
            url: "/content-maker/import/edition",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: { url: url },
            success: function(data) {        
                // $("#loading-indicator").hide();
                if (data.error){
                    $("#error_import").html(data.error);
                    $("#error_import").show();   
                }      
                else {
                    console.log(data);
                    let edition = data.edition;
                    ImportPrePrint(edition);
                }
                HideLoading('btn_url_import'); 
            },
            error: function(error) {
                $("#error_import").show();        
                // $("#loading-indicator").hide();
                HideLoading('btn_url_import');
            }
        });
    });
});
// Виведення даних імпорту
function ImportPrePrint(edition){
    $("input[name='name']").val(edition.name);
    $("input[name='year']").val(edition.year);
    $("input[name='pages']").val(edition.pages);
    $("input[name='isbn']").val(edition.isbn);
    $("input[name='format']").val(edition.format);
    $("textarea[name='about']").val(edition.about);

    $("#import_add").html('');    

    // Чи нема ще такого видання
    $.ajax({
        type: "GET",
        url: "/api/search/editions",
        data: {
            name: edition.name,
            year: edition.year,
        },
        success: function(data) {
            if (data && data.length > 0) {
                if (confirm("Видання такого року з такою назвою вже знайдено в базі. Все одно продовжити? "+data)) {
                    ImportPrint(edition);
                }
            }
            else {
                ImportPrint(edition);
            }
            // $("#loading-indicator").hide();
            HideLoading('btn_url_import');
        },
        error: function(error) {
            // $("#loading-indicator").hide();
            HideLoading('btn_url_import');
        }
    });

}
function ImportPrint(edition){
    // Перекладач
    let translatorName = edition.translator;
    $.ajax({
        type: "GET",
        url: "/api/search/translators",
        data: {
            term: translatorName
        },
        success: function(data) {
            if (data.length > 0 && translatorName && translatorName!=undefined) {
                console.log(translatorName);
                // Якщо немає, створюємо форму для додавання
                let csrfToken = $('meta[name="csrf-token"]').attr('content');
                let formHtml = `
                    <form action="" method="POST" class="validate-form">
                        <input type="hidden" name="_token" value="${csrfToken}">
                        <hr>
                        <div class="mb-3">
                            <label for="persons" class="form-label">Додайте персону</label>
                            <div class="error-text hide error">Виникла помилка з додаванням персони</div>
                            <div class="success-text hide success">Персону додано успішно</div>
                            <div class="error-text hide error_name">Заповніть це поле</div>
                            <div class="input-group mb-1">
                                <input type="text" class="input-with-btt enter_btn" name="name" placeholder="Введіть персону" value="${translatorName}">
                                <button class="btt-with-input btn_person_add_tr" type="button"
                                data-to-delete="true">Додати</button>
                            </div>
                            <div class="mb-3 at_least_one person_type">
                                <div class="error-text hide error_person_type">Виберіть хоч один варіант</div>
                                <input type="checkbox" name="is_avtor" id="is_avtor" value="1">
                                <label for="is_avtor" class="form-label">Автор</label>
                                <input type="checkbox" name="is_translator" id="is_translator" value="1" checked>
                                <label for="is_translator" class="form-label">Перекладач</label>
                                <input type="checkbox" name="is_designer" id="is_designer" value="1">
                                <label for="is_designer" class="form-label">Дизайнер</label>
                                <input type="checkbox" name="is_illustrator" id="is_illustrator" value="1">
                                <label for="is_illustrator" class="form-label">Ілюстратор</label>
                            </div>
                        </div>
                    </form>
                `;
                

                $("#import_add").append(formHtml);
                $(".btn_person_add_tr").click(function() { personQuickAddBtn($(this)); });
            }
        },
        error: function(error) {
            // Обробка помилки
        }
    });

    // Автор (+ в ньому твір)
    let authorName = edition.avtor;
    $.ajax({
        type: "GET",
        url: "/api/search/avtors",
        data: {
            term: authorName
        },
        success: function(data) {
            // Автор є
            if (data.length > 0) {
                let avtor = data[0];
                AddAvtorPost(avtor.id,avtor.name+' (id = '+avtor.id+')',avtor.name,edition.work,edition.about);
                // Твір
                let workName = edition.name;
                $.ajax({
                    type: "GET",
                    url: "/api/search/work-with-avtor",
                    data: {
                        name: workName,
                        avtor: authorName,
                    },
                    success: function(data) {
                        if (data.length > 0) {
                            let work = data[0];
                            // Перекладач
                            if (translatorName)
                            $.ajax({
                                type: "GET",
                                url: "/api/search/translators",
                                data: {
                                    term: translatorName
                                },
                                success: function(translator) {
                                    if (translator.length > 0) AddWorkPost(work.id,work.name+' (id = '+work.id+')',work.name,authorName,translator[0]);
                                    else AddWorkPost(work.id,work.name+' (id = '+work.id+')',work.name,authorName);
                                },
                                error: function(error) {
                                    // Обробка помилки
                                }
                            });
                            else
                            AddWorkPost(work.id,work.name+' (id = '+work.id+')',work.name,authorName);
                        }
                        else {
                            workQuickAddForm(workName,avtor,edition.about);
                        }
                    },
                    error: function(error) {
                        // Обробка помилки
                    }
                });
            }
            // Якщо автора немає, створюємо форму для додавання автора
            else {
                let csrfToken = $('meta[name="csrf-token"]').attr('content');
                let formHtml = `
                    <form action="" method="POST" class="validate-form">
                        <input type="hidden" name="_token" value="${csrfToken}">
                        <hr>
                        <div class="mb-3">
                            <label for="persons" class="form-label">Додайте персону</label>
                            <div class="error-text hide error">Виникла помилка з додаванням персони</div>
                            <div class="success-text hide success">Персону додано успішно</div>
                            <div class="error-text hide error_name">Заповніть це поле</div>
                            <div class="input-group mb-1">
                                <input type="text" class="input-with-btt enter_btn" name="name" placeholder="Введіть персону" value="${authorName}">
                                <button class="btt-with-input btn_person_add_av" type="button"
                                data-to-delete="true">Додати</button>
                            </div>
                            <div class="mb-1">
                                <input type="text" name="alt_name" placeholder="Альтернативне ім'я">
                            </div>
                            <div class="mb-3 at_least_one person_type">
                                <div class="error-text hide error_person_type">Виберіть хоч один варіант</div>
                                <input type="checkbox" name="is_avtor" id="is_avtor" value="1" checked>
                                <label for="is_avtor" class="form-label">Автор</label>
                                <input type="checkbox" name="is_translator" id="is_translator" value="1">
                                <label for="is_translator" class="form-label">Перекладач</label>
                                <input type="checkbox" name="is_designer" id="is_designer" value="1">
                                <label for="is_designer" class="form-label">Дизайнер</label>
                                <input type="checkbox" name="is_illustrator" id="is_illustrator" value="1">
                                <label for="is_illustrator" class="form-label">Ілюстратор</label>
                            </div>
                        </div>
                    </form>
                `;
                

                $("#import_add").append(formHtml);
                $(".btn_person_add_av").click(function() { personQuickAddBtn($(this)); });
            }
        },
        error: function(error) {
            // Обробка помилки
        }
    });

    // Видавництво
    let publisherName = edition.publisher;
    $.ajax({
        type: "GET",
        url: "/api/search/publishers",
        data: {
            term: publisherName
        },
        success: function(data) {
            if (data.length > 0) {
                let publisher = data[0];
                AddPublisherPost(publisher.id,publisher.name);
            }
            else {
                // Якщо немає, створюємо форму для додавання
                let csrfToken = $('meta[name="csrf-token"]').attr('content');                
                let formHtml = `
                    <form action="" method="POST" class="validate-form">
                        <input type="hidden" name="_token" value="${csrfToken}">
                        <hr>
                        <div class="mb-3 mt-1">
                            <label for="publishers" class="form-label">Додайте видавництво</label>
                            <div class="error-text hide error">Виникла помилка з додаванням</div>
                            <div class="success-text hide success">Додано успішно</div>
                            <div class="error-text hide error_name">Заповніть це поле</div>
                            <div class="input-group mb-4">
                                <input type="text" class="input-with-btt enter_btn" name="name" placeholder="Введіть видавництво"
                                value="${publisherName}">
                                <button class="btt-with-input btn_publisher_add" type="button"
                                data-to-delete="true">Додати</button>
                            </div>
                        </div>
                    </form>
                `;
                

                $("#import_add").append(formHtml);
                $(".btn_publisher_add").click(function() { publisherQuickAddBtn($(this)); });
            }
        },
        error: function(error) {
            // Обробка помилки
        }
    });    

    // Випадні списки
    if (edition.language && !searchAndSetOption('language_select',edition.language)) {
        $("#import_add").append('<p>Потрібно додати мову: '+edition.language+'</p>');
    }
    if (edition.type_of_cover && !searchAndSetOption('type_of_cover_select',edition.type_of_cover)){
        $("#import_add").append('<p>Потрібно додати тип обкладинки: '+edition.type_of_cover+'</p>');
    }

    // Картинка
    if (edition.img !== '') {  
        let containerImg = document.getElementById('container_img');
        let imgPasses = document.getElementById('img_passes');   
        containerImg.innerHTML=''; 
        imgPasses.innerHTML='';
        PrintNewImgEdit(edition.img, containerImg, imgPasses);
    }
}
// Очистка полів і контейнерів
function clearFieldsAndContainers() {
    $("input[name='name']").val('');
    $("input[name='year']").val('');
    $("input[name='pages']").val('');
    $("input[name='isbn']").val('');
    $("textarea[name='about']").val('');
    $("#import_add").html('');
    $("#container_img").html('');
    $("#img_passes").html('');
    $("#avtor_container").html('');
    $("#publisher_edition_container").html('');
    $("#item_container").html('');
}
// Завантаження картинки
let img_index = 0;
function PrintNewImgEdit(imageURL, containerImg, imgPasses){
    let imgContainer = document.createElement('div');
    imgContainer.classList.add('edit-img-container');
    // imgContainer.classList.add('');

    let img = document.createElement('img');
    img.src = imageURL;
    imgContainer.appendChild(img);
    
    let btnContainer = document.createElement('div');
    btnContainer.classList.add('row');
    btnContainer.classList.add('edit-img-items');

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
// Перевірка радіобатонів
function CheckRadioButtons(){
    let imgPasses = document.getElementById('img_passes');
    let radioButtons = document.querySelectorAll('[name="is_main_img"]');
    let mainImgInput = document.querySelector('[name="main_img"]');
    if (radioButtons.length>0 && !Array.from(radioButtons).some(radioButton => radioButton.checked)) {
        radioButtons[0].checked = true;
        mainImgInput.value = 0;
    }
}

// Швидке додавання персони
function personQuickAdd(formData,parentForm,to_delete=false) {
    // $("#loading-indicator").show();
    ShowLoading('btn_url_import');
    parentForm.find(".error").hide();
    parentForm.find(".success").hide();
    $.ajax({
        type: "POST",
        url: "/content-maker/person/quick-add",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: formData,
        success: function(data) {
            if (data.error) {
                parentForm.find(".error").text(data.error).show();
            }
            else if (data.success) {
                if (to_delete){
                    parentForm.remove();
                }
                else {
                    parentForm.find(".success").text(data.success).show();
                    parentForm.find('[name="name"]').val('')
                }
            }
        },
        error: function(error) {
            parentForm.find(".error").show();
        }
    });
}
function personQuickAddBtn(btn){
    let parentForm = btn.closest('form');
    let toDelete = btn.data("to-delete");
    let formData = parentForm.serialize();
    parentForm.find(".error_name").hide();
    parentForm.find(".error_person_type").hide();
    if (parentForm.find('[name="name"]').val()=="") {
        parentForm.find(".error_name").show();
    }
    else if (
        formData.indexOf('is_avtor=') === -1 &&
        formData.indexOf('is_designer=') === -1 &&
        formData.indexOf('is_illustrator=') === -1 &&
        formData.indexOf('is_translator=') === -1
    )
    {
        parentForm.find(".error_person_type").show();
    }
    else {           
        // $("#loading-indicator").show();
        ShowLoading('btn_url_import');

        $.ajax({
            type: "GET",
            url: "/api/search/persons",
            data: {
                term: parentForm.find('[name="name"]').val()
            },
            success: function(data) {
                if (data && data.length > 0) {
                    if (confirm("Персону з таким ім'ям вже знайдено в базі. Все одно додати її?")) {
                        personQuickAdd(formData,parentForm,toDelete);
                    }
                } else {
                    personQuickAdd(formData,parentForm,toDelete);
                }
                // $("#loading-indicator").hide();
                HideLoading('btn_url_import');
            },
            error: function(error) {
                // $("#loading-indicator").hide();
                HideLoading('btn_url_import');
            }
        });
    }
}
$(document).ready(function() {
    $(".btn_person_add").click(function() {
        personQuickAddBtn($(this));
    });
});

// Швидке додавання видавництва
function publisherQuickAdd(formData,parentForm,to_delete=false) {
    // $("#loading-indicator").show();
    ShowLoading('btn_url_import');
    parentForm.find(".error").hide();
    parentForm.find(".success").hide();
    
    $.ajax({
        type: "POST",
        url: "/content-maker/publisher/quick-add",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: formData,
        success: function(data) {
            if (data.error) {
                parentForm.find(".error").text(data.error).show();
            }
            else if (data.success) {
                if (to_delete){
                    parentForm.remove();
                }
                else {
                    parentForm.find(".success").text(data.success).show();
                    parentForm.find('[name="name"]').val('')
                }
            }
        },
        error: function(error) {
            parentForm.find(".error").show();
        }
    });
}
function publisherQuickAddBtn(btn){
    let parentForm = btn.closest('form');
    let toDelete = btn.data("to-delete");
    let formData = parentForm.serialize();
    parentForm.find(".error_name").hide();

    if (parentForm.find('[name="name"]').val()=="") {
        parentForm.find(".error_name").show();
    }
    else {           
        // $("#loading-indicator").show();
        ShowLoading('btn_url_import');

        $.ajax({
            type: "GET",
            url: "/api/search/publishers",
            data: {
                term: parentForm.find('[name="name"]').val()
            },
            success: function(data) {
                if (data && data.length > 0) {
                    if (confirm("Персону з таким ім'ям вже знайдено в базі. Все одно додати її?")) {
                        publisherQuickAdd(formData,parentForm,toDelete);
                    }
                } else {
                    publisherQuickAdd(formData,parentForm,toDelete);
                }
                // $("#loading-indicator").hide();
                HideLoading('btn_url_import');
            },
            error: function(error) {
                // $("#loading-indicator").hide();
                HideLoading('btn_url_import');
            }
        });
    }
}
$(document).ready(function() {
    $(".btn_publisher_add").click(function() {
        publisherQuickAddBtn($(this));
    });
});

// Автозаповнення автора при швидкому додаванні твору
$(document).ready(function() {  
    $(".work-avtor-add").select2({
        ajax: {
            url: "/api/search/avtors",
            dataType: "json",
            delay: 250,
            processResults: function(data) {
            return {
                results: data.map(item => ({
                id: item.id,
                text: item.name+' (id = '+item.id+")",
                item_name: item.name
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
    $(".work-avtors").on("click", ".btn-remove-el-insert", function() {
        $(this).closest(".el-insert").remove();
    });
    $(".work-avtor-add").on("select2:select", function(e) {
        let selectedItem = e.params.data;
        let newElement = $('<div class="el-insert input-group mb-1">' +
        '<input readonly type="text" class="hide" name="avtor_id[]" value="' + selectedItem.id + '">' +
        '<input readonly type="text" class="form-control input-with-btt" value="' + selectedItem.text + '">' +
        '<button type="button" class="btt-with-input btn-remove-el-insert">Видалити</button>' +
        '</div>');
        $(this).closest('.input-group-big').find(".work-avtors").append(newElement);
    });
});
// Швидке додавання твору
function workQuickAdd(formData,parentForm,to_delete=false) {
    // $("#loading-indicator").show();
    ShowLoading('btn_url_import');
    parentForm.find(".error").hide();
    parentForm.find(".success").hide();
    
    $.ajax({
        type: "POST",
        url: "/content-maker/work/quick-add",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: formData,
        success: function(data) {
            if (data.error) {
                parentForm.find(".error").text(data.error).show();
            }
            else if (data.success) {
                if (to_delete){
                    parentForm.remove();
                }
                else {
                    parentForm.find(".success").text(data.success).show();
                    parentForm.find('[name="name"]').val('');
                    parentForm.find('[name="anotation"]').val('');
                    parentForm.find('.work-avtors').html('');
                }
            }
        },
        error: function(error) {
            parentForm.find(".error").show();
        }
    });
}
function workQuickAddBtn(btn){
    let parentForm = btn.closest('form');
    let toDelete = btn.data("to-delete");
    let formData = parentForm.serialize();
    parentForm.find(".error_name").hide();
    parentForm.find(".error-avtor").hide();

    if (parentForm.find('[name="name"]').val()=="") {
        parentForm.find(".error_name").show();
    }
    else if (!parentForm.find('[name="avtor_id[]"]').length > 0){
        parentForm.find(".error-avtor").show();
    }
    else {           
        // $("#loading-indicator").show();
        ShowLoading('btn_url_import');

        $.ajax({
            type: "GET",
            url: "/api/search/works",
            data: {
                term: parentForm.find('[name="name"]').val()
            },
            success: function(data) {
                if (data && data.length > 0) {
                    if (confirm("Твір такого автора з такою назвою вже знайдено в базі. Все одно додати його?")) {
                        workQuickAdd(formData,parentForm,toDelete);
                    }
                }
                else {
                    workQuickAdd(formData,parentForm,toDelete);
                }
                // $("#loading-indicator").hide();
                HideLoading('btn_url_import');
            },
            error: function(error) {
                // $("#loading-indicator").hide();
                HideLoading('btn_url_import');
            }
        });
    }
}
$(document).ready(function() {
    $(".btn_work_add").click(function() {
        workQuickAddBtn($(this));
    });
});
function workQuickAddForm(workName,avtor,about){
    let csrfToken = $('meta[name="csrf-token"]').attr('content');     
    let genres = '';
    genres_for_work_item.forEach(genre => {
        genres += `<li data-value="${genre.id}">${genre.name}</li>`;
    });           
    let formHtml = `                  
        <form action="" method="POST" class="validate-form">                    
            <div class="mb-3 mt-1">
                <input type="hidden" name="_token" value="${csrfToken}">
                <hr>
                <label for="editions" class="form-label">Додайте твір</label>
                <div class="error-text hide error_name">Заповніть це поле</div>
                <div class="input-group-big mb-4">
                    <input type="text" class="input-with-btt enter_btn mb-1" name="name" placeholder="Введіть твір"
                    value="${workName}">
                    <div class="mb-1">
                        <input type="text" name="alt_name" placeholder="Альтернативна назва">
                    </div>
                    <div class="mb-1">
                        <div class="base-select">
                            <div class="select-box">
                                <span class="selected-option d-flex align-items-center">Виберіть жанр</span>
                                <ul class="options hide">
                                <li data-value="">Інше</li>
                                ${genres}
                                </ul>
                            </div>
                            <input type="hidden" name="genre_id">
                        </div>                      
                    </div>
                    <div class="work-avtors mt-1">
                        <div class="el-insert mb-1">
                            <input readonly type="text" class="hide" name="avtor_id[]" value="${avtor.id}">
                            <input readonly type="text" class="form-control"  value="${avtor.name} (id = ${avtor.id})">
                        </div>
                    </div>
                    <textarea class="mt-1" name="anotation" rows="4" placeholder="Введіть анотацію">${about}</textarea>
                    <div class='text-end mt-1'>
                        <button class="btn_work_add" type="button" data-to-delete="true">Додати</button>
                    </div>
                </div>
            </div>
        </form>
    `;
    

    $("#import_add").append(formHtml);
    $(".btn_work_add").click(function() { workQuickAddBtn($(this)); });
    $(".base-select").on("click", ".selected-option", function() {
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
}