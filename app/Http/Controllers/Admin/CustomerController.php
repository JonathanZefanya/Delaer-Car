<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Listing;
use App\Models\PackagePurchase;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use DB;
Use Auth;

class CustomerController extends Controller
{
    public function __construct() {
        $this->middleware('auth.admin:admin');
    }

    public function index() {
        $customers = User::get();
        return view('admin.customer_view', compact('customers'));
    }

    public function create() {
        return view('admin.customer_create');
    }

    public function store(Request $request) {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png,gif',
            'status' => 'required|in:Active,Pending',
        ]);

        $data = new User();
        $data->name = $request->name;
        $data->email = $request->email;
        $data->password = bcrypt($request->password);
        $data->status = $request->status;
        $data->token = Str::random(64);

        if($request->hasFile('photo')) {
            $ext = $request->file('photo')->getClientOriginalExtension();
            $fileName = time().'.'.$ext;
            $request->file('photo')->move(public_path('uploads/user_photos/'), $fileName);
            $data->photo = $fileName;
        }

        $data->save();

        return redirect()->route('admin_customer_view')->with('success', SUCCESS_ACTION);
    }

    public function edit($id) {
        $customer = User::where('id',$id)->first();
        return view('admin.customer_edit', compact('customer'));
    }

    public function update(Request $request, $id) {
        $request->validate([
            'name' => 'required',
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore($id),
            ],
            'password' => 'nullable|min:6',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png,gif',
            'status' => 'required|in:Active,Pending',
        ]);

        $data = User::find($id);
        $data->name = $request->name;
        $data->email = $request->email;
        if($request->password) {
            $data->password = bcrypt($request->password);
        }
        $data->status = $request->status;

        if($request->hasFile('photo')) {
            if($data->photo) {
                unlink(public_path('uploads/user_photos/'.$data->photo));
            }
            $ext = $request->file('photo')->getClientOriginalExtension();
            $fileName = time().'.'.$ext;
            $request->file('photo')->move(public_path('uploads/user_photos/'), $fileName);
            $data->photo = $fileName;
        }

        $data->save();

        return redirect()->route('admin_customer_view')->with('success', SUCCESS_ACTION);
    }

    public function detail($id) {
        $customer_detail = User::where('id',$id)->first();
        return view('admin.customer_detail', compact('customer_detail'));
    }

    public function destroy($id) {

        if(env('PROJECT_MODE') == 0) {
            return redirect()->back()->with('error', env('PROJECT_NOTIFICATION'));
        }

        // Before deleting, check this customer is used in another table
        $cnt = Listing::where('admin_id',0)->where('user_id',$id)->count();
        if($cnt>0) {
            return redirect()->back()->with('error', ERR_ITEM_DELETE);
        }

        $cnt1 = PackagePurchase::where('user_id',$id)->count();
        if($cnt1>0) {
            return redirect()->back()->with('error', ERR_ITEM_DELETE);
        }

        $cnt2 = Review::where('agent_id',$id)->where('agent_type','Customer')->count();
        if($cnt2>0) {
            return redirect()->back()->with('error', ERR_ITEM_DELETE);
        }

        User::where('id', $id)->delete();
        return Redirect()->back()->with('success', SUCCESS_ACTION);
    }

    public function change_status($id) {
        $customer = User::find($id);
        if($customer->status == 'Active') {
            if(env('PROJECT_MODE') == 0) {
                $message=env('PROJECT_NOTIFICATION');
            } else {
                $customer->status = 'Pending';
                $message=SUCCESS_ACTION;
                $customer->save();
            }
        } else {
            if(env('PROJECT_MODE') == 0) {
                $message=env('PROJECT_NOTIFICATION');
            } else {
                $customer->status = 'Active';
                $message=SUCCESS_ACTION;
                $customer->save();
            }
        }
        return response()->json($message);
    }
}
