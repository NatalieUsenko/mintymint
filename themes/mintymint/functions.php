<?php

add_action( 'wp_enqueue_scripts', 'my_scripts' );
function my_scripts(){
    wp_enqueue_script( 'jquery' );
    wp_enqueue_script( 'my_script', get_stylesheet_directory_uri() . '/script.js');
}

function twentytwentyone_styles() {
    wp_enqueue_style( 'child-style', get_stylesheet_uri(),
        array( 'twenty-twenty-one-style' ), wp_get_theme()->get('Version') );
}
add_action( 'wp_enqueue_scripts', 'twentytwentyone_styles');

add_action( 'init', 'mm_create_taxonomy' );
function mm_create_taxonomy(){
    register_taxonomy( 'company_sector', [ 'company' ], [
        'label'                 => '',
        'labels'                => [
            'name'              => 'Company Sectors',
            'singular_name'     => 'Company Sector',
            'search_items'      => 'Search Company Sectors',
            'all_items'         => 'All Company Sectors',
            'view_item '        => 'View Company Sectors',
            'parent_item'       => 'Parent Company Sector',
            'parent_item_colon' => 'Parent Company Sector:',
            'edit_item'         => 'Edit Company Sector',
            'update_item'       => 'Update Company Sector',
            'add_new_item'      => 'Add New Company Sector',
            'new_item_name'     => 'New Company Sector Name',
            'menu_name'         => 'Company Sector',
        ],
        'description'           => '',
        'public'                => true,
        'hierarchical'          => true,
        'rewrite'               => array( 'slug' => 'company_sector' ),
        'query_var'             => true,
        'capabilities'          => array(),
        'meta_box_cb'           => null,
        'show_admin_column'     => false,
        'show_in_rest'          => null,
        'rest_base'             => null,
    ] );
}

function mm_register_post_type_init() {
    $labels = array(
        'name' => 'Companies',
        'singular_name' => 'Company',
        'add_new' => 'Add new',
        'add_new_item' => 'Add new company',
        'edit_item' => 'Edit company',
        'new_item' => 'New company',
        'all_items' => 'All companies',
        'view_item' => 'View companies',
        'search_items' => 'Search companies',
        'not_found' =>  'No companies found.',
        'not_found_in_trash' => 'No companies found in Trash.',
        'menu_name' => 'Companies'
    );
    $args = array(
        'labels' => $labels,
        'public' => true,
        'show_ui' => true,
        'has_archive' => true,
        'menu_icon' => 'dashicons-nametag',
        'menu_position' => 20,
        'supports' => array( 'title', 'editor', 'thumbnail')
    );
    register_post_type('company', $args);
}
add_action( 'init', 'mm_register_post_type_init' );

add_shortcode( 'companies_search', 'companies_search_shortcode' );
function companies_search_shortcode( $atts ){
    return '<form role="search" method="get" id="csearchform" action="'.home_url( '/' ).'" >
	<input type="text" value="'.get_search_query().'" placeholder="Placeholder" name="cs" id="cs" />
	<input type="submit" class="green-btn" id="csearchsubmit" value="Поиск" />
</form>';
}

function companies_list($term_id = 0){
    $list_content = '';
    $page_id = get_the_ID();
    $all_terms = get_terms(array(
        'taxonomy' => 'company_sector',
        'hide_empty' => 1,
        'parent' => '0'
    ));

    foreach ($all_terms as $term) { //внешний цикл
        if ($term_id == 0) $term_id = $term->term_taxonomy_id;
        $list_content .= '<li><a href="/' . $term->slug . '" data-id="' . $term->term_taxonomy_id . '">' . $term->name . '</a>';
        $term_children = get_term_children($term->term_taxonomy_id, 'company_sector');
        $list_content .= '<ul>';
        foreach ($term_children as $child) {
            $term = get_term_by('id', $child, 'company_sector');
            $list_content .= '<li><a href="' . get_term_link($term->term_id, $term->taxonomy) . '" data-id="' . $term->term_id . '">' . $term->name . '</a></li>';
        }
        $list_content .= '</ul>';
        $list_content .= '</li>';
    }
    return '<div class="wp-block-media-text alignwide is-stacked-on-mobile section-breadcrumbs-wrapper">
                <div class="section_breadcrumbs">'.companies_breadcrumbs($term_id, $page_id).'</div>
            </div>
            <div class="wp-block-media-text alignwide is-stacked-on-mobile section-list-wrapper">
                <ul class="section-list"  data-page="'.$page_id.'">'.$list_content.'</ul>
                <div class="section-items">'.companies_items($term_id).'</div>
            </div>';
}

function companies_breadcrumbs($term_id = 0, $page_id, $search = ''){
    $breadcrumbs_content = '';
    $terms_info = get_term($term_id, 'company_sector');
    $breadcrumbs_content.= '<a href="' . get_permalink( $page_id ) . '">' . get_the_title( $page_id ) . '</a>  ';
    $breadcrumbs_content.= ' <span>/</span> ';
    if ($search != ''){
        $breadcrumbs_content.= 'Search result';
    } else {
        if ( $terms_info->parent != 0 ){
            $breadcrumbs_content.= '<a href="' . get_term_link( $terms_info->parent ) . '">' . get_term($terms_info->parent)->name . '</a>  ';
            $breadcrumbs_content.= ' <span>/</span> ';
        }
        $breadcrumbs_content.= $terms_info->name;
    }

    return $breadcrumbs_content;
}
function companies_items($term_id = 0, $page_num = 0, $search = ''){
    $items_content = '';
    $paged_content = '';
    $paged = ($page_num != 0) ? $page_num : 1;
    if ($search == ''){
        $query = new WP_Query(array(
            'post_status' => 'publish',
            'post_type' => 'company',
            //       'posts_per_page' => '-1',
            'paged' => $paged,
            'tax_query' => array(
                array(
                    'taxonomy' => 'company_sector',
                    'field' => 'term_id',
                    'terms' => $term_id,
                )
            ),
            'meta_query' => array(
                'rating' => array(
                    'key' => 'company_rating',
                    'type'    => 'NUMERIC',
                ),
            ),
            'orderby' => 'rating',
            'order' => 'DESC'
        ));
    } else {
        $query = new WP_Query(array(
            'post_status' => 'publish',
            'post_type' => 'company',
            //       'posts_per_page' => '-1',
            'paged' => $paged,
            's' => $search,
            'meta_query' => array(
                'rating' => array(
                    'key' => 'company_rating',
                    'type'    => 'NUMERIC',
                ),
            ),
            'orderby' => 'rating',
            'order' => 'DESC'
        ));
    }

    while ($query->have_posts()) { # внутренний цикл
        $query->the_post();
        $company_id = get_the_ID();
        $company_rating = get_field('company_rating');
        $items_content .= '<div class="company_item">';

        $items_content .= '<a href="' . get_permalink($company_id) . '">';
        $items_content .= '<img src="' . get_the_post_thumbnail_url($company_id, 'full') . '" />';
        $items_content .= '</a>';

        $items_content .= '<div class="company_title">';
        $items_content .= '<a href="' . get_permalink($company_id) . '">';
        $items_content .= get_the_title($company_id);
        $items_content .= '</a>';
        $items_content .= '</div>';

        $items_content .= '<div class="small-text green-text">Starting at $' . get_field('company_cost') . '</div>';
        $items_content .= '<div class="small-text green-text star-wrapper">';
        $items_content .= '<span>';
        for ($i = 1; $i <= 5; $i++) {
            if ($i <= round($company_rating)) {
                $items_content .= '<span class="rating_star star_full"></span>';
            } else {
                $items_content .= '<span class="rating_star star_empty"></span>';
            }
        }
        $items_content .= '</span>';
        $items_content .= number_format($company_rating, 1, '.', '') . '(' . get_field('company_comments') . ')';
        $items_content .= '</div>';
        $items_content .= '</div>';
        wp_reset_postdata();
    } //конец внутреннего

    if (!($query->have_posts()) && $search != ''){
        $items_content .= 'No companies found!';
    }

    if ($paged <= $query->max_num_pages) {
        $links_data = kama_paginate_links_data([
            'total' => $query->max_num_pages,
            'current' => $paged,
            'url_base' => 'https://ec2-18-223-16-42.us-east-2.compute.amazonaws.com/page/{page_num}',
        ]);

        ob_start();

        $paged_content .= '<div class="page-pagination" data-term-id="'.$term_id.'">';
        $paged_content .= '<ul>';
        $paged_content .= '<li>';
        if ($paged < 10) {
            $paged_content .= '0' . $paged . ' / ';
        } else {
            $paged_content .= $paged . ' / ';
        }
        if ($query->max_num_pages < 10) {
            $paged_content .= '0' . $query->max_num_pages;
        } else {
            $paged_content .= $query->max_num_pages;
        }
        $paged_content .= '</li>';

        $paged_content .= '<li>';
        if ($paged < 2) {
            $paged_content .= '<a class="prev page-btn" href="javascript:;"><img src="'.get_stylesheet_directory_uri() .'/img/prev_page.png"></a>';
        } else {
            $paged_content .= '<a class="prev page-btn" data-paged="'.($paged-1).'" href="' . $links_data[$paged - 2]->url . '" onclick="page_nav(event)"><img src="'.get_stylesheet_directory_uri() .'/img/prev_page.png"></a>';
        }
        $paged_content .= '</li>';
        $paged_content .= '<li>';
        if ($paged == $query->max_num_pages) {
            $paged_content .= '<a class="next page-btn" href="javascript:;"><img src="'.get_stylesheet_directory_uri() .'/img/next_page.png"></a>';
        } else {
            $paged_content .= '<a class="next page-btn"  data-paged="'.($paged+1).'" href="' . $links_data[$paged]->url . '" onclick="page_nav(event)"><img src="'.get_stylesheet_directory_uri() .'/img/next_page.png"></a>';
        }
        $paged_content .= '</li>';

        $paged_content .= '</ul>';
        $paged_content .= '</div>';
    }
    $items_content.= $paged_content;

    return $items_content;
}


add_action("wp_ajax_my_ajax_action", "k_ajax_my_ajax_action");// для фронтенда
add_action("wp_ajax_nopriv_my_ajax_action", "k_ajax_my_ajax_action");// для админки
function k_ajax_my_ajax_action(){ // функция которая вызывается
    $result["companies_breadcrumbs"] = companies_breadcrumbs($_POST['term_id'], $_POST['page_id'], $_POST['search_val']);
    $result["companies_items"] =  companies_items($_POST['term_id']);
    $result = json_encode($result);
    echo $result;
    wp_die();
}

add_action("wp_ajax_page_ajax_action", "k_ajax_page_ajax_action");// для фронтенда
add_action("wp_ajax_nopriv_page_ajax_action", "k_ajax_page_ajax_action");// для админки
function k_ajax_page_ajax_action(){ // функция которая вызывается
    $result["companies_items"] =  companies_items($_POST['term_id'], $_POST['paged'], $_POST['search_val']);
    $result = json_encode($result);
    echo $result;
    wp_die();
}

add_action("wp_ajax_search_ajax_action", "k_ajax_search_ajax_action");// для фронтенда
add_action("wp_ajax_nopriv_search_ajax_action", "k_search_page_ajax_action");// для админки
function k_ajax_search_ajax_action(){ // функция которая вызывается
    $result["companies_breadcrumbs"] = companies_breadcrumbs(0, $_POST['page_id'], $_POST['search_val']);
    $result["companies_items"] =  companies_items(0, 0, $_POST['search_val']);
    $result = json_encode($result);
    echo $result;
    wp_die();
}

/**
 * @param array $args {
 *     @type int    $total    Max paginate page.
 *     @type int    $current  Current page.
 *     @type string $url_base URL pattern. Use {page_num} placeholder.
 * }
 *
 * @return array
 */
function kama_paginate_links_data( $args ){
    global $wp_query;

    $args = wp_parse_args( $args, [
        'total' => $wp_query->max_num_pages ?? 1,
        'current' => null,
        'url_base' => '', //
    ] );

    if( null === $args['current'] ){
        $args['current'] = max( 1, get_query_var( 'paged', 1 ) );
    }

    if( ! $args['url_base'] ){
        $args['url_base'] = str_replace( PHP_INT_MAX, '{page_num}', get_pagenum_link( PHP_INT_MAX ) );
    }

    $pages = range( 1, max( 1, (int) $args['total'] ) );

    foreach( $pages as & $page ){
        $page = (object) [
            'is_current' => $page == $args['current'] ,
            'page_num'   => $page,
            'url'        => str_replace( '{page_num}', $page, $args['url_base'] ),
        ];
    }
    unset( $page );

    return $pages;
}