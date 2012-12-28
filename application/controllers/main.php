<?php 
class Main extends CI_Controller {
	
	public $graph_url = "https://graph.facebook.com/";
	private $app_id;
	private $app_info;
	private $user_id;
	private $user_info;
	private $group_id;
	private $group_members;

	public function  __construct()
	{
		parent::__construct();

		// Load members model
		$this->load->model('members_model');
		// Load facebook config
		$this->load->config('facebook');
		// Set Application ID
		$this->app_id = $this->config->item('appId');
		// Set group id that will be used
		$this->group_id = $this->config->item('groupId');
		// Redirect URI/Canvas Page URI
		$canvas_page = $this->config->item('canvas_page');
		// OAuth Dialog Redirect URL
		// $auth_url = "http://www.facebook.com/dialog/oauth?client_id=".
  //   				$this->app_id."&redirect_uri=".urlencode($canvas_page)."&scope=email,user_birthday,user_interests,user_about_me";

    	// Get User ID. Returns 0 if the application is not authenticated
		$this->user_id = $this->facebook->getUser();
		
		if(empty($this->user_id)) 
		{
			echo '<script type="text/javascript">top.location.href="'.$this->facebook->getLoginURL().'";</script>';
	        exit;
    	}
    	else 
    	{
			try 
			{
				// Fetch the viewer's vasic information
				$this->user_info = $this->facebook->api('/me');
			} catch (FacebookApiException $e) {
				header('Location: '. getUrl($_SERVER['REQUEST_URI']));
				exit();
			}
		}

		// Fetch the basic info of the app that they are using
		$this->app_info = $this->facebook->api('/'.$this->app_id);

		// Fetch all the facebook group member's data
		$this->group_members = idx($this->facebook->api('/'.$this->group_id.'/members?limit=5000&offset=0'), 'data', array());
	}

	public function index() 
	{
		
		$members 			= $this->members_model->get_all_members();
		$members_pick 		= array();
		$members_picked 	= array();
		$members_not_picked = array();
		$form_hiddendata 	= array();
		$hide 				= "";
		$show 				= "block";
		$section_desc		= "";
		$user_image_url 	= "";
		$user_name 			= "";
		$pick_image_url 	= "";
		$pick_name			= "";

		if(count($members) == 0)
		{
			$members_data = array();
			foreach($this->group_members as $key => $value)
			{
				$members_data[$key]['fb_id']	= $value['id'];
				$members_data[$key]['fb_name'] 	= $value['name'];
			}
			// echo '<pre>';
			// print_r($members_data);
			// echo '</pre>';
			$this->members_model->set_all_members($members_data);
		}

		foreach($members as $key => $value)
		{
			if(empty($members[$key]->pick_id))
			{
				$members_pick[$key] = array(
					'fb_id' 				=> 	$members[$key]->fb_id, 
					'fb_name'				=>	$members[$key]->fb_name,
					'fb_image_url_large'	=>	$this->graph_url.$members[$key]->fb_id.'/picture?width=140&height=140',
					'fb_image_url_thumb' 	=>	$this->graph_url.$members[$key]->fb_id.'/picture?type=square',
					'pick_id'				=>	0,
					'pick_name'				=>	'Anonymous',
					'pick_image_url_large'	=>	base_url().'/images/unknown.jpg',
					'pick_image_url_thumb'	=>	base_url().'/images/unknown.gif'
				);

				$members_not_picked[$key] = array(
					'fb_id' 				=> 	$members[$key]->fb_id, 
					'fb_name'				=>	$members[$key]->fb_name,
					'fb_image_url_large'	=>	$this->graph_url.$members[$key]->fb_id.'/picture?width=140&height=140'
				);
			}
			else
			{
				$members_pick[$key] = array(
					'fb_id' 				=> 	$members[$key]->fb_id, 
					'fb_name'				=>	$members[$key]->fb_name,
					'fb_image_url_large'	=>	$this->graph_url.$members[$key]->fb_id.'/picture?width=140&height=140',
					'fb_image_url_thumb' 	=>	$this->graph_url.$members[$key]->fb_id.'/picture?type=square',
					'pick_id'				=>	$members[$key]->pick_id,
					'pick_name'				=>	$members[$key]->pick_name,
					'pick_image_url_large'	=>	$this->graph_url.$members[$key]->pick_id.'/picture?width=140&height=140',
					'pick_image_url_thumb'	=>	$this->graph_url.$members[$key]->pick_id.'/picture?type=square'
				);
				$members_picked[$key] = array(
					'fb_id' 				=> 	$members[$key]->fb_id, 
					'fb_name'				=>	$members[$key]->fb_name,
					'fb_image_url_large'	=>	$this->graph_url.$members[$key]->fb_id.'/picture?width=140&height=140',
					'pick_id'				=>	$members[$key]->pick_id,
					'pick_name'				=>	$members[$key]->pick_name,
					'pick_image_url_large'	=>	$this->graph_url.$members[$key]->pick_id.'/picture?width=140&height=140'
				);
			}
		}

		foreach($members_not_picked as $key => $value)
		{
			if($this->user_id == $members_not_picked[$key]['fb_id'])
			{
				 $section_desc 	 	= 	"Sino kaya ang mabubunot ni ".he($members_not_picked[$key]['fb_name'])."? <br /> Pindutin mo na ang mahiwagang buton ng malaman naten kung sino ang maswerteng taong mabububunot mo.";
			     $hide 			 	= 	"";
			     $show 			 	= 	"none";
			     $pick_image_url 	= 	base_url().'/images/unknown.jpg';
			     $pick_name		 	= 	'Sino kaya to?';
			     $form_hiddendata	= 	array(
		     	 'fb_name'			=> 	$members_not_picked[$key]['fb_name'],
		     	 'fb_id'			=>	$this->user_id,
		     	 'pick_id'			=>	'',
		     	 'pick_name'		=>  ''
			 	 );
			}
		}

		foreach($members_picked as $key => $value)
		{
			if($this->user_id == $members_picked[$key]['fb_id'])
			{
				$hide 				= 	'style="display:none;"';
		        $show 				= 	"block";
		        $section_desc 		= 	"Congratulations sayo ".he($members_picked[$key]['pick_name'])." dahil ikaw ang nabunot ni ".he($members_picked[$key]['fb_name']).". Malamang may surpresang nag hihintay sayo. Para naman dun sa nakabunot. Good luck na lang sayo haha!!!";
				$pick_image_url 	= 	$members_picked[$key]['pick_image_url_large'];
			    $pick_name			= 	$members_picked[$key]['pick_name'];
			    $form_hiddendata	= 	array(
		     	'fb_name'			=> 	$members_picked[$key]['fb_name'],
		     	'fb_id'				=>	$this->user_id,
		     	'pick_id'			=>	$members_picked[$key]['pick_id'],
		     	'pick_name'			=>  $members_picked[$key]['pick_name']
		     	);
			}
		}

		// Initialize the data to be used on views
		$data = array(
				'app_info' 			=> $this->app_info,
				'app_id'			=> $this->app_id,
				'app_name'			=> $this->app_info['name'],
				'app_image'			=> $this->app_info['logo_url'],
				'user_info'			=> $this->user_info,
				'user_id'			=> $this->user_id,
				'group_memebers' 	=> $this->group_members,
				'graph_url'			=> $this->graph_url,
				'members'			=> $members,
				'members_pick'		=> $members_pick,
				'members_not_picked'=> $members_not_picked,
				'show'				=> $show,
				'hide'				=> $hide,
				'section_desc'		=> $section_desc,
				'user_image_url'	=> $this->graph_url.$this->user_id.'/picture?width=140&height=140',
				'user_name'			=> he($this->user_id['name']),
				'pick_image_url'	=> $pick_image_url,
				'pick_name'			=> $pick_name,
				'form_hiddendata'	=> $form_hiddendata	
			);
		// Initialize Open Graph metadata
		$data['meta'] = array(
				'og:title' 		=> $this->app_info['name'],
				'og:type'		=> 'website',
				'og:url'		=> getUrl(),
				'og:image'		=> getUrl().'logo.png',
				'og:site_name'	=> $this->app_info['name'],
				'og:description'=> 'Online Bunutan on facebook',
				'fb:app_id'		=> $this->app_id
			);

		/**
		* For Debugging Purpose only. 
		* Uncomment it if you want to see 
		* all the data in a preformatted form
		*/
		// echo 'This is the user id: '.$this->user_id.'<br />';
		// echo 'This is the app id: '.$this->app_id.'<br />';
		// echo '<pre>';
		// // print_r($this->config);
		// echo 'App Info: <br />';
		// print_r($this->app_info);
		// echo 'Number of members: '.count($members).'<br />';
		// echo 'Data that will be sent to view: <br />';
		// print_r($data);
		// echo '<br />Facebook Group Data: <br />';
		// print_r($this->group_members);
		// echo 'List of members who dont already have pick: <br/>';
		// print_r($members_pick);
		// print_r($members_not_picked);
		// echo 'List of members who already pick: <br/>';
		// print_r($members_picked);
		// echo 'List of members from members table: <br/>';
		// print_r($members);
		// echo '</pre>';



		$this->load->view('mainview', $data);
	}

	public function pick()
	{

	}

	public function process()
	{
		
	}
}