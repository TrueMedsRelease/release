<?php

namespace App\Http\Controllers;


use Illuminate\View\View;
use Phattarachai\LaravelMobileDetect\Agent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Services\AdminServices;
use App\Models\Language;
use App\Models\Currency;
use App\Models\Category;
use Illuminate\Support\Facades\Redirect;

class AdminController extends Controller
{
    public function admin_enter() {
        if (session('logged_in')) {
            return redirect()->route('admin.admin_seo');
        } else {
            return redirect()->route('admin.admin_login');
        }
    }

    public function admin_login()
    {
        if (session('logged_in')) {
            return redirect()->route('admin.admin_seo');
        }

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

    public function admin_logout()
    {
        session(['logged_in' => false]);
        return redirect()->route('admin.admin_login');
    }

    public function request_login(Request $request) {
        $password = $request->password;
        $md5_password = md5($password);

        $user = DB::select("SELECT * FROM user WHERE md5_pw = '{$md5_password}'");

        if (count($user) > 0) {

            session(['logged_in' => true]);

            $result = [
                'status' => 'success',
                'url' => route('admin.admin_seo'),
            ];
        } else {
            $result = [
                'status' => 'error',
                'text' => __('text.admin_login_form_invalid_log_in'),
                'new_captcha' => captcha_img()
            ];
        }

        return json_encode($result);
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

    public function index()
    {
        if (!session()->has('logged_in') || !session('logged_in')) {
            return redirect()->route('admin.admin_login');
        }

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

    public function admin_seo() {
        if (!session()->has('logged_in') || !session('logged_in')) {
            return redirect()->route('admin.admin_login');
        }

        $title = $this->pageAdminTitle('seo');
        $agent = new Agent();

        return view('admin.seo', [
            'title' => $title,
            'agent' => $agent,
            'logged_in' => true
        ]);
    }

    public function admin_seo_content() {
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

        $all_products_info = AdminServices::getAllProductWithCategory();

        $returnHTML = view('admin.ajax.seo_content')->with([
            "templates" => $templates,
            "cur_template_scrin" => $cur_template_scrin,
            "cur_template" => $cur_design,
            "page_properties" => '',
            'language' => Language::class,
            'all_products_info' => $all_products_info,
            'product_url' => [],
        ])->render();

        return response()->json(array('success' => true, 'html' => "$returnHTML", 'template' => $cur_design));
    }

    public function load_product_url(Request $request) {
        $product_id = $request->product_id;

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

        $all_products_info = AdminServices::getAllProductWithCategory();
        $product_url = AdminServices::getProductUrl($product_id);

        $returnHTML = view('admin.ajax.seo_content')->with([
            "templates" => $templates,
            "cur_template_scrin" => $cur_template_scrin,
            "cur_template" => $cur_design,
            "page_properties" => '',
            'language' => Language::class,
            'all_products_info' => $all_products_info,
            'product_url' => $product_url,
        ])->render();

        return response()->json(array('success' => true, 'html' => "$returnHTML"));
    }

    public function save_product_url(Request $request) {
        $product_form = $request->product_form_data;
        $product_id = $product_form['all_products_field'];
        $error = [];

        foreach (Language::GetAllLanuages() as $language) {
            $product_url = $product_form[$language['id'] . "_url_field"];

            $has_errors = false;

            if (empty($product_url)) {
                $has_errors = true;

                $error = [
                    'status' => 'error',
                    'text' => 'Empty URL'
                ];
            }

            if (!$has_errors) {
                $cur_language_id = $language['id'];

                DB::update('UPDATE product_desc SET `url` = "' . $product_url . '" WHERE product_id = ' . $product_id . ' AND language_id = ' . $cur_language_id . ' ');
            }
        }

        if (count($error) > 0) {
            return response()->json(array('status' => 'error', 'text' => $error['text']));
        } else {
            return response()->json(array('status' => 'success', 'url' => route('admin.admin_seo')));
        }
    }

    public function load_pixel(Request $request) {
        $page = $request->page;

        $pixel_text = DB::select("SELECT * FROM `pixel` WHERE `page` = '$page'");

        if(count($pixel_text) > 0) {
            $pixel_text = $pixel_text[0];
            $text = $pixel_text->pixel;

            return response()->json(array('status' => 'success', 'text' => $text));
        } else {
            return response()->json(array('status' => 'success', 'text' => ''));
        }
    }

    public function save_pixel(Request $request) {
        $page = $request->selected_page;
        $pixel_text = $request->pixel_text['pixel_text'];
        $has_error = false;
        $error = [];

        if (empty($page)) {
            $has_error = true;
            $error = [
                'status' => 'error',
                'text' => 'Empty Page'
            ];
        }

        $pixel = DB::select("SELECT * FROM `pixel` WHERE `page` = '$page'");

        if (!$has_error) {
            if (count($pixel) > 0) {
                $pixel = $pixel[0];
                if ($pixel_text == null || $pixel_text == 'null') {
                    $pixel_text = '';
                }
                DB::table('pixel')
                    ->where('page', '=', $page)
                    ->update(['pixel' => $pixel_text]);
            } else {
                if ($pixel_text == null || $pixel_text == 'null') {
                    $pixel_text = '';
                }
                DB::table('pixel')->insert([
                    'page' => $page,
                    'pixel' => $pixel_text
                ]);
            }
        }

        if (count($error) > 0) {
            return response()->json(array('status' => 'error', 'text' => $error['text']));
        } else {
            return response()->json(array('status' => 'success', 'url' => route('admin.admin_seo')));
        }
    }

    public function main_properties() {
        if (!session()->has('logged_in') || !session('logged_in')) {
            return redirect()->route('admin.admin_login');
        }

        $title = $this->pageAdminTitle('main_properties');
        $agent = new Agent();

        $user_login = DB::select("SELECT `login` FROM `user` WHERE `role` = 'administrator'");
        $user_login = $user_login[0];

        return view('admin.main_properties', [
            'title' => $title,
            'agent' => $agent,
            'logged_in' => true,
            "user_login" => $user_login->login,
        ]);
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

    public function save_user_properties(Request $request) {
        $user_login = 'admin';
        $new_password = $request->new_password;

        $md5_pw = md5($new_password);
        DB::update("UPDATE user SET md5_pw = '{$md5_pw}' WHERE `login` = '$user_login'");

        return response()->json(['status' => 'success', 'url' => route('admin.main_properties')]);
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

        $returnHTML = view('admin.ajax.seo_content')->with([
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

        $all_products_info = AdminServices::getAllProductWithCategory();

        $returnHTML = view('admin.ajax.seo_content')->with([
            "templates" => $templates,
            "cur_template_scrin" => $cur_template_scrin,
            "cur_template" => $cur_design,
            "page_properties" => $page_properties,
            'language' => Language::class,
            'all_products_info' => $all_products_info,
            'product_url' => []
        ])->render();

        return response()->json(array('success' => true, 'html' => "$returnHTML"))->header('Content-Type', 'application/json; charset=utf-8');
    }

    public function save_page_properties(Request $request) {
        $page = $request->page;
        $language_id = $request->language_id;
        $title = $request->title;
        $keyword = $request->keyword;
        $description = $request->description;

        DB::update('UPDATE page_properties SET title = "' . $title . '", keyword = "' . $keyword . '", description = "' . $description . '" WHERE page = "' . $page . '" AND language = ' . $language_id);

        return response()->json(['status' => 'success', 'url' => route('admin.admin_seo')]);
    }

    public function available_products() {
        if (!session()->has('logged_in') || !session('logged_in')) {
            return redirect()->route('admin.admin_login');
        }

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

    public function available_packagings() {
        if (!session()->has('logged_in') || !session('logged_in')) {
            return redirect()->route('admin.admin_login');
        }

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


    public function packaging_up_in_sort(Request $request) {
        $selected_packagings_id = $request->selected_packaging;
        $product_id = $request->product_id;

        if ($selected_packagings_id == 'null') {
            $response = [
                'status' => 'error',
                'text' => __('text.admin_common_form_empty_field')
            ];
        } else {
            $showed_packaging_old = AdminServices::getShowedProductPackaging($product_id, 1);

            for($i = count($selected_packagings_id) - 1; $i >= 0;  $i--) {
                $cur_packaging_id = $selected_packagings_id[$i];
                $cur_packaging_order = DB::table('product_packaging')
                    ->where('id', '=', $cur_packaging_id)
                    ->where('is_showed', '=', 1)
                    ->get(['ord'])
                    ->toArray();

                $prev_packaging_index = -1;
                foreach ($showed_packaging_old as $key => $value) {
                    if ($value->id == $cur_packaging_id) {
                        $prev_packaging_index = $key - 1;
                        break;
                    }
                }

                if($prev_packaging_index < 0) {
                    continue;
                } else {
                    $prev_packaging_id = $showed_packaging_old[$prev_packaging_index]->id;
                    $prev_packaging_order = DB::table('product_packaging')
                        ->where('id', '=', $prev_packaging_id)
                        ->where('is_showed', '=', 1)
                        ->get(['ord'])
                        ->toArray();

                    DB::update("UPDATE product_packaging SET ord = '{$cur_packaging_order[0]->ord}' WHERE `id` = '$prev_packaging_id'");
                    DB::update("UPDATE product_packaging SET ord = '{$prev_packaging_order[0]->ord}' WHERE `id` = '$cur_packaging_id'");
                }

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

    public function packaging_down_in_sort(Request $request) {
        $selected_packagings_id = $request->selected_packaging;
        $product_id = $request->product_id;

        if ($selected_packagings_id == 'null') {
            $response = [
                'status' => 'error',
                'text' => __('text.admin_common_form_empty_field')
            ];
        } else {
            $showed_packaging_old = AdminServices::getShowedProductPackaging($product_id, 1);

            for($i = count($selected_packagings_id) - 1; $i >= 0;  $i--) {
                $cur_packaging_id = $selected_packagings_id[$i];
                $cur_packaging_order = DB::table('product_packaging')
                    ->where('id', '=', $cur_packaging_id)
                    ->where('is_showed', '=', 1)
                    ->get(['ord'])
                    ->toArray();

                $next_packaging_index = 0;
                foreach ($showed_packaging_old as $key => $value) {
                    if ($value->id == $cur_packaging_id) {
                        $next_packaging_index = $key + 1;
                        break;
                    }
                }

                if($next_packaging_index > count($showed_packaging_old) - 1) {
                    continue;
                } else {
                    $next_packaging_id = $showed_packaging_old[$next_packaging_index]->id;
                    $next_packaging_order = DB::table('product_packaging')
                        ->where('id', '=', $next_packaging_id)
                        ->where('is_showed', '=', 1)
                        ->get(['ord'])
                        ->toArray();

                    DB::update("UPDATE product_packaging SET ord = '{$cur_packaging_order[0]->ord}' WHERE `id` = '$next_packaging_id'");
                    DB::update("UPDATE product_packaging SET ord = '{$next_packaging_order[0]->ord}' WHERE `id` = '$cur_packaging_id'");
                }

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

    public function products() {
        if (!session()->has('logged_in') || !session('logged_in')) {
            return redirect()->route('admin.admin_login');
        }

        $title = $this->pageAdminTitle('products');
        $agent = new Agent();

        return view('admin.products', [
            'title' => $title,
            'agent' => $agent,
            'logged_in' => true,
        ]);
    }

    public function products_content() {
        $all_products_info = AdminServices::getAllProductWithCategory();

        $returnHTML = view('admin.ajax.products_content')->with([
            'all_products_info' => $all_products_info,
            'Language' => Language::class,
            'product_info' => [],
        ])->render();

        return response()->json(array('success' => true, 'html' => "$returnHTML"));
    }

    public function load_product_info(Request $request) {
        $product_id = $request->product_id;

        $all_products_info = AdminServices::getAllProductWithCategory();
        $product_info = AdminServices::getProductProperties($product_id);

        $returnHTML = view('admin.ajax.products_content')->with([
            'all_products_info' => $all_products_info,
            'Language' => Language::class,
            'product_info' => $product_info,
        ])->render();

        return response()->json(array('success' => true, 'html' => "$returnHTML"));
    }

    public function save_product_info(Request $request) {
        $product_form = $request->product_form_data;

        $product_id = $product_form['all_products_field'];
        $product_show = isset($product_form["is_show_field"]) ? 1 : 0;
        $product_sinonim = str_replace(', ', "\r\n", $product_form["sinonims_field"]);

        DB::update("UPDATE product SET is_showed = $product_show, sinonim = '$product_sinonim' WHERE id = $product_id");

        $error = [];
        foreach (Language::GetAllLanuages() as $language) {
            $product_name = $product_form[$language['id'] . "_name_field"];
            $product_desc = $product_form[$language['id'] . "_desc_field"];
            // $product_url = $product_form[$language['id'] . "_url_field"];
            $product_title = $product_form[$language['id'] . "_title_field"];
            $product_keywords = $product_form[$language['id'] . "_keywords_field"];
            $product_description = $product_form[$language['id'] . "_description_field"];

            $has_errors = false;

            if (empty($product_name)) {
                $has_errors = true;

                $error = [
                    'status' => 'error',
                    'text' => 'Empty Name'
                ];
            }

            // if (empty($product_url)) {
            //     $has_errors = true;

            //     $error = [
            //         'status' => 'error',
            //         'text' => 'Empty URL'
            //     ];
            // }

            if (empty($product_desc)) {
                $has_errors = true;

                $error = [
                    'status' => 'error',
                    'text' => 'Empty Desc'
                ];
            }

            if (!$has_errors) {
                // $product_desc = str_replace("'", "\'", $product_desc);
                // $product_desc = str_replace('"', '\"', $product_desc);
                // $product_desc = str_replace('%', "\%", $product_desc);
                // $product_desc = str_replace('_', "\_", $product_desc);
                // $product_desc = str_replace('>', "\>", $product_desc);
                // $product_desc = str_replace('<', "\<", $product_desc);
                // $product_description = str_replace("'", "\'", $product_description);
                // $product_description = str_replace('"', '\"', $product_description);
                // $product_description = str_replace('%', "\%", $product_description);
                // $product_description = str_replace('_', "\_", $product_description);
                // $product_description = str_replace('>', "\>", $product_description);
                // $product_description = str_replace('<', "\<", $product_description);
                // $product_keywords = str_replace("'", "\'", $product_keywords);
                // $product_keywords = str_replace('"', '\"', $product_keywords);
                // $product_keywords = str_replace('%', "\%", $product_keywords);
                // $product_keywords = str_replace('_', "\_", $product_keywords);
                // $product_keywords = str_replace('>', "\>", $product_keywords);
                // $product_keywords = str_replace('<', "\<", $product_keywords);

                $cur_language_id = $language['id'];

                DB::table('product_desc')
                    ->where('product_id', '=', $product_id)
                    ->where('language_id', '=', $cur_language_id)
                    ->update([
                        'name' => $product_name,
                        'desc' => $product_desc,
                        'title' => $product_title,
                        'keywords' => $product_keywords,
                        'description' => $product_description
                    ]);

                // DB::update('UPDATE product_desc SET `name` = "' . $product_name . '", `desc` = "' . $product_desc . '", `url` = "' . $product_url . '", `title` = "' . $product_title . '", `keywords` = "' . $product_keywords . '", `description` = "' . $product_description . '" WHERE product_id = ' . $product_id . ' AND language_id = ' . $cur_language_id . ' ');
                // DB::update('UPDATE product_desc SET `name` = "' . $product_name . '", `desc` = "' . $product_desc . '", `title` = "' . $product_title . '", `keywords` = "' . $product_keywords . '", `description` = "' . $product_description . '" WHERE product_id = ' . $product_id . ' AND language_id = ' . $cur_language_id . ' ');
            }
        }

        $packaging_list = AdminServices::getShowedProductPackaging($product_id, 1);
        foreach ($packaging_list as $packaging) {
            $cur_min_price = $packaging->min_price;
            $cur_product_packaging_id = $packaging->id;
            $new_product_price = $product_form[$packaging->id . "_price"];

            if ($new_product_price >= $cur_min_price) {
                DB::update("UPDATE product_packaging SET price = $new_product_price WHERE id = $cur_product_packaging_id");
            } else {
                $error = [
                    'status' => 'error',
                    'text' => __('text.admin_products_form_invalid_price')
                ];
            }
        }

        if (count($error) > 0) {
            return response()->json(array('status' => 'error', 'text' => $error['text']));
        } else {
            return response()->json(array('status' => 'success', 'url' => route('admin.products')));
        }
    }

    public function admin_languages() {
        if (!session()->has('logged_in') || !session('logged_in')) {
            return redirect()->route('admin.admin_login');
        }

        $title = $this->pageAdminTitle('languages');
        $agent = new Agent();

        $all_languages = Language::query()->orderBy('ord','asc')->get()->toArray();
        $default_language_code = config('app.language') ? config('app.language') : session('language');

        return view('admin.languages', [
            'title' => $title,
            'agent' => $agent,
            'logged_in' => true,
            'languages_info' => $all_languages,
            'default_language_code' => $default_language_code
        ]);
    }

    public function save_languages_info(Request $request) {
        $languages_form = $request->languages_form_data;

        $default_language_code = $languages_form["default_language_code_field"];
        $this->envUpdate('APP_LANGUAGE', $default_language_code);

        $all_languages = Language::query()->orderBy('ord','asc')->get()->toArray();
        $error = [];
        foreach ($all_languages as $cur_language) {
            $was_errors = false;

            $cur_language_name = $languages_form[$cur_language['id'] . "_name_field"];
            $cur_language_code = $languages_form[$cur_language['id'] . "_code_field"];
            $cur_language_country_iso2 = $languages_form[$cur_language['id'] . "_country_iso2_field"];

            if(empty($cur_language_name)) {
                $was_errors = true;

                $error = [
                    'status' => 'error',
                    'text' => __('text.admin_common_form_empty_field')
                ];
            }

            if(empty($cur_language_code)) {
                $was_errors = true;

                $error = [
                    'status' => 'error',
                    'text' => __('text.admin_common_form_empty_field')
                ];
            }

            if(!$was_errors) {
                DB::table('language')
                    ->where('id', '=', $cur_language["id"])
                    ->update([
                        'name' => $cur_language_name,
                        'code' => $cur_language_code,
                        'country_iso2' => $cur_language_country_iso2
                    ]);
                // DB::update('UPDATE `language` SET `name` = "' . $cur_language_name . '", `code` = "' . $cur_language_code . '", `country_iso2` = "' . $cur_language_country_iso2 . '" WHERE id = ' . $cur_language["id"] . ' ');
            }

            if(isset($languages_form[$cur_language['id'] . '_show_field'])) {
                DB::update("UPDATE `language` SET `show` = 1 WHERE id = " . $cur_language['id']);
            } else {
                DB::update("UPDATE `language` SET `show` = 0 WHERE id = " . $cur_language['id']);
            }
        }

        DB::update("UPDATE `language` SET `show` = 1 WHERE code = '$default_language_code'");

        if (count($error) > 0) {
            return response()->json(array('status' => 'error', 'text' => $error['text']));
        } else {
            return response()->json(array('status' => 'success', 'url' => route('admin.admin_languages')));
        }
    }

    public function admin_currencies() {
        if (!session()->has('logged_in') || !session('logged_in')) {
            return redirect()->route('admin.admin_login');
        }

        $title = $this->pageAdminTitle('currencies');
        $agent = new Agent();

        $all_currencies = Currency::query()->get()->toArray();
        $default_currency_code = config('app.currency') ? config('app.currency') : session('language');

        return view('admin.currencies', [
            'title' => $title,
            'agent' => $agent,
            'logged_in' => true,
            'default_currency_code' => $default_currency_code,
            'currencies_info' => $all_currencies
        ]);
    }

    public function save_currencies_info(Request $request) {
        $currencies_form = $request->currencies_form_data;

        $default_currency_code = $currencies_form["default_currency_code_field"];
        $this->envUpdate('APP_CURRENCY', $default_currency_code);

        $all_currencies = Currency::query()->get()->toArray();
        $error = [];
        foreach ($all_currencies as $cur_currency) {
            $was_errors = false;
            $cur_currency_id = $cur_currency['id'];
            $cur_currency_name = $currencies_form[$cur_currency_id . "_name_field"];
            $cur_currency_code = $currencies_form[$cur_currency_id . "_code_field"];
            $cur_currency_prefix = $currencies_form[$cur_currency_id . "_prefix_field"];
            $cur_currency_coef = $currencies_form[$cur_currency_id . "_coef_field"];
            $cur_currency_country_iso2 = $currencies_form[$cur_currency_id . "_country_iso2_field"];

            if(empty($cur_currency_name)) {
                $was_errors = true;

                $error = [
                    'status' => 'error',
                    'text' => __('text.admin_common_form_empty_field')
                ];
            }

            if(empty($cur_currency_code)) {
                $was_errors = true;

                $error = [
                    'status' => 'error',
                    'text' => __('text.admin_common_form_empty_field')
                ];
            }

            if(empty($cur_currency_prefix)) {
                $was_errors = true;

                $error = [
                    'status' => 'error',
                    'text' => __('text.admin_common_form_empty_field')
                ];
            }

            if(empty($cur_currency_coef)) {
                $was_errors = true;

                $error = [
                    'status' => 'error',
                    'text' => __('text.admin_common_form_empty_field')
                ];
            }

            if(!$was_errors) {
                DB::table('currency')
                    ->where('id', '=', $cur_currency_id)
                    ->update([
                        'name' => $cur_currency_name,
                        'code' => $cur_currency_code,
                        'country_iso2' => $cur_currency_country_iso2,
                        'coef' => $cur_currency_coef,
                        'prefix' => $cur_currency_prefix
                    ]);
                // DB::update('UPDATE `currency` SET `name` = "' . $cur_currency_name . '", `code` = "' . $cur_currency_code . '", `country_iso2` = "' . $cur_currency_country_iso2 . '", `coef` = "' . $cur_currency_coef . '", `prefix` = "' . $cur_currency_prefix . '" WHERE id = ' . $cur_currency_id . ' ');
            }

            if(isset($currencies_form[$cur_currency_id . '_show_field'])) {
                DB::update("UPDATE `currency` SET `show_in_menu` = 1 WHERE id = $cur_currency_id");
            } else {
                DB::update("UPDATE `currency` SET `show_in_menu` = 0 WHERE id = $cur_currency_id");
            }
        }

        DB::update("UPDATE `currency` SET `show_in_menu` = 1 WHERE code = '$default_currency_code'");

        if (count($error) > 0) {
            return response()->json(array('status' => 'error', 'text' => $error['text']));
        } else {

            session()->forget('currency');
            session()->forget('currency_c');

            return response()->json(array('status' => 'success', 'url' => route('admin.admin_currencies')));
        }
    }

    public function gift_card_info(Request $request) {
        $card_info = $request->card_info;

        if ($card_info == 'available') {
            $this->envUpdate('APP_GIFT_CARD', 1);
        } else {
            $this->envUpdate('APP_GIFT_CARD', 0);
        }

        return response()->json(array('status' => 'success', 'url' => route('admin.available_products')));
    }

    public function admin_checkout() {
        if (!session()->has('logged_in') || !session('logged_in')) {
            return redirect()->route('admin.admin_login');
        }

        $title = $this->pageAdminTitle('checkout');
        $agent = new Agent();

        return view('admin.admin_checkout', [
            'title' => $title,
            'agent' => $agent,
            'logged_in' => true,
        ]);
    }

    public function save_checkout_info(Request $request) {
        $default_shipping = $request->default_shipping;
        $default_insur = $request->default_insur;
        $default_secret = $request->default_secret;
        $paypal_setting = $request->paypal_setting;

        $this->envUpdate('APP_DEFAULT_SHIPPING', $default_shipping);
        $this->envUpdate('APP_INSUR_ON', $default_insur);
        $this->envUpdate('APP_SECRET_ON', $default_secret);
        $this->envUpdate('APP_PAYPAL_ON', $paypal_setting);

        return response()->json(array('status' => 'success', 'url' => route('admin.admin_checkout')));
    }

    public function save_subscribe_info(Request $request) {
        $popup_status = $request->popup_status;

        $this->envUpdate('SUBSCRIBE_POPUP_STATUS', $popup_status);

        return response()->json(array('status' => 'success', 'url' => route('admin.index')));
    }

    public function pageAdminTitle($page) {
        switch ($page){
            case 'login':
                $title = __('text.admin_login_title');
                break;
            case 'main_page':
                $title = __('text.admin_main_page_main_page_title');
                break;
            case 'seo':
                $title = __('text.admin_common_main_menu_14_element');
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
            case 'products':
                $title = __('text.admin_products_title');
                break;
            case 'languages':
                $title = __('text.admin_languages_title');
                break;
            case 'currencies':
                $title = __('text.admin_currencies_title');
                break;
            case 'checkout':
                $title = 'Checkout';
                break;
            default:
                $title = 'Admin';
                break;
        }
        return $title;
    }

    public function setEnv($key, $val)
    {
        $path = base_path('.env');

        if (file_exists($path)) {

            file_put_contents($path, str_replace($key . '=' . env($key), $key . '=' . $val, file_get_contents($path)));
        }
    }

    public function envUpdate($flag,$value)
    {
        $allow_flags = ["APP_DESIGN", 'APP_CURRENCY', 'APP_LANGUAGE', 'APP_GIFT_CARD', 'APP_DEFAULT_SHIPPING', 'APP_INSUR_ON', 'APP_SECRET_ON', 'SUBSCRIBE_POPUP_STATUS', 'APP_PAYPAL_ON'];

        if (in_array($flag, $allow_flags))
        {
            $this->setEnv((string)$flag, (string)$value);
            return env(env((string)$flag));
        }

    }
}