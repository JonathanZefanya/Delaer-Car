<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Listing;
use App\Models\ListingBrand;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Mail;
use DB;
use Auth;

class ListingBrandController extends Controller
{
    public function __construct() {
        $this->middleware('auth.admin:admin');
    }

    public function index() {
        $listing_brand = ListingBrand::orderBy('id', 'asc')->get();
        return view('admin.listing_brand_view', compact('listing_brand'));
    }

    public function create() {
        $listing_brand = ListingBrand::get();
        return view('admin.listing_brand_create', compact('listing_brand'));
    }

    public function store(Request $request) {

        if(env('PROJECT_MODE') == 0) {
            return redirect()->back()->with('error', env('PROJECT_NOTIFICATION'));
        }

        $request->validate([
            'listing_brand_name' => 'required|unique:listing_brands',
            'listing_brand_slug' => 'unique:listing_brands',
            'listing_brand_photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ],[
            'listing_brand_name.required' => ERR_NAME_REQUIRED,
            'listing_brand_name.unique' => ERR_NAME_EXIST,
            'listing_brand_slug.unique' => ERR_SLUG_UNIQUE,
            'listing_brand_photo.required' => ERR_PHOTO_REQUIRED,
            'listing_brand_photo.image' => ERR_PHOTO_IMAGE,
            'listing_brand_photo.mimes' => ERR_PHOTO_JPG_PNG_GIF,
            'listing_brand_photo.max' => ERR_PHOTO_MAX
        ]);

        $statement = DB::select("SHOW TABLE STATUS LIKE 'listing_brands'");
        $ai_id = $statement[0]->Auto_increment;

        $ext = $request->file('listing_brand_photo')->extension();
        $rand_value = md5(mt_rand(11111111,99999999));
        $final_name = $rand_value.'.'.$ext;
        $request->file('listing_brand_photo')->move(public_path('uploads/listing_brand_photos/'), $final_name);

        $listing_brand = new ListingBrand();
        $data = $request->only($listing_brand->getFillable());
        if(empty($data['listing_brand_slug'])) {
            unset($data['listing_brand_slug']);
            $data['listing_brand_slug'] = Str::slug($request->listing_brand_name);
        }

        if(preg_match('/\s/',$data['listing_brand_slug'])) {
            return Redirect()->back()->with('error', ERR_SLUG_WHITESPACE);
        }

        unset($data['listing_brand_photo']);
        $data['listing_brand_photo'] = $final_name;
        
        $listing_brand->fill($data)->save();

        return redirect()->route('admin_listing_brand_view')->with('success', SUCCESS_ACTION);
    }

    public function edit($id) {
        $listing_brand = ListingBrand::findOrFail($id);
        return view('admin.listing_brand_edit', compact('listing_brand'));
    }

    public function update(Request $request, $id) {

        if(env('PROJECT_MODE') == 0) {
            return redirect()->back()->with('error', env('PROJECT_NOTIFICATION'));
        }

        $listing_brand = ListingBrand::findOrFail($id);
        $data = $request->only($listing_brand->getFillable());

        if ($request->hasFile('listing_brand_photo')) {

            $request->validate([
                'listing_brand_photo' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
            ],[
                'listing_brand_photo.image' => ERR_PHOTO_IMAGE,
                'listing_brand_photo.mimes' => ERR_PHOTO_JPG_PNG_GIF,
                'listing_brand_photo.max' => ERR_PHOTO_MAX
            ]);

            unlink(public_path('uploads/listing_brand_photos/'.$listing_brand->listing_brand_photo));

            // Uploading the file
            $ext = $request->file('listing_brand_photo')->extension();
            $rand_value = md5(mt_rand(11111111,99999999));
            $final_name = $rand_value.'.'.$ext;
            $request->file('listing_brand_photo')->move(public_path('uploads/listing_brand_photos/'), $final_name);

            unset($data['listing_brand_photo']);
            $data['listing_brand_photo'] = $final_name;
        }

        $request->validate([
            'listing_brand_name'   =>  [
                'required',
                Rule::unique('listing_brands')->ignore($id),
            ],
            'listing_brand_slug'   =>  [
                Rule::unique('listing_brands')->ignore($id),
            ]
        ],[
            'listing_brand_name.required' => ERR_NAME_REQUIRED,
            'listing_brand_name.unique' => ERR_NAME_EXIST,
            'listing_brand_slug.unique' => ERR_SLUG_UNIQUE,
        ]);

        if(empty($data['listing_brand_slug']))
        {
            unset($data['listing_brand_slug']);
            $data['listing_brand_slug'] = Str::slug($request->listing_brand_name);
        }

        if(preg_match('/\s/',$data['listing_brand_slug']))
        {
            return Redirect()->back()->with('error', ERR_SLUG_WHITESPACE);
        }

        $listing_brand->fill($data)->save();

        return redirect()->route('admin_listing_brand_view')->with('success', SUCCESS_ACTION);
    }

    public function destroy($id)
    {

        if(env('PROJECT_MODE') == 0) {
            return redirect()->back()->with('error', env('PROJECT_NOTIFICATION'));
        }
        
        $tot = Listing::where('listing_brand_id',$id)->count();
        if($tot) {
            return Redirect()->back()->with('error', ERR_ITEM_DELETE);   
        }

        $listing_brand = ListingBrand::findOrFail($id);
        unlink(public_path('uploads/listing_brand_photos/'.$listing_brand->listing_brand_photo));
        $listing_brand->delete();

        // Success Message and redirect
        return Redirect()->back()->with('success', SUCCESS_ACTION);
    }

}
