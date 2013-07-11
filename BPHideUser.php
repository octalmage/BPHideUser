<?php
/*
Plugin Name: BPHideUser
Plugin URI: http://json.sx/projects/bphideuser/
Description: This plugin allows you to exclude users from Members List, aswell as the included widgets. 
Version: 0.2
Author: Jason Stallings
Author URI: http://json.sx
License: GPL2
*/
add_action( 'admin_menu', 'bphideuser_menu' );

add_action('bp_ajax_querystring','bphideuser_exclude_users',20,2);
add_action('init','bphideuser_stealth');



function bphideuser_menu() 
{
	add_submenu_page("tools.php", 'BPHideUser', 'BPHideUser', 'manage_options', 'bphideuser_menu', 'bphideuser_options' );
}

function bphideuser_options() 
{
    if ( !current_user_can( 'manage_options' ) )  
	{
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}
	
	$opt_name = 'bpuserarray';
	$hidden_field_name = 'bpuserfield';
    $data_field_name = 'bpuserarray';
    
	$opt_name2 = 'bpwidgetuserarray';
	$hidden_field_name2 = 'bpwidgetuserfield';
    $data_field_name2 = 'bpwidgetuserarray';

    // Read in existing option value from database
    $opt_val = get_option( $opt_name );
    $opt_val2 = get_option( $opt_name2 );
    
     if( isset($_POST[ $data_field_name ]) || isset($_POST[ $data_field_name2 ])) 
     {
        // Read their posted value
        $opt_val = $_POST[ $data_field_name ];
        $opt_val2 = $_POST[ $data_field_name2 ];

        // Save the posted value in the database
        update_option( $opt_name, $opt_val );
        update_option( $opt_name2, $opt_val2 );

        // Put an settings updated message on the screen

	?>
	<div class="updated"><p><strong><?php _e('Settings Saved.', 'menu-test' ); ?></strong></p></div>
	<?php

	}
	?>
<div class="wrap">
	<div id="icon-tools" class="icon32"></div><h2>BPHideUser</h2>
	<form name="form1" method="post" action="">
		<input type="hidden" name="<?php echo $hidden_field_name; ?>" value="Y">

		<p>Users Excluded from Directory (comma separated ids)</p>
			<input type="text" name="<?php echo $data_field_name; ?>" value="<?php echo $opt_val; ?>" size="20">
		<p>Users Excluded from Member Activity (comma separated ids)</p>
			<input type="text" name="<?php echo $data_field_name2; ?>" value="<?php echo $opt_val2; ?>" size="20">	
		<hr />

		<p class="submit">
			<input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Save Changes') ?>" />
		</p>
	</form>	
</div>

<?php
	
}



function bphideuser_exclude_users($qs=false,$object=false)
{
    //list of users to exclude
	$excluded_user=get_option("bpuserarray");
   

	$args=wp_parse_args($qs);
 
	//check if we are listing friends?, do not exclude in this case

 
	if(!empty($args['exclude']))
		$args['exclude']=$args['exclude'].','.$excluded_user;
	else
		$args['exclude']=$excluded_user;
 
	$qs=build_query($args);
 
	return $qs;    
}

function bphideuser_stealth()
{
	$excluded_user=explode(",", get_option("bpwidgetuserarray"));
	$current_user = wp_get_current_user();
	if(is_user_logged_in()) 
	{
		if(in_array($current_user->ID, $excluded_user)) 
		{
			remove_action('wp_head','bp_core_record_activity');
			delete_user_meta($current_user->ID, 'last_activity');
		}
	}
}
?>