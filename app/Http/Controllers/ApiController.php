<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Interest;
use App\Models\InterestUser;
use App\Models\Post;
use App\Models\Question;
use App\Models\Answer;
use App\Models\PostComment;
use App\Models\PostLike;
use App\Models\PostDislike;
use App\Models\CommentReply;
use App\Models\Notification;
use App\Mail\ForgotPass;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Http;

use Validator;
use DB;
use Google_Client;


class ApiController extends Controller
{
  public function Register(request $req)
  {
    $validator = Validator::make($req->all(), [
      'first_name' => 'required|string',
      'last_name' => 'required|string',
      'phone' => 'required|string',
      'email' => 'unique:users|required|email',
      'password' => 'required|min:8',
    ]);

    if ($validator->fails()) {
      return response()->json([
        'validation_errors' => $validator->messages(),

      ]);

    } else {
      //return $req->first_name;
      $user = User::create([
        'first_name' => $req->first_name,
        'last_name' => $req->last_name,
        'email' => $req->email,
        'password' => Hash::make($req->password),
        'phone' => $req->phone,
        'dob' => $req->dob,
        'device_token' => $req->device_token,
        'role' => 'user'
      ]);

      $token = $user->createToken($user->email . '_Token')->plainTextToken;
      $user->save();
      return response()->json([

        'status' => 201,
        'token' => $token,
        'user' => User::where('id', $user->id)->first(),
        'message' => 'successfully registered',
      ]);
    }
  }
  public function login(request $req)
  {
    $validator = Validator::make($req->all(), [

      'email' => 'required|email',
      'password' => 'required|string',
    ]);

    //check email
    $user = User::where('email', $req->email)->first();
    //check password

    if ($validator->fails()) {

      return response()->json([
        'validation_errors' => $validator->messages(),

      ]);
    }
    if (!$user || !Hash::check($req->password, $user->password)) {
      return response()->json([

        'status' => 401,


        'message' => ' username or password is incorrect',
      ], 401);
    }

    $token = $user->createToken('token')->plainTextToken;
    $user->device_token = $req->device_token;
    $user->save();
    return response()->json([

      'status' => 200,
      'token' => $token,
      'user' => User::where('id', $user->id)->first(),
      'message' => ' successfully Logged in ',
    ]);
  }

  public function google_login(Request $request)
  {
    /*
    Android dev are providing us id_token from their app
    */
        $url = "https://oauth2.googleapis.com/tokeninfo?id_token=".$request->code;
        // $url = "https://www.googleapis.com/oauth2/v2/tokeninfo?access_token=".$request->code;
    $response = Http::get($url);
    return $response;
    $CLIENT_ID = "376481187797-9ko5nfnru8g205hhf45fjcvrg881r7mo.apps.googleusercontent.com";
    $id_token = $request->code;
    // return $id_token;
    $client = new Google_Client(['client_id' => $CLIENT_ID]);  // Specify the CLIENT_ID of the app that accesses the backend
    $client->setScopes(array('https://www.googleapis.com/auth/plus.login','https://www.googleapis.com/auth/userinfo.email', 'https://www.googleapis.com/auth/plus.me'));
    // $client->setScopes('email');
$payload = $client->verifyIdToken($id_token);
return $payload;

    if (!$response->ok()) {
      return response()->json([
        "responseCode" => 402,
        "responseMessage" => "Invalid Access Token"
      ], 402);
    } else {
      $email = $response['email'];
      $exists = User::where('email', $email)->exists();
      if ($exists) {
        $data = User::where('email', $email)->first();
        return response()->json([
          "response_code" => 200,
          "response_message" => "Ok",
          "success" => $data
        ], 200);
      } else {
        $length = 50;
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';

        for ($i = 0; $i < $length; $i++) {
          $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        function random_password($lengthh = 12)
        {
          $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_-=+;:,.?";
          $password = substr(str_shuffle($chars), 0, $lengthh);
          return $password;
        }
        $pass = random_password();
        $id = User::insertGetId(
          array(
            'first_name' => $response['name'],
            'email' => $response['email'],
            'password' => $pass,
            'google_id' => $response['id'],
          )
        );

        $userinfo = User::where('id', $id)->first();

        return response()->json([
          "response_code" => 201,
          "response_message" => "New user created",
          "success" => $userinfo
        ], 201);
      }
    }
  }

  public function facebook_login(Request $request)
  {
    if (empty($request->token)) {
      return response()->json([
        "response_code" => 401,
        "response_message" => "Access Token is mandatory"
      ], 401);
    }

    $fb_access_token = $request->token;
    $user = Socialite::driver('facebook')->userFromToken($fb_access_token);

    if (!$user) {
      return response()->json([
        "responseCode" => 402,
        "responseMessage" => "Invalid Access Token"
      ], 402);
    } else {

      $data['id'] = $user->getId();
      $data['name'] = $user->getName();
      $data['email'] = $user->getEmail();
      $data['avatar'] = $user->getAvatar();
  
      $email = $data['email'];
      $exists = User::where('email', $email)->exists();
      if ($exists) {
        $data = User::where('email', $email)->first();
        return response()->json([
          "response_code" => 200,
          "response_message" => "Ok",
          "success" => $data
        ], 200);
      } else {
        $length = 50;
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';

        for ($i = 0; $i < $length; $i++) {
          $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        function random_password($lengthh = 12)
        {
          $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_-=+;:,.?";
          $password = substr(str_shuffle($chars), 0, $lengthh);
          return $password;
        }

        $pass = random_password();
        $id = User::insertGetId(
          array(
            'first_name' => $data['name'],
            'email' => $data['email'],
            'password' => $pass,
          )
        );

        $userinfo = User::where('id', $id)->get();

        $success['id'] = $userinfo[0]->id;
        $success['first_name'] = $userinfo[0]->first_name;
        $success['email'] = $userinfo[0]->email;
        $success['password'] = $userinfo[0]->password;



        return response()->json([
          "response_code" => 201,
          "response_message" => "New user created",
          "success" => $userinfo[0]
        ], 201);
      }
    }

  }

  public function logout(Request $request)
  {
    User::where('id', $request->user()->id)->update(['device_token' => null]);
    auth()->user()->tokens()->delete();

    return response()->json([

      'status' => 200,

      'message' => ' successfully Logged out ',
    ]);
  }

  public function forgot_password(Request $request)
  {
    $email = $request->email;
    $data = User::where('email', $email)->get();

    if (!User::where('email', $email)->exists()) {
      return response()->json([
        "responseCode" => 404,
        "responseMessage" => "User not found!",
      ], 404);
    } else {
      $digits = 4;
      $otp = str_pad(rand(0, pow(10, $digits) - 1), $digits, '0', STR_PAD_LEFT);

      $details = [
        'title' => 'Mail from Zoom Scholar',
        'body' => 'Use this OTP to  Reset Your password',
        'otp' => $otp,
      ];

      \Mail::to($email)->send(new ForgotPass($details));

      User::where('email', $email)->update([
        'otp' => $otp,
      ]);

      return response()->json([
        'responseCode' => 200,
        'responseMessage' => 'Email with OTP Sent Successfully',
      ], 200);
    }
  }

  public function verify_otp(Request $request)
  {
    $email = $request->email;
    $otp = $request->otp;

    $user = User::where('email', $email)->first()->makeVisible(['otp']);

    if ($user->otp == $otp) {
      $user = User::where('email', $email)->first();
      $token = $user->createToken($email . '_Token')->plainTextToken;

      User::where('otp', $otp)->update([
        'otp' => null,
      ]);

      return response()->json([
        'responseCode' => 200,
        'responseMessage' => 'OTP has been Successfully Verified',
        'token' => $token,
      ], 200);

    } else {
      return response()->json([
        'responseCode' => 401,
        'responseMessage' => 'OTP does not match '
      ], 401);
    }
  }

  public function change_password(Request $request)
  {
    $id = $request->user()->id;
    User::where('id', $id)->update([
      'password' => $request->password,
    ]);

    return response()->json([
      'responseCode' => 200,
      'responseMessage' => 'Password has been Changed Successfully',
    ], 200);
  }

  public function Getallusers(Request $request)
  {
    $id = $request->user()->id;
    $user = User::where('status', 1)->where('role', 'user')->where('id', '!=', $id)->get();

    return response()->json([
      'responseCode' => 200,
      'responseMessage' => $user,
    ]);
  }
  public function GetAllInterest()
  {

    $data = Interest::where('status', 1)->get(['id', 'name', 'image']);

    return response()->json([

      'status' => 200,
      'data' => $data,
      'message' => ' success',
    ]);
  }
  public function GetUserProfile()
  {

    $data = auth()->user();


    return response()->json([

      'status' => 200,
      'data' => $data,
      'message' => ' success',
    ]);
  }


  public function GetPosts(Request $request)
  {
      
      if($request->has('post_id')) {
              $posts = Post::with(['user', 'comments'])->where('posts.id',$request->post_id)->first();
              if(!$posts) {
                  return response()->json([
                  'responseCode' => 404,
                  'responseMessage' => 'post not found',
                ]);
              }
              $condition = ['post_id' => $request->post_id, 'user_id' => $request->user()->id];
              $like = PostLike::where($condition)->first();
              $dislike = PostDislike::where($condition)->first();
              $user_id = $request->user()->id;    
              
              // Checking if a post is liked by logged in user
              if ($like && $like->user_id == $user_id) {
                $posts->setAttribute('is_liked', true);
              } else {
                $posts->setAttribute('is_liked', false);
              }
              // Checking if a post is dis-liked by logged in user
              if ($dislike && $dislike->user_id == $user_id) {
                $posts->setAttribute('is_disliked', true);
              } else {
                $posts->setAttribute('is_disliked', false);
              }
              
              return response()->json([
              'responseCode' => 200,
              'responseMessage' => 'success',
              'data' => $posts,
            ]);
      } else {
          $posts = Post::with(['user', 'comments'])->orderBy('id', 'DESC')->get();
    foreach ($posts as $post) {
      $condition = ['post_id' => $post->id, 'user_id' => $request->user()->id];
      $like = PostLike::where($condition)->first();
      $dislike = PostDislike::where($condition)->first();
      $user_id = $request->user()->id;

      // Checking if a post is liked by logged in user
      if ($like && $like->user_id == $user_id) {
        $post->setAttribute('is_liked', true);
      } else {
        $post->setAttribute('is_liked', false);
      }
      // Checking if a post is dis-liked by logged in user
      if ($dislike && $dislike->user_id == $user_id) {
        $post->setAttribute('is_disliked', true);
      } else {
        $post->setAttribute('is_disliked', false);
      }
    }
    $count = count($posts);

    return response()->json([
      'responseCode' => 200,
      'responseMessage' => 'success',
      'count' => $count,
      'data' => $posts,
    ]);
      }
      
    
  }

  public function get_comment_replies(Request $request)
  {
    $data = CommentReply::with('users:id,first_name,last_name,profile_image')->where('post_comment_id', $request->post_comment_id)->get();
    return response()->json([
      'responseCode' => 200,
      'responseMessage' => $data,
    ], 200);
  }

  public function get_comments(Request $request)
  {
    $comments = PostComment::with('users:id,first_name,last_name,profile_image')->where('post_id', $request->post_id)->orderBy('id','desc')->get();
    // $comments = PostComment::with('users:id,first_name,last_name,profile_image')->where('post_id', $request->post_id)->orderBy('id','desc')->get();
    foreach ($comments as $comment) {
      $comment['replies'] = CommentReply::where('post_comment_id', $comment->id)->get();
    }
    return response()->json([
      'responseCode' => 200,
      'responseMessage' => $comments,
    ], 200);
  }


  public function AddPost(request $req)
  {

    $author = auth()->user()->id;

    $post = new Post();
    $post->title = $req->post_title;
    $post->description = $req->post_description;
    if ($req->post_image) {
      $image = $req->post_image;
      $name = time() . '.' . $image->getClientOriginalExtension();
      $destination = public_path('/post');
      $image->move($destination, $name);
      // $path = public_path("images\\".$name);
      $baseurl = url('/');
      $path = $baseurl . "/public/post/" . $name;
      $post->image = $path;
    }
    $post->user_id = $author;
    $post->save();
    //$count=count($data);
    $data = Post::with('user')->where('id', $post->id)->first();
    return response()->json([
      'status' => 200,
      'message' => ' success',
      'data' => $data,
    ]);
  }


  public function UpdateProfile(request $req)
  {

    $validator = Validator::make($req->all(), [
      'first_name' => 'required|string',
      'last_name' => 'required|string',
      'phone' => 'required|string',
    ]);

    if ($validator->fails()) {
      return response()->json([
        'validation_errors' => $validator->messages(),

      ]);

    }

    $user_id = auth()->user()->id;
    //update user

    $data = User::find($user_id);

    $data->first_name = $req->first_name;
    $data->last_name = $req->last_name;
    $data->phone = $req->phone;
    $data->dob = $req->dob;

    if ($req->profile_image) {
      $image = $req->profile_image;
      $name = time() . '.' . $image->getClientOriginalExtension();
      $destination = public_path('/user');
      $image->move($destination, $name);
      // $path = public_path("images\\".$name);
      $baseurl = url('/');
      $path = $baseurl . "/public/user/" . $name;
      $data->profile_image = $path;
    }

    $data->save();

    return response()->json([

      'status' => 200,

      'message' => ' success profile updated',
      'data' => $data,

    ]);
  }

  public function Postquestion(request $req)
  {
    $user_id = auth()->user()->id;

    $question = new Question();
    $question->question = $req->question;
    $question->user_id = $user_id;
    $question->save();

    $data = Question::with('user', 'answers')->where('id', $question->id)->first();

    return response()->json([

      'status' => 200,

      'message' => ' success question posted',
      'data' => $data,

    ]);

  }

  public function Postanswers(request $req)
  {
    $user_id = auth()->user()->id;

    $answer = new Answer();
    $answer->question_id = $req->question_id;
    $answer->user_id = $user_id;
    $answer->answer = $req->answer;
    $answer->save();

    $data = Answer::with('user')->where('id', $answer->id)->first();
    
    //get question creator
    $question = Question::where('id', $req->question_id)->first();
    $user = User::where('id', $question->user_id)->first();
    
    //get user token who created question
    $device_tokens = [
      $user->device_token
    ];

    //get name of user who commented
    $answerUser = User::where('id', $req->user()->id)->first();
    $answerByUser = $answerUser->first_name." ".$answerUser->last_name;

    
  $info = [
    'title' => 'Zoom Scholar',
    'image' => $answerUser->profile_image,
    // 'message' => 'Someone Comment on Your Post: ' . $post->title,
    'message' => $answerByUser.' asnwered on your question: ' . $question->question,
  ];
  $noti = $this->send_firebase_push($device_tokens, $info);
  if ($noti['result'] == true) {
    // Comment Added and Notification sent Successfully
    $noti_msg = "Sent Successfully";
    // $data['user_id'] = $request->user()->id;
    $info['user_id'] = $user->id;
    $info['from_user_id'] = $user_id;
    $info['question_id'] = $req->question_id;
    
    
    $ins = new Notification();
    $ins->title = $info['title'];
    $ins->image = $info['image'];
    $ins->message = $info['message'];
    $ins->user_id = $info['user_id'];
    $ins->from_user_id = $info['from_user_id'];
    $ins->question_id = $info['question_id'];
    $ins->save();
    
  } else {
    // Comment Added but Notification error occurred
    $noti_msg = "FCM Send Error";
  }
    
    return response()->json([
      'status' => 200,
      'message' => ' success answer posted',
      'data' => $data,
      "notificationMessage" => $noti['message']
    ]);
    /* return response()->json([
      'status' => 200,
      'message' => ' success answer posted',
      'data' => $data,

    ]); */

  }
  public function Getmyquestions()
  {
    $user_id = auth()->user()->id;
    $data = Question::with('answers')->where('user_id', $user_id)->orderBy('id','desc')->get();
    // return $data;
    // if(!empty($data)) {
    if(count($data) != 0){
        foreach($data as $item) {
            $user = User::where('id', $item->user_id)->get(['first_name','last_name']);
            $username = $user[0]->first_name.' '.$user[0]->last_name;
            $item->username = $username;
            // if(!empty($item->answers)) {
            if(count($item->answers)  != 0 ) {
                for($i = 0; $i < count($item->answers); $i++) {
                    $answered = User::where('id', $item->answers[$i]->user_id)->get(['first_name','last_name','profile_image']);
                    $answeredBy = $answered[0]->first_name.' '.$answered[0]->last_name;
                    $item->answers[$i]->answeredBy = $answeredBy;
                    $item->answers[$i]->answeredByUserImage = $answered[0]->profile_image;
                    
                    // $item->isEmpty = false;
                }
                
            }
        }
    }
    
    return response()->json([
      'status' => 200,
      'message' => ' success questions found',
      'data' => $data,

    ]);

  }
  public function GetMyPosts()
  {
    $user_id = auth()->user()->id;
    $data = Post::where('user_id', $user_id)->orderBy('id','desc')->get();
    foreach($data as $item) {
        $item->comment_count =  PostComment::where('post_id', $item->id)->count();
    }
    $total_comments = PostComment::all()->count();

    if (count($data) == 0) {
      return response()->json([
        'status' => 200,
        'message' => ' No posts found',
        'data' => $data,

      ]);

    }
    return response()->json([
      'status' => 200,
      'message' => ' success posts found',
      'total_comments' => $total_comments,
      'data' => $data,

    ]);

  }

  public function Getallquestions(Request $req)
  {
      
      if($req->has('question_id')) {
          
        $user_id = auth()->user()->id;
        $data = Question::with('answers.user', 'user')->where('questions.id',$req->question_id)->first();
        if(!$data) {
            return response()->json([
            'status' => 404,
            'message' => ' Invalid question id'
          ]);
        } else {    
        return response()->json([
            'status' => 200,
            'message' => ' success questions found',
            'data' => $data
        ]);
        }
      } else {
          $user_id = auth()->user()->id;
        $data = Question::with('answers.user', 'user')->get();
        if (count($data) == 0) {
          return response()->json([
            'status' => 200,
            'message' => ' no  questions found'
          ]);
        } else {
          return response()->json([
            'status' => 200,
            'message' => ' success questions found',
            'data' => $data,
    
          ]);
        }
      }
      
    
  }
  public function Getmyinterets()
  {
    $user_id = auth()->user()->id;
    $data = User::with('interests')->where('id', $user_id)->first();
    $interests = $data->interests;

    return response()->json([
      'status' => 200,
      'message' => ' success',
      'data' => $interests,

    ]);

  }
  public function Postmyinterets(request $req)
  {
    $user_id = auth()->user()->id;
    $user = User::with('interests')->find($user_id);
    
    //get interest id's
    $interestids = $req->interest;
    //atttach roles to user
    $user->interests()->attach($interestids);
    
    $user = User::with('interests')->find($user_id);

    return response()->json([
      'status' => 200,
      'message' => ' success',
      'data' => $user,

    ]);

  }

  public function edit_post(Request $request)
  {
    if (Post::where('id', $request->id)->exists()) {
      if ($request->image != null) {
        $url = Post::where('id', $request->id)->select('image')->get()->toArray();
        $image_name = substr($url[0]['image'], strlen(url('/')));
        $image_path = public_path() . $image_name;
        if (file_exists($image_path)) {
          if ($image_name != null) {
            unlink($image_path);
          }
        }
        $image = $request->file('image');
        $image_name = time() . '.' . $request->file('image')->getClientOriginalExtension();
        $image->move(public_path('post'), $image_name);
        $baseurl = url('/');
        $path = $baseurl . "/public/post/" . $image_name;
        Post::where('id', $request->id)->update([
          'image' => $path,
        ]);
      }
      Post::where('id', $request->id)->update([
        'title' => $request->title,
        'description' => $request->description,
      ]);
      return response()->json([
        "responseCode" => 200,
        "responseMessage" => "Post Updated Successfully",
        "responseData" => Post::where('id', $request->id)->first(),
      ], 200);
    } else {
      return response()->json([
        "responseCode" => 404,
        "responseMessage" => "Post Not Found - Invalid ID",
      ], 404);
    }
  }

  public function edit_question(Request $request)
  {
    if (Question::where('id', $request->id)->exists()) {
      Question::where('id', $request->id)->update([
        'question' => $request->question,
      ]);
      return response()->json([
        "responseCode" => 200,
        "responseMessage" => "Question Updated Successfully",
      ], 200);
    } else {
      return response()->json([
        "responseCode" => 404,
        "responseMessage" => "Question Not Found - Invalid ID",
      ], 404);
    }
  }

  public function delete_post($id)
  {
    if (Post::where('id', $id)->exists()) {
      Post::where('id', $id)->delete();
      return response()->json([
        "responseCode" => 200,
        "responseMessage" => "Post Deleted Successfully",
      ], 200);
    } else {
      return response()->json([
        "responseCode" => 404,
        "responseMessage" => "Post Not Found - Invalid ID",
      ], 404);
    }
  }

  public function delete_question($id)
  {
    if (Question::where('id', $id)->exists()) {
      Question::where('id', $id)->delete();
      return response()->json([
        "responseCode" => 200,
        "responseMessage" => "Question Deleted Successfully",
      ], 200);
    } else {
      return response()->json([
        "responseCode" => 404,
        "responseMessage" => "Question Not Found - Invalid ID",
      ], 404);
    }
  }

  public function like_post(Request $request)
  {
    $keys = [
      'user_id' => $request->user()->id,
      'post_id' => $request->post_id,
    ];
    $post_like = PostLike::where($keys)->get();
    $post_dislike = PostDislike::where($keys)->exists();
    if (!Post::where('id', $request->post_id)->exists()) {
      // Post not exixts with given id
      return response()->json([
        "responseCode" => 404,
        "responseMessage" => "Post Not Found - Invalid ID",
      ], 404);
    }
    if (!$post_dislike) {
      if (count($post_like) == 0) {
        // This User is allowed to like post
        try {
          PostLike::insert($keys);
          Post::where('id', $request->post_id)->increment('likes');
          
          //get post creator
          $post = Post::where('id', $request->post_id)->first();
          $user = User::where('id', $post->user_id)->first();
          
          //post creator token
          $device_tokens = [
            $user->device_token
          ];
          
          //get name of user who liked the post
          $likeUser = User::where('id', $request->user()->id)->first();
          $likeByUser = $likeUser->first_name." ".$likeUser->last_name;
          
          $data = [
                'title' => 'Zoom Scholar',
                'image' => $likeUser->profile_image,
                // 'message' => 'Someone Comment on Your Post: ' . $post->title,
                'message' => $likeByUser.' Liked Your Post: ' . $post->title,
            ];
          
          $noti = $this->send_firebase_push($device_tokens, $data);
          if ($noti['result'] == true) {
            // Comment Added and Notification sent Successfully
            $noti_msg = "Sent Successfully";
            // $data['user_id'] = $request->user()->id;
            $data['user_id'] = $user->id;
            $data['from_user_id'] = $request->user()->id;
            $data['post_id'] = $request->post_id;
            Notification::insert($data);
          } else {
            // Comment Added but Notification error occurred
            $noti_msg = "FCM Send Error";
          }
          return response()->json([
            "responseCode" => 200,
            "responseMessage" => "Post Liked",
            "notification" => $noti_msg,
            "notificationMessage" => $noti['message'],
          ], 200);
        } catch (\Throwable $th) {
          throw $th;
        }
      } else {
        // This User have been already liked this post, hence reversing the commited action, i.e unlike the post
        try {
          PostLike::where($keys)->delete();
          Post::where('id', $request->post_id)->decrement('likes');
          
          
          
          return response()->json([
            "responseCode" => 200,
            "responseMessage" => "Post Unliked",
          ], 200);
        } catch (\Throwable $th) {
          throw $th;
        }
      }
    } else {
        
        //remove the post like
        PostDislike::where($keys)->delete();
        Post::where('id', $request->post_id)->decrement('dislikes');
        
        //insert the post like
        PostLike::insert($keys);
        Post::where('id', $request->post_id)->increment('likes');
        
      return response()->json([
        "responseCode" => 200,
        "responseMessage" => "Dislike Removed, Post Liked!",
      ], 200);
    }
  }

  public function dislike_post(Request $request)
  {
    $keys = [
      'user_id' => $request->user()->id,
      'post_id' => $request->post_id,
    ];
    
    $post_dislike = PostDislike::where($keys)->get();
    $post_like = PostLike::where($keys)->exists();
    
    if (!Post::where('id', $request->post_id)->exists()) {
      // Post not exixts with given id
      return response()->json([
        "responseCode" => 404,
        "responseMessage" => "Post Not Found - Invalid ID",
      ], 404);
    }
    if (!$post_like) {
      if (count($post_dislike) == 0) {
        // This User is allowed to like post
        try {
          PostDislike::insert($keys);
          Post::where('id', $request->post_id)->increment('dislikes');
          return response()->json([
            "responseCode" => 200,
            "responseMessage" => "Post Disliked",
          ], 200);
        } catch (\Throwable $th) {
          throw $th;
        }
      } else {
        // This User have been already liked this post, hence reversing the commited action, i.e un-dislike the post
        try {
          PostDislike::where($keys)->delete();
          Post::where('id', $request->post_id)->decrement('dislikes');
          return response()->json([
            "responseCode" => 200,
            "responseMessage" => "Remove Dislike",
          ], 200);
        } catch (\Throwable $th) {
          throw $th;
        }
      }
    } else {
      
        //remove the post like
        PostLike::where($keys)->delete();
        Post::where('id', $request->post_id)->decrement('likes');
        
        //insert the post like
        PostDislike::insert($keys);
        Post::where('id', $request->post_id)->increment('dislikes');
      
      
      return response()->json([
        "responseCode" => 200,
        "responseMessage" => "Like removed, post Disliked",
      ], 200);
    }

  }

  public function post_comment(Request $request)
  {
    if (Post::where('id', $request->post_id)->exists()) {
      PostComment::insert([
        'post_id' => $request->post_id,
        'description' => $request->post_comment,
        'user_id' => $request->user()->id,
        'created_at' => now(),
      ]);

         $post = Post::where('id', $request->post_id)->first();
        $user = User::where('id', $post->user_id)->first();
        $commentUser = User::where('id', $request->user()->id)->first();
        $commentByUser = $commentUser->first_name." ".$commentUser->last_name;
      $device_tokens = [
        $user->device_token
      ];
      $data = [
        'title' => 'Zoom Scholar',
        'image' => $commentUser->profile_image,
        // 'message' => 'Someone Comment on Your Post: ' . $post->title,
        'message' => $commentByUser.' Commented on Your Post: ' . $post->title,
      ];
      $noti = $this->send_firebase_push($device_tokens, $data);
      if ($noti['result'] == true) {
        // Comment Added and Notification sent Successfully
        $noti_msg = "Sent Successfully";
        // $data['user_id'] = $request->user()->id;
        $data['user_id'] = $user->id;
        $data['from_user_id'] = $request->user()->id;
        $data['post_id'] = $request->post_id;
        Notification::insert($data);
      } else {
        // Comment Added but Notification error occurred
        $noti_msg = "FCM Send Error";
      }
      return response()->json([
        "responseCode" => 200,
        "responseMessage" => "Comment Added",
        "notification" => $noti_msg,
        "notificationMessage" => $noti['message'],
      ], 200);
    } else {
      return response()->json([
        "responseCode" => 404,
        "responseMessage" => "Post Not Found - Invalid ID",
      ], 404);
    }
  }

  public function post_comment_reply(Request $request)
  {
    $comment = PostComment::where('post_id', $request->post_id)->get();
    
    if (!$comment) {
      // Comment not exists
      return response()->json([
        "responseCode" => 404,
        "responseMessage" => "Comment Not Found - Invalid ID",
      ], 404);
    } else {
      // Comment Exists, hence inserting reply
      $reply = CommentReply::insert([
        'post_comment_id' => $request->post_comment_id,
        'user_id' => $request->user()->id,
        'reply' => $request->post_reply,
        'created_at' => now(),
      ]);

        $comment = PostComment::where('post_id', $request->post_id)->first();
        //post owner
        $user = User::where('id', $comment->user_id)->first();
        //comment reply owner
        $commentUser = User::where('id', $request->user()->id)->first();
        $commentByUser = $commentUser->first_name." ".$commentUser->last_name;
        
        
      $device_tokens = [
        $user->device_token
      ];
      $data = [
        'title' => 'Zoom Scholar',
        'image' => $commentUser->profile_image,
        // 'message' => $user->first_name . ' ' . $user->last_name . ' Replied on Your Comment',
        'message' => $commentByUser. ' Replied on Your Comment',
      ];
      $noti = $this->send_firebase_push($device_tokens, $data);
      if ($noti['result'] == true) {
        // Comment Added and Notification sent Successfully
        $noti_msg = "Sent Successfully";
        // $data['user_id'] = $request->user()->id;
        $data['user_id'] = $user->id;
        $data['from_user_id'] = $request->user()->id;
        $data['post_id'] = $request->post_id;
        $data['comment_id'] = $request->post_comment_id;
        Notification::insert($data);
      } else {
        // Comment Added but Notification error occurred
        $noti_msg = "FCM Send Error";
      }

      return response()->json([
        'responseCode' => 200,
        'responseMessage' => $reply,
        'notification' => $noti_msg,
        'notificationMessage' => $noti['message'],
      ]);
    }
  }

  public function search_posts(Request $request)
  {
    if (
      Post::where('description', 'LIKE', "%$request->text%")
        ->orWhere('title', 'LIKE', "%$request->text%")->exists()
    ) {
      $data = [
        'data' => Post::with('user')->with('comments')->where('description', 'LIKE', "%$request->text%")
          ->orWhere('title', 'LIKE', "%$request->text%")->get(),
      ];
      return response()->json([
        "responseCode" => 200,
        "searchResult" => count($data['data']),
        "responseMessage" => $data,
      ], 200);
    } else {
      return response()->json([
        "responseCode" => 404,
        "searchResult" => 0,
        "responseMessage" => "Search Not Found",
      ], 404);
    }
  }

  public function search_questions(Request $request)
  {
    if (Question::where('question', 'LIKE', "%$request->text%")->exists()) {
      $data = [
        'data' => Question::with('answers')->where('question', 'LIKE', "%$request->text%")->get(),
      ];
      return response()->json([
        "responseCode" => 200,
        "searchResult" => count($data['data']),
        "responseMessage" => $data,
      ], 200);
    } else {
      return response()->json([
        "responseCode" => 404,
        "searchResult" => 0,
        "responseMessage" => "Search Not Found",
      ], 404);
    }
  }

  public function search_accounts(Request $request)
  {
    if (User::where('first_name', 'LIKE', "%$request->text%")->orWhere('last_name', 'LIKE', "%$request->text%")->orWhere('email', 'LIKE', "%$request->text%")->exists()) {
      $data = [
        'data' => User::where('first_name', 'LIKE', "%$request->text%")->orWhere('last_name', 'LIKE', "%$request->text%")->orWhere('email', 'LIKE', "%$request->text%")->get(),
      ];
      return response()->json([
        "responseCode" => 200,
        "searchResult" => count($data['data']),
        "responseMessage" => $data,
      ], 200);
    } else {
      return response()->json([
        "responseCode" => 404,
        "searchResult" => 0,
        "responseMessage" => "Search Not Found",
      ], 404);
    }
  }

  public function update_interest(Request $request)
  {
    $user_id = $request->user()->id;
    DB::table('interest_user')->where('user_id', $user_id)->delete();

    foreach ($request->interest_ids as $id) {
      DB::table('interest_user')->insert([
        'user_id' => $user_id,
        'interest_id' => $id,
      ]);  
    }

    return response()->json([
      "responseCode" => 200,
      "responseMessage" => 'Interest Updated',
    ], 200);
  }

  public function delete_user_image(Request $request)
  {
    User::where('id', $request->user()->id)->update([
      'profile_image' => "https://images.unsplash.com/photo-1572635148687-307f8ca9b737?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1528&q=80",
    ]);
    return response()->json([
      "responseCode" => 200,
      "responseMessage" => 'Profile Image Deleted & Set to Default Image',
    ], 200);
  }

  /**
   * Write code on Method
   *
   * @return response()
   */

  public function send_firebase_push($tokens, $data)
  {
    $server_key = 'AAAAV6gHB9U:APA91bE7BWYYx2wQljFgsbNNvltPS7-CFfSQ9QR6qRJtlPEOBkotw5N1xifOVFGZIZNZEDdZG_oCTLeNS9qk3Wh2COYJo2ALRVcBLKeeUt2nskIvY28pgFfMqJuZ08zV38faq7frh7om';

    

    // Prep the Bundle
    $msg = [
      'message' => $data['message'],
    ];
    $notify_data = [
      'body' => $data['message'],
      'title' => $data['title'],
    ];
    $registration_ids = $tokens;

    if (count($tokens) > 1) {
      // For Multiple Users
      $fields = [
        'registration_ids' => $registration_ids,
        'notification' => $notify_data,
        'data' => $msg,
        'priority' => 'high'
      ];
    } else {
      // For Single User
      $fields = [
        'to' => $registration_ids[0],
        'notification' => $notify_data,
        'data' => $msg,
        'priority' => 'high'
      ];
    }

    $headers[] = 'Content-Type: application/json';
    $headers[] = 'Authorization: key=' . $server_key;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

    $result = curl_exec($ch);
    if ($result === FALSE) {
      return [
        'result' => false,
        'message' => curl_error($ch)
      ];
    }
    curl_close($ch);
    return [
      'result' => true,
      'message' => $result,
    ];
  }

  public function get_notifications(Request $request) {
    $noti = Notification::where('user_id', $request->user()->id)->orderBy('created_at','desc')->get();
    //return $noti;
    if(!empty($noti)) {
        foreach($noti as $item) {
            unset($item->image);
            $user = User::where('id', $item->user_id)->get(['first_name','last_name']);
            $username = $user[0]->first_name.' '.$user[0]->last_name;
            $item->username = $username;
            
            $imageUser = User::where('id',$item->from_user_id)->pluck('profile_image');
            $item->image = $imageUser[0];
            
            $item->newDate = Carbon::parse($item->created_at)->diffForhumans();
            
        }
    }
    return response()->json([
      'responseCode' => 200,
      'responseMessage' => $noti,
    ], 200);
  }

  public function mark_as_read(Request $request) {
    $noti_id = $request->noti_id;
    $noti = Notification::where('id', $noti_id)->update(['seen' => 1]);
    
    return response()->json([
      'responseCode' => 200,
      'responseMessage' => 'Marked as Read',
    ], 200);
  }

  public function user_status(Request $request) {
    $status = (User::where('id', $request->user()->id)->first())->status;

    return response()->json([
      'responseCode' => 200,
      'responseMessage' => $status,
    ], 200);
  }
  
  public function user_exists(Request $request) {
    $user = User::where('id', $request->user()->id)->exists();
    if ($user) {
      $exists = true;
    } else {
      $exists = false;
    }
    return response()->json([
      'responseCode' => 200,
      'exists' => $exists, 
    ]);
  }
}