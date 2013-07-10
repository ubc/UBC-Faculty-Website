<?php

/*
  Plugin Name: UBC Faculty Website
  Plugin URI:
  Description: Transforms the UBC Collab Theme into a specific faculty website | Note: This plugin will only work on wp-hybrid-clf theme
  Version: 1
  Author: Amir Entezaralmahdi | Arts ISIT
  Licence: GPLv2
  Author URI: http://isit.arts.ubc.ca
 */

Class UBC_Faculty_Theme_Options {

    static $prefix;
    static $add_script;

    /**
     * init function.
     * 
     * @access public
     * @return void
     */
    function init() {
		
        self::$prefix = 'wp-hybrid-clf'; // function hybrid_get_prefix() is not available within the plugin
        
        $theme = wp_get_theme();
       	
        if( "UBC Collab" != $theme->name )
        	return true;
        // include general faculty plugin specific css file
        wp_register_style('faculty-theme-option-style', plugins_url('faculty-website') . '/css/style.css');
        // include general faculty plugin specific javascript file
        wp_register_script('faculty-theme-option-script', plugins_url('faculty-website') . '/js/script.js');

        add_action('ubc_collab_theme_options_ui', array(__CLASS__, 'faculty_ui'));
        
        add_action( 'admin_init',array(__CLASS__, 'admin' ) );

        add_action( 'init',array(__CLASS__, 'load_faculty_options' ) );
        
        add_filter( 'ubc_collab_default_theme_options', array(__CLASS__, 'default_values'), 10,1 );
        add_filter( 'ubc_collab_theme_options_validate', array(__CLASS__, 'validate'), 10, 2 );
      	
    }
   
        
    /*
     * This function includes the css and js for this specifc admin option
     *
     * @access public
     * @return void
     */
     function faculty_ui(){
        wp_enqueue_style('faculty-theme-option-style');
        wp_enqueue_script('faculty-theme-option-script', array('jquery'));
     }
     
    /**
     * admin function.
     * 
     * @access public
     * @return void
     */
    function admin(){
        //Add Arts Options tab in the theme options
        add_settings_section(
                'faculty-options', // Unique identifier for the settings section
                'Faculty options', // Section title
                '__return_false', // Section callback (we don't want anything)
                'theme_options' // Menu slug, used to uniquely identify the page; see ubc_collab_theme_options_add_page()
        );
        //Add Colour options
        add_settings_field(
                'faculty-selection', // Unique identifier for the field for this section
                'Faculty Selection', // Setting field label
                array(__CLASS__,'faculty_selection_options'), // Function that renders the settings field
                'theme_options', // Menu slug, used to uniquely identify the page; see ubc_collab_theme_options_add_page()
                'faculty-options' // Settings section. Same as the first argument in the add_settings_section() above
        );     
    }

    function load_faculty_options(){
        if(class_exists(UBC_Collab_Theme_Options)) {
            $selected_facult = UBC_Collab_Theme_Options::get('faculty');
        } else{
            $selected_facult = 'general';
        }
        switch($selected_facult){
            case 'arts':
                require('faculty/arts/arts.php');
                break;
            case 'education':
                require('faculty/education/education.php');
                break;
            case 'medicine':
                require('faculty/medicine/medicine.php');
                break;
            default:
                //none
                break;
        }
    }
     /**
     * faculty_list.
     * Array list of faculties
     * @access public
     * @return void
     */
	function faculty_list() {

		$faculty_list = array(
	        'arts' => array(
	            'value' => 'arts',
	            'label' => __( 'Faculty of Arts', 'faculty-clf' )
	        ),
	        'education' => array(
	            'value' => 'education',
	            'label' => __( 'Faculty of Education', 'faculty-clf' )
	        ),
	        'medicine' => array(
	            'value' => 'medicine',
	            'label' => __( 'Faculty of Medicine', 'faculty-clf' )
	        ),
	        'general' => array(
	            'value' => 'general',
	            'label' => __( 'UBC general', 'faculty-clf' )
	        ),
	    );

		return $faculty_list;

	}
    /**
     * arts_colour_options.
     * Display colour options for Arts specific template
     * @access public
     * @return void
     */
    function faculty_selection_options(){ ?>


		<div class="explanation"><a href="#" class="explanation-help">Info</a>

			<div> Select which faculty you belong to, and the website will deploy faculty specific style and options.</div>
		</div>
		<div id="faculty-selection-box">
            <select name="ubc-collab-theme-options[faculty]">
            <?php foreach(UBC_Faculty_Theme_Options::faculty_list() as $faculty)
                UBC_Collab_Theme_Options::option( 'faculty', $faculty['value'], $faculty['label'] );
            ?>
            </select> <p><input id="submit-buttom" class="button-primary faculty-select-input" type="submit" value="Load Faculty Settings" name="submit"></p>
		</div>
            
            <?php
        //UBC_Collab_Theme_Options_Admin::admin_page();
    }
 
    /*********** 
     * Default Options
     * 
     * Returns the options array for arts.
     *
     * @since ubc-clf 1.0
     */
    function default_values( $options ) {

            if (!is_array($options)) { 
                    $options = array();
            }

            $defaults = array(
                'faculty'    => 'general',

            );

            $options = array_merge( $options, $defaults );

            return $options;
    }  
	/**
	 * Sanitize and validate form input. Accepts an array, return a sanitized array.
	 *
	 *
	 * @todo set up Reset Options action
	 *
	 * @param array $input Unknown values.
	 * @return array Sanitized theme options ready to be stored in the database.
	 *
	 */
	function validate( $output, $input ) {
		
		// Grab default values as base
		$starter = UBC_Faculty_Theme_Options::default_values( array() );
		
            
            // Validate Faculty selection
            if ( isset( $input['faculty'] ) && array_key_exists( $input['faculty'], UBC_Faculty_Theme_Options::faculty_list()) ) {
	        $starter['faculty'] = $input['faculty'];
	    }
            $output = array_merge($output, $starter);

            return $output;            
        }
}


UBC_Faculty_Theme_Options::init();

//var_dump( get_option( 'ubc-collab-theme-options' ));
