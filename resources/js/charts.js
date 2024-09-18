// Укладач вмісту
if (document.getElementById('content_maker_activity_on_types')){
let content_maker_activity_on_types = new Chart(document.getElementById('content_maker_activity_on_types').getContext('2d'), {
    type: 'bar',
    data: {
        datasets: [{
            label: 'Додані матеріали',
            data: content_maker_activity_on_types_data,
            backgroundColor: 'rgba(24, 31, 37, 0.75)',
            borderColor: 'black',
            borderWidth: 1,
            datalabels: {
                color: 'red'
              }
        }]
    }
});
}

// Профіль користувача
// 
// Оцінки
if (document.getElementById('ratings_statistic')){
let profile_ratings = new Chart(document.getElementById('ratings_statistic').getContext('2d'), {
    type: 'bar',
    data: {
        datasets: [{
            label: 'Оцінки',
            data: ratings_statistic_data,
            backgroundColor: 'rgba(24, 31, 37, 0.75)',
            borderColor: 'black',
            borderWidth: 1,
            datalabels: {
                color: 'red'
              }
        }]
    }
});
}