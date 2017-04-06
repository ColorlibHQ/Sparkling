<?php

/**
 * Social  Widget
 * Sparkling Theme
 */
class sparkling_social_widget extends WP_Widget
{
	 function __construct(){
            
            $widget_ops = array('classname' => 'sparkling-social','description' => esc_html__( "Sparkling Social Widget" ,'sparkling') );
            parent::__construct('sparkling-social', esc_html__('Sparkling Social Widget','sparkling'), $widget_ops);
            
    }

    function widget($args , $instance) {
    	extract($args);
        $title = isset($instance['title']) ? $instance['title'] : esc_html__('Follow us' , 'sparkling');

        echo $before_widget;
        echo $before_title;
        echo $title;
        echo $after_title;

        /**
         * Widget Content
         */ ?>

        <!-- social icons -->
        <div class="social-icons sticky-sidebar-social">

            <?php sparkling_social_icons(); ?>

        </div><!-- end social icons --><?php

        echo $after_widget;
    }

    function form($instance) {
      if(!isset($instance['title'])) $instance['title'] = esc_html__('Follow us' , 'sparkling'); ?>

      <p><label for="<?php echo $this->get_field_id('title'); ?>"><?php esc_html_e('Title ','sparkling') ?></label>

      <input type="text" value="<?php echo esc_attr($instance['title']); ?>"
                          name="<?php echo $this->get_field_name('title'); ?>"
                          id="<?php $this->get_field_id('title'); ?>"
                          class="widefat" />
      </p><?php
    }

}
?>