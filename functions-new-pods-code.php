<?php

/* 
	Custom functions for displaying and creating documents associated with a series of posts.
	Uses PODs plugin for custom fields.
	Author: Rohan Vyas
*/

/* Show custom fields meta box in post editor */
add_filter('acf/settings/remove_wp_meta_box', '__return_false');


/* Series Posts Files Download */

/* code for ACF download all start */

// function to check file exist or not from URL
function remote_file_exists($url){
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if( $httpCode == 200 ){
        $path_parts = pathinfo($url);
        $a=$path_parts['extension'];
        $allowed = array("png", "jpg", "pdf", "docx", "doc", "xlxs","xls","ppt","pptx");
		if(in_array($a, $allowed)) {
			return true;
		}
    }
    return false;
}
// function to create zip file 

function create_zip($files,$desti,$overwrite=true)
{
	$zipname = $desti;
	var_dump($zipname);
	if(is_array($files)) 
	{
		if(count($files)!=0)
		{
			$zip = new ZipArchive;
			if($zip->open( $zipname, ZIPARCHIVE::CREATE | ZIPARCHIVE::OVERWRITE )) 
			{  

				foreach ($files as $file) 
				{
					if(remote_file_exists($file))
					{
						$download_file = file_get_contents($file);

						$zip->addFromString(basename($file), $download_file);

					}
				}    
				$zip->close();

				return basename($zipname);
			}
		}
		else
		{
			return "no";
		}
	}
	else
	{
		$zip = new ZipArchive;
		if($zip->open( $zipname, ZIPARCHIVE::CREATE | ZIPARCHIVE::OVERWRITE )) 
		{  
			if(remote_file_exists($files))
			{
				$download_file = file_get_contents($files);
				$zip->addFromString(basename($files), $download_file);
			}

			$zip->close();

			return basename($zipname);
		}

		return "no";

	}

}
function save_post_callback($post_id){
    
	if("contributions" == get_post_type($post_id) ){
		
		
		
		$files_to_zip1=array();
		$pod = pods( 'contributions', $post_id );
		$related = $pod->field( 'series' );
		
		$i=0;
		
		foreach ( $related as $rel ) {
			
			
			$pod2 = pods( 'series', $rel['ID'] );
			$related2 = $pod2->field( 'members' );
			
			
			$j=1;
			foreach ( $related2 as $rel2 ) {
				$pod3 = pods( 'contributions', $rel2["ID"] );
				$total_docs = 20;
				for($i=1;$i<=$total_docs;$i++)
				{
					$doc = $pod3->field( 'document_'.$i );
					if(!empty($doc)) {
						$files_to_zip1[$j]=$doc;	
					}
					$user_id = 1;
					if($i = 1) {
						$website = $doc;	
					}
					

					$user_id = wp_update_user( array( 'ID' => $user_id, 'user_url' => $website ) );

					if ( is_wp_error( $user_id ) ) {
						// There was an error, probably that user doesn't exist.
					} else {
						// Success!
					}		
					//$doc = pods( 'document_'.$i, $rel2['ID'] );
					//$doc = $pod->field( 'document_'.$i );
					//$files_to_zip1[$j]=$doc;
					$j++;
				}
				//echo "<pre>f2z:";
				//var_dump($files_to_zip1);
				//echo "</pre>";
			}
			     
			
			if(!empty($files_to_zip1)) {
				$upload_dir   = wp_upload_dir();
				$upload_dir1= $upload_dir['basedir'].'/documents-zips';
				
				$post_slug = $rel['post_name'];
				$result = create_zip($files_to_zip1,$upload_dir1.'/'.$post_slug.'.zip');
				if($result!='no')
				{
					$ver = rand(22,220000);
					//update_field( 'zip_file', $upload_dir['baseurl'].'/documents-zips/'.$result, $rel['ID'] );
					$pod->save( 'zip_file2', $upload_dir['baseurl'].'/documents-zips/'.$result.'?ver='.$ver, $rel['ID']); 
				}
			}
		}
	}
	
    if ( 'series' == get_post_type() ) {


		
		$files_to_zip1=array();
		//echo "TEST@@=====";
		$pod = pods( 'series', $post_id );
		$related = $pod->field( 'members' );
		//echo "TEST22=====";
		//var_dump($related);
		$i=0;
		$j=1;
		foreach ( $related as $rel ) {
			
			$pod2 = pods( 'contributions', $rel["ID"] );
			//echo "<pre>";
			//var_dump($rel["ID"]);
			//echo "</pre>";

			$total_docs = 20;
			for($i=1;$i<=$total_docs;$i++)
			{
				$doc = pods( 'document_'.$i, $rel['ID'] );
				//$doc = $pod->field( 'document_'.$i );
				$files_to_zip1[$j]=$doc;
				$j++;
			}
		
		}
		if(!empty($files_to_zip1)) {		
			$upload_dir   = wp_upload_dir();
			$upload_dir1= $upload_dir['basedir'].'/documents-zips';
			global $post;
			$post_slug = $post->post_name;
			$result = create_zip($files_to_zip1,$upload_dir1.'/'.$post_slug.'.zip');
			if($result!='no')
			{
				$ver = rand(22,220000);
				//update_field( 'zip_file', $upload_dir['baseurl'].'/documents-zips/'.$result, $rel['ID'] );
				$pod->save( 'zip_file2', $upload_dir['baseurl'].'/documents-zips/'.$result.'?ver='.$ver, $rel['ID']);
				//echo "TEST55=====";
				//var_dump($docs);		
			}    
        }
	}
}
//add_action('save_post','save_post_callback');
/* code for ACF download all end */