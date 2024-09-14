<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">    
	<?php 
		wp_head();
	?>
	</head>

	<body <?php body_class(); ?>>
	<?php 
		wp_body_open();
	?>

	<!-- Header -->
    <header class="container">      
      <nav>
        <ul>
            <li><img src="<?php echo esc_url( get_site_icon_url(32) ) ?>" alt=""></li>  
            <li><strong><?php bloginfo('name'); ?></strong></li>
            <li><small><?php bloginfo('description'); ?></small></li>
        </ul>
        <ul>
            <?php
            wp_nav_menu( array( 
                'theme_location' => 'nav-menu', 
                'container'       => false,  
                'menu_class'      => '',
                'items_wrap'      => '%3$s'
            ) ); 
            ?>
            <li style="text-align: center;">                
                <a href="" style="display: block;">8-961-855-43-99</a>
                <small>10.00-19.00, пн-пт</small>
            </li>
            <li><button class="secondary">Заказать звонок</button></li>
        </ul>
      </nav>
    </header>
	<?php // get_template_part( 'template-parts/navigation'); ?>
    <!-- ./ Header -->

    <!-- Main -->
    <main class="container">