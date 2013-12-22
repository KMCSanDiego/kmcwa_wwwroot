<?php

/**
 * 
 *
 * @version $Id$
 * @copyright 2003 
 **/

$default_meta_field_labels = array(
	'fc_allday'			=> __('All day','rhc'),
	'fc_start_datetime'	=> __('Start datetime','rhc'),
	'fc_start'			=> __('Start date','rhc'),
	'fc_start_time'		=> __('Start time','rhc'),
	'fc_end_datetime'	=> __('End datetime','rhc'),
	'fc_end'			=> __('End date','rhc'),
	'fc_end_time'		=> __('End time','rhc')
); 
$default_meta_field_labels = apply_filters('default_meta_field_labels',$default_meta_field_labels);
 
$default_skip_meta_fields = array(
	/*'fc_start','fc_start_time',*/'fc_interval','fc_end_interval','fc_color','fc_text_color','fc_post_info','fc_rrule','fc_dow_except','fc_click_target'
);
?>