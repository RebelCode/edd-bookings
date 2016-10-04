<?php

namespace Aventura\Edd\Bookings\Renderer;

if (!defined('EDD_BK_ABOUT_PAGE_VIEWS_DIR')) {
    define('EDD_BK_ABOUT_PAGE_VIEWS_DIR', EDD_BK_VIEWS_DIR . DIRECTORY_SEPARATOR . 'about' . DIRECTORY_SEPARATOR);
}

/**
 * Renders the main page.
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
class MainPageRenderer extends \Aventura\Edd\Bookings\Renderer\RendererAbstract
{

    /**
     * Gets the URL of the main page, optionally with a tab GET param.
     * 
     * @param string $tab Optional name of the current active tab. Default: ''
     * @return string
     */
    public function getMainPageUrl($tab = '')
    {
        $tabStr = empty($tab)
                ? $tab
                : sprintf('&tab=%s', $tab);
        return \admin_url(sprintf('admin.php?page=%s%s', $this->getObject()->getMenuSlug(), $tabStr));
    }

    /**
     * Renders the main page.
     * 
     * @param array $data Optional array of data. Accepted args are:
     *                    'tab' => The name of the tab to show by default
     * @return string The rendered output.
     */
    public function render(array $data = array())
    {
        // Parse given param args
        $args = \wp_parse_args($data, array(
                'tab' => ''
        ));
        // Get tab GET param
        $tabGet = isset($_GET['tab']) ? $_GET['tab'] : '';
        // Determine active tab
        $activeTab = empty($args['tab'])? $tabGet : $args['tab'];
        ob_start();
        ?>
        <div class="wrap about-wrap">
            <?php
            echo $this->renderHeader();
            echo $this->renderTabs($activeTab);
            echo $this->renderTabContent($activeTab);
            ?>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Renders the header.
     * 
     * @return string
     */
    public function renderHeader()
    {
        ob_start();
        ?>
        <header>
            <img src="<?php echo EDD_BK_IMGS_URL . 'logo.png'; ?>" alt="EDD Bookings /">
            <h1><?php printf(__('Welcome to EDD Bookings v%s', 'eddbk'), EDD_BK_VERSION); ?></h1>
            <p class="about-text">
                <?php printf('Thank you for updating to the latest version!', 'eddbk'); ?>
            </p>
        </header>
        <?php
        return ob_get_clean();
    }

    /**
     * Renders the tabs.
     * 
     * @param string $activeTab Optional name of active tab. Default: ''
     * @return string
     */
    public function renderTabs($activeTab = '')
    {
        ob_start();
        ?>
        <h2 class="nav-tab-wrapper">
            <?php
            foreach ($this->getTabs() as $tab) {
                echo $this->renderTab($tab->id, $tab->label, $tab->id === $activeTab);
            }
            ?>
        </h2>
        <?php
        return ob_get_clean();
    }

    /**
     * Renders the content of a tab using its callback, if given.
     * 
     * @param string $tabId The ID of the tab to render.
     * @return string
     */
    public function renderTabContent($tabId) {
        $tab = $this->getTab($tabId);
        $output = '';
        if (!is_null($tab) && is_callable($tab->callback)) {
            $output = call_user_func_array($tab->callback, array($tab));
        }
        return $output;
    }
    
    /**
     * Gets the tabs.
     * 
     * @return array An array of std objects.
     */
    public function getTabs()
    {
        $defaultTabs = array(
            static::tab('', __("What's New?", 'eddbk'), array($this, 'renderAboutTabView')),
            static::tab('getting-started', __('Getting Started', 'eddbk'), array($this, 'renderAboutTabView')),
            //static::tab('documentation', __('Documentation', 'eddbk'), array($this, 'renderAboutTabView')),
            static::tab('changelog', __('Changelog', 'eddbk'), array($this, 'renderAboutTabView')),
        );
        if (EDD_BK_DEBUG) {
            $defaultTabs[] = static::tab('debug', __('Debugging', 'eddbk'), array($this, 'renderAboutTabView'));
        }
        return \apply_filters('edd_bk_mainpage_tabs', $defaultTabs);
    }
    
    /**
     * Renders the content of an about page tab, using its matching view file.
     * 
     * @param stdClass $tab The tab object.
     * @return string The rendered content.
     */
    public function renderAboutTabView($tab)
    {
        $fileName = $tab->id === ''
                ? 'index'
                : $tab->id;
        $viewFile = realpath(sprintf('%s%s.php', EDD_BK_ABOUT_PAGE_VIEWS_DIR, $fileName));
        if (file_exists($viewFile)) {
            ob_start();
            include $viewFile;
            return ob_get_clean();
        } else {
            return sprintf('View file "%s" not found!', $viewFile);
        }
    }
    
    /**
     * Gets a tab by ID.
     * 
     * @param string $id The ID of the tab to return.
     * @return \stdClass The tab object.
     */
    public function getTab($id)
    {
        $tabs = $this->getTabs();
        $return = null;
        foreach($tabs as $tab) {
            if ($tab->id === $id) {
                $return = $tab;
            }
        }
        return $return;
    }

    /**
     * Renders a single tab.
     * 
     * @param string $id The ID of the tab.
     * @param string $label The tab label, i.e. the text to show on the tab.
     * @param boolean $active True if the tab is active, false if not.
     * @param string $class Optional string to append to the class attribute of the tab.
     * @return string
     */
    public function renderTab($id, $label, $active = false, $class = '')
    {
        $activeClass = $active
                ? 'nav-tab-active'
                : '';
        $finalClass = sprintf('%s %s %s', 'nav-tab', $class, $activeClass);
        $url = $this->getMainPageUrl($id);
        return sprintf('<a class="%s" href="%s">%s</a>', $finalClass, $url, $label);
    }

    /**
     * Creates an std object instance for a tab using given data.
     * 
     * @param string $id The tab ID.
     * @param string $label The tab label.
     * @param \callable|null $callback Optional callback that renders the tab content. Default: null
     * @return \stdClass
     */
    public static function tab($id, $label, $callback = null)
    {
        $tab = new \stdClass();
        $tab->id = $id;
        $tab->label = $label;
        $tab->callback = $callback;
        return $tab;
    }

}
