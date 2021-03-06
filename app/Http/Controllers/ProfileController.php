<?php

namespace App\Http\Controllers;

use App\Notifications\NotifyAddFriend;
use App\UserImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\Profile;
use App\UserInterest;
use App\Post;
use App\Friends;
use Psy\Util\Json;
use Hash;
use Notification;
use Illuminate\Support\Facades\Validator;

// use Illuminate\Foundation\Testing\WithoutMiddleware;
// use Illuminate\Foundation\Testing\DatabaseMigrations;
// use Illuminate\Foundation\Testing\DatabaseTransactions;

class ProfileController extends Controller
{

	/* public function __construct()
    {
        //$this->middleware('auth');
    }
*/
	public function basic(Request $request)
    {
    	$id =  auth()->user()->id;   
    	
    	dd($id);
//    	return response()->json($this->user);
    }
     public function Basic_info(Request $request)
    {
 		$id = auth()->user()->id;

        $user = User::where('id' , $id)
            ->update(['f_name' => $request->f_name , 'l_name' => $request->l_name ,
                    'email' => $request->email , 'date' => $request->date ,
                    'month' => $request->month , 'year' => $request->year ,
                    'gender' => $request->gender ,
                    'city' => $request->city , 'country'=>$request->country , 'description'=>$request->description]);

        if ($user) {
        	 $result = User::where('id' , $id)->first();
        } 
        else
        {
       		$result = "Something wents gone";
        }
 
            return response()->json($result);
    }

    public function get_basic()
    {

    	$id = auth()->user()->id;
    	$result = User::where('id' , $id)->first();
    	return response()->json($result);

    }

    public function get_edu()
    {
    	$id = auth()->user()->id;
    	$result = Profile::where('user_id' , $id)->limit(5)->get();
    	return response()->json($result);
    }
    public function Edu_info(Request $request)
    {
	
		$id = auth()->user()->id;

        $user = Profile::where('user_id', $id)->count();

        if ($user == 0)
        {
            $user = new Profile;

            $user->user_id = $id;

            $user->university = $request->university;

            $user->edu_from = $request->edu_from;

            $user->edu_to = $request->edu_to;

            $user->edu_description = $request->edu_description;

            $user->graduate = $request->graduate;

            $user->save();

            $user = 0;

        }

        else
        {
            $user = Profile::where('user_id' , $id)
            ->update(['university'=> $request->university, 'edu_from'=> $request->edu_from,
                'edu_to'=> $request->edu_to, 'edu_description'=> $request->edu_description, 'graduate'=>$request->graduate]);

            $user = 1;

        }

        return response()->json($user);



    }

    public function Work_info(Request $request )
    {

		$id = auth()->user()->id;

        $user = Profile::where('user_id', $id)->count();

        if ($user == 0) {

            $user = new Profile;

            $user->user_id = $id;

            $user->work_company = $request->work_company;

            $user->work_designation = $request->work_designation;

            $user->work_from = $request->work_from;

            $user->work_to = $request->work_to;

            $user->work_city = $request->work_city;

            $user->work_description = $request->work_description;

            $user->save();

            $user = 0;
        }
        else
        {

            //$user = Profile::find(3);
            $user = Profile::where('user_id', $id)
            ->update(['work_company' => $request->work_company, 'work_designation'=> $request->work_designation,
                'work_from' => $request->work_from , 'work_to' => $request->work_to ,
                'work_city' => $request->work_city , 'work_description'=>$request->work_description
                ]);

            $user = 1;
        }

        return response()->json($user);

    }
    public function interest(Request $request )
    {
    	$id = auth()->user()->id;

        $interest = $request->user_interest;

        $interst_count = UserInterest::where('user_id' , $id)->count();

        if ($interst_count < 5)
        {
            $query = UserInterest::create(['user_id'=>$id , 'interest'=>$interest]);
            $getInterest = UserInterest::where('user_id' , $id)->get();
            return response()->json(['interests' => $getInterest]);
        }
        else
        {
            return response()->json(['error' => 500]);
        }

    }
    public function get_interest()
    {
        $id = auth()->user()->id;
        $getInterest = UserInterest::where('user_id' , $id)->get();
        return response()->json(['interests' => $getInterest]);
    }
    public function interestDelete($id){
        $auth = auth()->user()->id;
        $delete = UserInterest::where('id' , $id)->delete();
        $getInterest = UserInterest::where('user_id' , $auth)->get();
        return response()->json(['interests' => $getInterest]);
    }
     public function get_friend_info($id)
     {

            $user = User::where('id' , $id)->first();

            $profile =Profile::where('user_id' , $id)->first();

            $userImg =UserImage::where('user_id' , $id)->get();

            $interest =UserInterest::where('user_id' , $id)->get();

            $posts = Post::where('user_id' , $id)->get();

            $profileImg = UserImage::where('user_id' , $id)->orderBy('created_at', 'desc')->first();

            return response()->json(['user'=>$user , 'profile'=>$profile , 'userImg'=>$userImg,
            'userInterest'=>$interest , 'posts'=>$posts , 'profileImg'=>$profileImg
            ]);
     }

     public function addFriend(Request $request , $id)
     {
         $sender_id = auth()->user()->id;
         $friend_id = $id;

         dd($request->status); //2 => sent ,

         $isFriend1 = $sender_id."_".$friend_id;

         $isFriend2 = $friend_id."_".$sender_id;


         $find = Friends::where('sender_id' , $sender_id)->orWhere('isFriends' , $isFriend1)->where('status' , 0)->first();

         if ($find > 0)
         {
                return response()->json(['sent' => $find]);
         }
         else{
             return response()->json('asd');
         }

         /* Notification::send($friend, new NotifyAddFriend($sender_data));*/

         return response()->json($getStatus);
     }
     public function accept_Request($id)
     {
         $auth = auth()->user()->id;
         $accept = Friends::orWhere('sender_id' , $id)->orWhere('receiver_id' , $auth)->get();
         dd($accept);
     }
     public function get_add_friend($id) 
     {
         $auth = auth()->user()->id;
         $url_id = $id;


         $isFriend1 = $auth."_".$url_id;

         $isFriend2 = $url_id."_".$auth;

         $sent = Friends::where('sender_id' , $auth)->where('isFriends' , $isFriend1)->where('status' , 0)->first();

         if ($sent != null)
         {
             return response()->json(0);
         }

         $accept = Friends::where('receiver_id' , $auth)->where('isFriends' , $isFriend2)->where('status' , 0)->first();

         if ($accept != null )
         {

             return response()->json(2);
         }

         $friend = Friends::where('isFriends' , $isFriend1)->orWhere('isFriends' , $isFriend2)->where('status' , 1)->first();

         if ($friend != null)
         {

             return response()->json(1);
         }

         //dd($sent);
        return response()->json(3);
     }
     public function friendAdded(Request $request)
     {
         $auth = auth()->user()->id;
         $slug = $request->url_id;
             //dd($slug);

       //  $isFriend = $auth."_".$request->url_id;

         /*$add = Friends::create([
            'sender_id' => $auth ,
            'receiver_id' => $request->url_id,
            'isFriends' => $isFriend,
             'status' => 0
         ]);*/
         $user = auth()->user();

        $receiver = User::where('id', $slug)->first();

        dd($receiver->id);
    // get list of friends (so who have status = 1)

    $friend = Friends::where('status',1)->where(function($query) use ($receiver,$user)
    {
        $query->where([
            'sender_id'   => $auth,
            'receiver_id' => $receiver->id
        ])->orWhere([
            'user_id'   => $receiver->id,
            'friend_id' => $auth
        ]);

    })->get();

    return ! $result->isEmpty();

     }
     public function get_user_notification_info($id)
     {
        dd($id);
     }
     public function get_user_info(){

	    $id = auth()->user()->id;
         $user = User::where('id' , $id)->first();

        $profile =Profile::where('user_id' , $id)->first();
//
//         $userImg =UserImage::where('user_id' , $id)->get();
//
        $interest =UserInterest::where('user_id' , $id)->get();
//
//         $posts = Post::where('user_id' , $id)->get();
//
//         $profileImg = UserImage::where('user_id' , $id)->orderBy('created_at', 'desc')->first();

         return response()->json(['user'=>$user , 'profile'=>$profile ,
             'userInterest'=>$interest
         ]);
     }
     public function all_friends()
     {

         $id = auth()->user()->id;

         $sender = Friends::where('sender_id' , $id)->where('status' , 1)->pluck('receiver_id')->toArray();

         $receiver = Friends::where('receiver_id' , $id)->where('status' , 1)->pluck('sender_id')->toArray();

         $merge = array_merge($sender , $receiver);


//         $resIdsArr2 = auth()->user()->friends->where('status' , 1)->pluck('receiver_id')->toArray();
//
            $allFriends = User::with('user_image')->whereIn('id', $merge)->get();


            $arr = array();
            foreach ($allFriends as $name) {
                $n = $name->f_name;
                $arr[] .= $name->id;

            }

       $profile =    Profile::whereIn('user_id' , $arr)->get();

        // $imgs = UserImage::whereIn('user_id' , $merge)->OrderBy('created_at' , 'desc')->first();

      //   $imgs = UserImages::with('user_images')->whereIn('id', $merge)->get();
//          dd($imgs);
        //  return response()->json($imgs);
         //'images'=>$imgs
         return json_encode(['all_friends' => $allFriends, 'Profiles'=>$profile ]);
    
     }

     public function changePassword(Request $request)
    {

      //  dd($request->old_pass);

        $user = Auth::user();

        $old_pass = $request->old_pass;
        $new_pass = $request->new_pass;
        $confirm_pass = $request->confirm_pass;

        $data = array('password'=>$new_pass , 'password_confirmation'=>$confirm_pass , 'old_pass'=> $old_pass);

     //   dd($data);

        $validate = Validator::make($data, [
              

            'password' => [
                                'required', 
                                'min:6','confirmed',
                                'max:50',
                                'regex:/^(?=.*[a-z|A-Z])(?=.*[A-Z])(?=.*\d)(?=.*(_|[^\w])).+$/',
                            ], // asdASD123,./
             ]);
 

        if ($validate->fails()) {
           return response()->json(["result"=> 1 ]);   
        } 
 
        if (Hash::check($old_pass, $user->password) ) {
            $user_id = $user->id;
            $obj_user = User::find($user_id)->first();
            $obj_user->password = Hash::make($new_pass);
            $obj_user->save();

            return response()->json(["result"=>2]);
        }
        else
        {
            return response()->json(["result"=>0]);
        }
    }

    public function friend_request() {

        $id = auth()->user()->id;
        $req = Friends::where(['sender_id' => $id ])->get();

        return response()->json($req);
    }

    public function who_to_follow(){
	    $id = auth()->user()->id;
	     $check_friends1 = Friends::where(['sender_id'=>$id , 'status'=>1])->pluck('receiver_id')->toArray();

        $check_friends2 = Friends::where(['receiver_id'=>$id , 'status'=>1])->pluck('sender_id')->toArray();

        $friends = array_merge($check_friends1 , $check_friends2);

        $auth_push = array($id);

        $merge = array_merge($friends , $auth_push);

       $users_ids = User::whereNotIn('id' , $merge)->limit(5)->with('user_image')->get();

       return response()->json($users_ids);

       //dd($users_ids);
    }

}
