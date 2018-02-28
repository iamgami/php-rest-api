<?php
/*
Author : Vinod Kumar
*/
	ini_set("display_errors", "1");
	error_reporting(E_ALL);
	require_once("Rest.inc.php");
	
	class API extends REST {
		public $data = "";
		public function __construct(){
			parent::__construct();				// Init parent contructor
		}

		protected function register(){
			
			if(!empty($this->_request['email']) && !empty($this->_request['password'])){

				$check_info = array(
						'fields'=>'user_id,email',
						'where'=>'email like "'.$this->_request['email'].'"'
					);
				$exist_email = $this->GetSingleRecord("user_master",$check_info);

				if(count($exist_email)>0) {
					$response_array['status']='fail';
					$response_array['message']='Email already exists.';
					$response_array['data']='';
					$this->response($this->json($response_array), 200);
 				} else {
					$info_array = array(
							'firstname'=>$this->_request['firstname'],
							'lastname'=>$this->_request['lastname'],
							'email'=>$this->_request['email'],
							'password'=>$this->MakePassword($this->_request['password']),
							'register_date'=>date("Y-m-d H:i:s"),
							'register_ipaddress'=>$_SERVER['REMOTE_ADDR']
						);
					//$this->response($this->json($info_array), 200);		
					$user_id = $this->InsertRecord("user_master",$info_array);

					if($user_id>0) {
						$response_array['status']='success';
						$response_array['message']='register successfully.';
						$response_array['data']=array('user_id'=>$user_id);
						$this->response($this->json($response_array), 200);
					} else {
						$response_array['status']='fail';
						$response_array['message']='insufficient data.';
						$response_array['data']='';
						$this->response($this->json($response_array), 204);
					}
				}
			}
		}

		protected function login(){
						
			$email = $this->_request['email'];		
			$password = $this->_request['password'];

			if(!empty($email) && !empty($password) && $this->validate($email,'email')){

				$info_array = array(
						"fields"=>"user_id,firstname,lastname,email,active_status",
						"where"=>"email = '".$email."' and password = '".$this->MakePassword($password)."'"
					);
				$user_data = $this->GetSingleRecord("user_master",$info_array);

				if(count($user_data)>0) {
					$response_array['status']='success';
					$response_array['message']='logged in successfully.';
					$response_array['data']=$user_data;
					$this->response($this->json($response_array), 200);
				} else {
					$response_array['status']='fail';
					$response_array['message']='invalid email or password.';
					$response_array['data']='';
					$this->response($this->json($response_array));
				}
			}
			
			// If invalid inputs "Bad Request" status message and reason
			$error = array('status' => "Failed", "msg" => "Invalid data");
			$this->response($this->json($error), 400);
		}
		
		protected function users(){	
			
			$info_array = array(
						"fields"=>"user_id,username,email,mobile,first_name,last_name,active,gender,profile_img",
					);
			$user_data = $this->GetRecord("user",$info_array);

			if(count($user_data)>0) {
				$response_array['success']= true;
				$response_array['message']='Total '.count($user_data).' record(s) found.';
				$response_array['total_record']= count($user_data);
				$response_array['data']=$user_data;
				$this->response($this->json($response_array), 200);
			} else {
				$response_array['status']='fail';
				$response_array['message']='Record not found.';
				$response_array['data']='';
				$this->response($this->json($response_array), 204);
			}
		}

		protected function deleteuser(){
			// Cross validation if the request method is DELETE else it will return "Not Acceptable" status
			if($this->get_request_method() != "DELETE"){
				$this->response('',406);
			}
			$id = (int)$this->_request['id'];
			if($id > 0){
				$where = "user_id = '".$id."'";
				$delete = $this->DeleteRecord("user_master",$where);

				if($delete>0) {
					$response_array['status']='success';
					$response_array['message']='Total '.count($delete).' record(s) Deleted.';
					$response_array['data']=$delete;
					$this->response($this->json($response_array), 200);
				} else {
					$response_array['status']='fail';
					$response_array['message']='no record deleted';
					$response_array['data']='';
					$this->response($this->json($response_array), 200);
				}
			} else {
				$this->response('',204);	// If no records "No Content" status
			}
		}
		

		protected function addEvent()
		{

			$data = $this->_request;
			$txt = json_encode($_FILES); 
        	$myfile = file_put_contents('req.txt', $txt.PHP_EOL , FILE_APPEND | LOCK_EX);
   
        	// print_r($_FILES);
        	// die();

   //      	$target_path = "images/";

			// $target_path = $target_path . basename( $_FILES['image']['name']); 

			// if(move_uploaded_file($_FILES['image']['tmp_name'], $target_path))
			// {
			 
			// echo "The file ". basename( $_FILES['image']['name']). " is uploaded";
			// die();
			 
			// }
			 
			// else {
			 
			// echo "Problem uploading file";
			// die();
			 
			// }


			if (isset($data['title']) && !empty($this->_request['title'])) 
			{
				$title = $this->_request['title'];
			}
			else
			{
				$response_array['success'] 	= 	false;
				$response_array['message']	=	'Title required';
				$response_array['data']		=	(object)array();
				print_r($this->json($response_array));
				die();
			}

			if (isset($data['description']) && !empty($this->_request['description'])) 
			{
				$description = $this->_request['description'];
			}
			else
			{
				$response_array['success'] 	= 	false;
				$response_array['message']	=	'Description required';
				$response_array['data']		=	(object)array();
				print_r($this->json($response_array));
				die();
			}

			if (isset($data['user_id']) && !empty($this->_request['user_id'])) 
			{
				$userId = $this->_request['user_id'];
			}
			else
			{
				$response_array['success'] 	= 	false;
				$response_array['message']	=	'User id required';
				$response_array['data']		=	(object)array();
				print_r($this->json($response_array));
				die();
			}

			if (isset($data['destination']) && !empty($this->_request['destination'])) 
			{
				$destination = $this->_request['destination'];
			}
			else
			{
				$response_array['success'] 	= 	false;
				$response_array['message']	=	'Destination required';
				$response_array['data']		=	(object)array();
				print_r($this->json($response_array));
				die();
			}

			if (isset($data['amount']) && !empty($this->_request['amount'])) 
			{
				$amount = $this->_request['amount'];
			}
			else
			{
				$response_array['success'] 	= 	false;
				$response_array['message']	=	'Amount required';
				$response_array['data']		=	(object)array();
				print_r($this->json($response_array));
				die();
			}

			if (isset($data['people']) && !empty($this->_request['people'])) 
			{
				$people = $this->_request['people'];
			}
			else
			{
				$response_array['success'] 	= 	false;
				$response_array['message']	=	'People required';
				$response_array['data']		=	(object)array();
				print_r($this->json($response_array));
				die();
			}

			if (isset($data['from_date']) && !empty($this->_request['from_date'])) 
			{
				$startDate = date("Y-m-d H:i:s", strtotime($this->_request['from_date']));
			}
			else
			{
				$response_array['success'] 	= 	false;
				$response_array['message']	=	'People required';
				$response_array['data']		=	(object)array();
				print_r($this->json($response_array));
				die();
			}

			if (isset($data['to_date']) && !empty($this->_request['to_date'])) 
			{
				$endDate = date("Y-m-d H:i:s", strtotime($this->_request['to_date']));
			}
			else
			{
				$response_array['success'] 	= 	false;
				$response_array['message']	=	'People required';
				$response_array['data']		=	(object)array();
				print_r($this->json($response_array));
				die();
			}

			$info_array = array(
					'title'				=> $title,
					'description'		=> $description,
					'user_id'			=> $userId,
					'destination'		=> $destination,
					'amount'			=> $amount,
					'event_category_id'	=> 0,
					'people'			=> $people,
					'start_time'		=> $startDate,
					'end_time'			=> $endDate,
					'publish_status' 	=> 1
				);
			//$this->response($this->json($info_array), 200);		
			$insertedData = $this->InsertRecord("events",$info_array);

			if($insertedData > 0) 
			{
				$response_array['success']	= 	true;
				$response_array['message']	=	'Event successfully publish.';
				$response_array['data']		= 	 (object)array();
				// $this->response($this->json($response_array), 200);
				print_r($this->json($response_array));
				die();
			} 
			else 
			{
				$response_array['success'] 	= 	false;
				$response_array['message']	=	'Event publish fail.';
				$response_array['data']		=	(object)array();
				// $this->response($this->json($response_array), 204);
				print_r($this->json($response_array));
				die();
			}
				
		}

		protected function getUserTimeline()
		{	

			$data = $this->_request;

			$txt = json_encode($data); 
        	$myfile = file_put_contents('test.txt', $txt.PHP_EOL , FILE_APPEND | LOCK_EX);
			if (isset($data['user_id']) && !empty($this->_request['user_id'])) 
			{
				$userId = $this->_request['user_id'];
			}
			else
			{
				$response_array['success'] 	= 	false;
				$response_array['message']	=	'User id required';
				$response_array['data']		=	(object)array();
				print_r($this->json($response_array));
				die();
			}
			
			$info_array = array(
						"fields"=>"id,title,description,user_id,destination,amount,event_category_id,people,start_time,end_time",
						"where"=>"user_id = '".$userId."'"
					);
			$team = array('id' => 1, 'user_image' => '');

			$eventsData = $this->GetRecord("events",$info_array);

			$mainArray = array();
			$resultArray = array();


			if(count($eventsData) > 0) {
				$response_array['success']	= 	true;
				$response_array['message']	=	'Publish events list';
				$response_array['data']		= 	 $eventsData;
				// $this->response($this->json($response_array), 200);
				print_r($this->json($response_array));
				die();
			} else {
				$response_array['success'] 	= 	false;
				$response_array['message']	=	'No Record found';
				$response_array['data']		=	(object)array();
				// $this->response($this->json($response_array), 204);
				print_r($this->json($response_array));
				die();
			}
		}

		protected function getTimeline()
		{	

			$data = $this->_request;


			if (isset($data['user_id']) && !empty($this->_request['user_id'])) 
			{
				$userId = $this->_request['user_id'];
			}
			else
			{
				$response_array['success'] 	= 	false;
				$response_array['message']	=	'User id required';
				$response_array['data']		=	(object)array();
				print_r($this->json($response_array));
				die();
			}
			
			$info_array = array(
						"fields"=>"id,title,description,user_id,destination,amount,event_category_id,people,start_time,end_time,created_at",
						"where"=>"user_id = '".$userId."'"
					);


			$team = array('id' => 1, 'user_image' => '');

			$eventsData = $this->GetRecord("events",$info_array);
			$mainArray = array();
			$resultArray = array();

			
			// $eventName   = array("Hiking","Camping","Boating","River Rafting","Mount Climbing");
			// $randomName = array_rand($eventName, 1);
			for ($i = 0; $i < 3; $i++) 
			{ 
				// $teamArray[$i]['id'] = 1;
				$teamArray[$i]['user_id'] = 2;
				// $teamArray[$i]['event_id'] = 3;
				$teamArray[$i]['user_image'] = "https://i1.wp.com/www.winhelponline.com/blog/wp-content/uploads/2017/12/user.png?resize=256%2C256&quality=100";
			}

			if(count($eventsData) > 0) 
			{
				foreach ($eventsData as $key => $value) 
				{
					$eventName   = array("Hiking","Camping","Boating","River Rafting","Mount Climbing");
					$randomName = array_rand($eventName);

					$isFriend   = array(true, false);
					$randomIsFriend = array_rand($isFriend);

					$user_info_array = array(
						"fields"=>"user_id,first_name",
						"where"=>"user_id = '".$value['user_id']."'"
					);
					$userData = $this->GetSingleRecord("user",$user_info_array);	

					$like_info_array = array(
						"fields"=>"user_id,event_id",
						"where"=>"user_id = '".$value['user_id']."' and event_id = '".$value['id']."'"
					);

					$likeData = $this->GetSingleRecord("event_likes",$like_info_array);
					$isLike = false;
					if (count($likeData) > 0) 
					{
						$isLike = true;
					}

					$bookmark_info_array = array(
						"fields"=>"user_id,event_id",
						"where"=>"user_id = '".$value['user_id']."' and event_id = '".$value['id']."'"
					);

					$bookmarkData = $this->GetSingleRecord("bookmark",$bookmark_info_array);
					$isBookmark = false;
					if (count($bookmarkData) > 0) 
					{
						$isBookmark = true;
					}

					$team_info_array = array(
						"fields"=>"user_id,event_id",
						"where"=>"user_id = '".$value['user_id']."' and event_id = '".$value['id']."'"
					);

					$teamData = $this->GetSingleRecord("event_team",$team_info_array);
					$isJoin = false;
					if (count($teamData) > 0) 
					{
						$isJoin = true;
					}


					$resultArray['id']     			= $value['id'];
					$resultArray['title']     		= $value['title'];
					$resultArray['user_name']     	= $userData['first_name'];
					$resultArray['description']     = $value['description'];
					$resultArray['user_id']     	= $value['user_id'];
					$resultArray['destination']     = $value['destination'];
					$resultArray['expence']     	= $value['amount'];
					$resultArray['category_id']     = $value['event_category_id'];
					$resultArray['people']     		= $value['people'];
					$resultArray['start_time']     	= $value['start_time'];
					$resultArray['end_time']     	= $value['end_time'];
					$resultArray['event_name']  	= $eventName[$randomName];
					$resultArray['like_count']  	= 5;
					$resultArray['comment_count'] 	= 9;
					$resultArray['is_like']       	= $isLike;
					$resultArray['is_bookmarked'] 	= $isBookmark;
					$resultArray['is_join'] 		= $isJoin;
					$resultArray['is_friend'] 		= $isFriend[$randomIsFriend];
					$resultArray['team'] 		    = $teamArray;
					$resultArray['created_at'] 	    = $value['created_at'];

					array_push($mainArray, $resultArray);
				}
				$response_array['success']	= 	true;
				$response_array['message']	=	'Publish events list';
				$response_array['data']		= 	 $mainArray;
				// $this->response($this->json($response_array), 200);
				print_r($this->json($response_array));
				die();
			} else {
				$response_array['success'] 	= 	false;
				$response_array['message']	=	'No Record found';
				$response_array['data']		=	(object)array();
				// $this->response($this->json($response_array), 204);
				print_r($this->json($response_array));
				die();
			}
		}

		protected function likeEvent()
		{

			$data = $this->_request;

			if (isset($data['event_id']) && !empty($this->_request['event_id'])) 
			{
				$eventId = $this->_request['event_id'];
			}
			else
			{
				$response_array['success'] 	= 	false;
				$response_array['message']	=	'Event id required';
				$response_array['data']		=	(object)array();
				print_r($this->json($response_array));
				die();
			}

			if (isset($data['user_id']) && !empty($this->_request['user_id'])) 
			{
				$userId = $this->_request['user_id'];
			}
			else
			{
				$response_array['success'] 	= 	false;
				$response_array['message']	=	'User id required';
				$response_array['data']		=	(object)array();
				print_r($this->json($response_array));
				die();
			}

			if (isset($data['like_flag']) && !empty($this->_request['like_flag'])) 
			{
				$likeFlag = $this->_request['like_flag'];
			}
			else
			{
				$response_array['success'] 	= 	false;
				$response_array['message']	=	'flag required';
				$response_array['data']		=	(object)array();
				print_r($this->json($response_array));
				die();
			}

			if ($likeFlag == 'true') 
			{
				

				$check_info = array(
						"fields"=>"event_id,user_id",
						"where"=>"user_id = '".$userId."' and event_id = '".$eventId."'"
					);

				$detailsExist = $this->GetSingleRecord("event_likes",$check_info);

				if(count($detailsExist) > 0) 
				{
					$response_array['success']	= 	false;
					$response_array['message']	=	'You already like this event';
					$response_array['data']		= 	 (object)array();
					// $this->response($this->json($response_array), 200);
					print_r($this->json($response_array));
					die();
 				}
 				else
 				{
 					$info_array = array(
						'event_id'			=> $eventId,
						'user_id'			=> $userId
					);

 					$insertedData = $this->InsertRecord("event_likes",$info_array);

					if($insertedData > 0) 
					{
						$response_array['success']	= 	true;
						$response_array['message']	=	'You like this event';
						$response_array['data']		= 	 (object)array();
						// $this->response($this->json($response_array), 200);
						print_r($this->json($response_array));
						die();
					} 
					else 
					{
						$response_array['success'] 	= 	false;
						$response_array['message']	=	'like event fail';
						$response_array['data']		=	(object)array();
						// $this->response($this->json($response_array), 204);
						print_r($this->json($response_array));
						die();
					}
 				}

			}
			else
			{
				$where = "user_id = '".$userId."' and event_id = '".$eventId."'";
				$deleteData = $this->DeleteRecord("event_likes",$where);
				if($deleteData > 0) 
				{
					$response_array['success']	= 	true;
					$response_array['message']	=	'You unlike this event';
					$response_array['data']		= 	 (object)array();
					// $this->response($this->json($response_array), 200);
					print_r($this->json($response_array));
					die();
				} 
				else 
				{
					$response_array['success'] 	= 	false;
					$response_array['message']	=	'unlike event fail';
					$response_array['data']		=	(object)array();
					// $this->response($this->json($response_array), 204);
					print_r($this->json($response_array));
					die();
				}
			}	
				
		}

		protected function bookmarkEvent()
		{

			$data = $this->_request;


			if (isset($data['user_id']) && !empty($this->_request['user_id'])) 
			{
				$userId = $this->_request['user_id'];
			}
			else
			{
				$response_array['success'] 	= 	false;
				$response_array['message']	=	'User id required';
				$response_array['data']		=	(object)array();
				print_r($this->json($response_array));
				die();
			}
			
			if (isset($data['event_id']) && !empty($this->_request['event_id'])) 
			{
				$eventId = $this->_request['event_id'];
			}
			else
			{
				$response_array['success'] 	= 	false;
				$response_array['message']	=	'Event id required';
				$response_array['data']		=	(object)array();
				print_r($this->json($response_array));
				die();
			}

			if (isset($data['bookmark_flag']) && !empty($this->_request['bookmark_flag'])) 
			{
				$bookmarkFlag = $this->_request['bookmark_flag'];
			}
			else
			{
				$response_array['success'] 	= 	false;
				$response_array['message']	=	'flag required';
				$response_array['data']		=	(object)array();
				print_r($this->json($response_array));
				die();
			}

			if ($bookmarkFlag == 'true') 
			{
				

				$check_info = array(
						"fields"=>"user_id,event_id",
						"where"=>"user_id = '".$userId."' and event_id = '".$eventId."'"
					);

				$detailsExist = $this->GetSingleRecord("bookmark",$check_info);

				if(count($detailsExist) > 0) 
				{
					$response_array['success']	= 	false;
					$response_array['message']	=	'Already bookmark';
					$response_array['data']		= 	 (object)array();
					// $this->response($this->json($response_array), 200);
					print_r($this->json($response_array));
					die();
 				}
 				else
 				{
 					$info_array = array(
						'user_id'			=> $userId,
						'event_id'			=> $eventId
					);

 					$insertedData = $this->InsertRecord("bookmark",$info_array);

					if($insertedData > 0) 
					{
						$response_array['success']	= 	true;
						$response_array['message']	=	'Bookmark done';
						$response_array['data']		= 	 (object)array();
						// $this->response($this->json($response_array), 200);
						print_r($this->json($response_array));
						die();
					} 
					else 
					{
						$response_array['success'] 	= 	false;
						$response_array['message']	=	'Bookmark Fail';
						$response_array['data']		=	(object)array();
						// $this->response($this->json($response_array), 204);
						print_r($this->json($response_array));
						die();
					}
 				}

			}
			else
			{
				$where = "user_id = '".$userId."' and event_id = '".$eventId."'";
				$deleteData = $this->DeleteRecord("bookmark",$where);
				if($deleteData > 0) 
				{
					$response_array['success']	= 	true;
					$response_array['message']	=	'Remove from bookmark done';
					$response_array['data']		= 	 (object)array();
					// $this->response($this->json($response_array), 200);
					print_r($this->json($response_array));
					die();
				} 
				else 
				{
					$response_array['success'] 	= 	false;
					$response_array['message']	=	'Remove from bookmark fail';
					$response_array['data']		=	(object)array();
					// $this->response($this->json($response_array), 204);
					print_r($this->json($response_array));
					die();
				}
			}	
				
		}

		protected function comment()
		{

			$data = $this->_request;

			if (isset($data['user_id']) && !empty($this->_request['user_id'])) 
			{
				$userId = $this->_request['user_id'];
			}
			else
			{
				$response_array['success'] 	= 	false;
				$response_array['message']	=	'User id required';
				$response_array['data']		=	(object)array();
				print_r($this->json($response_array));
				die();
			}

			if (isset($data['event_id']) && !empty($this->_request['event_id'])) 
			{
				$eventId = $this->_request['event_id'];
			}
			else
			{
				$response_array['success'] 	= 	false;
				$response_array['message']	=	'Event id required';
				$response_array['data']		=	(object)array();
				print_r($this->json($response_array));
				die();
			}

			if (isset($data['comment']) && !empty($this->_request['comment'])) 
			{
				$comment = $this->_request['comment'];
			}
			else
			{
				$response_array['success'] 	= 	false;
				$response_array['message']	=	'Comment required';
				$response_array['data']		=	(object)array();
				print_r($this->json($response_array));
				die();
			}


			$info_array = array(
					'user_id'		=> $userId,
					'event_id'		=> $eventId,
					'comment'		=> $comment
				);
			//$this->response($this->json($info_array), 200);		
			$insertedData = $this->InsertRecord("comments",$info_array);

			if($insertedData > 0) 
			{
				$response_array['success']	= 	true;
				$response_array['message']	=	'Comment done';
				$response_array['data']		= 	 (object)array();
				// $this->response($this->json($response_array), 200);
				print_r($this->json($response_array));
				die();
			} 
			else 
			{
				$response_array['success'] 	= 	false;
				$response_array['message']	=	'Comment fail.';
				$response_array['data']		=	(object)array();
				// $this->response($this->json($response_array), 204);
				print_r($this->json($response_array));
				die();
			}
				
		}

		protected function joinTeam()
		{

			$data = $this->_request;

			if (isset($data['user_id']) && !empty($this->_request['user_id'])) 
			{
				$userId = $this->_request['user_id'];
			}
			else
			{
				$response_array['success'] 	= 	false;
				$response_array['message']	=	'User id required';
				$response_array['data']		=	(object)array();
				print_r($this->json($response_array));
				die();
			}

			if (isset($data['event_id']) && !empty($this->_request['event_id'])) 
			{
				$eventId = $this->_request['event_id'];
			}
			else
			{
				$response_array['success'] 	= 	false;
				$response_array['message']	=	'Event id required';
				$response_array['data']		=	(object)array();
				print_r($this->json($response_array));
				die();
			}



			$check_info = array(
						"fields"=>"user_id,event_id",
						"where"=>"user_id = '".$userId."' and event_id = '".$eventId."'"
					);

			$detailsExist = $this->GetSingleRecord("event_team",$check_info);

			if(count($detailsExist) > 0) 
			{
				$response_array['success']	= 	false;
				$response_array['message']	=	'Already joined';
				$response_array['data']		= 	 (object)array();
				// $this->response($this->json($response_array), 200);
				print_r($this->json($response_array));
				die();
			}
			else
			{
				$info_array = array(
						'user_id'		=> $userId,
						'event_id'		=> $eventId
					);
				$insertedData = $this->InsertRecord("event_team",$info_array);

				if($insertedData > 0) 
				{
					$response_array['success']	= 	true;
					$response_array['message']	=	'Succesfully join team';
					$response_array['data']		= 	 (object)array();
					// $this->response($this->json($response_array), 200);
					print_r($this->json($response_array));
					die();
				} 
				else 
				{
					$response_array['success'] 	= 	false;
					$response_array['message']	=	'Joining team fail.';
					$response_array['data']		=	(object)array();
					// $this->response($this->json($response_array), 204);
					print_r($this->json($response_array));
					die();
				}
			}	
				
		}


	}

	// Initiiate Library
	$api = new API();
	$api->processApi();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <title>upload</title>
</head>
<body>
<form action="http://localhost:8000" method="post" enctype="multipart/form-data">
  <p><input type="file" name="file1">
  <p><button type="submit">Submit</button>
</form>
</body>
</html>