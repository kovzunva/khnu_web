<?php
return [
    'menu_links' => [
        ['text' => 'Головна', 'url' => '/', 'icon' => 'fa-solid fa-house'],
        ['text' => 'Персони', 'url' => '/persons', 'icon' => 'fa-solid fa-feather'],
        ['text' => 'Твори', 'url' => '/works', 'icon' => 'fa-solid fa-book'],
        ['text' => 'Видавництва', 'url' => '/publishers', 'icon' => 'fa-solid fa-book'],
        ['text' => 'Блоги', 'url' => '/blogs', 'icon' => 'fa-solid fa-scroll'],
        ['text' => 'Користувачі', 'url' => '/profiles', 'icon' => 'fa-solid fa-users'],
        ['text' => 'Довідка', 'url' => '/faqs'],
    ],

    'profile_links' => [
        ['text' => 'Профіль', 'url' => '/profile', 'icon' => 'fa-solid fa-user'],
        ['text' => 'Відгуки', 'url' => '/profile/reviews', 'icon' => 'fa-solid fa-house'],
        ['text' => 'Полички', 'url' => '/profile/shelves', 'icon' => 'fa-solid fa-house'],
        ['text' => 'Блоги', 'url' => '/profile/blogs', 'icon' => 'fa-solid fa-feather'],
        ['text' => 'Зворотний зв\'язок', 'url' => '/profile/user-requests', 'icon' => 'fa-solid fa-users'],
        ['text' => 'Майстерня', 'url' => '/content-maker', 'icon' => 'fa-solid fa-scroll'],
    ],

    'admin_links' => [
        ['text' => 'Адмінка', 'url' => '/adminka'],
        ['text' => 'Категорії', 'url' => '/adminka/categories'],
        ['text' => 'Класифікатор', 'url' => '/adminka/classificator'],

        ['text' => 'Персони', 'url' => '/adminka/persons'],
        ['text' => 'Твори', 'url' => '/adminka/works'],
        ['text' => 'Видання', 'url' => '/adminka/editions'],
        ['text' => 'Видавництва', 'url' => '/adminka/publishers'],
        
        ['text' => 'Довідка', 'url' => '/adminka/faqs'],
        ['text' => 'Заявки', 'url' => '/adminka/user-requests'],
    ],

    'content_maker_links' => [
        ['text' => 'Майстерня', 'url' => '/content-maker'],
        ['text' => 'Персони', 'url' => '/content-maker/persons'],
        ['text' => 'Твори', 'url' => '/content-maker/works'],
        ['text' => 'Видання', 'url' => '/content-maker/editions'],
        ['text' => 'Видавництва', 'url' => '/content-maker/publishers'],

        // якщо Укладач вмісту:
        // - Затвердження
        // - Дублі
    ],
];
