
<?php 

function mozilla_get_discourse_info($id, $type = 'group') {
    $discourse_info = Array();

    if($type === 'event') {
        if($id) {
            $event_meta = get_post_meta($id, 'event-meta');

            if(!empty($event_meta) && isset($event_meta[0]->discourse_group_id)) { 
                $data = Array();
                $data['group_id'] = $event_meta[0]->discourse_group_id;
                $discourse_group = mozilla_discourse_api('groups', $data, 'get');
                $discourse_info['discourse_group_id'] = $data['group_id'];
                
                if($discourse_group && !isset($discourse_group->status)) {

                    $discourse_info['discourse_group_name'] = $discourse_group->name;
                    $discourse_info['discourse_group_description'] = $discourse_group->description;
                    $discourse_info['discourse_group_users'] = $discourse_group->users;
                }

            }
        }
        return $discourse_info;
    } else {
        if($id) {
            $group_meta = groups_get_groupmeta($id, 'meta');
            if(isset($group_meta['discourse_category_id'])) {
                $data = Array();
                $data['category_id'] = $group_meta['discourse_category_id'];
                $discourse_category = mozilla_discourse_api('categories', $data, 'get');
                $discourse_info['discourse_category_id'] = $group_meta['discourse_category_id'];
    
                if($discourse_category && !isset($discourse_category->status)) {
                    $discourse_info['discourse_category_name'] = $discourse_category->name;
                    $discourse_info['discourse_category_description'] = $discourse_category->description;
                    $discourse_info['discourse_category_url'] = $discourse_category->url;
                    $discourse_info['discourse_category_groups'] = $discourse_category->groups;
                }
            }
        
            if(isset($group_meta['discourse_group_id'])) {
                $discourse_info['discourse_group_id'] = $group_meta['discourse_group_id'];
                $data = Array();
                $data['group_id'] = $group_meta['discourse_group_id'];
                $discourse_group = mozilla_discourse_api('groups', $data, 'get');
                if($discourse_group && !isset($discourse_group->status)) {
                    $discourse_info['discourse_group_name'] = $discourse_group->name;
                    $discourse_info['discourse_group_description'] = $discourse_group->description;
                    $discourse_info['discourse_group_users'] = $discourse_group->users;
                }
            }
            return $discourse_info;
        }
    }

    return false;
}


function mozilla_discourse_api($type, $data, $request = 'get') {
    $discourse = false;

    $options = wp_load_alloptions();
    if(isset($options['discourse_api_key']) && strlen($options['discourse_api_key']) > 0 && isset($options['discourse_api_url']) && strlen($options['discourse_api_url']) > 0) {

        // Get the API URL without the trailing slash
        $api_url = rtrim($options['discourse_api_url'], '/');
        $api_key = trim($options['discourse_api_key']);
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_HTTPHEADER, Array(
                "Content-Type: Application/json",
                "x-api-key: {$api_key}"
            )
        );
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $type = strtolower($type);
        $api_data = Array();

        $request = strtolower($request);
        $type = strtolower($type);

        switch($type) {
            case 'categories':
                switch($request) {
                    case 'post':
                        if(isset($data['name']) && strlen($data['name']) > 0) {
                            curl_setopt($curl, CURLOPT_URL, "{$api_url}/categories");
                            curl_setopt($curl, CURLOPT_POST, 1);
                            $api_data['name'] = $data['name'];

                            if(isset($data['description']) && strlen($data['description']) > 0) 
                                $api_data['description'] = $data['description'];

                            if(isset($data['groups']) && !empty($data['groups'])) {
                                $api_data['groups'] = $data['groups'];
                            }
                        }                    
                        break;
                    case 'patch':
                        if(isset($data['category_id']) && intval($data['category_id']) > 0) {    
                            $api_data['name'] = $data['name'];
                            if(isset($data['description']) && strlen($data['description']) > 0) 
                                $api_data['description'] = $data['description'];

                            if(isset($data['groups']) && !empty($data['groups'])) {
                                $api_data['groups'] = $data['groups'];
                            }

                            curl_setopt($curl, CURLOPT_URL, "{$api_url}/categories/{$data['category_id']}");
                            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PATCH");
                        }

                        break;
                    case 'delete':
                        if(isset($data['category_id']) && intval($data['category_id']) > 0) {    
                            curl_setopt($curl, CURLOPT_URL, "{$api_url}/categories/{$data['category_id']}");
                            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DEL");
                        }

                        break;
                    default:
                        if(isset($data['category_id']) && intval($data['category_id']) > 0) { 
                            curl_setopt($curl, CURLOPT_URL, "{$api_url}/categories/{$data['category_id']}");
                        }

                        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");
                        
                }
                break;
            case 'groups':
                switch($request) {
                    case 'post':
                        if(isset($data['name']) && strlen($data['name']) > 0) {
                            curl_setopt($curl, CURLOPT_POST, 1);
                            curl_setopt($curl, CURLOPT_URL, "{$api_url}/groups");

                            $api_data['name'] = $data['name'];
                            if(isset($data['description']) && strlen($data['description']) > 0) 
                                $api_data['description'] = $data['description'];

                            if(isset($data['users']) && is_array($data['users'])) {
                                $api_data['users'] = $data['users'];
                            }
                        }

                        break;
                    case 'patch':
                        if(isset($data['group_id']) && intval($data['group_id']) > 0) {    
                            $api_data['name'] = $data['name'];
                            if(isset($data['description']) && strlen($data['description']) > 0) 
                                $api_data['description'] = $data['description'];

                            curl_setopt($curl, CURLOPT_URL, "{$api_url}/groups/{$data['group_id']}");
                            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PATCH");
                        }

                        break;
                    case 'delete':
                        if(isset($data['group_id']) && intval($data['group_id']) > 0) {    
                            curl_setopt($curl, CURLOPT_URL, "{$api_url}/groups/{$data['group_id']}");
                            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DEL");
                        }

                        break;
                    default:
                        if(isset($data['group_id']) && intval($data['group_id']) > 0) {    
                            curl_setopt($curl, CURLOPT_URL, "{$api_url}/groups/{$data['group_id']}");
                            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");
                        }
                        
                }
                break;
            case 'groups/users':
                if(isset($data['group_id']) && intval($data['group_id']) > 0) { 
                    curl_setopt($curl, CURLOPT_URL, "{$api_url}/groups/{$data['group_id']}/users");
                    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PATCH");

                    if(isset($data['add_users']) && is_array($data['add_users'])) {
                        $api_data['add'] = $data['add_users'];
                    }

                    if(isset($data['remove_users']) && is_array($data['remove_users'])) {
                        $api_data['remove'] = $data['remove_users'];
                    }
                }

                break;
        }

        if(!empty($api_data) || $request !== 'get') {  
            $json_data = json_encode($api_data);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $json_data);
        }
        
        $curl_result = curl_exec($curl);
        $discourse = json_decode($curl_result);
    }

    return $discourse;
}

function mozilla_discourse_get_category_topics($url) {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    curl_setopt($curl, CURLOPT_URL, "{$url}.json");
    $curl_result = curl_exec($curl);
    $discourse_category = json_decode($curl_result);
    
    curl_close($curl);
    
    if(isset($discourse_category->topic_list) && isset($discourse_category->topic_list->topics))
        $topics = is_array($discourse_category->topic_list->topics) ? $discourse_category->topic_list->topics : Array();
    else 
        $topics = Array();

    return $topics;
}


function mozilla_create_mailchimp_list($campaign) {
    $options = wp_load_alloptions();
    
    $create_audience = get_field('mailchimp_integration', $campaign->ID);

    if($create_audience && isset($options['mailchimp'])) {
        $apikey = trim($options['mailchimp']);
        $dc = substr($apikey, -3);
        if($dc) {
            $mailchimp_check = get_post_meta($campaign->ID, 'mailchimp-list-id', true);

            if(empty($mailchimp_check)) {

                $curl = curl_init();
                $api_url = "https://{$dc}.api.mailchimp.com/3.0/lists";

                $auth_string = "user:{$apikey}";
                $auth = base64_encode($auth_string);

                curl_setopt($curl, CURLOPT_URL, $api_url);
                curl_setopt($curl, CURLOPT_HTTPHEADER, Array("Content-Type: application/json"));
                curl_setopt($curl, CURLOPT_USERPWD, 'user:' . $apikey);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_POST, true);

                $campaign_list_name = "{$campaign->post_title} - Mozilla";
                
                $data = Array();
                $data['name'] = $campaign_list_name;

                $data['contact'] = Array(
                    'company'   =>  $options['company'],
                    'address1'  =>  $options['address'],
                    'city'      =>  $options['city'],
                    'state'     =>  $options['state'],
                    'zip'       =>  $options['zip'],
                    'country'   =>  $options['country'],
                    'phone'     =>  $options['phone']
                );

                $data['campaign_defaults'] = Array(
                    'from_name'     =>  $campaign_list_name,
                    'from_email'    =>  $options['report_email'],
                    'subject'       =>  $campaign_list_name,
                    'language'      =>  'English'
                );

                $data['permission_reminder'] = "You are participating in the Mozilla {$campaign->post_title} campaign";
                $data['email_type_option'] = true;

                $json = json_encode($data);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $json);
                $result = curl_exec($curl);
                curl_close($curl);

                $json_result = json_decode($result);
                return update_post_meta($campaign->ID, 'mailchimp-list-id', $json_result);
            }
        }
    }

    return false;
}

function mozilla_remove_email_from_list($id, $email) {
    $options = wp_load_alloptions();
  	$subscriberHash = md5(strtolower($email));

    if(isset($options['mailchimp'])) {
        $apikey = trim($options['mailchimp']);
        $dc = substr($apikey, -3);
        if($dc) {
            $curl = curl_init();
            $api_url = "https://{$dc}.api.mailchimp.com/3.0/lists/{$id}/members/{$subscriberHash}";
            $auth_string = "user:{$apikey}";
            $auth = base64_encode($auth_string);

            curl_setopt($curl, CURLOPT_URL, $api_url);
            curl_setopt($curl, CURLOPT_HTTPHEADER, Array("Content-Type: application/json"));
            curl_setopt($curl, CURLOPT_USERPWD, 'user:' . $apikey);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");
         
            $result = curl_exec($curl);
            curl_close($curl);

            $json_result = json_decode($result);
            return $json_result;
        }
    }
  
    return false;
}

function mozilla_add_email_to_list($id, $email) {

    $options = wp_load_alloptions();
    
    if(isset($options['mailchimp'])) {
        $apikey = trim($options['mailchimp']);
        $dc = substr($apikey, -3);
        if($dc) {
            $curl = curl_init();
            $api_url = "https://{$dc}.api.mailchimp.com/3.0/lists/{$id}/members";
            $auth_string = "user:{$apikey}";
            $auth = base64_encode($auth_string);

            curl_setopt($curl, CURLOPT_URL, $api_url);
            curl_setopt($curl, CURLOPT_HTTPHEADER, Array("Content-Type: application/json"));
            curl_setopt($curl, CURLOPT_USERPWD, 'user:' . $apikey);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

            $data = Array();
            $data['email_address'] = $email;
            $data['status'] = 'subscribed';


            $json = json_encode($data);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $json);
            $result = curl_exec($curl);
            curl_close($curl);
          
            $json_result = json_decode($result);
            return $json_result;
        }
    }

    return false;
}

?>