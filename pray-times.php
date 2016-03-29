<?php
/*
Plugin Name: Pray Times
Description: Muslim Pray Times
Author: Ibrahim Mohamed Abotaleb
Version: 1.0
Author URI: http://mrkindy.com/
Text Domain: pray-times
Domain Path: /languages
*/
include('libs/PrayTime.php');
// Creating the widget
class pray_times extends WP_Widget
{
    function __construct()
    {
        parent::__construct( // Base ID of your widget
            'pray_times', // Widget name will appear in UI
            __('Muslim Pray Times', 'pray-times'), // Widget description
            array('description' => __('Muslim Pray Times Table .','pray-times'))
            );
    }
    // Creating widget front-end
    // This is where the action happens
    public function widget($args, $instance)
    {
        $title = apply_filters('widget_title', $instance['title']);
        // before and after widget arguments are defined by themes
        echo $args['before_widget'];
        if(! empty($title))
            echo str_replace('fa-bars','fa-moon-o',$args['before_title']) . $title . $args['after_title'];
        // This is where you run the code and display the output
        if($instance['latitude']&& $instance['longitude'])
        {
        	$prayTime = new PrayTime($instance['C_Methods']);
            $date = mktime(0,0,0,date('m'),date('d'),date('Y'));
        	$prayTime->setTimeFormat($instance['Time_Formats']);
        	$times = $prayTime->getPrayerTimes($date, $instance['longitude'], $instance['latitude'], get_option('gmt_offset'));
            
            require plugin_dir_path( __FILE__ ) . 'pray-times-view.php';
        }else{
            _e( 'Please Set Latitude and Longitude' , 'pray-times');
        }
        echo $args['after_widget'];
    }
    // Widget Backend
    public function form($instance)
    {
        if(isset($instance['title']))
        {
            $title = $instance['title'];
        }
        else
        {
            $title = __('Pray Time', 'pray-times');
        }
        if(isset($instance['longitude']))
        {
            $longitude = $instance['longitude'];
        }else{
            $longitude = '30.2';
        }
        if(isset($instance['latitude']))
        {
            $latitude = $instance['latitude'];
        }else{
            $latitude = '31.21';
        }
        if(isset($instance['C_Methods']))
        {
            $C_Methods = $instance['C_Methods'];
        }else{
            $C_Methods = 5;
        }
        if(isset($instance['Time_Formats']))
        {
            $Time_Formats = $instance['Time_Formats'];
        }else{
            $Time_Formats = 1;
        }
        // Widget admin form
        $Methods = array(0=>'Ithna Ashari',1=>'University of Islamic Sciences, Karachi',2=>'Islamic Society of North America (ISNA)',
        3=>'Muslim World League (MWL)',4=>'Umm al-Qura, Makkah',5=>'Egyptian General Authority of Survey');
        
        $T = array(0=>'24-hour format',1=>'12-hour format',2=>'12-hour format with no suffix',3=>'floating point number');
        ?>
        <p>
        <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'title :' , 'pray-times'); ?></label> 
        <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
        </p>
        <p>
        <label for="<?php echo $this->get_field_id( 'longitude' ); ?>"><?php _e( 'longitude :' , 'pray-times'); ?></label> 
        <input class="widefat" id="<?php echo $this->get_field_id( 'longitude' ); ?>" name="<?php echo $this->get_field_name( 'longitude' ); ?>" type="text" value="<?php echo esc_attr( $longitude ); ?>" />
        </p>
        <p>
        <label for="<?php echo $this->get_field_id( 'latitude' ); ?>"><?php _e( 'latitude :' , 'pray-times'); ?></label> 
        <input class="widefat" id="<?php echo $this->get_field_id( 'latitude' ); ?>" name="<?php echo $this->get_field_name( 'latitude' ); ?>" type="text" value="<?php echo esc_attr( $latitude ); ?>" />
        </p>
        <p>
        <label for="<?php echo $this->get_field_id( 'C_Methods' ); ?>"><?php _e( 'Method :' , 'pray-times'); ?></label> 
            <select class="widefat" id="<?php echo $this->get_field_id( 'C_Methods' ); ?>" name="<?php echo $this->get_field_name( 'C_Methods' ); ?>">
                <?php foreach($Methods as $code=>$m){?>
                    <?php if($C_Methods!=$code){?>
                    <option value="<?=$code?>"><?=$m?></option>
                    <?php }else{?>
                    <option selected="selected" value="<?=$code?>"><?=$m?></option>
                    <?php }?>
                <?php }?>
            </select>
        </p>
        <p>
        <label for="<?php echo $this->get_field_id( 'Time_Formats' ); ?>"><?php _e( 'Time Format :' , 'pray-times'); ?></label> 
            <select class="widefat" id="<?php echo $this->get_field_id( 'Time_Formats' ); ?>" name="<?php echo $this->get_field_name( 'Time_Formats' ); ?>">
                <?php foreach($T as $code=>$times){?>
                    <?php if($Time_Formats!=$code){?>
                    <option value="<?=$code?>"><?=$times?></option>
                    <?php }else{?>
                    <option selected="selected" value="<?=$code?>"><?=$times?></option>
                    <?php }?>
                <?php }?>
            </select>
        </p>
        <?php 
    }
    // Updating widget replacing old instances with new
    public function update($new_instance, $old_instance)
    {
        delete_transient( 'pray_times' );
        $instance = array();
        $instance['title'] = (! empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
        $instance['longitude'] = (! empty($new_instance['longitude'])) ? strip_tags($new_instance['longitude']) : '';
        $instance['latitude'] = (! empty($new_instance['latitude'])) ? strip_tags($new_instance['latitude']) : '';
        $instance['C_Methods'] = (! empty($new_instance['C_Methods'])) ? strip_tags($new_instance['C_Methods']) : '';
        $instance['Time_Formats'] = (! empty($new_instance['Time_Formats'])) ? strip_tags($new_instance['Time_Formats']) : '';
        return $instance;
    }
} // Class pray_times ends here
// Register and load the widget
function pray_times_load_widget()
{
    register_widget('pray_times');
}
add_action('widgets_init', 'pray_times_load_widget');

function pray_times_style() {
	wp_enqueue_style( 'pray-times-style', plugins_url( 'css/style.css', dirname(__FILE__) ));
}

add_action( 'wp_enqueue_scripts', 'pray_times_style' );
