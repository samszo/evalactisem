<?php
require_once('library/delicious.php');

// define some error messages
$errors = array(
	1=>'You must enter credentials',
	2=>'You have been throttled. Please stop attempting to connect to del.icio.us',
	3=>'The del.icio.us service did not return a proper HTTP response',
	4=>'Unable to connect to del.icio.us',
	5=>'Unable to send data to del.icio.us',
	6=>'Unable to parse the XML returned from del.icio.us'
);

// instanciate delicious object
$del = new delicious('luckysemiosis','Samszo0');

// toggle debugging mode
$del->debug_flag = 1;

// Example #1: get all posts to date, to initially import existing delicious entries into your blog
// PLEASE USE SPARINGLY OR del.icio.us WILL THROTTLE YOU
if(!$result = $del->get_all_posts())
{
	$message = $errors[ $del->getError() ];
}
else
{
	// debugging helpers
	//echo $del-&gt;htmlvar_dump($result);
	//echo $del-&gt;request;
	//echo $del-&gt;response;
	
	if( count($result) > 0)
	{
		// loop thru result set, inserting into db
		foreach($result as $properties)
		{
			echo implode(', ',$properties)."\r\n\r\n";
			
			// map delicious fields onto blog fields
			$blogfields = array();
			$blogfields['title'] = $properties['description'];
			$blogfields['date'] = date('Y-m-d H:m:s',strtotime(ereg_replace('T|Z',' ',$properties['time'])));
			$blogfields['link'] = $properties['href'];
			$blogfields['content'] = $properties['extended'];
			$blogfields['category'] = str_replace(' ',',',$properties['tag']);
			
			$yourBlogObject->addPost($blogfields);
		}
	}
	else
	{
		$message = $result['code'];
	}
}?>