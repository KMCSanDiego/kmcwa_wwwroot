<?php

/**
 * 
 * @version $Id$
 * @copyright 2003 
 **/

class rhc_styles_and_scripts {
	var $version;
	var $jquery_ui;
	var $is_admin=false;
	var $debugging_js_css;
	var $in_footer;
	function rhc_styles_and_scripts($in_footer=false,$debugging_js_css=false){
		$this->is_admin = is_admin();
		$this->debugging_js_css = $debugging_js_css;
		$this->in_footer = $in_footer;		
		//------
		global $wp_version;	
		//3.5-beta1-22133
		$this->version = substr($wp_version,0,3);

		if($this->is_admin){
			add_action('admin_init',array(&$this,'wp'));	//althought the codex says wp hook is fired in the admin, it doesnt seems like it is.
		}else{
			add_action('wp',array(&$this,'wp'));
		}
	}
	
	function wp(){
		if($this->skip_scripts())return;
		if($this->is_admin){
			$this->set_jquery_ui('backend_jquery_ui');
			
			add_action('admin_enqueue_scripts',array(&$this,'admin_and_frontend_scripts'),10);
			add_action('admin_enqueue_scripts',array(&$this,'admin_only_scripts'),11);
		}else{
			$this->set_jquery_ui('frontend_jquery_ui');
			
			add_action('wp_enqueue_scripts',array(&$this,'admin_and_frontend_scripts'),10);
			add_action('wp_enqueue_scripts',array(&$this,'frontend_only_scripts'),11);
		}	
	}
	
	function set_jquery_ui( $jquery_ui_option_name ){
		$version = $this->version;
		$jquery_ui = $this->get_option($jquery_ui_option_name,'',true);
		if(''==$jquery_ui){
			if($version>=3.7){
				$jquery_ui = 'jquery-ui-wp';
			}else if($version>=3.6){
				$jquery_ui = 'rhc-jquery-ui-1-10-3';
			}else if($version>=3.5){
				$jquery_ui = 'rhc-jquery-ui-1-9-0';
			}else{
				$jquery_ui = 'rhc-jquery-ui-1-8-22';
			}
		}else if('wp'==$jquery_ui){
			$jquery_ui = 'jquery-ui-wp';
		}
		$this->jquery_ui = $jquery_ui;	
	}
	
	function admin_and_frontend_scripts(){
		global $rhc_plugin;
		wp_register_script( 'rhc-jquery-ui-1-9-0', RHC_URL.'js/jquery-ui-1.9.0.custom.min.js', array('jquery'),'1.9.0');
		wp_register_script( 'rhc-jquery-ui-1-8-22', RHC_URL.'js/jquery-ui-1.8.22.custom.min.js', array('jquery'),'1.8.22');
		wp_register_script( 'rhc-jquery-ui-1-10-3', RHC_URL.'js/jquery-ui-1.10.3.custom.min.js', array('jquery'),'1.10.3');
		wp_register_script('jquery-ui-wp',RHC_URL.'js/deprecated.js',array(
			'jquery-ui-core',
			'jquery-ui-accordion',
			'jquery-ui-widget',
			'jquery-ui-slider',
			'jquery-ui-dialog',
			'jquery-ui-button',
			'jquery-ui-tabs',
			'jquery-ui-sortable',
			'jquery-ui-droppable',
			'jquery-ui-datepicker',
			'jquery-ui-autocomplete',
			'jquery-ui-mouse'
		),'bundled-jquery-ui');
				
		wp_register_script( 'jquery-easing', RHC_URL.'js/jquery.easing.1.3.js', array('jquery'),'1.3.0',$this->in_footer);
		
		wp_register_script( 'rrecur-parser', RHC_URL.'js/rrecur-parser.js', array('jquery'),'1.1.0.3',$this->in_footer);	
		wp_register_script( 'fullcalendar', RHC_URL.'fullcalendar/fullcalendar/fullcalendar.custom.js', array('jquery','rrecur-parser'),'1.6.4.2',$this->in_footer);	
		wp_register_script( 'fullcalendar-gcal', RHC_URL.'fullcalendar/fullcalendar/gcal.js', array('fullcalendar'),'1.6.1.1',$this->in_footer);	
		wp_register_script( 'calendarize-fcviews', RHC_URL.'js/fullcalendar_custom_views.js', array('fullcalendar-gcal'),'1.1.5.1',$this->in_footer);	
				
		wp_register_script( 'fechahora', RHC_URL.'js/fechahora.js', array('jquery'),'1.0.0',$this->in_footer);//are this used?
		wp_register_script( 'fc_dateformat_helper', RHC_URL.'js/fc_dateformat_helper.js', array('fullcalendar'),'1.0.0',$this->in_footer);//are this used?
		//-------
		$dependency = array('jquery','jquery-easing','calendarize-fcviews');
		if('none'!=$this->jquery_ui){
			$dependency[]=$this->jquery_ui;
		}
	
		wp_register_script( 'calendarize', RHC_URL.'js/calendarize.js', $dependency,'2.6.6.3',$this->in_footer);
		wp_localize_script( 'calendarize', 'RHC', array( 
			'ajaxurl' => site_url('/'),
			'mobile_width' => $rhc_plugin->get_option('mobile_width','480',true)
		) );
		//-------
		if('1'==$this->get_option('disable_google_map_api_load','',true)){
			//it seems that now if map lib is loaded twice, a js error is producted. we register an empty js anyway to avoid dependency issues with other libs that may be loaded by addon.
			wp_register_script( 'google-api3', RHC_URL.'js/deprecated.js', array('jquery'),'3.0',$this->in_footer);
		}else{
			if(is_ssl()){
				wp_register_script( 'google-api3', 'https://maps.google.com/maps/api/js?sensor=false&libraries=places', array('jquery'),'3.0',$this->in_footer);
			}else{
				wp_register_script( 'google-api3', 'http://maps.google.com/maps/api/js?sensor=false&libraries=places', array('jquery'),'3.0',$this->in_footer);
			}			
		}

		wp_register_script( 'rhc_gmap3', RHC_URL.'js/rhc_gmap3.js', array('google-api3'), '1.0.1',$this->in_footer );//do we use this in the backend?
		wp_register_script( 'rhc_setup', RHC_URL.'js/setup_and_notices.js', array('jquery'),'1.0.0',$this->in_footer);	
		
		return true;
	}
		
	function frontend_only_scripts(){
		do_action('rh-php-commons');
		if('1'==$this->get_option('visibility_check','0',true)){
			wp_enqueue_script( 'rhc-visibility-check', RHC_URL.'js/visibility_check.js', array('jquery'),'1.0.0',$this->in_footer);
		}
		
		if('0'==$this->get_option('disable_print_css','0',true)){
			wp_enqueue_style( 'rhc-print-css', RHC_URL.'css/print.css', array(),'1.0.0');
		}
		
		if('0'==$this->get_option('disable_bootstrap','0',true)){
			rh_enqueue_script( 'bootstrap', RHC_URL.'js/bootstrap.min.js', array('jquery'),'3.0.0',$this->in_footer );
			wp_enqueue_script( 'bootstrap-select', RHC_URL.'js/bootstrap-select.js', array('bootstrap'),'1.0.0',$this->in_footer );
		}else{
			wp_enqueue_script( 'bootstrap-select', RHC_URL.'js/bootstrap-select.js', array(),'1.0.0',$this->in_footer );
		}
					
		wp_enqueue_style( 'fullcalendar-theme', RHC_URL.'ui-themes/default/style.css', array(),'1.8.18');
		if( $this->debugging_js_css ){
			wp_enqueue_style( 'calendarize', RHC_URL.'style.css', array(),'2.4.4.1');			
			wp_enqueue_style( 'rhc-frontend', RHC_URL.'frontend.css', array(),'2.0.0.7');				
		}else{
			wp_enqueue_style( 'calendarizeit', RHC_URL.'css/frontend.min.css', array(),'2.5.4.1');
		}	
		wp_enqueue_style( 'rhc-last-minue', RHC_URL.'css/last_minute_fixes.css', array(),'1.0.5');		
		wp_enqueue_script('calendarize');
		do_action('rhc_scripts_frontend');
	}
	
	function admin_only_scripts(){
		wp_enqueue_style( 'fullcalendar-theme', RHC_URL.'ui-themes/default/style.css', array(),'1.8.18');
		wp_enqueue_style( 'calendarize', RHC_URL.'style.css', array(),'2.4.4.2');		
		wp_register_style( 'post-meta-boxes', RHC_URL.'css/post_meta_boxes.css', array(),'1.0.0');
		wp_register_style( 'rhc-admin', RHC_URL.'css/admin_rhc.css', array(),'1.0.0');
		//wp_register_style( 'rhc-jquery-ui', RHC_URL.'ui-themes/default/style.css', array(),'1.8.14');
		wp_register_style( 'calendarize-metabox', RHC_URL.'css/calendarize_metabox.css', array(),'1.0.4');			
		$dependency = array();
		if('none'!=$this->jquery_ui){
			$dependency[]=$this->jquery_ui;
		}
	
		wp_register_script( 'rhc-jquery-ui-timepicker', RHC_URL.'js/jquery-ui-timepicker-addon.js', $dependency,'0.9.5');
		wp_register_script( 'rhc-admin', RHC_URL.'js/admin_rhc.js', array('rhc-jquery-ui-timepicker'),'1.0.0');					
		wp_register_script( 'calendarize-metabox', RHC_URL.'js/calendarize_metabox_rrule.js', array('jquery'),'1.2.6.1');				
		wp_register_script( 'postinfo-metabox', RHC_URL.'js/post_info_metabox.js', array('jquery'),'1.2.6.3');	
		do_action('rhc_scripts_admin');
	}
	
	function skip_scripts(){
		if($this->is_admin){
			
		}else{
			//frontend
			if(false===apply_filters( 'rhc_scripts', true )){
				return true;	
			}
			
			if(!$this->is_rhc()){		
				$pages_ids = trim($this->get_option('rhc_scripts_on_specific_pages','',true));
				if( !empty( $pages_ids ) ){
					$arr = explode(',',str_replace(' ','',$pages_ids));
					
					if( $o = get_queried_object() ){
						if(false!==strpos($o->post_content,'[calendarize'))return false;//post contains calendarize shortcode.
						if(false!==strpos($o->post_content,'[rhc_upcoming_events'))return false;//post contains upcoming events widget in content
						//--
						$current_page_id = $o->ID;			
						$arr = is_array($arr)?$arr:array();
						if(!in_array($current_page_id,$arr)){
							return true;
						}					
					}			
				}		
			}
		}
		
		return false;
	}
	
	function is_rhc(){
		$o = get_queried_object();
		if(is_single()){
			if($o->post_type==RHC_EVENTS)return true;
			return false;
		}	
		//----
		if(!is_tax())return false;
		$default_taxonomies = array(
			RHC_CALENDAR 	=> __('Calendar','rhc'),
			RHC_ORGANIZER	=> __('Organizer','rhc'),
			RHC_VENUE		=> __('Venues','rhc')
		);
	
		$taxonomies = apply_filters('rhc-taxonomies',$default_taxonomies);		
		if(in_array($o->taxonomy,array_keys($taxonomies))){
			return true;
		}
		return false;
	}	
	
	function get_option($a,$b,$c){
		global $rhc_plugin;
		return $rhc_plugin->get_option($a,$b,$c);
	}
}
?>