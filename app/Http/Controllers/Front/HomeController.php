<?php
namespace App\Http\Controllers\Front;
use App\Http\Controllers\Controller;
use App\Models\PageHomeItem;
use Illuminate\Http\Request;
use App\Models\Listing;
use App\Models\ListingBrand;
use App\Models\ListingLocation;
use App\Models\HomeAdvertisement;
use App\Models\Testimonial;
use DB;

class HomeController extends Controller
{
    public function index()
    {
        $adv_home_data = HomeAdvertisement::where('id',1)->first();

    	$page_home_items = PageHomeItem::where('id',1)->first();

        $testimonials = Testimonial::get();

        $listing_brands = ListingBrand::orderBy('listing_brand_name','asc')->get();
        $listing_locations = ListingLocation::orderBy('listing_location_name','asc')->get();

        $orderwise_listing_brands = DB::select('SELECT *
                        FROM listing_brands as r1
                        LEFT JOIN (SELECT listing_brand_id, count(*) as total
                            FROM listings as l
                            JOIN listing_brands as lc
                            ON l.listing_brand_id = lc.id
                            GROUP BY listing_brand_id
                            ORDER BY total DESC) as r2
                        ON r1.id = r2.listing_brand_id
                        ORDER BY r2.total DESC');

        $orderwise_listing_locations = DB::select('SELECT *
                        FROM listing_locations as r1
                        LEFT JOIN (SELECT listing_location_id, count(*) as total
                            FROM listings as l
                            JOIN listing_locations as ll
                            ON l.listing_location_id = ll.id
                            GROUP BY listing_location_id
                            ORDER BY total DESC) as r2
                        ON r1.id = r2.listing_location_id
                        ORDER BY r2.total DESC');

        $listings = Listing::with('rListingBrand','rListingLocation')
            ->orderBy('listing_name','asc')
            ->where('listing_status','Active')
            ->where('is_featured','Yes')
            ->get();

        return view('front.index', compact('adv_home_data','page_home_items','orderwise_listing_brands','orderwise_listing_locations','listings','listing_brands','listing_locations','testimonials'));
    }
}
