<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use App\Models\GeneralSetting;
use App\Models\Category;
use App\Models\Brand;
use App\Models\SocialMedia;
use App\Models\Contact;
use App\Models\CreatePage;
use App\Models\OrderStatus;
use App\Models\EcomPixel;
use App\Models\GoogleTagManager;
use App\Models\Order;
use App\Models\PaymentGateway;
use Config;
use Session;
use Throwable;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            return;
        }

        // ২. Safety Catch: যদি কোনো কারণে DB Connection না থাকে তবে যেন App ক্র্যাশ না করে
        try {
            // shurjoPay Config
            if (Schema::hasTable('payment_gateways')) {
                $shurjopay = PaymentGateway::where(['status' => 1, 'type' => 'shurjopay'])->first();
                if ($shurjopay) {
                    config([
                        'shurjopay.apiCredentials.username'   => $shurjopay->username,
                        'shurjopay.apiCredentials.password'   => $shurjopay->password,
                        'shurjopay.apiCredentials.prefix'     => $shurjopay->prefix,
                        'shurjopay.apiCredentials.return_url' => $shurjopay->success_url,
                        'shurjopay.apiCredentials.cancel_url' => $shurjopay->return_url,
                        'shurjopay.apiCredentials.base_url'   => $shurjopay->base_url,
                    ]);
                }
            }

            // General Setting
            if (Schema::hasTable('general_settings')) {
                $generalsetting = GeneralSetting::where('status', 1)->limit(1)->first();
                view()->share('generalsetting', $generalsetting);
            }

            // Categories
            if (Schema::hasTable('categories')) {
                $sidecategories = Category::where('parent_id', '=', '0')->where('status', 1)->select('id', 'name', 'slug', 'status', 'image')->get();
                view()->share('sidecategories', $sidecategories);

                $menucategories = Category::where('status', 1)->select('id', 'name', 'slug', 'status', 'image')->get();
                view()->share('menucategories', $menucategories);
            }

            // Contact
            if (Schema::hasTable('contacts')) {
                $contact = Contact::where('status', 1)->first();
                view()->share('contact', $contact);
            }

            // Social Media
            if (Schema::hasTable('social_media')) {
                $socialicons = SocialMedia::where('status', 1)->get();
                view()->share('socialicons', $socialicons);
            }

            // Pages
            if (Schema::hasTable('create_pages')) {
                $pages = CreatePage::where('status', 1)->limit(3)->get();
                view()->share('pages', $pages);

                $pagesright = CreatePage::where('status', 1)->skip(3)->limit(10)->get();
                view()->share('pagesright', $pagesright);

                $cmnmenu = CreatePage::where('status', 1)->get();
                view()->share('cmnmenu', $cmnmenu);
            }

            // Brands
            if (Schema::hasTable('brands')) {
                $brands = Brand::where('status', 1)->get();
                view()->share('brands', $brands);
            }

            // Orders
            if (Schema::hasTable('orders')) {
                $neworder = Order::where('order_status', '1')->count();
                view()->share('neworder', $neworder);

                $pendingorder = Order::where('order_status', '1')->latest()->limit(9)->get();
                view()->share('pendingorder', $pendingorder);
            }

            // Order Status
            if (Schema::hasTable('order_statuses')) {
                $orderstatus = OrderStatus::get();
                view()->share('orderstatus', $orderstatus);
            }

            // Ecom Pixel
            if (Schema::hasTable('ecom_pixels')) {
                $pixels = EcomPixel::where('status', 1)->get();
                view()->share('pixels', $pixels);
            }

            // GTM
            if (Schema::hasTable('google_tag_managers')) {
                $gtm_code = GoogleTagManager::where('status', 1)->get();
                view()->share('gtm_code', $gtm_code);
            }
        } catch (Throwable $e) {
        }
    }
}