<?php



namespace App\Http\Controllers\Admin;



use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Models\Interest;

use Mail;

use PDF;



class InterestController extends Controller
{



    public function Index()
    {

        $data = Interest::get();

        //dd($users);

        return view('admin.Interests.Index', compact('data'));

    }

    public function change_status(Request $request)
    {
        $status = $request->status;
        if ($status == 1) {
            $status = 0;
        } else {
            $status = 1;
        }

        Interest::where('id', $request->id)->update([
            'status' => $status,
        ]);

        return response()->json([
            'status' => $status
        ]);
    }

    public function add(Request $request) {
        $request->validate([
            'name' => 'required',
            'image' => 'required | file',
        ]);

        // Uploading new image to project and its url into db
        $image = $request->file('image');
        $image_name = time() . '.' . $request->file('image')->getClientOriginalExtension();
        $image->move(public_path('interest'), $image_name);
        $baseurl = url('/');
        $path = $baseurl . "/public/interest/" . $image_name;
        Interest::insert([
            'name' => $request->name,
            'status' => 1,
            'image' => $path,
            'created_at' => now(),
        ]);

        session()->flash('success', 'Interest has been Added Successfully');
        return redirect()->route('Admin.InterestIndex');
    }

    public function edit(Request $request)
    {
        Interest::where('id', $request->id)->update([
            'name' => $request->name,
        ]);

        if ($request->file('image') != null) {
            // deleting old image from project and db
            $url = Interest::where('id', $request->id)->select('image')->get()->toArray();
            $image_name = substr($url[0]['image'], strlen(url('/')));
            $image_path = public_path() . $image_name;
            if (file_exists($image_path)) {
                if ($image_name != null) {
                    unlink($image_path);
                }
            }
            // Uploading new image to project and its url into db
            $image = $request->file('image');
            $image_name = time() . '.' . $request->file('image')->getClientOriginalExtension();
            $image->move(public_path('interest'), $image_name);
            $baseurl = url('/');
            $path = $baseurl . "/public/interest/" . $image_name;
            Interest::where('id', $request->id)->update([
                'image' => $path,
            ]);
        }

        session()->flash('success', 'Interest has been Updated Successfully');
        return redirect()->route('Admin.InterestIndex');
    }

    public function delete($id)
    {
        Interest::where('id', $id)->delete();
        session()->flash('success', 'Interest has been Deleted Successfully');
        return redirect()->route('Admin.InterestIndex');
    }







}