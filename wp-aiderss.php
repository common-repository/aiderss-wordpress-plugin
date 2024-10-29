<?php
/*
Plugin Name: AideRSS WordPress Plugin
Version: 0.2
Plugin URI: http://fairyfish.net/2008/05/26/aiderss-wordpress-plugin/
Description: Show the top posts for your WordPress blog base on AideRSS
Author: Denis
Author URI: http://fairyfish.net/
*/


if(!class_exists('AideAPI')){
  require('aideapi.php');
}

function aide_get_top_posts($period="month",$num=10){  
  $aide_top_posts = wp_cache_get('aide'.$period.$num, 'aide_rss');

  if (false === $aide_top_posts) {
    $url = get_option('siteurl');
    $aide_rss = new AideAPI($url);
    
    $feed_id = $aide_rss -> feed_id($url) ;
    
    $top_posts = $aide_rss -> top_posts($feed_id,$period,$num) ;
    
    $aide_top_posts_data = '';
    
    if(!function_exists('curl_init')){
      $aide_top_posts_data .= '<ul>';
      foreach($top_posts as $top_post){
        $aide_top_posts_data .= '<li><a href="'.urldecode(str_replace('http://api.aiderss.com/log?url=', '',$top_post['link'])).'" title="'.$top_post['title'].'">'.$top_post['title'].' - '.$top_post['postrank'].'</a></li>';
        $aide_top_posts_data = '</ul>';
      }
    } else {
      $post_links = array();
      foreach($top_posts as $top_post){
        $post_link = urldecode(str_replace('http://api.aiderss.com/log?url=', '',$top_post['link'])); 
        $post_links[]=$post_link;
      }
      $post_stats = $aide_rss -> entry_stats($post_links, $feed_id);
      
      $aide_top_posts_data .= '
        <table class="aide_table" cellpadding="0" cellspacing="4">
        <thead>
          <tr>
            <th>PostRank</th>
            <th>Date</th>
            <th>Title</th>
            <th colspan="3">Top conversations</th>
          </tr>
        </thead>
          <tbody>';
    
      foreach($top_posts as $top_post){
        $post_link = urldecode(str_replace('http://api.aiderss.com/log?url=', '',$top_post['link'])); 
        
        $a_post_stats = $post_stats[$post_link];       
      
        $aide_top_posts_data .= '
            <tr>
              <td bgcolor="'.$a_post_stats['postrank_color'].'" style="padding-left:4px;">'.$top_post['postrank']. '<!-- '.$a_post_stats['postrank']. '--></td>
              <td>'.date('M d, Y',$top_post['pubdate']).'</td>
              <td>
                <a href="'.$post_link.'" title="'.$top_post['title'].'">'.$top_post['title'].'</a>
              </td>
              <td>
                <img src="http://www.aiderss.com//images/sources/comments.gif" /> '.$a_post_stats['slash_comments'].'
              </td>
              <td>
                <img src="http://www.aiderss.com//images/sources/delicious.gif" /> '.$a_post_stats['delicious'].'
              </td>
              <td>
                <img src="http://www.aiderss.com//images/sources/digg.gif" />  '.$a_post_stats['digs'].' 
              </td>
              <td>              
                <img src="http://www.aiderss.com/images/sources/google.gif" />  '.$a_post_stats['google'].' 
              </td>
            </tr>';
      }
      
      $aide_top_posts_data .= '      
          </tbody>
        </table>
	';
    }
    
    $aide_expire = 60 * 60 * 24; // Cache data for one day (86400 seconds)
    
    wp_cache_set('aide'.$period.$num,$aide_top_posts_data, 'aide_rss', $aide_expire);
    
    echo $aide_top_posts_data;
  }else{
    echo $aide_top_posts;
  }
}
?>
