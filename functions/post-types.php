<?php
// Добавляем свойи типы постов

function register_custom_post_types(){
    /*function get_taxonomy_args($labels){
        return array(
            'label'                 => '', // определяется параметром $labels->name
            'labels'                => $labels,
            'description'           => '', // описание таксономии
            'public'                => true,
            'publicly_queryable'    => null, // равен аргументу public
            'show_in_nav_menus'     => true, // равен аргументу public
            'show_ui'               => true, // равен аргументу public
            'show_tagcloud'         => true, // равен аргументу show_ui
            'hierarchical'          => true,
            'update_count_callback' => '',
            'rewrite'               => true,
            //'query_var'             => $taxonomy, // название параметра запроса
            'capabilities'          => array(),
            'meta_box_cb'           => null, // callback функция. Отвечает за html код метабокса (с версии 3.8): post_categories_meta_box или post_tags_meta_box. Если указать false, то метабокс будет отключен вообще
            'show_admin_column'     => false, // Позволить или нет авто-создание колонки таксономии в таблице ассоциированного типа записи. (с версии 3.5)
            '_builtin'              => false,
            'show_in_quick_edit'    => null, // по умолчанию значение show_ui
        );
    }*/
    /*
    register_post_type('services',
        array(
            'labels' => array(
                'name' => 'Услуги',
                'singular_name' => 'Услуги',
                'add_new' => 'Добавить услугу',
                'add_new_item' => 'Добавить новую услугу'
            ),
            'public' => true,
            'supports' => array('title', 'editor', 'thumbnail'),
            'has_archive' => true,
            //'rewrite' => array('slug' => 'services-items'),
            'taxonomies' => array('category', 'post_tag'),
            'menu_icon' => 'dashicons-admin-tools'
        )
    );*/

    /* ТАКСОНОМИИ */
    // заголовки
    /*$case_type_labels = array(
        'name'              => 'Типы проектов',
        'singular_name'     => 'Тип проекта',
        'search_items'      => 'Искать типы проектов',
        'all_items'         => 'Все типы проектов',
        'parent_item'       => 'Родительский тип проекта',
        'parent_item_colon' => 'Родительский тип проекта:',
        'edit_item'         => 'Изменить тип проекта',
        'update_item'       => 'Изменить тип проекта',
        'add_new_item'      => 'Добавить тип проекта',
        'new_item_name'     => 'Новый тип проекта',
        'menu_name'         => 'Типы проектов',
        'not_found'         => 'Типов проектов не найдено',
    );
    register_taxonomy('project-type', '', get_taxonomy_args($case_type_labels) );*/

}

add_action( 'init', 'register_custom_post_types' );
