<?php



namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Interest;
use App\Models\Post;
use App\Models\Question;
use Mail;
use DB;
use PDF;



class UserController extends Controller
{



    public function Index()
    {

        $data=User::where('role','user')->get();
        //dd($users);

        return view('admin.User.Index', compact('data'));

    }

    public function dashboard() {
        $data = [
            'total_users' => User::count(),
            'total_interests' => Interest::count(),
        ];
        return view('admin.Dashboard', ['data' => $data]);
    }

    public function change_status(Request $request)
    {
        $status = $request->status;
        if ($status == 1) {
            $status = 0;
        } else {
            $status = 1;
        }

        User::where('id', $request->id)->update([
            'status' => $status,
        ]);

        return response()->json([
            'status' => $status
        ]);
    }


    public function delete($id)
    {

        if (\DB::table('post_likes')->where('user_id', $id)->exists()) {
            \DB::table('post_likes')->where('user_id', $id)->delete();
        }
        if (\DB::table('post_dislikes')->where('user_id', $id)->exists()) {
            \DB::table('post_dislikes')->where('user_id', $id)->delete();
        }
        if (\DB::table('comment_replies')->where('user_id', $id)->exists()) {
            \DB::table('comment_replies')->where('user_id', $id)->delete();
        }
        User::where('id', $id)->delete();
        session()->flash('success', 'User has been Deleted Successfully');
        return redirect()->route('Admin.UserIndex');
    }


    public function getUserPosts(Request $request)
    {
        if($request->has('userId')) {
            $userId = $request->userId;
            $data = Post::where('user_id',$userId)->get();
            return view('admin.posts.index', compact('data')) ;
        } else {
            session()->flash('success', 'Some error occured');
            return back();
        }
    }

    public function getUserQuestions(Request $request)
    {
        if($request->has('userId')) {
            $userId = $request->userId;
            $questions = Question::where('user_id',$userId)->get();
            $questionsMap = $questions->map(function($question) {
                $user = User::find($question->user_id);
                $question->askedBy = $user->first_name." ".$user->last_name;
                $question->askedByImage = $user->profile_image;


                $answers = DB::table('answers')->where('question_id', $question->id)->get();
                $answerMap = $answers->map(function($answer){
                    $userr = User::find($answer->user_id);
                    $answer->answeredBy = $userr->first_name." ".$userr->last_name;
                    $answer->answeredByImage = $userr->profile_image." ".$userr->profile_image;
                    return $answer;
                });
                $question->answers = $answerMap;

                return $question;
            });
            // return $questionsMap;
            $data = $questionsMap;
            // return $data;
            return view('admin.posts.questions', compact('data')) ;
        } else {
            session()->flash('success', 'Some error occured');
            return back();
        }
    }



}