<?php

/**
 * class Bootstrap_Walker_Nav_Menu()
 * Extending Walker_Nav_Menu to modify class assigned to submenu ul element
 */
class Bootstrapwp_Walker_Nav_Menu extends Walker_Nav_Menu {

    function __construct() {

    }

    function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {

        $indent = ($depth) ? str_repeat("\t", $depth) : '';
        $args = (object)$args;

        if( 'phone' == $item->title ) {

            $output .= $indent .  '<li class="phone"><span><i class="icon icon-phone"></i>' . get_setting( 'topbar_ph' ) . '</span></li>';
        }
        elseif( is_str_contain( 'divider', $item->title ) ) {
            $output .= $indent . '<li class="divider"></li>';
        }
        else {
            $classes = empty( $item->classes ) ? array() : (array) $item->classes;
            $classes = apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args );

            if( ( $item->current && 'secondary' != $args->theme_location ) || in_array( 'current-menu-parent', $classes ) || in_array( 'current-menu-ancestor', $classes ) )
                $classes[] = 'active';

            if( $item->isMega )
                $classes[] = 'mega-menu-item mega-menu-fullwidth';

            if ( $args->has_children && $depth > 0 ) {
                $classes[] = 'dropdown-submenu';
            } else if ( $args->has_children && $depth === 0 ) {
                $classes[] = 'dropdown';
            }

            $class_names = spyropress_clean_cssclass( $classes );
            $class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

            $output .= $indent . '<li' . $class_names . '>';

            $atts = array();
    		$atts['title']  = ! empty( $item->attr_title ) ? esc_attr( $item->attr_title ) : '';
    		$atts['target'] = ! empty( $item->target )     ? esc_attr( $item->target )     : '';
    		$atts['rel']    = ! empty( $item->xfn )        ? esc_attr( $item->xfn )        : '';
    		$atts['href']   = ! empty( $item->url )        ? esc_url( $item->url )        : '';

            if( $args->has_children && $depth == 0 ) {
                $atts['class'] = 'dropdown-toggle';
            }

            $attributes = apply_filters( 'nav_menu_link_attributes', spyropress_build_atts( $atts ), $item, $args );

            if( is_str_contain( '#HOME_URL#', $item->url ) )
                $attributes .= ' data-hash';

            $item_output = $args->before;
                $item_output .= '<a' . $attributes . '>';

                    $item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;

                    if( $args->has_children && $depth == 0 ) $item_output .= ' <i class="icon icon-angle-down"></i>';

                $item_output .= '</a>';
            $item_output .= $args->after;

            $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
        }
    }

    function start_lvl( &$output, $depth = 0, $args = array() ) {

        $indent = str_repeat("\t", $depth);
        $output .= "\n$indent<ul class=\"dropdown-menu\">\n";
    }

    function end_lvl( &$output, $depth = 0, $args = array() ) {
		$indent = str_repeat("\t", $depth);
		$output .= "$indent</ul>\n";
	}
    
    function display_element($element, &$children_elements, $max_depth, $depth = 0, $args, &$output) {
        
        if (!$element)
            return;
        
        $id_field = $this->db_fields['id'];

        //display this element
        if ( is_array( $args[0] ) )
            $args[0]['has_children'] = !empty( $children_elements[$element->$id_field] );
        elseif ( is_object( $args[0] ) )
            $args[0]->has_children = !empty( $children_elements[$element->$id_field] );

        $cb_args = array_merge( array(
            &$output,
            $element,
            $depth
        ), $args );
        
        call_user_func_array( array( &$this, 'start_el' ), $cb_args );

        $id = $element->$id_field;

        // descend only when the depth is right and there are childrens for this element
        if ( ( $max_depth == 0 || $max_depth > $depth + 1 ) && isset( $children_elements[$id] ) ) {

            foreach ( $children_elements[$id] as $child ) {

                if ( !isset( $newlevel ) ) {
                    $newlevel = true;
                    //start the child delimiter
                    $cb_args = array_merge( array(
                        &$output,
                        $depth
                    ), $args );
                    call_user_func_array( array( &$this, 'start_lvl' ), $cb_args );
                }
                $this->display_element( $child, $children_elements, $max_depth, $depth + 1, $args, $output );
            }
            unset( $children_elements[$id] );
        }

        if (isset($newlevel) && $newlevel) {
            //end the child delimiter
            $cb_args = array_merge( array(
                &$output,
                $depth
            ), $args );
            call_user_func_array( array( &$this, 'end_lvl' ), $cb_args );
        }

        //end this element
        $cb_args = array_merge(array(
            &$output,
            $element,
            $depth), $args );
        call_user_func_array( array( &$this, 'end_el' ), $cb_args );
    }    
}
?>