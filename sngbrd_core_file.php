<?php
date_default_timezone_set('Asia/Kolkata');
/*
Controller name: Core
Controller description: Basic introspection methods
*/

class JSON_API_Core_Controller {

  
  public function info() {
    global $json_api;
    $php = '';
    if (!empty($json_api->query->controller)) {
      return $json_api->controller_info($json_api->query->controller);
    } else {
      $dir = json_api_dir();
      if (file_exists("$dir/json-api.php")) {
        $php = file_get_contents("$dir/json-api.php");
      } else {
        // Check one directory up, in case json-api.php was moved
        $dir = dirname($dir);
        if (file_exists("$dir/json-api.php")) {
          $php = file_get_contents("$dir/json-api.php");
        }
      }
      if (preg_match('/^\s*Version:\s*(.+)$/m', $php, $matches)) {
        $version = $matches[1];
      } else {
        $version = '(Unknown)';
      }
      $active_controllers = explode(',', get_option('json_api_controllers', 'core'));
      $controllers = array_intersect($json_api->get_controllers(), $active_controllers);
      return array(
        'json_api_version' => $version,
        'controllers' => array_values($controllers)
      );
    }
  }
  
  public function get_recent_posts() {
    global $json_api;
    $posts = $json_api->introspector->get_posts();
    return $this->posts_result($posts);
  }
  
  public function get_posts() {
    global $json_api;
    $url = parse_url($_SERVER['REQUEST_URI']);
    $defaults = array(
      'ignore_sticky_posts' => true
    );
    $query = wp_parse_args($url['query']);
    unset($query['json']);
    unset($query['post_status']);
    $query = array_merge($defaults, $query);
    $posts = $json_api->introspector->get_posts($query);
    $result = $this->posts_result($posts);
    $result['query'] = $query;
    return $result;
  }
  
  public function get_post() {
    global $json_api, $post;
    $post = $json_api->introspector->get_current_post();
    if ($post) {
      $previous = get_adjacent_post(false, '', true);
      $next = get_adjacent_post(false, '', false);
      $response = array(
        'post' => new JSON_API_Post($post)
      );
      if ($previous) {
        $response['previous_url'] = get_permalink($previous->ID);
      }
      if ($next) {
        $response['next_url'] = get_permalink($next->ID);
      }
      return $response;
    } else {
      $json_api->error("Not found.");
    }
  }

  public function get_page() {
    global $json_api;
    extract($json_api->query->get(array('id', 'slug', 'page_id', 'page_slug', 'children')));
    if ($id || $page_id) {
      if (!$id) {
        $id = $page_id;
      }
      $posts = $json_api->introspector->get_posts(array(
        'page_id' => $id
      ));
    } else if ($slug || $page_slug) {
      if (!$slug) {
        $slug = $page_slug;
      }
      $posts = $json_api->introspector->get_posts(array(
        'pagename' => $slug
      ));
    } else {
      $json_api->error("Include 'id' or 'slug' var in your request.");
    }
    
    // Workaround for https://core.trac.wordpress.org/ticket/12647
    if (empty($posts)) {
      $url = $_SERVER['REQUEST_URI'];
      $parsed_url = parse_url($url);
      $path = $parsed_url['path'];
      if (preg_match('#^http://[^/]+(/.+)$#', get_bloginfo('url'), $matches)) {
        $blog_root = $matches[1];
        $path = preg_replace("#^$blog_root#", '', $path);
      }
      if (substr($path, 0, 1) == '/') {
        $path = substr($path, 1);
      }
      $posts = $json_api->introspector->get_posts(array('pagename' => $path));
    }
    
    if (count($posts) == 1) {
      if (!empty($children)) {
        $json_api->introspector->attach_child_posts($posts[0]);
      }
      return array(
        'page' => $posts[0]
      );
    } else {
      $json_api->error("Not found.");
    }
  }
  
  public function get_date_posts() {
    global $json_api;
    if ($json_api->query->date) {
      $date = preg_replace('/\D/', '', $json_api->query->date);
      if (!preg_match('/^\d{4}(\d{2})?(\d{2})?$/', $date)) {
        $json_api->error("Specify a date var in one of 'YYYY' or 'YYYY-MM' or 'YYYY-MM-DD' formats.");
      }
      $request = array('year' => substr($date, 0, 4));
      if (strlen($date) > 4) {
        $request['monthnum'] = (int) substr($date, 4, 2);
      }
      if (strlen($date) > 6) {
        $request['day'] = (int) substr($date, 6, 2);
      }
      $posts = $json_api->introspector->get_posts($request);
    } else {
      $json_api->error("Include 'date' var in your request.");
    }
    return $this->posts_result($posts);
  }
  
  public function get_category_posts() {
    global $json_api;
    $category = $json_api->introspector->get_current_category();
    if (!$category) {
      $json_api->error("Not found.");
    }
    $posts = $json_api->introspector->get_posts(array(
      'cat' => $category->id
    ));
    return $this->posts_object_result($posts, $category);
  }
  
  public function get_tag_posts() {
    global $json_api;
    $tag = $json_api->introspector->get_current_tag();
    if (!$tag) {
      $json_api->error("Not found.");
    }
    $posts = $json_api->introspector->get_posts(array(
      'tag' => $tag->slug
    ));
    return $this->posts_object_result($posts, $tag);
  }
  
  public function get_author_posts() {
    global $json_api;
    $author = $json_api->introspector->get_current_author();
    if (!$author) {
      $json_api->error("Not found.");
    }
    $posts = $json_api->introspector->get_posts(array(
      'author' => $author->id
    ));
    return $this->posts_object_result($posts, $author);
  }
  
  public function get_search_results() {
    global $json_api;
    if ($json_api->query->search) {
      $posts = $json_api->introspector->get_posts(array(
        's' => $json_api->query->search
      ));
    } else {
      $json_api->error("Include 'search' var in your request.");
    }
    return $this->posts_result($posts);
  }
  
  public function get_date_index() {
    global $json_api;
    $permalinks = $json_api->introspector->get_date_archive_permalinks();
    $tree = $json_api->introspector->get_date_archive_tree($permalinks);
    return array(
      'permalinks' => $permalinks,
      'tree' => $tree
    );
  }
  
  public function get_category_index() {
    global $json_api;
    $args = null;
    if (!empty($json_api->query->parent)) {
      $args = array(
        'parent' => $json_api->query->parent
      );
    }
    $categories = $json_api->introspector->get_categories($args);
    return array(
      'count' => count($categories),
      'categories' => $categories
    );
  }
  
  public function get_tag_index() {
    global $json_api;
    $tags = $json_api->introspector->get_tags();
    return array(
      'count' => count($tags),
      'tags' => $tags
    );
  }
  
  public function get_author_index() {
    global $json_api;
    $authors = $json_api->introspector->get_authors();
    return array(
      'count' => count($authors),
      'authors' => array_values($authors)
    );
  }
  
  public function get_page_index() {
    global $json_api;
    $pages = array();
    $post_type = $json_api->query->post_type ? $json_api->query->post_type : 'page';
    
    // Thanks to blinder for the fix!
    $numberposts = empty($json_api->query->count) ? -1 : $json_api->query->count;
    $wp_posts = get_posts(array(
      'post_type' => $post_type,
      'post_parent' => 0,
      'order' => 'ASC',
      'orderby' => 'menu_order',
      'numberposts' => $numberposts
    ));
    foreach ($wp_posts as $wp_post) {
      $pages[] = new JSON_API_Post($wp_post);
    }
    foreach ($pages as $page) {
      $json_api->introspector->attach_child_posts($page);
    }
    return array(
      'pages' => $pages
    );
  }
  
  public function get_nonce() {
    global $json_api;
    extract($json_api->query->get(array('controller', 'method')));
    if ($controller && $method) {
      $controller = strtolower($controller);
      if (!in_array($controller, $json_api->get_controllers())) {
        $json_api->error("Unknown controller '$controller'.");
      }
      require_once $json_api->controller_path($controller);
      if (!method_exists($json_api->controller_class($controller), $method)) {
        $json_api->error("Unknown method '$method'.");
      }
      $nonce_id = $json_api->get_nonce_id($controller, $method);
      return array(
        'controller' => $controller,
        'method' => $method,
        'nonce' => wp_create_nonce($nonce_id)
      );
    } else {
      $json_api->error("Include 'controller' and 'method' vars in your request.");
    }
  }
  
  protected function get_object_posts($object, $id_var, $slug_var) {
    global $json_api;
    $object_id = "{$type}_id";
    $object_slug = "{$type}_slug";
    extract($json_api->query->get(array('id', 'slug', $object_id, $object_slug)));
    if ($id || $$object_id) {
      if (!$id) {
        $id = $$object_id;
      }
      $posts = $json_api->introspector->get_posts(array(
        $id_var => $id
      ));
    } else if ($slug || $$object_slug) {
      if (!$slug) {
        $slug = $$object_slug;
      }
      $posts = $json_api->introspector->get_posts(array(
        $slug_var => $slug
      ));
    } else {
      $json_api->error("No $type specified. Include 'id' or 'slug' var in your request.");
    }
    return $posts;
  }
  
  protected function posts_result($posts) {
    global $wp_query;
    return array(
      'count' => count($posts),
      'count_total' => (int) $wp_query->found_posts,
      'pages' => $wp_query->max_num_pages,
      'posts' => $posts
    );
  }
  
  protected function posts_object_result($posts, $object) {
    global $wp_query;
    // Convert something like "JSON_API_Category" into "category"
    $object_key = strtolower(substr(get_class($object), 9));
    return array(
      'count' => count($posts),
      'pages' => (int) $wp_query->max_num_pages,
      $object_key => $object,
      'posts' => $posts
    );
  }
	public function _requestStatus($code) {
		$status = array(
			1001 => 'Login Successfully',
			1004 => 'SignUp Successfully',
			1005 => 'Please Try Again',
			1006 => "Email doesn't exists",
			1007 => 'Something went wrong. Please try again',
			1009 => 'Email already exists',
			1010 => 'Password has been changed successfully. Please check your email for new password.',
			1011 => 'Mail Sent Successfully',
			1012 => 'Username already exists',
			1013 => 'Terms Conditions get success',
			7002 => 'Please fill all required fields',
			7006 => 'You are not registered with this email id.',
			1801 => 'Sender id must not be blank.',
			1802 => 'Receiver id must not be blank.',
			1803 => 'Please add your message.',
			1804 => 'Message sent successfully.',
			1805 => 'No chats.',
			1806 => 'Chats fetched successfully.',
			2001 => 'Email Required',
			2002 => 'Password Required',
			2003 => 'Video Must not be blank',
			2004 => 'File Format Not Suppoted',
			2005 => 'Uploaded Successfully',
			2006 => 'Feed Get Successfully',
			2007 => 'No Feed Found',
			2008 => 'User id does not be blank',
			2009 => 'You have dislike Successfully',
			2010 => 'You have Like Successfully',
			2011 => "User Id doesn't exist.",
			2012 => "User info get Successfully.",
			2013 => "Comment Added Successfully",
			2014 => "My Feed Fetch Successfully",
			2015 => "Name Cannot be blank",
			2016 => "video does not be blank",
			2017 => "Comment Fatch Successfully",
			2018 => "No Comment",
			2019 => "Follower can not be blank",
			2020 => 'You have follow Successfully',
			2021 => 'You have unfollow Successfully',
			2022 => 'Follower Fetched Successfully',
			2023 => 'No Followers Found',
			2024 => 'Something Missing',
			2025 => 'Thumbnail uploaded Successfully',
			2026 => 'Following users fetch Successfully',
			2027 => 'Not Found',
			2028 => 'Video Deleted Successfully',
			2029 => "User Id doesn't exist",
			2030 => "Get users on Chat screen Successfully",
			2031 => "No user Found",
			2032 => 'User Logged out successfully',
			2033 => "Badge reduced to 0",
            2034 => "Can't Reduce Badge",
            2035 => "Updated Successfully",
			
		);
		return $status[$code];
	}
	
	
	
	
  	//Login
	public function login() { 
        global $json_api;
      	$json     = file_get_contents("php://input");
		$data     = json_decode($json);
		$username = $data->username;
		$password = $data->password;
		$devicetype = $data->devicetype;
        $devicetoken = $data->devicetoken;
		if(filter_var($username, FILTER_VALIDATE_EMAIL)) {
			$userData               = get_user_by('email', $username);
            $creds['user_login']    = $userData->user_login;
            $creds['user_password'] = $password;
            $creds['remember']      = true; 
            $user                   = wp_signon($creds, false);
		} else {
			$userData               = get_user_by('login', $username);
            $creds['user_login']    = $userData->user_login;
            $creds['user_password'] = $password;
            $creds['remember']      = true; 
            $user                   = wp_signon($creds, false);
		}
		if (is_wp_error($user)) { 
			$message = $this->_requestStatus("1005");
			return array(
				"RespCode" => "1005", 
				"Message" => $message,
			);
		} else {
			$user_id = $user->ID;
			$user_email = $user->user_email;
			$user_login = $user->user_login;
			$birthday = get_user_meta($user_id,'birthday',true);
			$user_image = get_user_meta( $user_id, 'cupp_upload_meta', true );
			update_user_meta($user_id,'devicetoken',$devicetoken);
			update_user_meta($user_id,'devicetype',$devicetype);
			$user_data = array("id" => $user_id,"user_email" => $user_email,"username" => $user_login,"profile_image" => $user_image,"birthday" => $birthday);
			
			//wp_new_user_notification( $user_id );
			$message = $this->_requestStatus("1001");
			return array(
				"RespCode" => "1001",
				"success" => "true", 
				"Message" => $message,
				"user_data" => $user_data,
			);
		}
    }
	//Fb Login
	 public function Fblogin() { 
        global $json_api;
      	$json     = file_get_contents("php://input");
		$data     = json_decode($json);
		$email    = $data->email;
		$fbid    = $data->fbid;
		$picture    = $data->picture;
		$name    = $data->username;
		$devicetype = $data->devicetype;
        $devicetoken = $data->devicetoken;
		$password = 'demo';
		// $rand = rand(11111,999999);
		// $username = $ar[0].$rand;
		if($name == ''){
			 $message = $this->_requestStatus("2015");
            return array(
                "RespCode" => "2015",
                "success" => "false",
                "Message" => $message
            );
			
		}
		if($picture == ''){ 
			$picture = plugin_dir_url( __FILE__ ).'dummy-profile-pic.png';
			update_user_meta( $user_id, 'cupp_upload_meta', $picture );
		}
		$user =  get_user_by('email', $email);
		$user_ID = $user->ID;
		 update_user_meta($user_ID, 'social_media_id', $fbid );
		update_user_meta($user_ID,'devicetoken',$devicetoken);
		update_user_meta($user_ID,'devicetype',$devicetype);
		if($user_ID !=''){
			 $picture = get_user_meta($user_ID, 'cupp_upload_meta', $picture );
			 $email = $user->user_email;
			 //update_user_meta($user_ID, 'first_name',$name); 
			 $fname = get_user_meta($user_ID, 'first_name',true);
			 update_user_meta($user_ID, 'social_media_id', $fbid );		
		}else{
		$rand_name	= rand(5, 15);
		$user_ID = wp_create_user( $name.$rand_name, $password, $email);
			update_user_meta( $user_ID, 'first_name', $name );
		    update_user_meta($user_ID, 'cupp_upload_meta', $picture );
			$picture = get_user_meta($user_ID, 'cupp_upload_meta', true);
			$fname = get_user_meta($user_ID, 'first_name',true);
			update_user_meta($user_ID, 'social_media_id', $fbid );
		}
		$message = $this->_requestStatus("1001");
		return array(
                    "RespCode" => "1001", //Login Successfully
                    "success" => "true", //Login Successfully
                    "Message" => $message,
                    "userdata"=>array("username" => $fname,
                    "user_email" => $email,
                    "id" => $user_ID,
                    "profile_image" => $picture,
                    "fullname"=> $fname,
                    )
                );	
		
    }
    //Signup
    public function signup() {
        global $json_api;
        
        $json           = file_get_contents("php://input");
        $data           = json_decode($json);
		//print_r($data);die();
        $username       = $data->username;
        $email          = $data->email;
        $password       = $data->password;
        $birthday	    = $data->birthday;
        $user_image     = $data->user_image;
       
        if ($email == "") {
            $message = $this->_requestStatus("2001");
            return array(
                "RespCode" => "2001",
                "success" => "false",
                "Message" => $message
            );
        } else if ($password == "") {
            $message = $this->_requestStatus("2002");
            return array(
                "RespCode" => "2002",
                "success" => "false",
                "Message" => $message
            );
        }
        $user_id = username_exists($username);
		if ($user_id){
			$message = $this->_requestStatus("1012");
            return array(
                "RespCode" => "1012",
                "success" => "false",
                "Message" => $message
            );
		}
        if (email_exists($email) == false) {
			$user_id = wp_create_user( $username, $password, $email);
			//~ wp_update_user( array('ID'=>$user_id,'role'=>$user_role) );
			//$user_id->set_role($user_role);
			//~ update_user_meta( $user_id, 'user_role', $user_role );
			update_user_meta( $user_id, 'birthday', $birthday );
			update_user_meta( $user_id, 'first_name', $username );

			$wp_upload_dir = wp_upload_dir();
			if($user_image != ''){
			    $filename='image_'.rand().'.jpg';
				file_put_contents( $wp_upload_dir['path']."/" . $filename, base64_decode($user_image) );
				$image_path = $wp_upload_dir['path']."/" . $filename;
				
				$server_path = $_SERVER["DOCUMENT_ROOT"]."/sngbrd";
				
				$site_url = home_url();
				$image_path = str_replace($server_path,$site_url,$image_path);
				$user_image = $image_path;
				$attachment = array(
									'guid' => $wp_upload_dir['url'] . '/' . $filename, 
									'post_mime_type' => 'image/jpg',
									'post_title' => preg_replace( '/\.[^.]+$/', '', $filename ),
									'post_content' => '',
									'post_status' => 'inherit'
								);
					
				require_once( ABSPATH . 'wp-admin/includes/image.php' );
				$attach_id = '';
				$attach_id = wp_insert_attachment( $attachment, $wp_upload_dir['path'].'/'.$filename );  
				$attach_data = wp_generate_attachment_metadata( $attach_id,  $wp_upload_dir['path'].'/'.$filename ); 
				wp_update_attachment_metadata( $attach_id, $attach_data ); 
				$att_url = wp_get_attachment_url( $attach_id );
				update_user_meta( $user_id, 'cupp_upload_meta', $att_url );
				$cupp_upload_edit_meta = home_url()."/wp-admin/post.php?post=".$attach_id."&action=edit&image-editor";
				$cupp_upload_edit_meta = str_replace(home_url(),"",$cupp_upload_edit_meta);
				update_user_meta( $user_id, 'cupp_upload_edit_meta', $cupp_upload_edit_meta );
				update_user_meta( $user_id, 'cupp_meta', '' );
			}else{
				$user_image = plugin_dir_url( __FILE__ ).'dummy-profile-pic.png';
				update_user_meta( $user_id, 'cupp_upload_meta', $user_image );
			}
			
			if ($user_id == "") {
				$message = $this->_requestStatus("1007");
				return array(
					"RespCode" => "1007",
					"success" => "false", 
					"Message" => $message
				);
			} else { 
				$user_name = get_user_meta($user_id, 'first_name', true);
				$user_image = get_user_meta($user_id, 'cupp_upload_meta', true);
				if($user_name == ''){
					$user_name='';
				}
				$user_data = array("id" => $user_id,
									"user_email" => $email,
									"username" => $username,
									"profile_image" => $user_image,
									"birthday" => $birthday,
								);
				//wp_new_user_notification( $user_id );
				$message = $this->_requestStatus("1004");
				return array(
					"RespCode" => "1004",
					"success" => "true", 
					"Message" => $message,
					"user_data" => $user_data,
				);
			}
        } else {
			$message = $this->_requestStatus("1009");
			return array(
				"RespCode" => "1009",
				"success" => "false",
				"Message" => $message,
			);
        }
    }
		public function logout(){
		global $json_api;
		$json     = file_get_contents("php://input");
		$data     = json_decode($json);
		$user_id       = $data->user_id;
		update_user_meta($user_id, 'devicetoken', '');
		update_user_meta($user_id, 'devicetype', '');
		update_user_meta($user_id,'online_status','0');
	   
		$message = $this->_requestStatus("2032");
		return array(
			 "RespCode" => "2032",
			 "Message" => $message
		);
	}	

	
	//Forgot passowrd
	public function forgotpassword() {
		global $json_api, $wpdb;
		$json = file_get_contents("php://input");
		$data = json_decode($json);
		$user_email = $data->email;
	
		if($user_email == ''){
			$message = $this->_requestStatus("7002");
			return array(
				"RespCode" => "7002",
				"Message" => $message,
			);
		}
		if (email_exists($user_email)) {
			$user = get_user_by('email', $user_email);
			$username = get_user_meta( $user->ID, 'first_name', true);
			$generatedpassword = wp_generate_password();
			$updatePasswordQuery = $wpdb->update($wpdb->users, array(
					'user_pass' => md5($generatedpassword)
				), array(
					'user_email' => $user_email
				));
			
			$message .= __('Hi, ', 'simplr-reg') . "\r\n\r\n";
			$message .= __('Password has been reset for the following account:', 'simplr-reg') . "\r\n\r\n";
			//$message .= network_site_url() . "\r\n\r\n";
			$message .= sprintf(__('Username: %s', 'simplr-reg'), $user->user_login) . "\r\n\r\n";
			$message .= sprintf(__('Email: %s', 'simplr-reg'), $user_email) . "\r\n\r\n";
			$message .= __('Following is new generated password:', 'simplr-reg') . "\r\n\r\n";
			$message .= $generatedpassword . "\r\n\r\n";
			$message .= __('Regards,', 'simplr-reg') . "\r\n\r\n";
			
			if (is_multisite())
				$blogname = $GLOBALS['current_site']->site_name; 
			else
			// The blogname option is escaped with esc_html on the way into the database in sanitize_option
				
			// we want to reverse this for the plain text arena of emails.
			$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
			
			$title = sprintf(__('[%s] Password Reset', 'simplr-reg'), $blogname);
			
			$title   = apply_filters('retrieve_password_title', $title);
			$message = apply_filters('retrieve_password_message', $message, $key);
			
			if (isset($simplr_options->default_email)) {
				$from = $simplr_options->default_email;
			} else {
				$from = get_option('admin_email');
			}
			$headers = "From: " . $blogname . " <" . $from . "> \r\n";
			
			if($updatePasswordQuery){
				$returnvalue = "Password Updated".$generatedpassword;
				if (wp_mail($user_email, $title, $message, $headers)) {
					$message = $this->_requestStatus("1010");
					return array(
						"RespCode" => "1010", //Mail Sent Successfully
						"Message" => $message
					);
				} else {
					$message = $this->_requestStatus("1005");
					return array(
						"RespCode" => "1005", //Please try again
						"Message" => $message
					);
				} 
			} else {
				$message = $this->_requestStatus("1005");
				return array(
					"RespCode" => "1005", //Please try again
					"Message" => $message
				);
			}
				
		} else {
			$message = $this->_requestStatus("1006");
			return array(
				"RespCode" => "1006", //*Email doesn't exists
				"Message" => $message
			);
		}
	}
	public function termsconditions() {
		global $json_api, $wpdb;
		$json = file_get_contents("php://input");
		$data = json_decode($json);
		$prod_id    = 9;
		$post_9 = get_post($prod_id); 
		$prod_data = $post_9->post_content;
		$shortcode_tags = array('vc_column_text','VC_CUSTOM_HEADING', 'vc_column');
		$values = array_values( $shortcode_tags );
		$exclude_codes  = implode( '|', $values );

		// strip all shortcodes except $exclude_codes and keep all content
		$the_content = preg_replace( "~(?:\[/?)(?!(?:$exclude_codes))[^/\]]+/?\]~s", '', $prod_data );
		$message = $this->_requestStatus("1013");
		return array(
			"RespCode" => "1013",
			"Message" => $message,
			"TermsConditions" => $the_content
		);
	}
	Public function get_post_for_feed(){
		global $json_api, $wpdb;
		$json = file_get_contents("php://input");
		$data = json_decode($json);
		$current_login_userid = $data->user_id;
		if($current_login_userid == ''){
			$message = $this->_requestStatus("2008");
			return array(
				"RespCode" => "2008",
				"Message" => $message,
			);
		}
		 $args = array(
				'post_type' => 'attachment',
			    'meta_query' => array(
				   array(
					   'key' => 'video_url_withdate_time',
					   'value' => '',
					   'compare' => '!=',
				   )
			   )
			);
			$query = get_posts($args);
			//print_r($query);die();
				
               $i=0;
			   $feed_data = array();
			    
                foreach ($query as $post_Data)
                {
				    $Author_id = $post_Data->post_author;
					//For getting user likes of particular post
					$get_data = $wpdb->get_results("select * from `sng_like_unlike` where `user_id` = '".$current_login_userid."' AND `post_id` = '".$post_Data->ID."'");
					$current_login_user_like = 0;
					foreach($get_data as $user_like){
						$current_login_user_like+= $user_like->like_unlike;
					}
					//For getting user likes of particular post
					//For getting total Posts likes of particular post
					$get_data_post = $wpdb->get_results("select * from `sng_like_unlike` where `post_id` = '".$post_Data->ID."'");
					
					$all_user_posts_like = 0;
					foreach($get_data_post as $post_like){
						$all_user_posts_like+= $post_like->like_unlike;
					}
					
					//For getting total Posts likes of particular post
					
					//GEt current users follow or not to post video
					
					$get_data_for_follow = $wpdb->get_results("select * from `sng_follow_unfollow` where `user_id` = '".	$current_login_userid."'");
					$dd ='';
					foreach($get_data_for_follow as $follow_Data){
						if($Author_id == $follow_Data->follow_unfollow_user_id){
							$dd = 	$follow_Data->follow_unfollow;
						}
					//$dd = 	$follow_Data->follow_unfollow;
						
					}
					if(empty($dd)){
						$dd = "0";
					}
					
					//die();
					
					
					
					//Get current users follow or not to post video
					//date_default_timezone_set('Asia/Kolkata');
					$datat = get_post_meta( $post_Data->ID, 'video_url_withdate_time', true);
					$thumb_url = get_post_meta( $post_Data->ID, 'video_thumb_url', true);
					
					$adddate = $datat['datetime'];
					$strdate = strtotime($adddate);
					$chdate = strtotime("+7 day", $strdate);
					$after_seven_days_date = date('Y/m/d h:i:s A', $chdate);
					$current_Date = date('Y/m/d h:i:s A');
					$date1Timestamp = strtotime($current_Date);
					$date2Timestamp = strtotime($after_seven_days_date);
					$difference = $date2Timestamp - $date1Timestamp;
					$days = floor($difference / (60*60*24) );
					
					$date1 = $current_Date;
					$date2 = $after_seven_days_date;
					$timestamp1 = strtotime($date1);
					$timestamp2 = strtotime($date2);
					$hour = abs($timestamp2 - $timestamp1)/(60*60);
					$seconds = $hour * 3600;

					$init = $seconds;
					$hours = floor($init / 3600);
					$minutes = floor(($init / 60) % 60);
					$seconds = $init % 60;
							
				$feed_data[$i]['leftdays'] = $days;
				$feed_data[$i]['hours'] = $hours;
				$feed_data[$i]['minute'] = $minutes;
				$feed_data[$i]['url'] = $datat['url'];
				$feed_data[$i]['thumb_url'] = $thumb_url;
				$feed_data[$i]['author_name'] = get_user_meta($Author_id, 'first_name' ,true  );		
				$feed_data[$i]['author_id'] = $post_Data->post_author;		
				$feed_data[$i]['post_id'] = $post_Data->ID;		
				$feed_data[$i]['likes'] = $current_login_user_like;		
				$feed_data[$i]['total_likes'] = $all_user_posts_like;		
				$feed_data[$i]['follow'] = $dd;		
				$feed_data[$i]['login_user_id'] = $current_login_userid;		
				$i++;
                }
			if(!empty($feed_data)){
		 $message = $this->_requestStatus("2006");
            return array(
                "RespCode" => "2006",
                "success" => "true",
                "Message" => $message,
                "detail" => $feed_data
            );
		}else{
			$message = $this->_requestStatus("2007");
            return array(
                "RespCode" => "2007",
                "success" => "false",
                "Message" => $message
               );
			
		}
	}
	// Public function Like_post_demo(){
		// global $json_api, $wpdb;
		// $json = file_get_contents("php://input");
		// $data = json_decode($json);
		// $user_id = $data->user_id;
		// $author_id = $data->author_id;//Post id those current login user like
		// $like_post_id = $data->like_post_id;//user id those those have post 
		// $like_unlike = $data->like_unlike;//0 for unlike and 1 for like
		
		// if($user_id == ''){
			// $message = $this->_requestStatus("2008");
			// return array(
				// "RespCode" => "2008",
				// "Message" => $message,
			// );
		// }
		// if($like_unlike == '1'){
			// $user_Data = get_user_meta($user_id, 'user_likes_post',true);
			// $post_Data = get_post_meta($like_post_id, 'posts_like_by_users',true);
			// if(empty($user_Data && $post_Data)){
				// $insert_post_data = array(
								// 'post_id' =>$like_post_id,
								// 'like_unlike' =>$like_unlike
								// );
				// update_post_meta($like_post_id, 'posts_like_by_users', $insert_post_data);
								
				// update_user_meta($user_id, 'user_likes_post', $insert_post_data);
				
			// }else{
				// foreach($user_Data as $key => $value){
					  // $user_Data[$key]['like_unlike'] = $like_unlike;
				    // }
					// $updated_value = array(
									// 'post_id' => $like_post_id,		
									// 'like_unlike' => $user_Data[$key]['like_unlike']
									// );
					// update_user_meta($user_id, 'user_likes_post', $updated_value);
					// update_post_meta($like_post_id, 'posts_like_by_users', $updated_value);
				// }
				// $message = $this->_requestStatus("2010");
					// return array(
						// "RespCode" => "2010",
						// "Message" => $message,
					// );
					
			// }else{
				// $user_Data = get_user_meta($user_id, 'user_likes_post',true);
			    // $post_Data = get_post_meta($like_post_id, 'posts_like_by_users',true);
			// if(empty($user_Data) && empty($post_Data)){
				// $insert_post_data = array(
								// 'post_id' =>$like_post_id,
								// 'like_unlike' =>$like_unlike
								// );
				// update_post_meta($like_post_id, 'posts_like_by_users', $insert_post_data);
								
				// update_user_meta($user_id, 'user_likes_post', $insert_post_data);
			// }else{
				   // foreach($user_Data as $key => $value){
					  // $user_Data[$key]['like_unlike'] = $like_unlike;
				    // }
					// $updated_value = array(
									// 'post_id' => $like_post_id,		
									// 'like_unlike' => $user_Data[$key]['like_unlike']
									// );
					// update_user_meta($user_id, 'user_likes_post', $updated_value);
					// update_post_meta($like_post_id, 'posts_like_by_users', $updated_value);
				// }
				// $message = $this->_requestStatus("2009");
					// return array(
						// "RespCode" => "2009",
						// "Message" => $message,
					// );
					
			
			
		// }
	    
	// }
	
	Public function getProfile() { 
		global $json_api, $wpdb;
		$json     = file_get_contents("php://input");
		$data     = json_decode($json);
		$user_id    = $data->user_id;
		
		if ($user_id == "") {
			$message = $this->_requestStatus("2008");
			return array(
				"RespCode" => "2008", //User Id must not be blank.
				"Message" => $message
			);
		}

		$first_name = get_user_meta($user_id,'first_name',true);
		$birthday = get_user_meta($user_id,'birthday',true);
		$user_image = get_user_meta($user_id,'cupp_upload_meta',true);
		if($user_image === NULL || $user_image == ''){ $user_image = plugin_dir_url( __FILE__ ).'dummy-profile-pic.png'; }
		
		$userData = get_userdata( $user_id );
		
		$userEmail = $userData->data->user_email;
			
		
		$count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $wpdb->users WHERE ID = %d", $user_id));
		if($count != 1){ 
			$message = $this->_requestStatus("2011");
			return array(
				"RespCode" => "2011", //User Id doesn't exist.
				"Message" => $message
			);
		}
		$user_data = array("id" => $user_id,
									"user_email" => $userEmail,
									"username" => $first_name,
									"profile_image" => $user_image,
									"birthday" => $birthday,
								);
		
		$message = $this->_requestStatus("2012");
				return array(
					"RespCode" => "2012",
					"success" => "true", 
					"Message" => $message,
					"user_data" => $user_data,
				);
	}
   //Api for upload thumbanail of viedo
   public function upload_thumb(){
		global $json_api, $wpdb;
		$json     = file_get_contents("php://input");
		$data     = json_decode($json);
		$sendattach_id  = $data->attach_id;
		$video_thumb  = $data->video_thumb;
		
		if ($sendattach_id == "") {
			$message = $this->_requestStatus("2024");
			return array(
				"RespCode" => "2024", //User Id must not be blank.
				"Message" => $message
			);
		}
		$wp_upload_dir = wp_upload_dir();
		if($video_thumb != ''){
			    $filename='image_'.rand().'.jpg';
				file_put_contents( $wp_upload_dir['path']."/" . $filename, base64_decode($video_thumb) );
				$image_path = $wp_upload_dir['path']."/" . $filename;
				
				$server_path = $_SERVER["DOCUMENT_ROOT"]."/sngbrd";
				
				$site_url = home_url();
				$image_path = str_replace($server_path,$site_url,$image_path);
				$video_thumb = $image_path;
				$attachment = array(
									'guid' => $wp_upload_dir['url'] . '/' . $filename, 
									'post_mime_type' => 'image/jpg',
									'post_title' => preg_replace( '/\.[^.]+$/', '', $filename ),
									'post_content' => '',
									'post_status' => 'inherit'
								);
					
				require_once( ABSPATH . 'wp-admin/includes/image.php' );
				$attach_id = '';
				$attach_id = wp_insert_attachment( $attachment, $wp_upload_dir['path'].'/'.$filename );  
				$attach_data = wp_generate_attachment_metadata( $attach_id,  $wp_upload_dir['path'].'/'.$filename ); 
				wp_update_attachment_metadata( $attach_id, $attach_data ); 
				$att_url = wp_get_attachment_url( $attach_id );
				update_post_meta($sendattach_id, 'video_thumb_url', $att_url);
				$thumb_url = get_post_meta($sendattach_id, 'video_thumb_url', true);
				$message = $this->_requestStatus("2025");
				return array(
					"RespCode" => "2025", 
					"Message" => $message,
					"thumb_url"=>$thumb_url
				);
				
			}
		
   }

   //Api for upload thumbanail of viedo
	public function comment_on_video(){
		global $json_api, $wpdb;
		$json     = file_get_contents("php://input");
		$data     = json_decode($json);
		$post_id  = $data->post_id;
		$comment_msg  = $data->comment_msg;
		$user_id = $data->user_id;
		
		if ($user_id == "") {
			$message = $this->_requestStatus("2008");
			return array(
				"RespCode" => "2008", //User Id must not be blank.
				"Message" => $message
			);
		}
		
		if ($post_id == "") {
			$message = $this->_requestStatus("2016");
			return array(
				"RespCode" => "2016", //User Id must not be blank.
				"Message" => $message
			);
		}
		$author_id = get_post_field('post_author', $post_id);
		$author_name = get_user_meta($author_id,'first_name',true);
		$userData = get_userdata( $author_id );
		$author_userEmail = $userData->data->user_email;
		$time = current_time('mysql');
		$data = array(
				'comment_post_ID' => $post_id,
				'comment_author' => $author_name,
				//'comment_author_email' => $author_userEmail,
				'comment_content' => $comment_msg,
				'user_id' => $user_id,
				'comment_date' => $time,
				'comment_approved' => 1,
			);
		
		wp_insert_comment($data);
		$message = $this->_requestStatus("2013");
			return array(
				"RespCode" => "2013", //User Id must not be blank.
				"Message" => $message
			);	
	}	
	

	Public function Like_post(){
		global $json_api, $wpdb;
		$json = file_get_contents("php://input");
		$data = json_decode($json);
		$user_id = $data->user_id;
		//$author_id = $data->author_id;//Post id those current login user like
		$like_post_id = $data->like_post_id;//user id those those have post 
		$like_unlike = $data->like_unlike;//0 for unlike and 1 for like	
		if ($user_id == "") {
			$message = $this->_requestStatus("2008");
			return array(
				"RespCode" => "2008", //User Id must not be blank.
				"Message" => $message
			);
		}
			global $wpdb;
			$get_data = $wpdb->get_row("select * from `sng_like_unlike` where `user_id` = '".$user_id."' AND `post_id` = '".$like_post_id."'");
			
			if(empty($get_data)){
				$wpdb->insert( $wpdb->prefix . 'like_unlike', 
							array( 
								'user_id'     	 => $user_id,
								'post_id'  	     => $like_post_id,
								'like_unlike'	 => '1',
							) 
						);
					$video_author_id = get_post_field( 'post_author', $like_post_id );	
					$authordevicetoken = get_user_meta($video_author_id,'devicetoken', true);
				    $authordevicetype = get_user_meta($video_author_id,'devicetype', true);
					$badge = get_user_meta($video_author_id,'badge',true);
					
					$login_user_name = get_user_meta( $user_id, 'first_name',true);
					$dataf = array();
					$dataf['login_user_image'] = get_user_meta( $user_id, 'cupp_upload_meta',true);
			        $dataf['login_user_name'] = get_user_meta($user_id, 'first_name', true);
					$message = $video_author_id;
			
					if($badge == 0 || $badge == ''){
						//echo "Else IF Part";
						update_user_meta($video_author_id,'badge',1);
						//Create the payload body
						$body['aps'] = array(
							'alert' => 'Your video has been Liked by '.trim($login_user_name).'.',
							'sound' => 'default',
							'badge' => 1,
							'type' => 'Video Like',
							'Data' => $message,
						);
						
										
					}else{ 
						$badge = $badge +1;
						$body['aps'] = array(
							'alert' => 'Your video has been Liked by '.trim($login_user_name).'.',
							'sound' => 'default',
							'badge' => $badge,
							'type' => 'Video Like',
							'Data' => $message,
						);
						update_user_meta($problem_author_id, 'badge', $badge);
					}
					$result = $this->send_ios_notification($dataf,$authordevicetoken,$body);	
						
				$message = $this->_requestStatus("2010");
				return array(
					"RespCode" => "2010", 
					"Message" => $message
				);	
			}
			if($like_unlike == '1'){
				global $wpdb;
				$sql_update = "UPDATE `sng_like_unlike` SET `like_unlike`= '1' WHERE  `user_id` = '".$user_id."' AND `post_id` = '".$like_post_id."'";
				$wpdb->query($sql_update);
				$video_author_id = get_post_field( 'post_author', $like_post_id );	
					$authordevicetoken = get_user_meta($video_author_id,'devicetoken', true);
				    $authordevicetype = get_user_meta($video_author_id,'devicetype', true);
					$badge = get_user_meta($video_author_id,'badge',true);
					
					$login_user_name = get_user_meta( $user_id, 'first_name',true);
					$dataf = array();
					$dataf['login_user_image'] = get_user_meta( $user_id, 'cupp_upload_meta',true);
			        $dataf['login_user_name'] = get_user_meta($user_id, 'first_name', true);
					$message = $video_author_id;
			
					if($badge == 0 || $badge == ''){
						//echo "Else IF Part";
						update_user_meta($video_author_id,'badge',1);
						//Create the payload body
						$body['aps'] = array(
							'alert' => 'Your video has been Liked by '.trim($login_user_name).'.',
							'sound' => 'default',
							'badge' => 1,
							'type' => 'Video Like',
							'Data' => $message,
						);
						
										
					}else{ 
						$badge = $badge +1;
						$body['aps'] = array(
							'alert' => 'Your video has been Liked by '.trim($login_user_name).'.',
							'sound' => 'default',
							'badge' => $badge,
							'type' => 'Video Like',
							'Data' => $message,
						);
						update_user_meta($problem_author_id, 'badge', $badge);
					}
					$result = $this->send_ios_notification($dataf,$authordevicetoken,$body);
				
				
				
				
				
				$message = $this->_requestStatus("2010");
				return array(
					"RespCode" => "2010", 
					"Message" => $message
				);	
			}else{
				global $wpdb;
				$sql_update = "UPDATE `sng_like_unlike` SET `like_unlike`= '0' WHERE  `user_id` = '".$user_id."' AND `post_id` = '".$like_post_id."'";
				$wpdb->query($sql_update);
				$message = $this->_requestStatus("2009");
				return array(
					"RespCode" => "2009", 
					"Message" => $message
				);	
			}
	}
	
	public  function myfeed(){
		global $json_api, $wpdb;
		$json = file_get_contents("php://input");
		$data = json_decode($json);
		$user_id = $data->user_id;
		if ($user_id == "") {
			$message = $this->_requestStatus("2008");
			return array(
				"RespCode" => "2008", //User Id must not be blank.
				"Message" => $message
			);
		}
		global $wpdb;

			$get_data = $wpdb->get_results("select * from `sng_like_unlike` where `user_id` = '".$user_id."' AND `like_unlike` = '1'");
		if(!empty($get_data)){
			
			$unlike_like = array();
			$i=0;
			foreach($get_data as $value){
				
				$datat = get_post_meta( $value->post_id, 'video_url_withdate_time', true);
				$post_author_id = get_post_field( 'post_author', $value->post_id );
				$get_like_Data = $wpdb->get_results("SELECT like_unlike FROM `sng_like_unlike` WHERE `like_unlike` = '1' AND `post_id` = '".$value->post_id."'");
				$total_like_sum = 0;
				foreach($get_like_Data as $ttl){
					$total_like_sum+= $ttl->like_unlike;
				}
				$get_data_for_follow = $wpdb->get_results("select * from `sng_follow_unfollow` where `user_id` = '".$user_id."'");
				$dd ='';
				foreach($get_data_for_follow as $follow_Data){
					if($post_author_id == $follow_Data->follow_unfollow_user_id){
							$dd = 	$follow_Data->follow_unfollow;
						}
			        }
					if(empty($dd)){
						$dd = "0";
					}
				
				
				$postt_id = (int)$value->post_id;
				
				 $unlike_like[$i]['like'] =$value->like_unlike;
				 $unlike_like[$i]['total_likes'] =$total_like_sum;
				 $unlike_like[$i]['post_id'] = $postt_id;
				 $unlike_like[$i]['thumb_url'] = get_post_meta( $value->post_id, 'video_thumb_url', true);
				 $unlike_like[$i]['url'] = $datat['url'];
				 $unlike_like[$i]['author_name'] = get_user_meta($post_author_id, 'first_name' ,true  );
				 $unlike_like[$i]['author_id'] = $post_author_id;
				 $unlike_like[$i]['follow'] = $dd;
				$i++;	
			}
			$message = $this->_requestStatus("2014");
					return array(
						"RespCode" => "2014", 
						"success" => "true", 
						"Message" => $message,
						"mydetails"=>$unlike_like
					);
		}else{
			$message = $this->_requestStatus("2007");
					return array(
						"RespCode" => "2007", 
						"success" => "true", 
						"Message" => $message,
					);
		}		
		
	}
	
	public  function GetComment(){
		global $json_api, $wpdb;
		$json = file_get_contents("php://input");
		$data = json_decode($json);
		$post_id = $data->post_id;
			
		if ($post_id == "") {
			$message = $this->_requestStatus("2016");
			return array(
				"RespCode" => "2016", 
				"Message" => $message
			);
		}
			global $wpdb;
			$comment = $wpdb->get_results("select * from `sng_comments` where `comment_post_ID` = '".$post_id."'");
			
			
			if(!empty($comment)){
				$post_comment = array();
				$i=0;
			foreach($comment as $comment_data){
			     $post_comment[$i]['post_author'] = $comment_data->comment_author;
			     $post_comment[$i]['comment_post_ID'] = $comment_data->comment_post_ID;
			     $post_comment[$i]['comment_content'] = $comment_data->comment_content;
			     $post_comment[$i]['comment_user_img'] = get_user_meta($comment_data->user_id,'cupp_upload_meta',true);
			     $post_comment[$i]['comment_date'] = date("d-m-Y", strtotime($comment_data->comment_date));
			$i++;	
			} 
			$message = $this->_requestStatus("2017");
			return array(
				"RespCode" => "2017", //User Id must not be blank.
				"Message" => $message,
				"comment" => $post_comment,
			);
				
				
			}else{
				$message = $this->_requestStatus("2018");
				return array(
					"RespCode" => "2018", //User Id must not be blank.
					"Message" => $message,
				);
			}
		
	}
	
	public  function follow_unfollow_users(){
		global $json_api, $wpdb;
		$json = file_get_contents("php://input");
		$data = json_decode($json);
		$user_id = $data->user_id;//current user login id
		$follow_unfollow_user_id = $data->follow_unfollow_user_id;//follow unfollow  user id
		$follow_unfollow = $data->follow_unfollow;//0 for unfollow and 1 fo follow
		
		if ($follow_unfollow_user_id == "") {
				$message = $this->_requestStatus("2019");
				return array(
					"RespCode" => "2019", 
					"Message" => $message
				);
			}
			if ($user_id == "") {
				$message = $this->_requestStatus("2008");
				return array(
					"RespCode" => "2008", //User Id must not be blank.
					"Message" => $message
				);
			}
		    global $wpdb;
			$data_follow = $wpdb->get_results("select * from `sng_follow_unfollow` where `follow_unfollow_user_id` = '".$follow_unfollow_user_id."' AND `user_id` = '".$user_id."'");
			
			if(empty($data_follow)){
				$wpdb->insert( $wpdb->prefix . 'follow_unfollow', 
							array( 
								'user_id'     	 => $user_id,
								'follow_unfollow_user_id' => $follow_unfollow_user_id,
								'follow_unfollow'	 => '1',
							) 
						);
					//For Notification	
					$follow_unfollow_devicetoken = get_user_meta($follow_unfollow_user_id,'devicetoken', true);
				    $follow_unfollow_devicetype = get_user_meta($follow_unfollow_user_id,'devicetype', true);
					$badge = get_user_meta($follow_unfollow_user_id,'badge',true);
					
					$login_user_name = get_user_meta( $user_id, 'first_name',true);
					$data_user = array();
					$data_user['login_user_image'] = get_user_meta( $user_id, 'cupp_upload_meta',true);
			        $data_user['login_user_name'] = get_user_meta($user_id, 'first_name', true);
					$message = $data_user;
			
					if($badge == 0 || $badge == ''){
						//echo "Else IF Part";
						update_user_meta($video_author_id,'badge',1);
						//Create the payload body
						$body['aps'] = array(
							'alert' => 'Your are followed by'.trim($login_user_name).'.',
							'sound' => 'default',
							'badge' => 1,
							'type' => 'follow user',
							'Data' => $message,
						);
						
										
					}else{ 
						$badge = $badge +1;
						$body['aps'] = array(
							'alert' => 'Your are followed by'.trim($login_user_name).'.',
							'sound' => 'default',
							'badge' => $badge,
							'type' => 'follow user',
							'Data' => $message,
						);
						update_user_meta($follow_unfollow_user_id, 'badge', $badge);
					}
					$result = $this->send_ios_notification($data_user,$follow_unfollow_devicetoken,$body);
					//For Notification
					$message = $this->_requestStatus("2020");
					return array(
						"RespCode" => "2020", 
						"Message" => $message
					);	
			}
			if($follow_unfollow == '1'){
				global $wpdb;
				$sql_update = "UPDATE `sng_follow_unfollow` SET `follow_unfollow`= '1' WHERE  `user_id` = '".$user_id."' AND `follow_unfollow_user_id` = '".$follow_unfollow_user_id."'";
				$wpdb->query($sql_update);
				//For Notification
				    $follow_unfollow_devicetoken = get_user_meta($follow_unfollow_user_id,'devicetoken', true);
				    $follow_unfollow_devicetype = get_user_meta($follow_unfollow_user_id,'devicetype', true);
					$badge = get_user_meta($follow_unfollow_user_id,'badge',true);
					
					$login_user_name = get_user_meta( $user_id, 'first_name',true);
					$data_user = array();
					$data_user['login_user_image'] = get_user_meta( $user_id, 'cupp_upload_meta',true);
			        $data_user['login_user_name'] = get_user_meta($user_id, 'first_name', true);
					$message = $data_user;
			
					if($badge == 0 || $badge == ''){
						//echo "Else IF Part";
						update_user_meta($video_author_id,'badge',1);
						//Create the payload body
						$body['aps'] = array(
							'alert' => 'Your are followed by'.trim($login_user_name).'.',
							'sound' => 'default',
							'badge' => 1,
							'type' => 'follow user',
							'Data' => $message,
						);
						
										
					}else{ 
						$badge = $badge +1;
						$body['aps'] = array(
							'alert' => 'Your are followed by'.trim($login_user_name).'.',
							'sound' => 'default',
							'badge' => $badge,
							'type' => 'follow user',
							'Data' => $message,
						);
						update_user_meta($follow_unfollow_user_id, 'badge', $badge);
					}
					$result = $this->send_ios_notification($data_user,$follow_unfollow_devicetoken,$body);
				//For Notification
					$message = $this->_requestStatus("2020");
					return array(
						"RespCode" => "2020", //User Id must not be blank.
						"Message" => $message
						);	
			}else{
				global $wpdb;
				$sql_update = "UPDATE `sng_follow_unfollow` SET `follow_unfollow`= '0' WHERE  `user_id` = '".$user_id."' AND `follow_unfollow_user_id` = '".$follow_unfollow_user_id."'";
				$wpdb->query($sql_update);
					$message = $this->_requestStatus("2021");
					return array(
						"RespCode" => "2021", 
						"Message" => $message
					);	
			}		
	}
	
	public function user_follower(){
		global $json_api, $wpdb;
		$json = file_get_contents("php://input");
		$data = json_decode($json);
		$user_id = $data->user_id;
		if ($user_id == "") {
				$message = $this->_requestStatus("2008");
				return array(
					"RespCode" => "2008", //User Id must not be blank.
					"Message" => $message
				);
			}
			global $wpdb;
			$follower_Data = $wpdb->get_results("select * from `sng_follow_unfollow` where `follow_unfollow` = '1' AND `follow_unfollow_user_id` = '".$user_id."'");
			
			if(!empty($follower_Data)){
				$get_folloer_Data = array();
				$i=0;
				foreach($follower_Data as $valfollower){
					
					$following_to_follow_current_user = $wpdb->get_results("select * from `sng_follow_unfollow` where `follow_unfollow` = '1' AND `user_id` = '".$valfollower->follow_unfollow_user_id."'");
					$users_follows = '';
					foreach($following_to_follow_current_user as $follow_user_data){
						$users_follows = $follow_user_data->follow_unfollow;
					}
					  if(empty($users_follows)){
						  $users_follows = '0';
					  }
					 
					 $follower_count =  $wpdb->get_results("SELECT count(`follow_unfollow`)as `follower_count_total` FROM `sng_follow_unfollow` WHERE `user_id` = '".$valfollower->user_id."' AND `follow_unfollow` = 1"); 
					
					if($follower_count[0]->follower_count_total <=10000){
						$followers_count = 0;
					}else{
						$followers_count = 1;
					}
					
					$get_folloer_Data[$i]['follower_name'] = get_user_meta($valfollower->user_id,'first_name',true);
					$get_folloer_Data[$i]['profile_pic'] = get_user_meta($valfollower->user_id,'cupp_upload_meta',true);
					$get_folloer_Data[$i]['follow_status'] = $users_follows;
					$get_folloer_Data[$i]['follower_user_id'] = $valfollower->user_id;
					$get_folloer_Data[$i]['followers_count'] = $followers_count;
				$i++;
				
				
				}
				$message = $this->_requestStatus("2022");
				return array(
					"RespCode" => "2022", 
					"Message" => $message,
					"follower_Data" => $get_folloer_Data
				);
			}else{
				$message = $this->_requestStatus("2023");
				return array(
					"RespCode" => "2023", 
					"Message" => $message,
				);
			}
	}
	public function user_following(){
		global $json_api, $wpdb;
		$json = file_get_contents("php://input");
		$data = json_decode($json);
		$user_id = $data->user_id;
		if ($user_id == "") {
				$message = $this->_requestStatus("2008");
				return array(
					"RespCode" => "2008", //User Id must not be blank.
					"Message" => $message
				);
			}
			global $wpdb;
			$following_Data = $wpdb->get_results("select * from `sng_follow_unfollow` where `follow_unfollow` = '1' AND `user_id` = '".$user_id."'");
			if(!empty($following_Data)){
			
			$get_Data = array();
			$i=0;
			foreach($following_Data as $get_following_data){
				//print_r($get_following_data);
				
				$following_to_follow_current_user = $wpdb->get_results("select * from `sng_follow_unfollow` where `follow_unfollow` = '1' AND `user_id` = '".$get_following_data->follow_unfollow_user_id."'");
					$users_follows = '';
					foreach($following_to_follow_current_user as $follow_user_data){
						$users_follows = $follow_user_data->follow_unfollow;
					}
					  if(empty($users_follows)){
						  $users_follows = '0';
					  }
				$follower_count =  $wpdb->get_results("SELECT count(`follow_unfollow`)as `follower_count_total` FROM `sng_follow_unfollow` WHERE `user_id` = '".$get_following_data->follow_unfollow_user_id."' AND `follow_unfollow` = 1"); 
					if($follower_count[0]->follower_count_total <=10000){
						$followers_count = 0;
					}else{
						$followers_count = 1;
					}  
					  
					  
					  
				
				$get_Data[$i]['following_user_id'] = $get_following_data->follow_unfollow_user_id;
				$get_Data[$i]['following_name'] = get_user_meta($get_following_data->follow_unfollow_user_id,'first_name',true);
				$get_Data[$i]['profile_pic'] = get_user_meta($get_following_data->follow_unfollow_user_id,'cupp_upload_meta',true);
				$get_Data[$i]['following_status'] = $users_follows;
				$get_Data[$i]['followers_count'] = $followers_count;
				$i++;
				
			}
			$message = $this->_requestStatus("2026");
				return array(
					"RespCode" => "2026", 
					"Message" => $message,
					"follower_Data" => $get_Data
				);
			}else{
				$message = $this->_requestStatus("2027");
				return array(
					"RespCode" => "2027", 
					"Message" => $message
				);
				
				
			}
		
	}	
	
	public function delete_posts_video(){
		global $json_api, $wpdb;
		$json = file_get_contents("php://input");
		$data = json_decode($json);
		$user_id = $data->user_id;
		$post_id = $data->post_id;
		if ($post_id == "") {
			$message = $this->_requestStatus("2016");
			return array(
				"RespCode" => "2016", 
				"Message" => $message
			);
		}
		if ($user_id == "") {
				$message = $this->_requestStatus("2008");
				return array(
					"RespCode" => "2008", //User Id must not be blank.
					"Message" => $message
				);
			}

			// if($user_id !='1'){
			$wpdb->query('DELETE  FROM `sng_like_unlike` WHERE `post_id` = "'.$post_id.'" AND `user_id` = "'.$user_id.'" ');
			
			$wpdb->query('DELETE  FROM `sng_posts` WHERE `post_author` = "'.$user_id.'" ');
			
			$wpdb->query('DELETE  FROM `sng_like_unlike WHERE `post_id` = "'.$post_id.'" ');
			
			//}
			
			//wp_delete_post($post_id);  
			
			$message = $this->_requestStatus("2028");
				return array(
					"RespCode" => "2028", //User Id must not be blank.
					"Message" => $message
				);
	}
	 //API to Update profile Info
	public function updateProfile() { 
		global $json_api, $wpdb;
		$json = file_get_contents("php://input");
		$data = json_decode($json);
		$user_id = $data->user_id;
		$fname = $data->username;
		$email = $data->email;
		$user_image = $data->user_image;
		
		if ($user_id == "") {
			$message = $this->_requestStatus("2008");
			return array(
				"RespCode" => "2008", //User Id must not be blank.
				"Message" => $message
			);
		} 
		
		$count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $wpdb->users WHERE ID = %d", $user_id));
		if($count != 1){ 
			$message = $this->_requestStatus("2029");
			return array(
				"RespCode" => "2029", //User Id doesn't exist.
				"Message" => $message
			);
		}

		if($fname != ''){
			$userData1 = get_userdata( $user_id ); 
			//print_r($userData1);die();
			if(strtolower($userData1->data->user_login) != strtolower($fname)){
				if(username_exists( $fname ) ){
					$message = $this->_requestStatus("1012");
					return array(
						"RespCode" => "1012",
						"Message" => $message
					);
				} else {
					$sql = $wpdb->prepare("UPDATE `sng_users` SET user_login=%s WHERE ID=%d", $fname, $user_id);
					$wpdb->query($sql);
				}
			}
		}
		
		if($email != ''){
			$userData12 = get_userdata( $user_id ); 
			//print_r($userData1);die();
			if(strtolower($userData12->data->user_email) != strtolower($email)){
				if(email_exists( $email ) ){
					$message = $this->_requestStatus("1009");
					return array(
						"RespCode" => "1009",
						"Message" => $message
					);
				} else {
					$args = array(
						'ID'         => $user_id,
						'user_email' => $email 
					);
					wp_update_user( $args );
				}
			}
		}
		
		//wp_update_user( array( 'ID' => $user_id, 'user_email' => $website ) );
		/*
				wp_update_user( array( 'ID' => $user_id, 'user_email' => $email ) );
			}
		}*/
		
		
		$wp_upload_dir = wp_upload_dir();
		if($user_image != ''){
			$filename='image_'.rand().'.jpg';
			file_put_contents( $wp_upload_dir['path']."/" . $filename, base64_decode($user_image) );
			$image_path = $wp_upload_dir['path']."/" . $filename;
			$server_path = $_SERVER["DOCUMENT_ROOT"]."/sngbrd";
			$site_url = home_url();
			$image_path = str_replace($server_path,$site_url,$image_path);
			$user_image = $image_path;
			
			$attachment = array(
				'guid' => $wp_upload_dir['url'] . '/' . $filename, 
				'post_mime_type' => 'image/jpg',
				'post_title' => preg_replace( '/\.[^.]+$/', '', $filename ),
				'post_content' => '',
				'post_status' => 'inherit'
			);
			
			require_once( ABSPATH . 'wp-admin/includes/image.php' );
			$attach_id = '';
			$attach_id = wp_insert_attachment( $attachment, $wp_upload_dir['path'].'/'.$filename );  
			
			$attach_data = wp_generate_attachment_metadata( $attach_id,  $wp_upload_dir['path'].'/'.$filename ); 
			wp_update_attachment_metadata( $attach_id, $attach_data ); 
			$att_url = wp_get_attachment_url( $attach_id );
			update_user_meta( $user_id, 'cupp_upload_meta', $att_url );
			$cupp_upload_edit_meta = home_url()."/wp-admin/post.php?post=".$attach_id."&action=edit&image-editor";
			$cupp_upload_edit_meta = str_replace(home_url(),"",$cupp_upload_edit_meta);
			update_user_meta( $user_id, 'cupp_upload_edit_meta', $cupp_upload_edit_meta );
			update_user_meta( $user_id, 'cupp_meta', '' );
		}
		else{
				//$user_profile_image = plugin_dir_url( __FILE__ ).'dummy-profile-pic.png';
				//update_user_meta( $user_id, 'cupp_upload_meta', $user_profile_image );
				
				$user_image = get_user_meta($user_id,'cupp_upload_meta',true);
				update_user_meta( $user_id, 'cupp_upload_meta', $user_image );
							
		}
		// fetching User data after update [START] 
		$userData = get_userdata( $user_id ); 
		$userEmail = $userData->data->user_email;
		
		
		 update_user_meta($user_id,'first_name',$fname);
		$fname = get_user_meta($user_id,'first_name',true);
		

		
		$user_image = get_user_meta($user_id,'cupp_upload_meta',true);
		if($user_image === NULL || $user_image == ''){ $user_image = plugin_dir_url( __FILE__ ).'dummy-profile-pic.png'; }

		
		$user_update_data = array(
								"id" => $user_id,
								"user_email" => $userEmail,
								"profile_image" => $user_image,
								"username" => $fname
							);
		//wp_new_user_notification( $user_id );2035
		$message = $this->_requestStatus("2010");
		return array(
			"RespCode" => "2010",
			"success" => "true", 
			"Message" => $message,
			"user_data" => $user_update_data,
		);
	}
	
	public function Get_users_onChat_screen() { 
		global $json_api, $wpdb;
		$json = file_get_contents("php://input");
		$data = json_decode($json);
		$user_id = $data->user_id;
			if ($user_id == "") {
			$message = $this->_requestStatus("2008");
			return array(
				"RespCode" => "2008", //User Id must not be blank.
				"Message" => $message
			);
		}
		
		$count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $wpdb->users WHERE ID = %d", $user_id));
		if($count != 1){ 
			$message = $this->_requestStatus("2029");
			return array(
				"RespCode" => "2029", //User Id doesn't exist.
				"Message" => $message
			);
		}
		
		global $wpdb;
		
			$select_users = $wpdb->get_results("SELECT * FROM `sng_posts` WHERE (`post_author` != '".$user_id."' AND `post_author` != '1') AND `post_mime_type` != 'image/jpg'");
				
				if(!empty($select_users)){
					$i=0;
					$user_data = array();
					foreach($select_users as $data_val){
						$user_data[$i]['user_id'] = $data_val->post_author;
						$user_data[$i]['proflie_pic'] = get_user_meta($data_val->post_author,'cupp_upload_meta',true);
						$user_data[$i]['name'] = get_user_meta($data_val->post_author,'first_name',true);
						$i++;	
					}
					
					$message = $this->_requestStatus("2030");
					return array(
						"RespCode" => "2030",
						"success" => "true", 
						"Message" => $message,
						"user_data" => $user_data,
						
					);
					
					}else{
						$message = $this->_requestStatus("2031");
							return array(
								"RespCode" => "2031",
								"success" => "true", 
								"Message" => $message
								
							);
					}
	}	
	
	
	
/*******************************Code for chat API [START]*******************************/
	
	//Code to send messages to one to one person
	public function send_message(){  
		global $json_api, $wpdb;
		$json = file_get_contents("php://input");
		$data = json_decode($json);
		//date_default_timezone_set('Asia/Kolkata');

		$sender_id = $data->sender_id;
		$sender_name = get_user_meta( $sender_id, 'first_name', true );
		$receiver_id = $data->receiver_id;
		// if($receiver_id == '0'){
			 // $receiver_id = 1;
			 // $receiver_id;
		// }
		
		
		//$receiver_id = '1';
		
		$msg = $data->message;
		$time = time();
		//echo date('h:i A',$time);die();

		$charset_collate = $wpdb->get_charset_collate();
		$table_name = $wpdb->prefix . 'sng_messages';

		$sql = "CREATE TABLE $table_name (
			id int(11) NOT NULL AUTO_INCREMENT,
			sender_id int(11) NOT NULL,
			receiver_id int(11) NOT NULL,
			messages text NOT NULL,
			time varchar(225) NOT NULL,
			UNIQUE KEY id (id)
		) $charset_collate;";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
		
		if($sender_id == ''){
			$message = $this->_requestStatus("1801");
			return array(
				"RespCode" => "1801",
				"Message" => $message,
			);
		}
		if($receiver_id == ''){
			$message = $this->_requestStatus("1802");
			return array(
				"RespCode" => "1802",
				"Message" => $message,
			);
		}
		if($msg == ''){
			$message = $this->_requestStatus("1803");
			return array(
				"RespCode" => "1803",
				"Message" => $message,
			);
		}
		$data = array(
			'sender_id' => $sender_id,
			'receiver_id' => $receiver_id,
			'messages' => $msg,
			'time' => $time
		);
		


		if($wpdb->insert($table_name, $data)){
			//Code to send chat notification [START]
			$devicetype = get_user_meta( (int)$receiver_id, 'devicetype', true);
			$devicetoken = get_user_meta( (int)$receiver_id, 'devicetoken', true);
			$badge = get_user_meta((int)$receiver_id,'badge',true);
			$datax = array(
				'message'   => $msg,
				'title'     => 'New message received',
				'vibrate'   => 1,
				'sound'     => 1,
				'type'		=> 'chat'
			);
			if($devicetoken != ''){
				if($devicetype != 'I'){
					if($badge == 0 || $badge == ''){
						$badge = 1;
					}else{ 
						$badge = $badge +1;
					}
					$datax['badge'] = $badge;
					$datax['sender_id'] = $sender_id;
					$datax['receiver_id'] = $receiver_id;
					update_user_meta((int)$receiver_id, 'badge', $badge);
					
					$message =array (
						'body'   => 'New Message Received',
						'title'     => 'New Message'
					);

					$fields = array(
						'to' =>  $devicetoken,
						'notification' => $message,
						'data' => $datax,
					);
					$result = $this->send_android_notification($fields);
				}else{
					
					$datax['sender_id'] = $sender_id;
					$datax['receiver_id'] = $receiver_id;
					$datax['sender_name'] = $sender_name;
					
					$datax['time'] = $time;
					$datax['format_date'] = date('d-m-Y',$time);
					$datax['format_time'] = date('h:i A',$time);
					$datax['message_id'] = $wpdb->insert_id;
					
					// Put your alert message here:
					$message = $datax;
					if($badge == 0 || $badge == ''){
						//echo "Else IF Part";
						update_user_meta((int)$receiver_id,'badge',1);
						//Create the payload body
						$body['aps'] = array(
							'alert' => 'You have a new message',
							'sound' => 'default',
							'badge' => 1,
							'type' => 'Chat',
							'Data' => $message
						);
					}else{ 
						$badge = $badge +1;
						$body['aps'] = array(
							'alert' => 'You have a new message',
							'sound' => 'default',
							'badge' => $badge,
							'type' => 'Chat',
							'Data' => $message
						);
						update_user_meta((int)$receiver_id, 'badge', $badge);
					}
					$result = $this->send_ios_notification($datax,$devicetoken,$body);
				}
			}
			//Code to send chat notification [END]
			
			$message = $this->_requestStatus("1804");
			return array(
				"RespCode" => "1804",
				"Message" => $message,
				"Details" => array(
					'id'=> $wpdb->insert_id,
					'sender_id' => $sender_id,
					'receiver_id' => $receiver_id,
					'messages' => $msg,
					'time' => $time,
					'format_date'=>date('d-m-Y',$time) ,
					'format_time' =>date('h:i A',$time)
				)
			);
		} else {
			$message = $this->_requestStatus("1007");
			return array(
				"RespCode" => "1007",
				"Message" => $message,
			);
		}
	}
	
	
	//Get user's all chats
	public function get_chat(){  
		global $json_api, $wpdb;
		$json = file_get_contents("php://input");
		$data = json_decode($json);
		$user_id = $data->user_id; 
		
		$chats = $wpdb->get_results("select * from `".$wpdb->prefix."sng_messages` where id in(select max(id) from (SELECT sender_id as cid,receiver_id as sid,id FROM `".$wpdb->prefix."sng_messages` where sender_id='".$user_id."' union SELECT receiver_id as cid,sender_id as sid,id FROM `".$wpdb->prefix."sng_messages` where receiver_id='".$user_id."') temp group by cid,sid)");

		$count = count($chats) / 2; 
		$j = 0; 
		if($chats){
			foreach($chats as $chat){ 
				//if($j >= $count) { $chats[$j]->shown = 0; }else{$chats[$j]->shown = 1; }
				if($user_id == $chat->sender_id){
					$_id = $chat->receiver_id;
				}elseif($user_id == $chat->receiver_id){
					$_id = $chat->sender_id;
				}
				
				$receiver_name = get_user_meta($_id,'first_name',true);
				$receiver_image = get_user_meta($_id,'cupp_upload_meta',true);
				/* Get Count of Followers*/
				 $follower_count =  $wpdb->get_results("SELECT count(`follow_unfollow`)as `follower_count_total` FROM `sng_follow_unfollow` WHERE `user_id` = '".$_id."' AND `follow_unfollow` = 1"); 
					
					if($follower_count[0]->follower_count_total <=10000){
						$followers_count = 0;
					}else{
						$followers_count = 1;
					}
				/* Get Count of Followers*/
				
				$chats[$j]->format_time = date('h:i A',$chat->time);
				$chats[$j]->format_date = date('d-m-Y',$chat->time);
				$chats[$j]->receiver_name = $receiver_name;
				$chats[$j]->receiver_image = $receiver_image;
				$chats[$j]->final_receiver = $_id;
				$chats[$j]->followers_count = $followers_count;
				$j++;
			}

			$message = $this->_requestStatus("1806");
			return array(
				"RespCode" => "1806",
				"Message" => $message,
				"chats" => $chats
			);
		}else{
			$message = $this->_requestStatus("1805");
			return array(
				"RespCode" => "1805",
				"Message" => $message,
			);
		}
	}
	
	//Get specific chat's all messages 
	public function get_chat_messages(){   
		global $json_api, $wpdb;
		$json = file_get_contents("php://input");
		$data = json_decode($json);
		
		$sender_id = $data->sender_id;
		$receiver_id = $data->receiver_id;
		// if($receiver_id == '0'){
			 // $receiver_id = 1;
			 // $receiver_id;
		// }
		
		$sent_msgs = $wpdb->get_results("select * from ".$wpdb->prefix."sng_messages where sender_id = $sender_id && receiver_id='$receiver_id'");
		$received_msgs = $wpdb->get_results("select * from ".$wpdb->prefix."sng_messages where sender_id = $receiver_id && receiver_id='$sender_id'");
		$full_chat = array_merge($sent_msgs, $received_msgs);
		usort($full_chat, function($a, $b) { 
			return $a->id - $b->id;
		});
		
		$j = 0;
		foreach($full_chat as $chat){ 
			$full_chat[$j]->format_time = date('h:i A',$chat->time);
			$full_chat[$j]->format_date = date('d-m-Y',$chat->time);
			$j++;
		}
		if(!empty($full_chat)){
			$message = $this->_requestStatus("1806");
			return array(
				"RespCode" => "1806",
				"Message" => $message,
				"chats" => $full_chat
			);
		} else {
			$message = $this->_requestStatus("1805");
			return array(
				"RespCode" => "1805",
				"Message" => $message,
			);
		}
	}
	
	//Get last/latest message
	public function get_last_message(){   
		global $json_api, $wpdb;
		$json = file_get_contents("php://input");
		$data = json_decode($json);
		$sender_id = $data->sender_id;
		$receiver_id = $data->receiver_id;
		// if($receiver_id == '0'){
			 // $receiver_id = 1;
			 // $receiver_id;
		// }

		$current_time = time();
		$current_time_before = time() - 10;

		//$last_message = $wpdb->get_results("select * from ".$wpdb->prefix."sng_messages where (sender_id='$sender_id' || sender_id='$receiver_id') && (receiver_id='$receiver_id' || receiver_id='$sender_id') && time >= '$current_time_before'");
		$last_message = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."sng_messages WHERE `sender_id` = $sender_id AND `receiver_id` = $receiver_id ORDER BY `ID` DESC LIMIT 1 ");
	
		$j = 0;
		foreach($last_message as $chat){ 			
			$last_message[$j]->format_time = date('h:i A',$chat->time);
			$last_message[$j]->format_date = date('d-m-Y',$chat->time);
			$j++;
		}
		
		if(!empty($last_message)){
			$message = $this->_requestStatus("1806");
			return array(
				"RespCode" => "1806",
				"Message" => $message,
				"chats" => $last_message
			);
		} else {
			$message = $this->_requestStatus("1805");
			return array(
				"RespCode" => "1805",
				"Message" => $message,
			);
		}
	}

/*******************************  Code for chat API [end] *******************************/
	
/*********************************Notification  ios*****************************************/
function send_ios_notification($data,$devicetoken,$body){
		// Put your private key's passphrase here:
		//~ print_r($body); die();
		$passphrase = '';
		// Put your alert message here:
		$message = $data;
		
		//$url = site_url();
		$cert_url = plugin_dir_path( __FILE__ ).'pushDistcert.pem';
		//$cert_url = '/var/www/html/getcool/wp-content/plugins/json-api/controllers/GetCoolAPNS.pem';
		//$cert_url = '/var/www/html/sngbrd/wp-content/plugins/json-api/controllers/pushdevcert.pem';
		//$cert_url = plugin_dir_path( __FILE__ ).'SecretCardProduction.pem';
		if (!$message)
			exit('Message: Not able to Send Notification' . "\n");
			////////////////////////////////////////////////////////////////////////////////
			$ctx = stream_context_create();
			stream_context_set_option($ctx, 'ssl', 'local_cert', $cert_url);
			stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase); 
			
			// Open a connection to the APNS server gateway.sandbox.push.apple.com
			$fp = stream_socket_client( 'ssl://gateway.sandbox.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT, $ctx);
		//$fp = stream_socket_client( 'ssl://gateway.push.apple.com:2195', $err, $errstr, 60, STREAM_CLIENT_CONNECT, $ctx);

		if (!$fp)
			exit("Failed to connect: $err $errstr" . PHP_EOL);

		$payload = json_encode($body);

		// Build the binary notification
		$msg = chr(0) . pack('n', 32) . pack('H*', $devicetoken) . pack('n', strlen($payload)) . $payload;
		// Send it to the server
		$result = fwrite($fp, $msg, strlen($msg));
		if (!$result)
		//~ exit('Message not delivered' . PHP_EOL);
		return 0;

		// Close the connection to the server
		fclose($fp);
		//~ return $result;
		return 1;
	}	
/*********************************Notification  ios*****************************************/
	
/******************** API for Badge Reduction [START] ****************/
	public function reduce_badge_count() { 
		global $json_api;
		$json     = file_get_contents("php://input");
		$data     = json_decode($json);
		$response       = $data->response;
		$devicetoken       = $data->devicetoken;
		$users = get_users(array(
			'meta_key'     => 'devicetoken',
			'meta_value'   => $devicetoken,
			'meta_compare' => '=',
			));	
			
		
		$results = update_user_meta($users[0]->ID, 'badge', 0);
		
		if($results == 1){
			$message = $this->_requestStatus("2033");
			return array(
				"RespCode" => "2033",
				"Message" => $message
			);
		} else {
			$message = $this->_requestStatus("2033");
			return array(
				"RespCode" => "2033",
				"Message" => $message
			);
		}
			
	}
	/******************** API for Badge Reduction [END] ****************/	
	
	
	
	
	
	
	
}//Main
<?php
//API For Upload Image 

public function upload_img()
    {
$image = base64_decode($this->input->post("img_front"));
$image_name = md5(uniqid(rand(), true));
$filename = $image_name . '.' . 'png';
//rename file name with random number
$path = "vehicle_image_upload/".$filename;
//image uploading folder path
file_put_contents($path . $filename, $image);
// image is bind and upload to respective folde

$data_insert = array('front_img'=>$filename);

$success = $this->add_model->insert_img($data_insert);
if($success){
    $b = "User Registered Successfully..";
}
else
{
    $b = "Some Error Occured. Please Try Again..";
}
echo json_encode($b);
}
}
?>
