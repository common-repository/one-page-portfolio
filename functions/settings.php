<?php
class PortfolioPluginSettings  {
	
	
	function __construct() {

		
		add_action('admin_menu', array($this,'addOptionsPage'));

  
		
		
	}
	

	
	function addOptionsPage() {
		add_options_page('My Portfolio Setting Page', 'One Page Portfolio', 'manage_options', 'page-pp', array($this,'optionsPage'));
	    add_action('admin_init', array($this,'portfolio_admin_init'));

 
	}
	
	function optionsPage() { ?>

   <h2> My portfolio Setting </h2>
		<div id="poststuff" class="metabox-holder has-right-sidebar">
    <div id="side-info-column" class="inner-sidebar">
     <div id="side-sortables" class="meta-box-sortables ui-sortable">
   <div class="postbox wrap">
   
    <h3 class="hndle opp-hndle">Subscribe by Email</h3>
    <div class="inside">
        <p> Subscribe to get notified of important updates and news
                  for our One Page Portfolio plugins </p>
<a href="http://eepurl.com/vCFVP" target="_blank" class="external">Get Updates</a>
   </div>
    </div>
  </div>
   </div>
   <div id="post-body">
    <div id="post-body-content">
   <div class="meta-box-sortables ui-sortable">
		<div id = "portfolio-general"  class="wrap">
            
             <form name = "portfolio_options_from_setting_api" method="post" action="options.php">
              <div class="postbox">
             
              <?php settings_fields('portfolio_setting'); ?>

              <?php do_settings_sections('portfolio_setting_section'); ?>
              </div>
         

           <input type="submit" value="Submit" class="button-primary" />
             </form>	


        </div> 
     </div>
   </div>
 </div>
        <br>

	</div>
		<?php
	}


  
	
  function my_list_cats() {
  $cats = get_categories();
  foreach($cats as $cat) {
      $catsArray[] = $cat->category_nicename ;
  }
  return $catsArray;
}

   function portfolio_admin_init(){

		  register_setting('portfolio_setting','opp_portfolio_options',array($this,'portfolio_validate_options'));

		  add_settings_section('portfolio_main_section','Main Setting',array($this, 'portfolio_main_setting_section_callback'),'portfolio_setting_section');

          add_settings_field('category_choose', 'Choose works category', array($this,'category_select_list'),'portfolio_setting_section','portfolio_main_section',
            array('name' => 'category','choices' => $this->my_list_cats())
            );
          //add_settings_field('sidebar_display','Sidebar Display', array($this,'sidebar_display_check_box'),'portfolio_setting_section','portfolio_main_section',array('name' => 'sidebar_display'));
          
          //add_settings_field('tag_filter', 'Tag Filter', array($this,'tag_filter_check_box'),'portfolio_setting_section','portfolio_main_section',array('name' => 'tag_filter'));
          
          add_settings_field('tag_filter_select','Select tags to filter works',array($this, 'tag_filter_select_check_box'),
            'portfolio_setting_section','portfolio_main_section');
          add_settings_field('thumbnail_sizes_choose','Choose thumbnail sizes',array($this, 'thumbnail_sizes_list'),
            'portfolio_setting_section','portfolio_main_section',
            array('name' => 'thumbnail_size','choices' => array("thumbnail","medium","large","full"))
            );
          add_settings_field('post_title_display','Display post title or not', array($this,'post_title_display_check_box'),'portfolio_setting_section','portfolio_main_section',array('name' => 'post_title_display'));
          
          add_settings_field('desc_display_current','Display description or not', array($this,'desc_display_current_check_box'),'portfolio_setting_section','portfolio_main_section',array('name' => 'desc_display_current'));
	}

  function desc_display_current_check_box( $data = array() ){
        extract ($data);

        $options = get_option( 'opp_portfolio_options');
          
       

         
        ?>
          <input type="checkbox" name="opp_portfolio_options[<?php echo $name;  ?>]"
           <?php if ($options[$name]) echo ' checked="checked"'; ?>>
          </input>
    <?php
       }

  function tag_filter_check_box( $data = array()) {

         extract ($data);

        $options = get_option( 'opp_portfolio_options');
          
          
        ?>
          <input type="checkbox" name="opp_portfolio_options[<?php echo $name;  ?>]"
           <?php if ($options[$name]) echo ' checked="checked"'; ?>>
          </input>
    <?php
       }
 
 function tag_filter_select_check_box($data = array() ) {

       extract($data);
       $options = get_option('opp_portfolio_options');

      
       
       

       $args = $options['category'];
       $tags = $this->get_category_tags($args);
      
      

       

      // $tags = get_tags();



       

       $html = ''; 
       $pag = 'tag_filter_select';
       
       foreach( $tags as $tag)
       {
            
          $checked = in_array('opp-'.$tag->tag_name,  (array) $options['tag_filter_select']) ? 'checked="checked"' : '';

            
          $html .= sprintf('<input type="checkbox" id="tag_filter_select[%1$s]" name="opp_portfolio_options[%3$s][]" 
             value="%1$s" %2$s />', 'opp-'.$tag->tag_name, $checked, $pag);
          $html .= sprintf('<label for="tag_filter_select[%1$s]"> %1$s </label>', $tag->tag_name);
            
         

           

        

       }
        
        echo $html;
 }

 function get_category_tags($args) {
  global $wpdb;
  $tp = $wpdb->prefix;
  $tags = $wpdb->get_results
  ("
    SELECT DISTINCT terms2.term_id as tag_id, terms2.name as tag_name, null as tag_link
    FROM
      ${tp}posts as p1
      LEFT JOIN ${tp}term_relationships as r1 ON p1.ID = r1.object_ID
      LEFT JOIN ${tp}term_taxonomy as t1 ON r1.term_taxonomy_id = t1.term_taxonomy_id
      LEFT JOIN ${tp}terms as terms1 ON t1.term_id = terms1.term_id,

      ${tp}posts as p2
      LEFT JOIN ${tp}term_relationships as r2 ON p2.ID = r2.object_ID
      LEFT JOIN ${tp}term_taxonomy as t2 ON r2.term_taxonomy_id = t2.term_taxonomy_id
      LEFT JOIN ${tp}terms as terms2 ON t2.term_id = terms2.term_id
    WHERE
      t1.taxonomy = 'category' AND p1.post_status = 'publish' AND terms1.name = '$args' AND
      t2.taxonomy = 'post_tag' AND p2.post_status = 'publish'
      AND p1.ID = p2.ID
    ORDER by tag_name
  ");

  
  $count = 0;
  foreach ($tags as $tag) {
    $tags[$count]->tag_link = get_tag_link($tag->tag_id);
    $count++;
  }
  return $tags;
}

 function thumbnail_sizes_list( $data = array() ) {

                extract($data);
                $options = get_option('opp_portfolio_options');

                ?>
                <select name="opp_portfolio_options[<?php echo $name; ?>]">  
        <?php foreach( $choices as $item ) { ?>
        <option value="<?php echo $item; ?>" 
        <?php selected( $options[$name] == $item ); ?>>
        <?php echo $item; ?></option>
    <?php } ?>
  </select>  

 <?php }
 
 function category_select_list( $data = array() ){


                 extract ($data);

               
                 $options = get_option('opp_portfolio_options');
                 
                
                 
                 ?>

            <select name="opp_portfolio_options[<?php echo $name; ?>]">  
        <?php foreach( $choices as $item ) { ?>
        <option value="<?php echo $item; ?>" 
        <?php selected( $options[$name] == $item ); ?>>
        <?php echo $item; ?></option>
    <?php } ?>
  </select>  
	<?php }   
             

     function post_title_display_check_box( $data = array() ){
        extract ($data);

        $options = get_option( 'opp_portfolio_options');
          
       

         
        ?>
          <input type="checkbox" name="opp_portfolio_options[<?php echo $name;  ?>]"
           <?php if ($options[$name]) echo ' checked="checked"'; ?>>
          </input>
    <?php
       }
	
	
    function sidebar_display_check_box( $data = array() ){
    	  extract ($data);

    	  $options = get_option( 'opp_portfolio_options');
          
       

         
    	  ?>
          <input type="checkbox" name="opp_portfolio_options[<?php echo $name;  ?>]"
           <?php if ($options[$name]) echo ' checked="checked"'; ?>>
          </input>
    <?php
       }




	function portfolio_validate_options( $input ){

		//$input['sidebar_display'] = true;

		return $input;


	}

	function portfolio_main_setting_section_callback() { ?>
   

 
	<?php
     }
   
	
	
}
$cptSettings  = new PortfolioPluginSettings();
?>