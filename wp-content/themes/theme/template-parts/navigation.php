<?php if ( has_nav_menu( 'nav-menu' ) ) : ?>
<nav class="nav container">    
    <?php 
        wp_nav_menu( [
                    'theme_location'  => 'nav-menu',
                    'walker'          => new BEM_Walker_Nav_Menu(),
                    'bem_block'      => 'nav',
                    'items_wrap'     => '
                        <ul>                        
                        %3$s
                        </ul>
                    '
        ] );
    ?>
</nav>
<?php endif; ?>
