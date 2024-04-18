<?php



namespace App\Http\Controllers\Admin;



use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Models\Extra;

use Mail;

use PDF;



class AboutUsController extends Controller
{
    public function Index()
    {
        $data = Extra::where('name', 'about_us')->first();
        $data = $data->content;
        return view('admin.aboutUs.index', ['data' => $data]);

    }

    public function editor()
    {
        $data = Extra::where('name', 'about_us')->first();
        $data = $data->content;
        return view('admin.aboutUs.editor', ['data' => $data]);

    }

    public function save(Request $request)
    {
        Extra::where('name', 'about_us')->update([
            'content' => $request->content,
        ]);
        session()->flash('success', 'About Us Updated/Saved Successfullly');
        return redirect()->route('about-us');

    }
}