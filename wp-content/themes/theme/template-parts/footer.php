	</main>
    <!-- ./ Main -->

    <!-- Footer -->
    <footer class="container">
		<nav>
			<ul>
				<li><small>&copy; <?php bloginfo('description'); ?></small></li>				
			</ul>
			<?php
			wp_nav_menu( array( 
				'theme_location' => 'footer-menu', 
				'container'       => false,  
				'menu_class'      => '',
				'items_wrap'      => '<ul>%3$s</ul>'
			) ); 
			?>
		</nav>
    </footer>
    <!-- ./ Footer -->

    <?php wp_footer(); ?>
</body>
</html>
<?php		
    global $_SITE, $_PAGE, $slug; 
    if(isset($_GET['d'])) {
        print_r($_SITE);
	    print_r($_PAGE);
	    print($slug);
    }
?>