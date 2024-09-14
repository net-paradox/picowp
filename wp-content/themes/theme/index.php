<?php

get_template_part( 'template-parts/header');    

if( function_exists('kama_breadcrumbs') ) kama_breadcrumbs('');

if ( have_posts() ) :  
    while ( have_posts() ) : the_post();
        $h1_meta = get_post_meta( get_the_ID(), 'h1', true );
        $h1 = (!empty($h1_meta)) ? $h1_meta : get_the_title();
    ?>    
        <h1><?php echo $h1; ?></h1>
        <?php the_content(); ?>               
    <?        
    endwhile;
    
    the_posts_navigation();
endif;	     


get_template_part( 'template-parts/footer');