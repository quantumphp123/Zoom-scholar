<?php



namespace App\Http\Controllers\Admin;



use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Models\Extra;

use Mail;

use PDF;



class TermsAndConditionsController extends Controller
{
    public function Index()
    {
        $data = Extra::where('name', 'terms_and_conditions')->first();
        $data = $data->content;
        return view('admin.termsAndConditions.index', ['data' => $data]);

    }

    public function editor()
    {
        $data = Extra::where('name', 'terms_and_conditions')->first();
        $data = $data->content;
        return view('admin.termsAndConditions.editor', ['data' => $data]);

    }

    public function save(Request $request)
    {
        Extra::where('name', 'terms_and_conditions')->update([
            'content' => $request->content,
        ]);
        session()->flash('success', 'Terms anb Conditions Updated/Saved Successfullly');
        return redirect()->route('terms-and-conditions');

    }
}