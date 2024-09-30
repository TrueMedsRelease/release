<?php

namespace App\Http\Controllers;


use Illuminate\View\View;
use Phattarachai\LaravelMobileDetect\Agent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Services\AdminServices;
use App\Models\Language;
use App\Models\Category;

class AdminController extends Controller
{
    public function admin_login() : View
    {
        $design = session('design') ? session('design') : config('app.design');
        $title = $this->pageAdminTitle('login');
        $agent = new Agent();

        return view('admin.login',
        [
            'design' => $design,
            'title' => $title,
            'agent' => $agent,
            'logged_in' => false,
        ]);
    }

    public function admin_main_content() {
        $not_showed_product = AdminServices::getNotShowedOnMainProduct();
        $showed_product = AdminServices::getShowedOnMainProduct();

        $returnHTML = view('admin.ajax.index_content')->with([
            'not_showed_product' => $not_showed_product,
            'showed_product' => $showed_product,
        ])->render();

        return response()->json(array('success' => true, 'html' => "$returnHTML"));
    }

    public function index() : View
    {
        $design = session('design') ? session('design') : config('app.design');
        $title = $this->pageAdminTitle('main_page');
        $agent = new Agent();

        return view('admin.index',
        [
            'design' => $design,
            'title' => $title,
            'agent' => $agent,
            'logged_in' => true,
        ]);
    }

    public function main_properties() : View {
        $title = $this->pageAdminTitle('main_properties');
        $agent = new Agent();


        return view('admin.main_properties', [
            'title' => $title,
            'agent' => $agent,
            'logged_in' => true,
        ]);
    }

    public function main_properties_content() {
        $cur_design = env('APP_DESIGN');
        $templates = [];
        $catalog_templates_path = resource_path() . '/views';
        $cur_template_scrin = "";
        $templates_dir_content = scandir($catalog_templates_path);
        foreach ($templates_dir_content as $cur_template) {
            if (is_dir($catalog_templates_path . "/" . $cur_template) && $cur_template != "." && $cur_template != ".." && $cur_template != "admin") {
                $cur_template_info = [];
                $cur_template_info["name"] = $cur_template;
                if (file_exists(public_path() . "/" . $cur_template . "/images/scrin.png")) {
                    $cur_template_info["scrin"] = "/" . $cur_template . "/images/scrin.png";
                }
                $templates[] = $cur_template_info;
                if ($cur_template_info["name"] == $cur_design) {
                    $cur_template_scrin = $cur_template_info["scrin"];
                }
            } else {
                continue;
            }
        }

        $user_login = DB::select("SELECT `login` FROM `user` WHERE `role` = 'administrator'");
        $user_login = $user_login[0];


        $returnHTML = view('admin.ajax.main_properties_content')->with([
            "user_login" => $user_login->login,
            "templates" => $templates,
            "cur_template_scrin" => $cur_template_scrin,
            "cur_template" => $cur_design,
            "page_properties" => '',
            'language' => Language::class,
        ])->render();

        return response()->json(array('success' => true, 'html' => "$returnHTML", 'template' => $cur_design));
    }

    public function add_to_main(Request $request) {
        $selected_products_id = $request->selected_products;

        if ($selected_products_id == 'null') {
            $response = [
                'status' => 'error',
                'text' => __('text.admin_common_form_empty_field')
            ];
        } else {
            foreach ($selected_products_id as $cur_product_id) {
                DB::update("UPDATE product SET is_showed_on_main = 1 WHERE `id` = $cur_product_id");
            }

            $not_showed_product = AdminServices::getNotShowedOnMainProduct();
            $showed_product = AdminServices::getShowedOnMainProduct();

            $returnHTML = view('admin.ajax.index_content')->with([
                'not_showed_product' => $not_showed_product,
                'showed_product' => $showed_product,
            ])->render();

            $response = [
                'status' => 'success',
                'html' => "$returnHTML"
            ];
        }

        return response()->json($response);
    }

    public function delete_from_main(Request $request) {
        $selected_products_id = $request->selected_products;

        if ($selected_products_id == 'null') {
            $response = [
                'status' => 'error',
                'text' => __('text.admin_common_form_empty_field')
            ];
        } else {
            foreach ($selected_products_id as $cur_product_id) {
                DB::update("UPDATE product SET is_showed_on_main = 0 WHERE `id` = $cur_product_id");
            }

            $not_showed_product = AdminServices::getNotShowedOnMainProduct();
            $showed_product = AdminServices::getShowedOnMainProduct();

            $returnHTML = view('admin.ajax.index_content')->with([
                'not_showed_product' => $not_showed_product,
                'showed_product' => $showed_product,
            ])->render();

            $response = [
                'status' => 'success',
                'html' => "$returnHTML"
            ];
        }

        return response()->json($response);
    }

    public function product_up_in_sort(Request $request) {
        $selected_products_id = $request->selected_products;

        if ($selected_products_id == 'null') {
            $response = [
                'status' => 'error',
                'text' => __('text.admin_common_form_empty_field')
            ];
        } else {
            $showed_product_old = AdminServices::getShowedOnMainProduct();

            for($i = count($selected_products_id) - 1; $i >= 0;  $i--) {
                $cur_product_id = $selected_products_id[$i];
                $cur_product_order = Product::query()
                    ->where('id', '=', $cur_product_id)
                    ->where('is_showed', '=', 1)
                    ->get(['main_order'])
                    ->toArray();

                $prev_product_index = -1;
                foreach ($showed_product_old as $key => $value) {
                    if ($value['id'] == $cur_product_id) {
                        $prev_product_index = $key - 1;
                        break;
                    }
                }

                if($prev_product_index < 0) {
                    continue;
                } else {
                    $prev_product_id = $showed_product_old[$prev_product_index]['id'];
                    $prev_product_order = Product::query()
                        ->where('id', '=', $prev_product_id)
                        ->where('is_showed', '=', 1)
                        ->get(['main_order'])
                        ->toArray();

                    DB::update("UPDATE product SET main_order = '{$cur_product_order[0]['main_order']}' WHERE `id` = '$prev_product_id'");
                    DB::update("UPDATE product SET main_order = '{$prev_product_order[0]['main_order']}' WHERE `id` = '$cur_product_id'");
                }

            }

            $not_showed_product = AdminServices::getNotShowedOnMainProduct();
            $showed_product = AdminServices::getShowedOnMainProduct();

            $returnHTML = view('admin.ajax.index_content')->with([
                'not_showed_product' => $not_showed_product,
                'showed_product' => $showed_product,
            ])->render();

            $response = [
                'status' => 'success',
                'html' => "$returnHTML"
            ];
        }

        return response()->json($response);
    }

    public function product_down_in_sort(Request $request) {
        $selected_products_id = $request->selected_products;

        if ($selected_products_id == 'null') {
            $response = [
                'status' => 'error',
                'text' => __('text.admin_common_form_empty_field')
            ];
        } else {
            $showed_product_old = AdminServices::getShowedOnMainProduct();

            for($i = count($selected_products_id) - 1; $i >= 0;  $i--) {
                $cur_product_id = $selected_products_id[$i];
                $cur_product_order = Product::query()
                    ->where('id', '=', $cur_product_id)
                    ->where('is_showed', '=', 1)
                    ->get(['main_order'])
                    ->toArray();

                $next_product_index = 0;
                foreach ($showed_product_old as $key => $value) {
                    if ($value['id'] == $cur_product_id) {
                        $next_product_index = $key + 1;
                        break;
                    }
                }

                if($next_product_index > count($showed_product_old) - 1) {
                    continue;
                } else {
                    $next_product_id = $showed_product_old[$next_product_index]['id'];
                    $next_product_order = Product::query()
                        ->where('id', '=', $next_product_id)
                        ->where('is_showed', '=', 1)
                        ->get(['main_order'])
                        ->toArray();

                    DB::update("UPDATE product SET main_order = '{$cur_product_order[0]['main_order']}' WHERE `id` = '$next_product_id'");
                    DB::update("UPDATE product SET main_order = '{$next_product_order[0]['main_order']}' WHERE `id` = '$cur_product_id'");
                }

            }

            $not_showed_product = AdminServices::getNotShowedOnMainProduct();
            $showed_product = AdminServices::getShowedOnMainProduct();

            $returnHTML = view('admin.ajax.index_content')->with([
                'not_showed_product' => $not_showed_product,
                'showed_product' => $showed_product,
            ])->render();

            $response = [
                'status' => 'success',
                'html' => "$returnHTML"
            ];
        }

        return response()->json($response);
    }

    public function request_login(Request $request) {
        $password = $request->password;
        $md5_password = md5($password);

        $user = DB::select("SELECT * FROM user WHERE md5_pw = '{$md5_password}'");

        if (count($user) > 0) {
            $result = [
                'status' => 'success',
                'url' => route('admin.index'),
            ];
        } else {
            $result = [
                'status' => 'error',
                'text' => __('text.admin_login_form_invalid_log_in'),
            ];
        }

        return json_encode($result);
    }

    public function save_user_properties(Request $request) {
        $user_login = 'admin';
        $new_password = $request->new_password;

        $md5_pw = md5($new_password);
        DB::update("UPDATE user SET md5_pw = '{$md5_pw}' WHERE `login` = '$user_login'");

        return $this->main_properties_content();
    }

    public function save_template(Request $request) {
        $selected_template = $request->selected_template;

        $this->envUpdate('APP_DESIGN', $selected_template);

        $cur_design = $selected_template;
        $templates = [];
        $catalog_templates_path = resource_path() . '/views';
        $cur_template_scrin = "";
        $templates_dir_content = scandir($catalog_templates_path);
        foreach ($templates_dir_content as $cur_template) {
            if (is_dir($catalog_templates_path . "/" . $cur_template) && $cur_template != "." && $cur_template != ".." && $cur_template != "admin") {
                $cur_template_info = [];
                $cur_template_info["name"] = $cur_template;
                if (file_exists(public_path() . "/" . $cur_template . "/images/scrin.png")) {
                    $cur_template_info["scrin"] = "/" . $cur_template . "/images/scrin.png";
                }
                $templates[] = $cur_template_info;
                if ($cur_template_info["name"] == $cur_design) {
                    $cur_template_scrin = $cur_template_info["scrin"];
                }
            } else {
                continue;
            }
        }

        $user_login = DB::select("SELECT `login` FROM `user` WHERE `role` = 'administrator'");
        $user_login = $user_login[0];


        $returnHTML = view('admin.ajax.main_properties_content')->with([
            "user_login" => $user_login->login,
            "templates" => $templates,
            "cur_template_scrin" => $cur_template_scrin,
            "cur_template" => $cur_design,
            "page_properties" => '',
            'language' => Language::class,
        ])->render();

        return response()->json(array('success' => true, 'html' => "$returnHTML"));
    }

    public function load_page_properties(Request $request) {
        $page = $request->page;
        $language_id = $request->language_id;

        $page_properties = DB::select("SELECT * FROM page_properties WHERE page = '$page' AND language = $language_id");
        $page_properties = $page_properties[0];

        $cur_design = env('APP_DESIGN');
        $templates = [];
        $catalog_templates_path = resource_path() . '/views';
        $cur_template_scrin = "";
        $templates_dir_content = scandir($catalog_templates_path);
        foreach ($templates_dir_content as $cur_template) {
            if (is_dir($catalog_templates_path . "/" . $cur_template) && $cur_template != "." && $cur_template != ".." && $cur_template != "admin") {
                $cur_template_info = [];
                $cur_template_info["name"] = $cur_template;
                if (file_exists(public_path() . "/" . $cur_template . "/images/scrin.png")) {
                    $cur_template_info["scrin"] = "/" . $cur_template . "/images/scrin.png";
                }
                $templates[] = $cur_template_info;
                if ($cur_template_info["name"] == $cur_design) {
                    $cur_template_scrin = $cur_template_info["scrin"];
                }
            } else {
                continue;
            }
        }

        $user_login = DB::select("SELECT `login` FROM `user` WHERE `role` = 'administrator'");
        $user_login = $user_login[0];


        $returnHTML = view('admin.ajax.main_properties_content')->with([
            "user_login" => $user_login->login,
            "templates" => $templates,
            "cur_template_scrin" => $cur_template_scrin,
            "cur_template" => $cur_design,
            "page_properties" => $page_properties,
            'language' => Language::class,
        ])->render();

        return response()->json(array('success' => true, 'html' => "$returnHTML"));
    }


    public function save_page_properties(Request $request) {
        $page = $request->page;
        $language_id = $request->language_id;
        $title = $request->title;
        $keyword = $request->keyword;
        $description = $request->description;

        DB::update('UPDATE page_properties SET title = "' . $title . '", keyword = "' . $keyword . '", description = "' . $description . '" WHERE page = "' . $page . '" AND language = ' . $language_id);

        return $this->main_properties();
    }

    public function available_products() : View {
        $title = $this->pageAdminTitle('available_product');
        $agent = new Agent();

        return view('admin.available_products', [
            'title' => $title,
            'agent' => $agent,
            'logged_in' => true,
        ]);
    }

    public function available_products_content() {
        $not_showed_products_info = AdminServices::getCategoriesWithUnavailableProducts();
        $showed_products_info = AdminServices::getCategoriesWithAvailableProducts();

        $returnHTML = view('admin.ajax.available_products_content')->with([
            'not_showed_products_info' => $not_showed_products_info,
            'showed_products_info' => $showed_products_info
        ])->render();

        return response()->json(array('success' => true, 'html' => "$returnHTML"));
    }

    public function add_to_showed(Request $request) {
        $selected_products_id = $request->selected_products;

        if ($selected_products_id == 'null') {
            $response = [
                'status' => 'error',
                'text' => __('text.admin_common_form_empty_field')
            ];
        } else {

            for($i = count($selected_products_id) - 1; $i >= 0;  $i--) {
                $product_id = $selected_products_id[$i];
                DB::update("UPDATE product SET is_showed = 1 WHERE `id` = '$product_id'");
            }

            $not_showed_products_info = AdminServices::getCategoriesWithUnavailableProducts();
            $showed_products_info = AdminServices::getCategoriesWithAvailableProducts();

            $returnHTML = view('admin.ajax.available_products_content')->with([
                'not_showed_products_info' => $not_showed_products_info,
                'showed_products_info' => $showed_products_info
            ])->render();

            $response = [
                'status' => 'success',
                'html' => "$returnHTML"
            ];
        }

        return response()->json($response);
    }

    public function delete_from_showed(Request $request) {
        $selected_products_id = $request->selected_products;

        if ($selected_products_id == 'null') {
            $response = [
                'status' => 'error',
                'text' => __('text.admin_common_form_empty_field')
            ];
        } else {

            for($i = count($selected_products_id) - 1; $i >= 0;  $i--) {
                $product_id = $selected_products_id[$i];
                DB::update("UPDATE product SET is_showed = 0 WHERE `id` = '$product_id'");
            }

            $not_showed_products_info = AdminServices::getCategoriesWithUnavailableProducts();
            $showed_products_info = AdminServices::getCategoriesWithAvailableProducts();

            $returnHTML = view('admin.ajax.available_products_content')->with([
                'not_showed_products_info' => $not_showed_products_info,
                'showed_products_info' => $showed_products_info
            ])->render();

            $response = [
                'status' => 'success',
                'html' => "$returnHTML"
            ];
        }

        return response()->json($response);
    }

    public function available_packagings() : View {
        $title = $this->pageAdminTitle('available_packagings');
        $agent = new Agent();

        return view('admin.available_packagings', [
            'title' => $title,
            'agent' => $agent,
            'logged_in' => true,
        ]);
    }

    public function available_packagings_content() {
        $all_products_info = AdminServices::getAllProductWithCategory();

        $returnHTML = view('admin.ajax.available_packagings_content')->with([
            'all_products_info' => $all_products_info,
            'showed_packagings' => [],
            'not_showed_packagings' => []
        ])->render();

        return response()->json(array('success' => true, 'html' => "$returnHTML"));
    }

    public function load_packaging_info(Request $request) {
        $product_id = $request->product_id;

        $all_products_info = AdminServices::getAllProductWithCategory();
        $showed_packagings = AdminServices::getShowedProductPackaging($product_id, 1);
        $not_showed_packagings = AdminServices::getShowedProductPackaging($product_id, 0);

        $returnHTML = view('admin.ajax.available_packagings_content')->with([
            'all_products_info' => $all_products_info,
            'showed_packagings' => $showed_packagings,
            'not_showed_packagings' => $not_showed_packagings
        ])->render();

        return response()->json(array('success' => true, 'html' => "$returnHTML"));
    }

    public function add_pack_to_showed(Request $request) {
        $selected_packagings_id = $request->selected_packaging;
        $product_id = $request->product_id;

        if ($selected_packagings_id == 'null') {
            $response = [
                'status' => 'error',
                'text' => __('text.admin_common_form_empty_field')
            ];
        } else {

            for($i = count($selected_packagings_id) - 1; $i >= 0;  $i--) {
                $packaging_id = $selected_packagings_id[$i];
                DB::update("UPDATE product_packaging SET is_showed = 1 WHERE `id` = $packaging_id");
            }

            $all_products_info = AdminServices::getAllProductWithCategory();
            $showed_packagings = AdminServices::getShowedProductPackaging($product_id, 1);
            $not_showed_packagings = AdminServices::getShowedProductPackaging($product_id, 0);

            $returnHTML = view('admin.ajax.available_packagings_content')->with([
                'all_products_info' => $all_products_info,
                'showed_packagings' => $showed_packagings,
                'not_showed_packagings' => $not_showed_packagings
            ])->render();

            $response = [
                'status' => 'success',
                'html' => "$returnHTML"
            ];
        }

        return response()->json($response);
    }

    public function delete_pack_from_showed(Request $request) {
        $selected_packagings_id = $request->selected_packaging;
        $product_id = $request->product_id;

        if ($selected_packagings_id == 'null') {
            $response = [
                'status' => 'error',
                'text' => __('text.admin_common_form_empty_field')
            ];
        } else {

            for($i = count($selected_packagings_id) - 1; $i >= 0;  $i--) {
                $packaging_id = $selected_packagings_id[$i];
                DB::update("UPDATE product_packaging SET is_showed = 0 WHERE `id` = $packaging_id");
            }

            $all_products_info = AdminServices::getAllProductWithCategory();
            $showed_packagings = AdminServices::getShowedProductPackaging($product_id, 1);
            $not_showed_packagings = AdminServices::getShowedProductPackaging($product_id, 0);

            $returnHTML = view('admin.ajax.available_packagings_content')->with([
                'all_products_info' => $all_products_info,
                'showed_packagings' => $showed_packagings,
                'not_showed_packagings' => $not_showed_packagings
            ])->render();

            $response = [
                'status' => 'success',
                'html' => "$returnHTML"
            ];
        }

        return response()->json($response);
    }

    // public function add_pack_to_showed() {

    // }

    // public function add_pack_to_showed() {

    // }

    public function pageAdminTitle($page) {
        switch ($page){
            case 'login':
                $title = __('text.admin_login_title');
                break;
            case 'main_page':
                $title = __('text.admin_main_page_main_page_title');
                break;
            case 'main_properties':
                $title = __('text.admin_main_properties_title');
                break;
            case 'available_product':
                $title = __('text.admin_products_show_title');
                break;
            case 'available_packagings':
                $title = __('text.admin_packagings_show_title');
                break;
        }
        return $title;
    }

    public function setEnv($key, $val)
    {
        $path = base_path('.env');

        if (file_exists($path)) {

            file_put_contents($path, str_replace(
                $key . '=' . env($key), $key . '=' . $val, file_get_contents($path)
            ));
        }
    }

    public function envUpdate($flag,$value)
    {
        $allow_flags = ["APP_DESIGN", 'APP_CURRENCY'];

        if (in_array($flag, $allow_flags))
        {
            $this->setEnv((string)$flag, (string)$value);
            return env(env((string)$flag));
        }

    }
}