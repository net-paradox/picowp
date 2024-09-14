<?php
class BEM_Walker_Nav_Menu extends Walker_Nav_Menu {

  public function start_lvl( &$output, $depth = 0, $args = null ) {
    $block = isset( $args->bem_block ) ? $args->bem_block : explode( ' ', $args->menu_class );
    $block = is_array( $block ) ? $block[0] : $block;
    if ( isset( $args->item_spacing ) && 'discard' === $args->item_spacing ) {
      $t = '';
      $n = '';
    } else {
      $t = "\t";
      $n = "\n";
    }
    $indent = str_repeat( $t, $depth );

    // sub menu classes
    $submenu_classes = array( $block . '-menu', $block . '-menu--level-' . ( $depth + 1 ) );
    $submenu_classes = implode( '  ', array_filter( $submenu_classes ) );

    $output .= "{$n}{$indent}<ul class=\"$submenu_classes\">{$n}";
  }

  public function end_lvl( &$output, $depth = 0, $args = null ) {
    if ( isset( $args->item_spacing ) && 'discard' === $args->item_spacing ) {
      $t = '';
      $n = '';
    } else {
      $t = "\t";
      $n = "\n";
    }
    $indent  = str_repeat( $t, $depth );
    $output .= "$indent</ul>{$n}";
  }

  public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
    $block = isset( $args->bem_block ) ? $args->bem_block : explode( ' ', $args->menu_class );
    $block = is_array( $block ) ? $block[0] : $block;
    if ( isset( $args->item_spacing ) && 'discard' === $args->item_spacing ) {
      $t = '';
      $n = '';
    } else {
      $t = "\t";
      $n = "\n";
    }
    $indent = ( $depth ) ? str_repeat( $t, $depth ) : '';

    // list item
    $item_classes = array( $block . '-item' );
    if ( in_array('menu-item-has-children', $item->classes) ){
      $item_classes[] = 'dropdown-item';
    }
    if ( $item->current ) {
      $item_classes[] = $block . '-item--active';
    }
    if ( $item->current_item_ancestor ) {
      $item_classes[] = $block . '-item--active-ancestor';
    }
    if ( $item->current_item_parent ) {
      $item_classes[] = $block . '-item--active-parent';
    }
    $classes = get_post_meta( $item->ID, '_menu_item_classes' );
    foreach ( $classes[0] as $class ) {
      if ( $class ) {
        $item_classes[] = $block . '-item--' . $class;
      }
    }
    // if ( is_single() ) {
    //   $category = get_the_category();
    //   $cat_id = $category[0]->cat_ID;
    //   $ancestors = get_ancestors( $cat_id, 'category' );
    //   if ( in_array( $item->object_id, $ancestors ) ) {
    //     $item_classes[] = $block . '__item--active-ancestor';
    //   }
    // }
    $item_classes = implode( '  ', array_filter( $item_classes ) );

    $args = apply_filters( 'nav_menu_item_args', $args, $item, $depth );

    $output .= $indent . '<li class="' . $item_classes .'">';

    // link attributes
    $atts           = array();
    $atts['title']  = ! empty( $item->attr_title ) ? $item->attr_title : '';
    $atts['target'] = ! empty( $item->target ) ? $item->target : '';
    if ( '_blank' === $item->target && empty( $item->xfn ) ) {
      $atts['rel'] = 'noopener noreferrer';
    } else {
      $atts['rel'] = $item->xfn;
    }
    $atts['href']         = ! empty( $item->url ) ? $item->url : '';
    $atts['aria-current'] = $item->current ? 'page' : '';

    $atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args, $depth );

    $attributes = '';
    foreach ( $atts as $attr => $value ) {
      if ( is_scalar( $value ) && '' !== $value && false !== $value ) {
        $value       = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
        $attributes .= ' ' . $attr . '="' . $value . '"';
      }
    }

    $title = apply_filters( 'the_title', $item->title, $item->ID );
    $title = apply_filters( 'nav_menu_item_title', $title, $item, $args, $depth );

    // link
    $item_output = $args->before;
    $item_output .= '<a class="' . $block . '-link"'. $attributes .'>';
    $item_output .= $args->link_before . $title . $args->link_after;
    $item_output .= '</a><span></span>';
    $item_output .= $args->after;

    $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
  }

  public function end_el( &$output, $item, $depth = 0, $args = null ) {
    $block = isset( $args->bem_block ) ? $args->bem_block : explode( ' ', $args->menu_class );
    $block = is_array( $block ) ? $block[0] : $block;
    if ( isset( $args->item_spacing ) && 'discard' === $args->item_spacing ) {
      $t = '';
      $n = '';
    } else {
      $t = "\t";
      $n = "\n";
    }
    $output .= "</li>{$n}";
  }

} // BEM_Walker_Nav_Menu