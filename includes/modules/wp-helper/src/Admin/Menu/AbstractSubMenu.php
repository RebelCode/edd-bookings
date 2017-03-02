<?php

namespace RebelCode\Wp\Admin\Menu;

/**
 * Basic functionality for a sub-menu.
 *
 * @since [*next-version*]
 */
abstract class AbstractSubMenu extends AbstractMenu
{
    /**
     * The parent top-level menu instance or ID.
     *
     * @since [*next-version*]
     *
     * @var TopLevelMenuInterface|string
     */
    protected $parentMenu;

    /**
     * Gets the parent top-level menu.
     *
     * @since [*next-version*]
     *
     * @return TopLevelMenuInterface|string The parent menu instance or ID.
     */
    protected function _getParentMenu()
    {
        return $this->parentMenu;
    }

    /**
     * Sets the parent top-level menu.
     *
     * @since [*next-version*]
     *
     * @param TopLevelMenuInterface|string $parentMenu The parent menu instance or ID.
     *
     * @return $this
     */
    protected function _setParentMenu($parentMenu)
    {
        $this->parentMenu = $parentMenu;

        return $this;
    }

    /**
     * Gets the parent ID.
     *
     * Since WordPress menus use their URLs as IDs if they redirect to a URL, this method
     * will check the parent's content type and if found to be a URL string, that string
     * will be the returned as the ID.
     *
     * Otherwise, if the parent content is a callback, its ID property is returned.
     *
     * @since [*next-version*]
     *
     * @return string
     */
    protected function _getParentId()
    {
        $parentMenu = $this->_getParentMenu();

        if (is_string($parentMenu) || is_null($parentMenu)) {
            return $parentMenu;
        }

        $parentContent = $this->_normalizeContent($parentMenu->getContent());

        return filter_var($parentContent, FILTER_VALIDATE_URL)
            ? $parentContent
            : $parentMenu->getId();
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    protected function _registerWithCallback($callback) {
        return add_submenu_page(
            $this->_getParentId(),
            $this->_getPageTitle(),
            $this->_getLabel(),
            $this->_getCapability(),
            $this->_getId(),
            $callback
        );
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    protected function _registerWithUrl($url) {
        $parentId   = $this->_getParentId();
        $label      = $this->_getLabel();
        $capability = $this->_getCapability();

        $parentSlug = filter_var($parentId, FILTER_VALIDATE_URL)
            ? $parentId
            : plugin_basename($parentId);

        return $this->_wpAddSubMenuPage($parentSlug, $url, $label, $capability, '');
    }

    /**
     * Cloned from WordPress `add_submenu_page()` function.
     *
     * Has some slight modifications to make it adhere to our standards and removes the
     * calls to `plugin_basename()` to keep URLs intact.
     *
     * @since [*next-version*]
     *
     * @global array $submenu
     * @global array $menu
     * @global array $_wp_real_parent_file
     * @global boolean $_wp_submenu_nopriv
     * @global array $_registered_pages
     * @global array $_parent_pages
     *
     * @param string $parentSlug
     * @param string $menuSlug
     * @param string $label
     * @param string $cap
     * @param string $pageTitle
     *
     * @return string
     */
    protected function _wpAddSubMenuPage($parentSlug, $menuSlug, $label, $cap, $pageTitle)
    {
        global $submenu, $menu, $_wp_real_parent_file, $_wp_submenu_nopriv,
            $_registered_pages, $_parent_pages;

        if (isset($_wp_real_parent_file[$parentSlug])) {
            $parentSlug = $_wp_real_parent_file[$parentSlug];
        }
        if (!current_user_can($cap)) {
            $_wp_submenu_nopriv[$parentSlug][$menuSlug] = true;
            return false;
        }

        // Create sub-menu for parent if no exists and submenu slug is different
        if (!isset($submenu[$parentSlug]) && $menuSlug != $parentSlug) {
            foreach ((array)$menu as $_parentMenu) {
                if ($_parentMenu[2] == $parentSlug && current_user_can($_parentMenu[1])) {
                    $submenu[$parentSlug][] = array_slice($_parentMenu, 0, 4);
                    break;
                }
            }
        }

        $submenu[$parentSlug][] = array($label, $cap, $menuSlug, $pageTitle);

        $hookname = get_plugin_page_hookname($menuSlug, $parentSlug);
        $_registered_pages[$hookname] = true;

        if ('tools.php' == $parentSlug) {
            $_registered_pages[get_plugin_page_hookname($menuSlug, 'edit.php')] = true;
        }

        $_parent_pages[$menuSlug] = $parentSlug;

        return $hookname;
    }
}
