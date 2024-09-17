<?php

use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\MainPageController;
use App\Http\Controllers\PersonController;
use App\Http\Controllers\WorkController;
use App\Http\Controllers\EditionController;
use App\Http\Controllers\PublisherController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\ShelfController;
use App\Http\Controllers\UserRequestController;
use App\Http\Controllers\DoubleController;
use App\Http\Controllers\ContentMakerController;
use App\Http\Controllers\FAQController;

// Реєстрація, автентифікація

Auth::routes(['verify' => true]);

// Лише адміни
Route::prefix('adminka')->middleware(['auth', 'permissions:admin'])->group(function () {
    Route::get('', [AdminController::class, 'basic'])->name('adminka');
    Route::post('/delete-image', [AdminController::class, 'delImg'])->name('deleteImage');

    // Матеріали
    Route::get('/persons', [PersonController::class, 'showAll'])->name('person.showAll');
    Route::get('/works', [WorkController::class, 'showAll'])->name('work.showAll');
    Route::get('/editions', [EditionController::class, 'showAll'])->name('edition.showAll');
    Route::get('/publishers', [PublisherController::class, 'showAll'])->name('publisher.showAll');

    // Категорії (жанр, мова, країна, тип обкладинки)
    Route::get('/categories', [AdminController::class, 'categories'])->name('categories.showAll');
    Route::post('/category/{table}/add', [AdminController::class, 'categoryAdd'])->name('category.add');
    Route::post('/category/{table}/{id}/edit', [AdminController::class, 'categoryEdit'])->name('category.edit');
    Route::get('/category/{table}/{id}/del', [AdminController::class, 'categoryDel'])->name('category.del');

    // Класифікатор
    Route::get('/classificator', [AdminController::class, 'classificator'])->name('classificator.showAll');
    Route::post('/classificator/group/add', [AdminController::class, 'classificatorGroupAdd'])->name('classificator-group.add');
    Route::post('/classificator/group/{id}/edit', [AdminController::class, 'classificatorGroupEdit'])->name('classificator-group.edit');
    Route::get('/classificator/group/{id}/del', [AdminController::class, 'classificatorGroupDel'])->name('classificator-group.del');
    Route::post('/classificator/option/add', [AdminController::class, 'classificatorOptionAdd'])->name('classificator-option.add');
    Route::post('/classificator/option/{id}/edit', [AdminController::class, 'classificatorOptionEdit'])->name('classificator-option.edit');
    Route::get('/classificator/option/{id}/del', [AdminController::class, 'classificatorOptionDel'])->name('classificator-option.del');

    // Заявки
    Route::get('/user-requests', [UserRequestController::class, 'showAll'])->name('user-request.showAll');
    Route::get('/user-request/{id}', [UserRequestController::class, 'show'])->name('user-request.show');
    Route::post('/user-request/{id}/process', [UserRequestController::class, 'process'])->name('user-request.process');
    Route::post('/user-request/{id}/reject', [UserRequestController::class, 'reject'])->name('user-request.reject');
    Route::get('/classificator', [AdminController::class, 'classificator'])->name('classificator.showAll');

    // Довідка
    Route::get('/faqs', [FAQController::class, 'showAll'])->name('faq.showAll');
    Route::get('/faq', [FAQController::class, 'emptyForm'])->name('faq.form');
    Route::post('/faq/add', [FAQController::class, 'add'])->name('faq.add');
    Route::get('/faq/{id}/edit', [FAQController::class, 'edit'])->name('faq.editForm');
    Route::post('/faq/{id}/edit', [FAQController::class, 'edit'])->name('faq.edit');
    Route::post('/faq/{id}/del', [FAQController::class, 'del'])->name('faq.del');
});

// Лише модератори
Route::group(['middleware' => 'permissions:moderate'], function () {
    Route::get('/ban/{id}', [ProfileController::class, 'ban'])->name('ban');
    Route::get('/unban/{id}', [ProfileController::class, 'unban'])->name('unban');
});

// Лише авторизовані користувачі
Route::middleware(['auth', 'verified'])->group(function () {
    // Профіль користувача
    Route::get('/profile', [ProfileController::class, 'profile'])->name('my-profile');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.editForm');
    Route::post('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::get('/profile/ratings', [ProfileController::class, 'myRatings'])->name('my-ratings');

    // Блог
    Route::get('/profile/blog', [BlogController::class, 'emptyForm'])->name('blog.form');
    Route::get('/profile/blog/{id}', [BlogController::class, 'edit'])->name('blog.editForm');
    Route::post('/profile/blog/{id}/edit', [BlogController::class, 'edit'])->name('blog.edit');
    Route::get('/profile/blog/{id}/del', [BlogController::class, 'destroy'])->name('blog.del');
    Route::get('/profile/blogs', [BlogController::class, 'profileBlogs'])->name('my-blogs');
    Route::post('/profile/blog/add', [BlogController::class, 'add'])->name('blog.add');

    // Вподобайка
    Route::post('/like', [LikeController::class, 'like'])->name('like');
    
    // Коментарі
    Route::post('/comment/add', [CommentController::class, 'add'])->name('comment.add');
    Route::post('/comment/{id}/edit', [CommentController::class, 'edit'])->name('comment.edit');
    Route::get('/comment/{id}/del', [CommentController::class, 'del'])->name('comment.del');

    // Сповіщення
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications');
    Route::post('/notifications/mark-as-read/{id}', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
    Route::get('/notifications/mark-all-as-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');

    // Твори
    Route::post('/works/{id}/rate', [WorkController::class, 'rate'])->name('rate');
    Route::post('/works/{id}/cancel-rate', [WorkController::class, 'cancelRate'])->name('cancel-rate');
    Route::post('/works/{id}/date-read', [WorkController::class, 'dateRead'])->name('date-read');
    Route::post('/works/{id}/cancel-date-read', [WorkController::class, 'cancelDateRead'])->name('cancel-date-read');

    // Відгуки
    Route::post('/review/add', [ReviewController::class, 'add'])->name('review.add');
    Route::post('/review/{id}/edit', [ReviewController::class, 'edit'])->name('review.edit');
    Route::get('/review/{id}/del', [ReviewController::class, 'del'])->name('review.del');
    Route::get('/profile/reviews', [ReviewController::class, 'profileReviews'])->name('my-profile-reviews');

    // Цитати
    Route::post('/quote/add', [QuoteController::class, 'add'])->name('quote.add');
    Route::post('/quote/{id}/edit', [QuoteController::class, 'edit'])->name('quote.edit');
    Route::get('/quote/{id}/del', [QuoteController::class, 'del'])->name('quote.del');

    // Полиці
    Route::post('/works/{id}/shelf', [ShelfController::class, 'workToShelf'])->name('work-to-shelf');
    Route::get('/profile/shelves', [ShelfController::class, 'profileShelves'])->name('my-profile-shelves');
    Route::post('/shelf/add', [ShelfController::class, 'add'])->name('shelf.add');
    Route::post('/shelf/{id}/edit', [ShelfController::class, 'edit'])->name('shelf.edit');
    Route::get('/shelf/{id}/del', [ShelfController::class, 'del'])->name('shelf.del');
    Route::get('/shelf/{id}/del-work/{w_id}', [ShelfController::class, 'delWork'])->name('shelf.del-work');
    Route::post('/shelf/add-work', [ShelfController::class, 'addToShelf'])->name('shelf.add-work');

    // Класифікатор
    Route::post('/work/{id}/classificate', [WorkController::class, 'classificate'])->name('work.classificate');

    // Заявки
    Route::get('/user-request', [UserRequestController::class, 'form'])->name('user-request.form');
    Route::post('/user-request/add', [UserRequestController::class, 'add'])->name('user-request.add');
    Route::get('/user-request/{id}', [UserRequestController::class, 'showStatus'])->name('user-request.show-status');
    Route::get('/profile/user-requests', [UserRequestController::class, 'profileUserRequests'])->name('my-profile-user-requests');
});

// Майстерня
Route::middleware(['auth', 'verified'])->prefix('content-maker')->group(function () {
    Route::get('', [ContentMakerController::class, 'index'])->name('content-maker'); 

    // Персони
    Route::get('/persons', [PersonController::class, 'showAllMy'])->name('person.showAllMy');
    Route::get('/person', [PersonController::class, 'emptyForm'])->name('person.form');
    Route::get('/person/{id}', [PersonController::class, 'edit'])->name('person.editForm');
    Route::post('/person/add', [PersonController::class, 'add'])->name('person.add');
    Route::post('/person/{id}/edit', [PersonController::class, 'edit'])->name('person.edit');
    Route::post('/person/quick-add', [PersonController::class, 'quickAdd'])->name('person.quickAdd');

    // Твір
    Route::get('/works', [WorkController::class, 'showAllMy'])->name('work.showAllMy');
    Route::get('/work', [WorkController::class, 'emptyForm'])->name('work.form');
    Route::get('/work/{id}', [WorkController::class, 'edit'])->name('work.editForm');
    Route::post('/work/add', [WorkController::class, 'add'])->name('work.add');
    Route::post('/work/{id}/edit', [WorkController::class, 'edit'])->name('work.edit');
    Route::post('/work/quick-add', [WorkController::class, 'quickAdd'])->name('work.quickAdd');

    // Видання
    Route::get('/editions', [EditionController::class, 'showAllMy'])->name('edition.showAllMy');
    Route::get('/edition', [EditionController::class, 'emptyForm'])->name('edition.form');
    Route::get('/edition/{id}', [EditionController::class, 'edit'])->name('edition.editForm');
    Route::post('/edition/add', [EditionController::class, 'add'])->name('edition.add');
    Route::post('/edition/{id}/edit', [EditionController::class, 'edit'])->name('edition.edit');

    // Видавництво
    Route::get('/publishers', [PublisherController::class, 'showAllMy'])->name('publisher.showAllMy');
    Route::get('/publisher', [PublisherController::class, 'emptyForm'])->name('publisher.form');
    Route::get('/publisher/{id}', [PublisherController::class, 'edit'])->name('publisher.editForm');
    Route::post('/publisher/add', [PublisherController::class, 'add'])->name('publisher.add');
    Route::post('/publisher/{id}/edit', [PublisherController::class, 'edit'])->name('publisher.edit');
    Route::post('/publisher/quick-add', [PublisherController::class, 'quickAdd'])->name('publisher.quickAdd');

    // Імпорт
    Route::post('/import/edition', [ImportController::class, 'edition'])->name('import.edition');

    // Укладач вмісту
    Route::middleware(['auth', 'permissions:content-make'])->group(function () {
        // Затвердження 
        Route::get('/items-to-apply', [ContentMakerController::class, 'itemsToApply'])->name('items-to-apply');
    
        // Дублі
        Route::get('/doubles', [DoubleController::class, 'index'])->name('doubles');
        Route::post('/double/add-not-a-double', [DoubleController::class, 'addNotADouble'])->name('double.add-not-a-double');
        Route::post('/double/unite', [DoubleController::class, 'unite'])->name('double.unite');
    });
});

//
// Клієнтська частина
Route::get('/', [MainPageController::class, 'show'])->name('main');
Route::get('/home', [HomeController::class, 'index'])->name('home');

// Блог
Route::get('/blogs', [BlogController::class, 'index'])->name('blogs');
Route::get('/blogs/page/{page}', [BlogController::class, 'showAll'])->name('blogPage');
Route::get('/blogs/{id}', [BlogController::class, 'show'])->name('blog');
Route::get('/profiles/{id}/blogs', [BlogController::class, 'profileBlogs'])->name('profile-blogs');

// Пошук
Route::get('/api/search', [MainPageController::class, 'search'])->name('search');
Route::get('/api/search/persons', [PersonController::class, 'search'])->name('search.persons');
Route::get('/api/search/avtors', [PersonController::class, 'searchAvtors'])->name('search.avtors');
Route::get('/api/search/designers', [PersonController::class, 'searchDesigners'])->name('search.designers');
Route::get('/api/search/illustrators', [PersonController::class, 'searchIllustrators'])->name('search.illustrators');
Route::get('/api/search/translators', [PersonController::class, 'searchTranslators'])->name('search.translators');
Route::get('/api/search/is-persons', [PersonController::class, 'searchIsPersons'])->name('search.isPersons');

Route::get('/api/search/publishers', [PublisherController::class, 'search'])->name('search.publishers');

Route::get('/api/search/cycles', [WorkController::class, 'searchCycles'])->name('search.cycles');
Route::get('/api/search/works', [WorkController::class, 'search'])->name('search.works');
Route::get('/api/search/work-with-avtor', [WorkController::class, 'searchWithAvtor'])->name('search.work-with-avtor');

Route::get('/api/search/editions', [EditionController::class, 'searchOne'])->name('search.edition');

Route::get('/api/search/blogs', [BlogController::class, 'search'])->name('search.blogs');

Route::get('/api/search/profiles', [ProfileController::class, 'search'])->name('search.profiles');

// Профілі
Route::get('/profiles', [ProfileController::class, 'index'])->name('profiles');
Route::get('/profiles/page/{page}', [ProfileController::class, 'index'])->name('profilesPage');
Route::get('/profiles/{id}', [ProfileController::class, 'profile'])->name('profile');

// Відгуки
Route::get('/profiles/{id}/reviews', [ReviewController::class, 'profileReviews'])->name('profile-reviews');

// Полиці
Route::get('/profile/{id}/shelves', [ShelfController::class, 'profileShelves'])->name('profile-shelves');
Route::get('/shelf/{id}', [ShelfController::class, 'show'])->name('shelf');

// Персони
Route::get('/persons', [PersonController::class, 'index'])->name('persons');
Route::get('/persons/page/{page}', [PersonController::class, 'index'])->name('personPage');
Route::get('/persons/{id}', [PersonController::class, 'show'])->name('person');

// Твори
Route::get('/works', [WorkController::class, 'index'])->name('works');
Route::post('/works', [WorkController::class, 'index'])->name('works');
Route::get('/works/page/{page}', [WorkController::class, 'index'])->name('workPage');
Route::get('/works/{id}', [WorkController::class, 'show'])->name('work');

// Видання
Route::get('/editions', [EditionController::class, 'index'])->name('editions');
Route::get('/editions/page/{page}', [EditionController::class, 'index'])->name('editionPage');
Route::get('/editions/{id}', [EditionController::class, 'show'])->name('edition');

// Видавництва
Route::get('/publishers', [PublisherController::class, 'index'])->name('publishers');
Route::get('/publishers/page/{page}', [PublisherController::class, 'index'])->name('publisherPage');
Route::get('/publishers/{id}', [PublisherController::class, 'show'])->name('publisher');

// Довідка
Route::get('/faqs', [FAQController::class, 'index'])->name('faqs');
Route::get('/faq/{id}', [FAQController::class, 'show'])->name('faq');