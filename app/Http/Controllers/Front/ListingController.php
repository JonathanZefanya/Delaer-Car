<?php
namespace App\Http\Controllers\Front;
use App\Http\Controllers\Controller;
use App\Mail\ListingPageMessage;
use App\Mail\ListingPageReport;
use App\Models\EmailTemplate;
use App\Models\GeneralSetting;
use App\Models\Listing;
use App\Models\ListingAdditionalFeature;
use App\Models\ListingAmenity;
use App\Models\ListingBrand;
use App\Models\ListingLocation;
use App\Models\ListingPhoto;
use App\Models\ListingSocialItem;
use App\Models\ListingVideo;
use App\Models\Amenity;
use App\Models\PageListingBrandItem;
use App\Models\PageListingItem;
use App\Models\PageListingLocationItem;
use App\Models\Review;
use App\Models\Wishlist;
use App\Models\User;
use App\Models\Admin;
use Illuminate\Http\Request;
use DB;
use Auth;
use Illuminate\Support\Facades\Mail;

class ListingController extends Controller
{
	public function index()
    {
        abort(404);
	}

    public function detail($slug)
    {
        $g_setting = GeneralSetting::where('id', 1)->first();
        $detail = Listing::with('rListingLocation', 'rListingBrand')
        	->where('listing_slug', $slug)
        	->first();

        $listing_social_items = ListingSocialItem::where('listing_id', $detail->id)->get();
        $listing_photos = ListingPhoto::where('listing_id', $detail->id)->get();
        $listing_videos = ListingVideo::where('listing_id', $detail->id)->get();
        $listing_amenities = ListingAmenity::where('listing_id', $detail->id)->get();
        $listing_additional_features = ListingAdditionalFeature::where('listing_id', $detail->id)->get();
        $listing_brands = ListingBrand::orderBy('listing_brand_name', 'asc')->get();
        $listing_locations = ListingLocation::orderBy('listing_location_name', 'asc')->get();

        $reviews = Review::where('listing_id',$detail->id)
            ->orderBy('id', 'asc')
            ->get();

        // Getting overall rating
        if($reviews->isEmpty()) {
            $overall_rating = 0;
        } else {
            $total_number = 0;
            $count = 0;
            foreach($reviews as $item) {
                $count++;
                $total_number = $total_number+$item->rating;
            }
            $overall_rating = $total_number/$count;
            if($overall_rating>0 && $overall_rating<=1) {
                $overall_rating = 1;
            }
            elseif($overall_rating>1 && $overall_rating<=1.5) {
                $overall_rating = 1.5;
            }
            elseif($overall_rating>1.5 && $overall_rating<=2) {
                $overall_rating = 2;
            }
            elseif($overall_rating>2 && $overall_rating<=2.5) {
                $overall_rating = 2.5;
            }
            elseif($overall_rating>2.5 && $overall_rating<=3) {
                $overall_rating = 3;
            }
            elseif($overall_rating>3 && $overall_rating<=3.5) {
                $overall_rating = 3.5;
            }
            elseif($overall_rating>3.5 && $overall_rating<=4) {
                $overall_rating = 4;
            }
            elseif($overall_rating>4 && $overall_rating<=4.5) {
                $overall_rating = 4.5;
            }
            elseif($overall_rating>4.5 && $overall_rating<=5) {
                $overall_rating = 5;
            }
        }

        if($detail->user_id == 0) {
            $agent_detail = Admin::where('id',$detail->admin_id)->first();
        } elseif($detail->admin_id == 0) {
            $agent_detail = User::where('id',$detail->user_id)->first();
        }

        $current_auth_user_id = 0;
        if(Auth::user()) {
            $current_auth_user_id = Auth::user()->id;
        }

        // If he already given review for this item
        $already_given = 0;
        $already_given = Review::where('listing_id', $detail->id)
            ->where('agent_id', $current_auth_user_id)
            ->where('agent_type', 'Customer')
            ->count();

        $all_amenities = Amenity::orderBy('id', 'asc')->get();

    	return view('front.listing_detail', compact('detail','g_setting','listing_social_items','listing_photos','listing_videos','listing_amenities','listing_additional_features','listing_brands','listing_locations','agent_detail','reviews','current_auth_user_id', 'already_given', 'overall_rating','all_amenities'));
    }

    public function brand_all()
    {
        $g_setting = GeneralSetting::where('id', 1)->first();
        $listing_brand_page_data = PageListingBrandItem::where('id', 1)->first();
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
        return view('front.listing_brands', compact('g_setting', 'listing_brand_page_data', 'orderwise_listing_brands'));
    }

    public function brand_detail($slug)
    {
    	$g_setting = GeneralSetting::where('id', 1)->first();
        $listing_brand_page_data = PageListingBrandItem::where('id', 1)->first();
        $listing_brand_detail = ListingBrand::where('listing_brand_slug',$slug)->first();
    	$listing_items = Listing::with('rListingBrand','rListingLocation')->where('listing_brand_id',$listing_brand_detail->id)->paginate(15);
    	return view('front.listing_brand_detail', compact('g_setting', 'listing_brand_detail', 'listing_items', 'listing_brand_page_data'));
    }

    public function location_all()
    {
        $g_setting = GeneralSetting::where('id', 1)->first();
        $listing_location_page_data = PageListingLocationItem::where('id', 1)->first();
        $orderwise_listing_locations = DB::select('SELECT *
                        FROM listing_locations as r1
                        LEFT JOIN (SELECT listing_location_id, count(*) as total
                            FROM listings as l
                            JOIN listing_brands as lc
                            ON l.listing_location_id = lc.id
                            GROUP BY listing_location_id
                            ORDER BY total DESC) as r2
                        ON r1.id = r2.listing_location_id
                        ORDER BY r2.total DESC');

        return view('front.listing_locations', compact('g_setting', 'listing_location_page_data', 'orderwise_listing_locations'));
    }

    public function location_detail($slug)
    {
        $g_setting = GeneralSetting::where('id', 1)->first();
        $listing_location_page_data = PageListingLocationItem::where('id', 1)->first();
        $listing_location_detail = ListingLocation::where('listing_location_slug',$slug)->first();
        $listing_items = Listing::with('rListingBrand','rListingLocation')->where('listing_location_id',$listing_location_detail->id)->paginate(15);
        return view('front.listing_location_detail', compact('g_setting', 'listing_location_detail', 'listing_items', 'listing_location_page_data'));
    }

    public function agent_detail($type,$id)
    {
        $g_setting = GeneralSetting::where('id', 1)->first();

	    if($type == 'admin') {
            $agent_detail = Admin::where('id',$id)->first();
            $all_listings = Listing::with('rListingBrand', 'rListingLocation')
                ->where('admin_id',$id)
                ->where('listing_status','Active')
                ->get();
        } else {
            $agent_detail = User::where('id',$id)->first();
            $all_listings = Listing::with('rListingBrand', 'rListingLocation')
                ->where('user_id',$id)
                ->where('listing_status','Active')
                ->get();
        }
    	return view('front.listing_agent_detail', compact('g_setting','agent_detail','all_listings'));
    }

    public function listing_result(Request $request)
    {
        $g_setting = GeneralSetting::where('id', 1)->first();
        $listing_page_data = PageListingItem::where('id', 1)->first();
        $listing_brands = ListingBrand::get();
        $listing_locations = ListingLocation::get();
        $amenities = Amenity::get();

        // Breaking Urls
        $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $actual_link_len = strlen($actual_link);

        $first_part = url()->current();
        $first_part_len = strlen($first_part);

        $all_brand = [];
        $all_location = [];
        $all_amenity = [];

        $aa = substr($actual_link,($first_part_len+1),($actual_link_len-1));
        $arr = explode('&',$aa);


        if($request->amenity){
            $listings = Listing::whereHas('listingAminities', function($query) use ($request){
                $sortArr = [];
                if($request->amenity){
                    foreach($request->amenity as $amnty){
                        $sortArr[] = $amnty;
                    }
                    $query->whereIn('amenity_id', $sortArr);
                }
            })->with('user')->orderBy('id','desc');
        }else{
            $listings = Listing::with('user')->orderBy('id','desc');
        }

        if($request->location){
            $location_arr = $request->location;
            $listings = $listings->whereIn('listing_location_id', $location_arr);
        }

        if($request->brand){
            $brand_arr = $request->brand;
            $listings = $listings->whereIn('listing_brand_id', $brand_arr);
        }


        if($request->listing_type){
            if($request->listing_type == 'New Car'){
                $listings = $listings->where('listing_type','New Car');
            }
            if($request->listing_type == 'Used Car'){
                $listings = $listings->where('listing_type','Used Car');
            }
        }

        if($request->text){
            $listings = $listings->where('listing_name', 'LIKE', '%'.$request->text.'%');
        }

        $listings = $listings->paginate(20);
        $listings = $listings->appends($request->all());



        return view('front.listing_result', compact('g_setting','listing_page_data','listing_brands', 'listing_locations', 'amenities', 'all_brand', 'all_location', 'all_amenity', 'listings'));

    }

    public function search_listing(Request $request){

        $g_setting = GeneralSetting::where('id', 1)->first();
        $listing_page_data = PageListingItem::where('id', 1)->first();
        $listing_brands = ListingBrand::get();
        $listing_locations = ListingLocation::get();
        $amenities = Amenity::get();

        // Breaking Urls
        $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $actual_link_len = strlen($actual_link);

        $first_part = url()->current();
        $first_part_len = strlen($first_part);

        $all_brand = [];
        $all_location = [];
        $all_amenity = [];

        $aa = substr($actual_link,($first_part_len+1),($actual_link_len-1));
        $arr = explode('&',$aa);


        if($request->amenity){
            $listings = Listing::whereHas('listingAminities', function($query) use ($request){
                $sortArr = [];
                if($request->amenity){
                    foreach($request->amenity as $amnty){
                        $sortArr[] = $amnty;
                    }
                    $query->whereIn('amenity_id', $sortArr);
                }
            })->with('user')->orderBy('id','desc');
        }else{
            $listings = Listing::with('user')->orderBy('id','desc');
        }

        if($request->location){
            $location_arr = $request->location;
            $listings = $listings->whereIn('listing_location_id', $location_arr);
        }

        if($request->brand){
            $brand_arr = $request->brand;
            $listings = $listings->whereIn('listing_brand_id', $brand_arr);
        }


        if($request->listing_type){
            if($request->listing_type == 'New Car'){
                $listings = $listings->where('listing_type','New Car');
            }
            if($request->listing_type == 'Used Car'){
                $listings = $listings->where('listing_type','Used Car');
            }
        }

        if($request->text){
            $listings = $listings->where('listing_name', 'LIKE', '%'.$request->text.'%');
        }

        $listings = $listings->paginate(20);
        $listings = $listings->appends($request->all());



        return view('front.listing_result', compact('g_setting','listing_page_data','listing_brands', 'listing_locations', 'amenities', 'all_brand', 'all_location', 'all_amenity', 'listings'));
    }


    public function search_listing_result(Request $request){
        if($request->amenity){
            $listings = Listing::whereHas('listingAminities', function($query) use ($request){
                $sortArr = [];
                if($request->amenity){
                    foreach($request->amenity as $amnty){
                        $sortArr[] = $amnty;
                    }
                    $query->whereIn('amenity_id', $sortArr);
                }
            })->with('user')->orderBy('id','desc');
        }else{
            $listings = Listing::with('user')->orderBy('id','desc');
        }

        if($request->location){
            if($request->location[0] != null){
                $location_arr = $request->location;
                $listings = $listings->whereIn('listing_location_id', $location_arr);
            }
        }

        if($request->brand){
            if($request->brand[0] != null){
                $brand_arr = $request->brand;
                $listings = $listings->whereIn('listing_brand_id', $brand_arr);
            }
        }

        if($request->listing_type){
            if($request->listing_type == 'New Car'){
                $listings = $listings->where('listing_type','New Car');
            }
            if($request->listing_type == 'Used Car'){
                $listings = $listings->where('listing_type','Used Car');
            }
        }

        if($request->text){
            $listings = $listings->where('listing_name', 'LIKE', '%'.$request->text.'%');
        }
        $listings = $listings->where('listing_status','Active');
        $listings = $listings->paginate(20);
        $listings = $listings->appends($request->all());


        return view('front.ajax_search_listing', compact('listings'));
    }

    public function send_message(Request $request)
    {
        if (env('PROJECT_MODE') == 0) {
            return redirect()->back()->with('error', env('PROJECT_NOTIFICATION'));
        }

        $g_setting = GeneralSetting::where('id', 1)->first();

        // Validasi input
        $request->validate([
            'name'    => 'required',
            'email'   => 'required|email',
            'message' => 'required'
        ], [
            'name.required'    => ERR_NAME_REQUIRED,
            'email.required'   => ERR_EMAIL_REQUIRED,
            'email.email'      => ERR_EMAIL_INVALID,
            'message.required' => ERR_MESSAGE_REQUIRED
        ]);

        // Validasi Google reCAPTCHA jika aktif
        if ($g_setting->google_recaptcha_status == 'Show') {
            $request->validate([
                'g-recaptcha-response' => 'required'
            ], [
                'g-recaptcha-response.required' => ERR_RECAPTCHA_REQUIRED
            ]);
        }

        // Data agent dan listing
        $listing_name = $request->listing_name;
        $listing_slug = $request->listing_slug;
        $agent_name   = $request->agent_name;
        $agent_phone  = $request->phone ?? ''; // nomor agent

        // Redirect ke WhatsApp jika nomor tersedia
        if (!empty($agent_phone)) {
            $clean_phone = preg_replace('/[^0-9]/', '', $agent_phone);
            if (substr($clean_phone, 0, 1) === '0') {
                $clean_phone = '62' . substr($clean_phone, 1); // ubah 08xx jadi 628xx
            }

            $wa_text = "Halo $agent_name, Saya $request->name ($request->email)";
            if (!empty($request->phone)) {
                $wa_text .= " - $request->phone";
            }
            $wa_text .= "\nTertarik dengan listing: $listing_name";
            $wa_text .= " \n\nLink: " . url('listing/' . $listing_slug);
            $wa_text .= " \n\nPesan: " . $request->message;

            $wa_url = "https://wa.me/$clean_phone?text=" . urlencode($wa_text);

            return redirect($wa_url);
        }

        // Jika tidak ada nomor agent, kembali ke halaman dengan pesan error
        return redirect()->back()->with('error', 'Nomor WhatsApp agent tidak tersedia.');
    }

    public function report_listing(Request $request)
    {
        if(env('PROJECT_MODE') == 0) {
            return redirect()->back()->with('error', env('PROJECT_NOTIFICATION'));
        }

        $g_setting = GeneralSetting::where('id', 1)->first();
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'message' => 'required'
        ], [
            'name.required' => ERR_NAME_REQUIRED,
            'email.required' => ERR_EMAIL_REQUIRED,
            'email.email' => ERR_EMAIL_INVALID,
            'message.required' => ERR_MESSAGE_REQUIRED,
        ]);

        if($g_setting->google_recaptcha_status == 'Show') {
            $request->validate([
                'g-recaptcha-response' => 'required'
            ], [
                'g-recaptcha-response.required' => ERR_RECAPTCHA_REQUIRED
            ]);
        }

        $listing_name = $request->listing_name;
        $listing_url = '<a href="'.url('listing/'.$request->listing_slug).'">'.url('listing/'.$request->listing_slug).'</a>';

        // Send Email
        $email_template_data = EmailTemplate::where('id', 10)->first();
        $subject = $email_template_data->et_subject;
        $message = $email_template_data->et_content;

        $message = str_replace('[[listing_name]]', $listing_name, $message);
        $message = str_replace('[[listing_url]]', $listing_url, $message);
        $message = str_replace('[[name]]', $request->name, $message);
        $message = str_replace('[[email]]', $request->email, $message);
        $message = str_replace('[[phone]]', $request->phone, $message);
        $message = str_replace('[[message]]', $request->message, $message);

        $admin_data = Admin::where('id',1)->first();

        Mail::to($admin_data->email)->send(new ListingPageReport($subject,$message));

        return redirect()->back()->with('success', SUCCESS_REPORT_SENT);
    }

    public function wishlist_add($id)
    {
        if(env('PROJECT_MODE') == 0) {
            return redirect()->back()->with('error', env('PROJECT_NOTIFICATION'));
        }

	    if(Auth::user() == null) {
            return redirect()->back()->with('error', ERR_LOGIN_FIRST);
        }

	    $check_previous = Wishlist::where('listing_id',$id)->count();
	    if($check_previous > 0) {
            return redirect()->back()->with('error', ERR_ALREADY_TO_WISHLIST);
        }

	    $user_data = Auth::user();

        $obj = new Wishlist;
        $obj->user_id = $user_data->id;
        $obj->listing_id = $id;
        $obj->save();

        return redirect()->back()->with('success', SUCCESS_WISHLIST_ADD);
    }
}
