<?php 
class Main extends CI_Controller {
	
	public $graph_url = "https://graph.facebook.com/";
	private $app_id;
	private $app_secret;
	private $app_info;
	private $user_id;
	private $user_info;
	private $group_id = '149889995159117';
	private $group_members;

	public function  __construct()
	{
		parent::__construct();

		// Load members model
		$this->load->model('members_model');
		// Load facebook config
		$this->load->config('facebook');


		$this->app_id = $this->config->item('appId');
		$this->app_secret = $this->config->item('secret');

		$this->user_id = $this->facebook->getUser();


		if($this->user_id) {
			try {
				// Fetch the viewer's vasic information
				$this->user_info = $facebook->api('/me');
			} catch (FacebookApiException $e) {
				header('Location: '. getUrl($_SERVER['REQUEST_URI']));
				exit();
			}

		}

		echo $this->user_id;

		// Fetch the basic info of the app that they are using
		$this->app_info = $this->facebook->api('/'.$this->app_id);

		// Fetch all the facebook group member's data
		$this->group_members = idx($this->facebook->api('/'.$this->group_id.'/members?limit=5000&offset=0'), 'data', array());
		
		
	}

	public function index() 
	{
		
		$members = $this->members_model->get_all_members();


		// Initialize the data to be used on views
		$data = array(
				'app_info' 			=> $this->app_info,
				'app_id'			=> $this->app_id,
				'app_name'			=> $this->app_info['name'],
				'user_info'			=> $this->user_info,
				'group_memebers' 	=> $this->group_members
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

		//For Debugging Purposes only. 
		//Uncomment it if you want to see all the data in a preformatted form
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
		// echo '</pre>';

		if(count($members) == 0)
		{

		}


		$this->load->view('mainview', $data);
	}

	public function process()
	{
		
	}
}