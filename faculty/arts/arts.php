<?php
/*
  Description: Transforms the UBC Collab Theme into an Arts website | Note: This plugin will only work on wp-hybrid-clf theme
  Version: 1
  Author: Amir Entezaralmahdi | Arts ISIT
  Licence: GPLv2
  Author URI: http://isit.arts.ubc.ca
 */
Class UBC_Arts_Theme_Options {

    static $prefix;
    static $faculty_main_homepage;
    static $add_script;

    /**
     * init function.
     *
     * @access public
     * @return void
     */
    function init() {

        self::$prefix = 'wp-hybrid-clf'; // function hybrid_get_prefix() is not available within the plugin

        self::$faculty_main_homepage = 'http://www.arts.ubc.ca';

        // include Arts specific css file
        wp_register_style('arts-faculty-theme-option-style', plugins_url('faculty-website') . '/faculty/arts/css/arts-website.css');
        // include Arts specific javascript file
        wp_register_script('arts-faculty-theme-option-script', plugins_url('faculty-website') . '/faculty/arts/js/arts-website.js');

        add_action('ubc_collab_theme_options_ui', array(__CLASS__, 'arts_faculty_ui'));

        add_action( 'init', array(__CLASS__, 'register_scripts' ), 12 );

        add_action( 'admin_init',array(__CLASS__, 'arts_admin' ) );
        
        add_filter( 'ubc_collab_default_theme_options', array(__CLASS__, 'default_values'), 10,1 );
        add_filter( 'ubc_collab_theme_options_validate', array(__CLASS__, 'validate'), 10, 2 );
        
        add_action( 'wp_head', array( __CLASS__,'wp_head' ) );
        add_action( 'wp_footer', array( __CLASS__,'wp_footer' ) );

        /************ Arts specifics *************/
        
        //Add Arts Logo
        add_filter('wp_nav_menu_items', array(__CLASS__,'add_arts_logo_to_menu'), 10, 2);
        
        //Add Apply Now button to Menu if selected
        add_filter('wp_nav_menu_items', array(__CLASS__,'add_apply_now_to_menu'), 10, 2);
        //Add Arts frontpage layout
        add_action( 'admin_footer', array(__CLASS__, 'arts_frontpage_layout' ) );
        //remove slider margin
        add_action( 'admin_footer', array(__CLASS__, 'remove_slider_margin'));
        //Select Transparent Slider
        add_action( 'admin_footer', array(__CLASS__, 'select_transparent_slider'));
        //Select Arts 3 column sub-page layout
        add_action( 'admin_footer', array(__CLASS__, 'select_arts_subpage_layout'));
        
    }

    /*
     * This function includes the css and js for this specifc admin option
     *
     * @access public
     * @return void
     */
     function arts_faculty_ui(){
        wp_enqueue_style('arts-faculty-theme-option-style');
        wp_enqueue_script('arts-faculty-theme-option-script', array('jquery'));
     }


    /**
     * admin function.
     *
     * @access public
     * @return void
     */
    function arts_admin(){

        //Add Colour options
        add_settings_field(
                'arts-colours', // Unique identifier for the field for this section
                'Colour Options', // Setting field label
                array(__CLASS__,'arts_colour_options'), // Function that renders the settings field
                'theme_options', // Menu slug, used to uniquely identify the page; see ubc_collab_theme_options_add_page()
                'faculty-options' // Settings section. Same as the first argument in the add_settings_section() above
        );

        //Add Apply Now options
        add_settings_field(
                'arts-apply-now', // Unique identifier for the field for this section
                'Apply Now', // Setting field label
                array(__CLASS__,'arts_apply_now_options'), // Function that renders the settings field
                'theme_options', // Menu slug, used to uniquely identify the page; see ubc_collab_theme_options_add_page()
                'faculty-options' // Settings section. Same as the first argument in the add_settings_section() above
        );

         //Add Why-Unit options
        add_settings_field(
                'arts-why-unit', // Unique identifier for the field for this section
                'Why Unit?', // Setting field label
                array(__CLASS__,'arts_why_unit_options'), // Function that renders the settings field
                'theme_options', // Menu slug, used to uniquely identify the page; see ubc_collab_theme_options_add_page()
                'faculty-options' // Settings section. Same as the first argument in the add_settings_section() above
        );
        //Add Hardcoded list
        add_settings_field(
                'arts-hardcoded-options', // Unique identifier for the field for this section
                'Hardcoded Options', // Setting field label
                array(__CLASS__,'arts_hardcoded_options'), // Function that renders the settings field
                'theme_options', // Menu slug, used to uniquely identify the page; see ubc_collab_theme_options_add_page()
                'faculty-options' // Settings section. Same as the first argument in the add_settings_section() above
        );  
    }
    /**
     * arts_colour_options.
     * Display colour options for Arts specific template
     * @access public
     * @return void
     */
    function arts_colour_options(){ ?>


		<div class="explanation"><a href="#" class="explanation-help">Info</a>

			<div> These colours are specific to each unit and represent the colour of Arts logo, and pieces of the items throughout the site.</div>
		</div>
		<div id="arts-unit-colour-box">
			<label><b>Unit/Website Main Colour:</b></label>
			<div class="arts-colour-item"><span>(A) Main colour: </span><?php  UBC_Collab_Theme_Options::text( 'arts-main-colour' ); ?></div><br/>
                        <div class="arts-colour-item"><span>(B) Gradient colour: </span><?php  UBC_Collab_Theme_Options::text( 'arts-gradient-colour' ); ?></div><br/>
                        <div class="arts-colour-item"><span>(C) Hover colour: </span><?php  UBC_Collab_Theme_Options::text( 'arts-hover-colour' ); ?></div><br/>
                        <div class="arts-colour-item"><span>(D) Reverse colour: </span></div>
                        <ul>
                        <?php
                            foreach ( UBC_Arts_Theme_Options::arts_reverse_colour() as $option ) {
                                ?>
                                <li class="layout">
                                <?php UBC_Collab_Theme_Options::radio( 'arts-reverse-colour', $option['value'], $option['label']); ?>
                                </li>
                      <?php } ?>
                        </ul>
		</div>   <?php
    }
    
    	/**
	 * Returns and array of reverse colours
	 */
	function arts_reverse_colour() {
		$reverse_colour = array(
	        'white' => array(
	            'value' => 'white',
	            'label' => __( 'White', 'arts-clf' )
	        ),
	        'black' => array(
	            'value' => 'black',
	            'label' => __( 'Black', 'arts-clf' )
	        )
	    );
	   return $reverse_colour;
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
                'arts-main-colour'		=> '#5E869F',
                'arts-gradient-colour'		=> '#71a1bf',
                'arts-hover-colour'		=> '#002145',
                'arts-reverse-colour'		=> 'white',
                'arts-enable-why-unit'  => true,
                'arts-why-unit-text'    => 'Why Unit/Department?',
                'arts-why-unit-url'     => '#',
                'arts-enable-apply-now' => true,
                'arts-apply-now-text'   => 'Apply Now',
                'arts-apply-now-url'    => '#',
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
		$starter = UBC_Arts_Theme_Options::default_values( array() );


	    // Validate Unit Colour Options A, B, and C
            $starter['arts-main-colour'] = UBC_Collab_Theme_Options::validate_text($input['arts-main-colour'], $starter['arts-main-colour'] );
            $starter['arts-gradient-colour'] = UBC_Collab_Theme_Options::validate_text($input['arts-gradient-colour'], $starter['arts-gradient-colour'] );
            $starter['arts-hover-colour'] = UBC_Collab_Theme_Options::validate_text($input['arts-hover-colour'], $starter['arts-hover-colour'] );

            // Validate Unit Colour Options D
            if ( isset( $input['arts-reverse-colour'] ) && array_key_exists( $input['arts-reverse-colour'], UBC_Arts_Theme_Options::arts_reverse_colour() ) ) {
	        $starter['arts-reverse-colour'] = $input['arts-reverse-colour'];
	    }

            //Validate Why-unit options
            $starter['arts-enable-why-unit'] = (bool)$input['arts-enable-why-unit'];
            $starter['arts-why-unit-text']   = UBC_Collab_Theme_Options::validate_text($input['arts-why-unit-text'], $starter['arts-why-unit-text'] );
            $starter['arts-why-unit-url']     = UBC_Collab_Theme_Options::validate_text($input['arts-why-unit-url'], $starter['arts-why-unit-url'] );

            //Validate Why-unit options
            $starter['arts-enable-apply-now'] = (bool)$input['arts-enable-apply-now'];
            $starter['arts-apply-now-text']   = UBC_Collab_Theme_Options::validate_text($input['arts-apply-now-text'], $starter['arts-apply-now-text'] );
            $starter['arts-apply-now-url']     = UBC_Collab_Theme_Options::validate_text($input['arts-apply-now-url'], $starter['arts-apply-now-url'] );

            $output = array_merge($output, $starter);

            return $output;
        }


    /**
     * register_scripts function.
     *
     * Include the arts specific css in the header
     *
     * @access public
     * @return void
     */
    function register_scripts() {
    	self::$add_script = true;
		// register the spotlight functions
        if( !is_admin() ):
        	wp_enqueue_style('ubc-collab-arts', plugins_url('faculty-website').'/faculty/arts/css/style.css');
        endif;

	}
	/**
	 * print_script function.
	 *
	 * @access public
	 * @static
	 * @return void
	 */
	static function print_script() {
		if ( ! self::$add_script )
			return;

		wp_print_scripts( 'ubc-collab-arts' );
	}    
    /**
     * add_arts_logo_to_menu
     * Adds the Arts logo to primary menu
     * @access public
     * @return menu items
     */
      function add_arts_logo_to_menu ( $items, $args ) {
            if ($args->theme_location == 'primary') {
                $items = '<a id="artslogo" href="'.self::$faculty_main_homepage.'" title="Arts" target="_blank">&nbsp;</a>'.$items;
            }
            return $items;
       }

      /**
     * add_apply_now_to_menu
     * Adds the optional Apply Now button to the  primary menu
     * @access public
     * @return menu items
     */
        function add_apply_now_to_menu( $items, $args ){
            if ($args->theme_location == 'primary') {
                if(UBC_Collab_Theme_Options::get('arts-enable-apply-now')){
                    $items .= '<a id="applybtn" href="'.UBC_Collab_Theme_Options::get('arts-apply-now-url').'" title="Apply Now">'.UBC_Collab_Theme_Options::get('arts-apply-now-text').'</a>';
                }
            }
            return $items;
        }

        
    /**
     * arts_apply_now_options.
     * Display Apply Now options for Arts specific template
     * @access public
     * @return void
     */
    function arts_apply_now_options(){ ?>
            <div class="explanation"><a href="#" class="explanation-help">Info</a>

                    <div> An optional button to be appended to the main navigation menu that will link to the specified application page</div>
            </div>
            <div id="arts-apply-now-box">
                <label><b>Apply Now Options:</b></label>
                <div><?php UBC_Collab_Theme_Options::checkbox( 'arts-enable-apply-now', 1, 'Enable Apply-now botton' ); ?></div>
                <div class="half arts-apply-inputs"><?php UBC_Collab_Theme_Options::text('arts-apply-now-text', 'Botton text'); ?></div>
                <div class="half arts-apply-inputs"><?php UBC_Collab_Theme_Options::text('arts-apply-now-url', 'URL'); ?></div>
            </div>

    <?php
    }
    /**
     * arts_why_unit_options.
     * Display Why-Unit options for Arts specific template
     * @access public
     * @return void
     */
    function arts_why_unit_options(){ ?>
            <div class="explanation"><a href="#" class="explanation-help">Info</a>

                    <div> By enabling this option, a "why unit" bar will be attached to the slider that links to the specified page.</div>
            </div>
            <div id="arts-why-unit-box">
                <label><b>Why Unit/Website Options:</b></label>
                <div><?php UBC_Collab_Theme_Options::checkbox( 'arts-enable-why-unit', 1, 'Enable Why-Unit bar' ); ?></div>
                <div class="half arts-why-inputs"><?php UBC_Collab_Theme_Options::text('arts-why-unit-text', 'Label text'); ?></div>
                <div class="half arts-why-inputs"><?php UBC_Collab_Theme_Options::text('arts-why-unit-url', 'URL'); ?></div>
            </div>
        
    <?php
    }
    /**
     * arts_apply_now_options.
     * Display Apply Now options for Arts specific template
     * @access public
     * @return void
     */
    function arts_hardcoded_options(){ ?>
            <div class="explanation"><a href="#" class="explanation-help">Info</a>

                    <div> The following are the description of hardcoded items in the Arts sites.</div>
            </div>
            <div id="arts-hardcoded-box">
                <label><b>The following options are hardcoded:</b></label>
                <ol>
                    <li>Unit/Website Bar Background Colour: #6D6E70</li>
                    <li>Add Arts logo in the menu</li>
                    <li>Add Apply Now button in the menu, if selected</li>
                    <li>Load Arts frontpage layout.</li>
                    <li>Remove Slider Margin</li>
                    <li>Select Transparent Slider</li>
                    <li>Attach Why-Unit under slider, if selected (using jQuery for now. It will need to be added as a slider on Collab)</li>
                </ol>
            </div>

    <?php
    UBC_Arts_Theme_Options::arts_defaults();
    }
    //REVIEW THIS
    function arts_defaults(){
        UBC_Collab_Theme_Options::update('clf-unit-colour', '#6D6E70');
    }
    function arts_frontpage_layout(){
        UBC_Collab_Theme_Options::update('frontpage-layout', 'layout-option5');
        // apply the right width divs to the columns
        //remove_filter( 'ubc_collab_sidebar_class', array(__CLASS__, 'add_sidebar_class' ), 10, 2 );
        remove_filter('ubc_collab_sidebar_class', $sidebar_class,  'frontpage');
    add_filter( 'ubc_collab_sidebar_class', array(__CLASS__, 'add_sidebar_class' ), 10, 2 );
    }
    
    function remove_slider_margin(){
        UBC_Collab_Theme_Options::update('slider-remove-margin', 1);
    }
    
    function select_transparent_slider(){
        UBC_Collab_Theme_Options::update('slider-option', 'transparent');
    }

    function select_arts_subpage_layout(){
        UBC_Collab_Theme_Options::update('layout', 'l3-column-pms');
    }
    /**
     * wp_head
     * Appends some of the dynamic css and js to the wordpress header
     * @access public
     * @return void
     */
        function wp_head(){ ?>
        <style type="text/css" media="screen">
            .gradient-color{
                color: <?php echo UBC_Collab_Theme_Options::get('arts-gradient-colour')?>;
            }
            .main-color {
                color: <?php echo UBC_Collab_Theme_Options::get('arts-main-colour')?>;
            }
            .hover-color{
                color: <?php echo UBC_Collab_Theme_Options::get('arts-hover-colour')?>;
            }
            a#artslogo, .main-bg, #qlinks a, #primary-secondary{
                background-color:<?php echo UBC_Collab_Theme_Options::get('arts-main-colour')?>;
            }
            .gradient-bg{
                background-color:<?php echo UBC_Collab_Theme_Options::get('arts-gradient-colour')?>;
            }
            a{
                color: <?php echo UBC_Collab_Theme_Options::get('arts-main-colour')?>;
                text-decoration:none;
            }
            a:hover{
                color:<?php echo UBC_Collab_Theme_Options::get('arts-hover-colour')?>;
            }
            a#artslogo{
                background-image:url(<?php echo plugins_url('arts-website').(UBC_Collab_Theme_Options::get('arts-reverse-colour')=='white'? '/img/ArtsLogoTrans.png' : '/img/ArtsLogoTrans-black.png')?>);
            }

            #primary a, #primary-secondary a {
                color: <?php echo (UBC_Collab_Theme_Options::get('arts-reverse-colour')=='white'? '#FFFFFF' : '#002145')?>;
            }
            #primary a:hover, #primary-secondary a:hover {
                color: <?php echo (UBC_Collab_Theme_Options::get('arts-reverse-colour')=='black'? '#FFFFFF' : '#FFFFFF')?>;
            }
            #primary a.opened,
            #primary-secondary a.opened,
            #primary .accordion-heading:hover a,
            #primary-secondary .accordion-heading:hover a{
                color: <?php echo (UBC_Collab_Theme_Options::get('arts-reverse-colour')=='black'? '#FFFFFF' : '#FFFFFF')?>;
            }
            #primary .ubc7-arrow.right-arrow, #primary-secondary .ubc7-arrow.right-arrow {
                background-position: <?php echo (UBC_Collab_Theme_Options::get('arts-reverse-colour')=='white'? '-1113px -227px' : '-1113px -261px')?>;
            }
            #primary .ubc7-arrow.down-arrow, #primary-secondary .ubc7-arrow.down-arrow {
                background-position: <?php echo (UBC_Collab_Theme_Options::get('arts-reverse-colour')=='white'? '-1178px -227px' : '-1178px -261px')?>;
            }
            #primary .sidenav .opened .accordion-toggle .ubc7-arrow, #primary-secondary .sidenav .opened .accordion-toggle .ubc7-arrow {
                background-position: <?php echo (UBC_Collab_Theme_Options::get('arts-reverse-colour')=='white'? '-1207px -226px' : '-1206x -261px')?> !important;
            }
            #primary .sidenav .opened .right-arrow, #primary-secondary .sidenav .opened .right-arrow {
                background-position: <?php echo (UBC_Collab_Theme_Options::get('arts-reverse-colour')=='white'? '-1113px -227px' : '-1113px -227px')?> !important;
            }
            
            a#applybtn:hover, .hover-bg, #qlinks li a:hover {
                background-color: <?php echo UBC_Collab_Theme_Options::get('arts-hover-colour');?>;
            }
            a#applybtn {
                background-color:<?php echo UBC_Collab_Theme_Options::get('arts-main-colour');?> ;
            }
            body.home .nav-tabs > li > a{background-color:<?php echo UBC_Collab_Theme_Options::get('arts-main-colour');?>;}
            body.home .nav-tabs > .active > a, .nav-tabs > .active > a:hover{background-color:<?php echo UBC_Collab_Theme_Options::get('arts-gradient-colour');?>;border:none;}
            body.home .nav-tabs > li > a:hover{background-color:<?php echo UBC_Collab_Theme_Options::get('arts-gradient-colour');?>;}
            .transparent .carousel-caption{
                background-color:<?php echo UBC_Collab_Theme_Options::get('arts-main-colour');?>;
                border:2px solid <?php echo UBC_Collab_Theme_Options::get('arts-gradient-colour');?>;
            }
            /*sidenav*/
            /*color*/
            .sidenav .accordion-inner a ,.sidenav.accordion{
                border-bottom: 1px solid <?php echo UBC_Collab_Theme_Options::get('arts-gradient-colour');?>;
            }
            div.sidenav a.opened{
                background:none <?php echo UBC_Collab_Theme_Options::get('arts-hover-colour');?>;
            }
            .sidenav div.single a, .supages-navi-level-0 a, .accordion-inner{
                border-top:1px solid <?php echo UBC_Collab_Theme_Options::get('arts-gradient-colour');?>;
            }
            .sidenav .accordion-inner{
                background-color: <?php echo UBC_Collab_Theme_Options::get('arts-gradient-colour');?>;
            }
            div.sidenav div.single, div.accordion-group{
                background-color:<?php echo UBC_Collab_Theme_Options::get('arts-main-colour');?>;
            }
            .accordion-heading .accordion-toggle {
                border-left: 1px solid <?php echo UBC_Collab_Theme_Options::get('arts-gradient-colour');?> !important;
            }
            /*end sidenav*/
            @media(max-width:980px){
                a#artslogo{
                    background-image:url(<?php echo plugins_url('arts-website').(UBC_Collab_Theme_Options::get('arts-reverse-colour')=='white'? '/img/FOA_FullLogo.png' : '/img/FOA_FullLogo-black.png')?>);
                }
            }
        </style>
    <?php
    } 


    /**
     * wp_footer
     * Appends some of the dynamic js to the wordpress header
     * @access public
     * @return void
    */
    function wp_footer(){
         if( is_front_page() && UBC_Collab_Theme_Options::get('arts-enable-why-unit')):
            ?>
            <script type="text/javascript">
                jQuery(document).ready(function($) {
                    $('div.flexslider').append('<div id="shadow"></div>');
                    $('div.flexslider').append('<a id="why-unit" href="<?php echo UBC_Collab_Theme_Options::get('arts-why-unit-url');?>" title="<?php echo UBC_Collab_Theme_Options::get('arts-why-unit-text');?>"><span><?php echo UBC_Collab_Theme_Options::get('arts-why-unit-text');?></span></a>');

                 });
                 jQuery(document).ready(function($) {
                    $( "div.when" ).each(function( index ) {
                       var datestr = $(this).html();
                       datestr = datestr.substr(0,datestr.indexOf(':')-2).trim(); //rid of second date and time
                       if (datestr) $(this).html(datestr);
                     });
                     $('div.section-widget-tabbed').css('display','block'); //handles screen lag
                 });
            </script>
        <?php endif;
    }
}


UBC_Arts_Theme_Options::init();

//var_dump( get_option( 'ubc-collab-theme-options' ));
