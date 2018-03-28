<?php
/*
Plugin Name:  Asana Views
Plugin URI:   https://developer.wordpress.org/plugins/asanaviews/
Description:  Viewing an Asana project on a Wordpress Page
Version:      0.1
Author:       Digital Ideas
Author URI:   https://www.digitalideas.io/
License:      GPL2
License URI:  https://www.gnu.org/licenses/gpl-2.0.html
*/

require 'php-asana/vendor/autoload.php';

use Asana\Client;

function asanaconnect_shortcode($atts = [], $content = null)
{
    if(empty($content)) {
        $content = "";
    }
    if (!defined('ASANACONNECT_PERSONAL_ACCESS_TOKEN')) {
        return 'Please define ASANACONNECT_PERSONAL_ACCESS_TOKEN in wp-config.php with your Asana personal access token';
    }
    
    $client = Asana\Client::accessToken(ASANACONNECT_PERSONAL_ACCESS_TOKEN);
    
    $project = $client->projects->findById($atts['project_id'], null, array('iterator_type' => false, 'page_size' => null));
    $sections = $client->projects->sections($atts['project_id'], null, array('iterator_type' => false, 'page_size' => null))->data;

    if($project) {
        foreach($sections as $section) {
            $content .= "<h2>$section->name</h2>";
            
            $path = sprintf("/sections/%s/tasks", $section->id);
            $tasks = $client->getCollection($path, array(), array('iterator_type' => false, 'page_size' => null))->data;
            $content .= "<ul>";
            foreach($tasks as $task) {
                $content .= "<li>$task->name</li>";
            }
            $content .= "</ul>";
        }
    }

    
    $tasks = $client->tasks->findByProject(334108213137383, null, array('iterator_type' => false, 'page_size' => null));
    
    return $content;
}
add_shortcode('asana_project_tasks', 'asanaconnect_shortcode');