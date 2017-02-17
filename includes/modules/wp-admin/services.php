<?php

use \Interop\Container\ContainerInterface;
use \RebelCode\WordPress\Admin\Menu\Menu;
use \RebelCode\WordPress\Admin\Menu\MenuBar;
use \RebelCode\WordPress\Admin\Menu\SubMenu;
use \RebelCode\WordPress\Admin\Page;

return array(
    'admin_menubar' => function(ContainerInterface $c) {
        return new MenuBar($c->get('plugin'), $c->get('event_manager'));
    },

    'admin_menu' => function(ContainerInterface $c, $prev, $config) {
        $data = array_merge($c->get('admin_menu_defaults'), $config);
        
        $menu = new Menu(
            $data['id'],
            $data['label'],
            $data['page'],
            $data['icon'],
            $data['position']
        );

        foreach ($data['submenus'] as $subMenu) {
            $menu->addSubMenu($subMenu);
        }

        return $menu;
    },

    'admin_submenu' => function(ContainerInterface $c, $prev, $config) {
        $data = array_merge($c->get('admin_submenu_defaults'), $config);

        return new SubMenu(
            $data['menu_id'],
            $data['id'],
            $data['label'],
            $data['page']
        );
    },

    'admin_page' => function(ContainerInterface $c, $prev, $config) {
        $data = array_merge($c->get('admin_page_defaults'), $config);

        return new Page(
            $data['title'],
            $data['content'],
            $data['capability']
        );
    },

    'admin_menu_defaults' => function() {
        return array(
            'id'       => '',
            'label'    => '',
            'page'     => null,
            'icon'     => '',
            'position' => 100,
            'submenus' => array()
        );
    },
    'admin_submenu_defaults' => function() {
        return array(
            'menu_id' => '',
            'id'      => '',
            'label'   => '',
            'page'    => null
        );
    },
    'admin_page_defaults' => function() {
        return array(
            'title'      => '',
            'content'    => '',
            'capability' => 'manage_options'
        );
    }
);
