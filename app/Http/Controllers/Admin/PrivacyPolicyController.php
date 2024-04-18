<?php



namespace App\Http\Controllers\Admin;



use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Models\Extra;

use Mail;

use PDF;

class PrivacyPolicyController extends Controller
{
    public function Index()
    {
        $data = Extra::where('name', 'privacy_policy')->first();
        $data = $data->content;
        return view('admin.privacyPolicy.index', ['data' => $data]);

    }

    public function editor()
    {
        $data = Extra::where('name', 'privacy_policy')->first();
        $data = $data->content;
        return view('admin.privacyPolicy.editor', ['data' => $data]);

    }

    public function save(Request $request)
    {
        Extra::where('name', 'privacy_policy')->update([
            'content' => $request->content,
        ]);
        session()->flash('success', 'Privacy Policy Updated/Saved Successfullly');
        return redirect()->route('privacy-policy');

    }
     public function privacyPolicy()
    {
       
        return view('admin.privacyPolicy.view');

    }
}