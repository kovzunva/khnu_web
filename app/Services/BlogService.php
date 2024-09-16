<?php
namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\DateService;
use App\Models\User;
use App\Models\Blog;
use App\Models\BlogCategory;
use Carbon\Carbon;

class BlogService extends BaseItemService 
{
    public $table = "blogs";
    public $sort_options = [
        1 => ['name' => 'За активністю', 'sql' => 'id'], // !!! Доробити
        2 => ['name' => 'За вподобайками', 'sql' => 'likes_count'],
        3 => ['name' => 'За коментарями', 'sql' => 'comments_count'],
        4 => ['name' => 'За датою додавання', 'sql' => 'id'],
        5 => ['name' => 'За назвою', 'sql' => 'name'],
    ];

    public function GetItems(Request $request = null, bool $paginate = true, $per_page = self::PER_PAGE_BASE){
        $this->sort = $this->GetOrderBy($request);
        $this->filter = $this->GetFilters($request);
        $this->paginator = $paginate? $this->GetPaginator($request,$per_page):null;

        $query = 'SELECT b.*, COUNT(DISTINCT c.item_id) AS comments_count, bc.name as category, COUNT(DISTINCT l.item_id) AS likes_count
            FROM blogs b
            LEFT JOIN blog_categories bc ON bc.id = b.category_id
            LEFT JOIN comments c ON b.id = c.item_id
            LEFT JOIN likes l ON b.id = l.item_id'
            .$this->filter->sql
            .' GROUP BY b.id, b.name '            
            .$this->sort->sql
            .$this->paginator->sql;
            
        $items = DB::select($query);
        foreach ($items as $item) {
            $item->user = User::find($item->user_id);
        }

        return $items;
    }
    
    public function GetCategories(){
        $categories = BlogCategory::orderBy('name')->get();
        return $categories;
    }

    // об'єкт фільтрування
    public function GetFilters(Request $request){
        $filter = new \stdClass();

        $filter->sql = null;
        $filter->filters = null;
        $filters = [];
        $conditions = [];   

        // Фільтр по категорії
        $categoryFilter = $request->input('category');
        $filter->selectedCategory = new BlogCategory();
        if ($categoryFilter === 'all') {
            $filter->selectedCategory->name = 'Всі категорії';
        }
        else if ($categoryFilter === '') {
            $conditions[] = 'category_id = ""';
            $filter->selectedCategory->name = 'Інше';
            $filters[] = $filter->selectedCategory->name;
        }
        else if ($categoryFilter) {
            $conditions[] = "category_id = $categoryFilter";
            $filter->selectedCategory = BlogCategory::find($categoryFilter);
            $filters[] = $filter->selectedCategory->name;
        }

        // Фільтр по даті
        $filter->selectedDateFrom = null;
        $filter->selectedDateTo = null;
        $dateFromFilter = $request->input('date_from');
        $dateToFilter = $request->input('date_to');
        try {
            if ($dateFromFilter && $dateFromFilter != '') {
                $carbonDateFrom = Carbon::createFromFormat('d.m.Y', $dateFromFilter)->format('Y-m-d');
                $conditions[] = "b.created_at >= '$carbonDateFrom'";
                $filter->selectedDateFrom = Carbon::createFromFormat('Y-m-d', $carbonDateFrom)->format('d.m.Y');
                $filters[] = 'дата від ' . $filter->selectedDateFrom;
            }

            if ($dateToFilter && $dateToFilter != '') {
                $carbonDateTo = Carbon::createFromFormat('d.m.Y', $dateToFilter)->format('Y-m-d');
                $conditions[] = "b.created_at <= '$carbonDateTo'";
                $filter->selectedDateTo = Carbon::createFromFormat('Y-m-d', $carbonDateTo)->format('d.m.Y');
                $filters[] = 'дата до ' . $filter->selectedDateTo;
            }
        } catch (\Exception $e) {}

        if (!empty($conditions)) {
            $filter->sql = ' WHERE ' . implode(' AND ', $conditions);
        }
        $filter->filters = implode(', ', $filters);

        return $filter;
    }

    public function GetCount(){
        $result = DB::select("select count(*) as count from $this->table b ".($this->filter? $this->filter->sql:""));
        return $result && count($result)>0? $result[0]->count:0; 
    }
    
}
