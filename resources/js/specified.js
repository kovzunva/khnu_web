import $ from 'jquery';
window.jQuery = $;
window.$ = $;

import '../js/summernote-lite.min.js';

// Експертна система
if (document.getElementById('question'))
$(document).ready(function() {
    var sizeByResultId = {}; // масив просування по гілках

    function loadQuestion(line) {
        // береться і видаляється 1-ий елемент гілки, щоб визначити поточне запитання
        var currentQuestion = line.length ? line.shift() : questions.line_0.shift();
        var foundOtherQuestion = false; // чи є інше запитання
    
        if (currentQuestion) {
            var answersToCurrentQuestion = answers.filter(function(answer) {
                return answer.q_id === currentQuestion.id; // відповіді на це запитання
            });
    
            $('#question').text(currentQuestion.text);
            $('#answers').empty();
            answersToCurrentQuestion.forEach(function(answer) {
                $('#answers').append('<button class="answer-button transparent-btn" data-answer-id="' + answer.id + 
                    '" data-size-id="' + answer.size  + '" data-answer-rezult-id="' + answer.r_id + '">'  + answer.text + '</button>');
            });
            $('#answers').append('<button class="answer-button transparent-btn" data-answer-id="'+ 
                    '" data-size-id="" data-answer-rezult-id="">Жоден варіант не підходить</button>');
        }
        else { // гілка кінчилась
            for (var key in questions) { // пошук першого ліпшого питання
                if (key !== 'line_0' && questions[key].length > 0) {
                    foundOtherQuestion = true;
                    currentLine = questions[key];
                    loadQuestion(currentLine);
                    break;
                }
            }
    
            if (!foundOtherQuestion) { // запитання кінчилися
                if (Object.keys(sizeByResultId).length) // є просування по жанрах
                {
                    // максимальний результат
                    var maxResultId = Object.keys(sizeByResultId).reduce(function(a, b) {
                        return sizeByResultId[a] > sizeByResultId[b] ? a : b;
                    });
        
                    // назва жанру, який набрав максимум
                    var resultName = rezults.find(function(result) {
                        return result.id == maxResultId;
                    });
        
                    if (resultName) {
                        $('#question').html('Вітаємо! Ваш жанр: ' + resultName.name + ' фентезі<br><br>' + generateStatistics());
        
                        $('#answers').empty();
                        $('#answers').append('<button id="reload-button" class="reload-button transparent-btn">Спробувати ще раз</button>');
                        $('#reload-button').on('click', function() {
                            location.reload();
                        });
                    }
                }
                else { // всі просування нульові
                    $('#question').html('Вітаємо! Фентезі Вам не підходить');
        
                        $('#answers').empty();
                        $('#answers').append('<button id="reload-button" class="reload-button transparent-btn">Спробувати ще раз</button>');
                        $('#reload-button').on('click', function() {
                            location.reload();
                        });
                }
            }
        }
    }
    

    function generateStatistics() { // кінцева статистика
        var totalSize = Object.values(sizeByResultId).reduce(function(a, b) {
            return a + b;
        }, 0); // сума просування
    
        var statisticsHtml = '<strong>Статистика:</strong><br>';
        for (var resultId in sizeByResultId) {
            if (sizeByResultId.hasOwnProperty(resultId)) {
                var resultName = rezults.find(function(result) {
                    return result.id === parseInt(resultId);
                });
    
                if (resultName) {
                    // вирахування відсотків
                    var percentage = (sizeByResultId[resultId] / totalSize * 100).toFixed(2);
                    statisticsHtml += resultName.name + ': ' + percentage + '%<br>';
                }
            }
        }
    
        return statisticsHtml;
    }   

    if (questions) {
        // запитання при завантаженні сторінки
        var currentLine = questions.line_0;
        loadQuestion(currentLine);

        // вибір відповіді
        $('#answers').on('click', '.answer-button', function() {
            var answerSize = $(this).data('size-id');
            if (answerSize){ // обрано відповідь з просуванням        
                var resultId = $(this).data('answer-rezult-id');
                
                if (!sizeByResultId.hasOwnProperty(resultId)) {
                    sizeByResultId[resultId] = 0; // ініціалізація гілки
                }
                sizeByResultId[resultId] += answerSize; // просування по гілці
                
                // просування набрало 5 балів
                if (Object.values(sizeByResultId).some(function(size) { return size >= 5; })) {
                    var resultName = rezults.find(function(result) {
                        return result.id == resultId;
                    });
                    if (resultName) {
                        $('#question').html('Вітаємо! Ваш жанр: ' + resultName.name + ' фентезі<br><br>' + generateStatistics());

                        $('#answers').empty();
                        $('#answers').append('<button id="reload-button" class="reload-button transparent-btn">Спробувати ще раз</button>');
                        $('#reload-button').on('click', function() {
                            location.reload();
                        });
                    }
                }
                else { // наступне запитання
                    var maxResultId = Object.keys(sizeByResultId).reduce(function(a, b) {
                        // пошук гілки з максимальним просуванням
                        return sizeByResultId[a] > sizeByResultId[b] ? a : b;
                    });
                    // вибір гілки
                    currentLine = questions['line_'+maxResultId] || questions.line_0;
                    loadQuestion(currentLine);
                }
            }
            else loadQuestion(currentLine); // була відповідь без просування
        });
    }
});

// текстовий редактор
$('.text-editor').summernote({
    styleTags: [
            'p',
            { title: 'Blockquote', tag: 'blockquote', className: 'blockquote', value: 'blockquote' },
            'h3', 'h4', 'h5'
        ],
    toolbar: [
        ['style', ['style']],
        ['font', ['bold', 'italic', 'underline', 'strikethrough', 'clear']],
        ['para', ['ul', 'ol', 'paragraph']],
        ['table', ['table']],
        ['insert', ['hr', 'link', 'picture', 'video']],
        ['view', ['codeview']],
    ],
});