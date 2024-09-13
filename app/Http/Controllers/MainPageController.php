<?php
	namespace App\Http\Controllers;

    use Illuminate\Http\Request;
	use Illuminate\Support\Facades\DB;

	use App\Models\Blog;
	use App\Models\BlogCategory;
	use App\Models\User;
	use App\Services\ImageService;
	
	class MainPageController extends Controller
	{
		public function show()
		{
			$works = DB::select('SELECT w.id, w.name, ed.id AS main_edition, ed.main_img AS main_img, avtors.avtors AS avtors,
			AVG(COALESCE(r.value, 0)) AS rating, 
			SUBSTRING_INDEX(GROUP_CONCAT(an.text ORDER BY an.id ASC SEPARATOR "|"), "|", 1) AS anotation,
			ucc.user_count AS classificator_users_count,
			GROUP_CONCAT(DISTINCT 
				CASE WHEN opt_p.opt_group_id IN (2, 6) AND opt_p.opt_percentage>=25 
				THEN CONCAT("#", opt_p.opt_name) 
				ELSE NULL 
				END 
				ORDER BY opt_p.opt_id SEPARATOR " ") AS options_info
			FROM work AS w      
			LEFT JOIN edition AS ed ON ed.id = w.main_edition
			LEFT JOIN rating AS r ON w.id = r.w_id 
			LEFT JOIN anotation AS an ON w.id = an.work_id 
			LEFT JOIN user_classification_count AS ucc ON w.id = ucc.w_id
			LEFT JOIN option_percentage AS opt_p ON w.id = opt_p.w_id
			LEFT JOIN (SELECT w.id AS work_id, GROUP_CONCAT(p.name ORDER BY p.name ASC SEPARATOR ", ") AS avtors
			FROM work AS w
			INNER JOIN avtor_work AS aw ON w.id = aw.w_id
			INNER JOIN person AS p ON p.id = aw.av_id
			GROUP BY w.id
			) AS avtors ON w.id = avtors.work_id
			WHERE w.is_public = 1
			GROUP BY w.id, w.name, ed.id, ed.main_img, avtors.avtors, ucc.user_count
			ORDER BY rating DESC LIMIT 5'
			);
			
			// Обкладинки
			foreach ($works as $work){
				if (!$work->main_edition) {
					$firstAddedEdition = DB::select("SELECT e.id, e.name, e.main_img from edition as e
										INNER JOIN edition_item AS ei ON e.id = ei.ed_id
										where ei.w_id = $work->id
										limit 1"); 
					if ($firstAddedEdition){    
						$firstAddedEdition = $firstAddedEdition[0];        
						$work->main_edition = $firstAddedEdition->id;   
						$work->main_img = $firstAddedEdition->main_img;   
					}
				}            
				$work->img = ImageService::getImg('edition',$work->main_edition,$work->main_img);
			}  

			return view('client.main-page',[
				'title' => '',
				'works' => $works,
			]);
		}		

	}
?>