<?php
class WorkPage{

  function __construct()
  {
            add_shortcode( 'opp', array($this,'portfolio_page_shortcode'));
            add_action('wp_enqueue_scripts', array($this, 'load_js'));
            add_action( 'wp_enqueue_scripts', array($this,'portfolio_stylesheet') );
          //  add_action('wp_enqueue_scripts', array($this, 'load_isotope_middle_js'));
           // add_action('wp_enqueue_scripts', array($this, 'load_isotope_setting_js'));
            add_action( 'wp_footer', array($this,'load_masonry_middle_js') );
            add_action( 'wp_footer', array($this,'load_masonry_setting_js') );
            //add_action( 'wp_footer', array($this,'load_masonry_css') );
          
  }


  function portfolio_stylesheet() {
    wp_enqueue_style( 'portfoliostyle', plugins_url( 'css/one-page-portfolio.css', dirname(__FILE__) ) );
  }

  function load_masonry_css() {

    $options = get_option('opp_portfolio_options');

    $columnWidth = "150px;";
    $thumbnail_size = $options['thumbnail_size'];
    
    if (!strcasecmp($thumbnail_size,'medium'))
      $columnWidth = "300px;";

     if (!strcasecmp($thumbnail_size,'large'))
      $columnWidth = "640px;";

    ?>
    <style type="text/css">

       .opp-work-content {

          width: <?php echo $columnWidth; ?>
        }
    </style>

    <?php
  }


  function load_js(){


         wp_enqueue_script('jquery');
        
         wp_enqueue_script('opp-masonry',plugins_url('js/jquery.masonry.min.js', dirname(__FILE__)),array('jquery'),'3.5.1.1');
         wp_enqueue_script('imagesloaded',plugins_url('js/jquery.imagesloaded.min.js', dirname(__FILE__)));


  }
  function load_masonry_middle_js(){  ?>


     <script type="text/javascript">

     		jQuery(function ($) {
    /* You can safely use $ in this code block to reference jQuery */

$.Massonry.prototype._getCenteredMasonryColumns = function() {

    this.width = this.element.width();

    var parentWidth = this.element.parent().width();

    var colW = this.options.masonry && this.options.masonry.columnWidth || // i.e. options.masonry && options.masonry.columnWidth

    this.$filteredAtoms.outerWidth(true) || // or use the size of the first item

    parentWidth; // if there's no items, use size of container

    var cols = Math.floor(parentWidth / colW);

    cols = Math.max(cols, 1);

    this.masonry.cols = cols; // i.e. this.masonry.cols = ....
    this.masonry.columnWidth = colW; // i.e. this.masonry.columnWidth = ...
};

$.Massonry.prototype._masonryReset = function() {

    this.masonry = {}; // layout-specific props
    this._getCenteredMasonryColumns(); // FIXME shouldn't have to call this again

    var i = this.masonry.cols;

    this.masonry.colYs = [];
        while (i--) {
        this.masonry.colYs.push(0);
    }
};

$.Massonry.prototype._masonryResizeChanged = function() {

    var prevColCount = this.masonry.cols;

    this._getCenteredMasonryColumns(); // get updated colCount
    return (this.masonry.cols !== prevColCount);
};

$.Massonry.prototype._masonryGetContainerSize = function() {

    var unusedCols = 0,

    i = this.masonry.cols;
        while (--i) { // count unused columns
        if (this.masonry.colYs[i] !== 0) {
            break;
        }
        unusedCols++;
    }

    return {
        height: Math.max.apply(Math, this.masonry.colYs),
        width: (this.masonry.cols - unusedCols) * this.masonry.columnWidth // fit container to columns that have been used;
    };
};

<?php

 $options = get_option('opp_portfolio_options');

if($options['desc_display_current'])
{
  ?>

$('.expand').click(function () {
if ($(this).is('.expanded')) {
restoreBoxes();
} else {
var size = ($(this).attr('data-size')) ? $(this).attr('data-size').split(',') : defaultSize;
$(".description").css({"height":size[1]});


    $(".description").css({"float":"right","height":size[1]}); 
  

$(this)
// save original box size
.data('size', [$(this).width(), $(this).height()]).animate({
width: size[0],
height: size[1]
}, function () {
// show hidden content when box has expanded completely
$(this).find('.expandable').show('normal')
$(this).find('.hideable').hide('normal');
$('#opp-portfolio').massonry();
});
restoreBoxes();
$(this).addClass('expanded');
}
function restoreBoxes() {
var len = $('.expanded').length - 1;
$('.expanded').each(function (i) {
var box = $(this).data('size');
$(this).find('.expandable').hide('normal');
$(this).find('.hideable').show('normal');
$(this).animate({
width: (box[0] || 100),
height: (box[1] || 'auto')
}, function () {
if (i >= len) {
$('#opp-portfolio').massonry();
}
}).removeClass('expanded');
})
}
});

<?php
}
?>





});


     </script>

  <?php
  }
  



  function load_masonry_setting_js(){  

    $options = get_option('opp_portfolio_options');

    $columnWidth = "160";
    $thumbnail_size = $options['thumbnail_size'];
    
    if (!strcasecmp($thumbnail_size,'medium'))
      $columnWidth = "310";

     if (!strcasecmp($thumbnail_size,'large'))
      $columnWidth = "650";



    ?>


     <script type="text/javascript">

          jQuery(function ($) {
    /* You can safely use $ in this code block to reference jQuery */

var $work = $('#opp-portfolio');

$work.imagesLoaded( function(){
$work.massonry({
   
  
   
  masonry: {
    columnWidth: <?php echo $columnWidth; ?>
  }
  
  

});

});

// filter items when filter link is clicked
$('#opp-filters a').click(function(){
 // $('#opp-filters a').not($(this)).removeClass("opp-active");
  //$(this).addClass("opp-active");
  $('#opp-filters a').not($(this)).removeAttr("id");
  $(this).attr("id","opp-active");
  var selector = $(this).attr('data-filter').toLowerCase();

  $work.massonry({ filter: selector });
  return false;
});

});


     </script>

  <?php
  }

  function portfolio_page_shortcode() {

    /*
	$output = '<p>
	              Hello World!
	           </p>';

	return $output;
	*/
	
	$this->display_work();
}



function removenontag($v){

        if (strpos($v,"tag")!==false )
        {

            return true;
        }

    return false;

}




function css_class_tag_filter($classes){

    

    $tag_class = array_filter($classes, array($this,'removenontag'));

    $tag_class_remove_blank = str_ireplace(" ","-",$tag_class);   
    
   
    return str_ireplace("tag-","opp-",$tag_class_remove_blank);
}





         

  function filter_tag() {

    ?>

     <div id="opp-portfolio-filters">
     <ul id="opp-filters" class="nav nav-pills">

       <li><a href="#" data-filter="*" id="opp-active">show all</a></li>
     <?php
        
        $options = get_option('opp_portfolio_options');

        $filter_options = $options['tag_filter_select'];
        foreach($filter_options as $filter_option)
        {

               $filter_option_remove_blank = str_ireplace(" ","-",$filter_option);   

               echo '<li><a href="#" data-filter=".'.$filter_option_remove_blank.'">'.substr_replace($filter_option,"",0,4).'</a></li>';

        }
        
     ?>   

  <!-- 
  <li><a href="#" data-filter=".test">test</a></li>
  <li><a href="#" data-filter=".test3">test3</a></li>
  -->


     </ul>
     </div>
     <br>

<?php
  }



  function display_work() {

    $options = get_option('opp_portfolio_options');

   // var_dump($options);


      
       

   // if($options['tag_filter'])
      if($options['tag_filter_select'])
        $this->filter_tag();


    echo "<div id=\"opp-portfolio\">\n"; 
    
    $query = array(
      'posts_per_page' => '-1',
    	'tax_query' => array(
    		array(
    			     'taxonomy' => 'category',
    			     'field' => 'slug',
    			     'terms' =>  $options['category']
    			 )
    		)
    	);

    $the_query = new WP_Query($query);

    
    
    while($the_query->have_posts()){

                  if($options['desc_display_current'])
{

         add_filter('post_class',array($this,'css_class_tag_filter'));
         $the_query->the_post();


          $tags = wp_get_post_tags($the_query->post->ID);

         $class = 'opp-work-content expand';
         foreach($tags as $tag)
         {
            $class = $class . ' opp-' . str_ireplace(" ","-",$tag->name);
         }

         $class = strtolower($class);
         
         //$class = 'opp-work-content expand ' . implode(' ', get_post_class());

       
        $image_data = wp_get_attachment_image_src( get_post_thumbnail_id( $the_query->post->ID ), "medium" );

        $image_width = (int)$image_data[1] + 300;
        $image_height = (int)$image_data[2];

     
        
         echo "<div class=\"$class\" data-size=\"$image_width,$image_height\">\n";
         
          

          
         //echo $the_query->post->post_content;
         ?>
         <div class="hideable">
          <?php
        // echo '<a href="' . get_permalink($post->ID) . '" >';
         echo get_the_post_thumbnail($the_query->post->ID, $options['thumbnail_size']);
         if($options['post_title_display'])
         echo '<h1 class="headline">' . $the_query->post->post_title . '</h1>';
        // echo  '</a>';
         ?>
       </div>
       <?php
         /*
         if ($the_query->post->post_excerpt) {
         echo '<div class="post_excerpt">' . $the_query->post->post_excerpt . '</div>';
         }
         */
         ?>

         <div class="expandable" style="display: none;">
           <?php 

           
           echo get_the_post_thumbnail($the_query->post->ID, 'medium'); 
           
          

            ?>
            <span class="description">
          <?php
            echo preg_replace('/<img[^>]+./', '', $the_query->post->post_content);
          ?>

        </span>

          
      
      </div>

         <?php



         echo '</div>';
        


     }
     else{



         add_filter('post_class',array($this,'css_class_tag_filter'));
         $the_query->the_post();
         
         $tags = wp_get_post_tags($the_query->post->ID);

         $class = 'opp-work-content';
         foreach($tags as $tag)
         {
            $class = $class . ' opp-' . str_ireplace(" ","-",$tag->name);
         }

         $class = strtolower($class);
         //$class = 'opp-work-content ' . implode(' ', get_post_class());

         

        
         echo "<div class=\"$class\">\n";
         
         
         //echo $the_query->post->post_content;
         echo '<a href="' . get_permalink($post->ID) . '" >';
         echo get_the_post_thumbnail($the_query->post->ID, $options['thumbnail_size']);
         if($options['post_title_display'])
         echo '<h1 class="headline">' . $the_query->post->post_title . '</h1>';
         echo  '</a>';
         
         if ($the_query->post->post_excerpt) {
         echo '<div class="post_excerpt">' . $the_query->post->post_excerpt . '</div>';
         }
         echo '</div>';
        



     }
        

    }
    
     echo '</div>';


    
     
     
   }
  }
  




$workpage = new Workpage();

?>