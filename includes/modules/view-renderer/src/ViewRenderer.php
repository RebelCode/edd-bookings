<?php

namespace RebelCode\EddBookings;

use \Dhii\App\AppInterface;
use \RebelCode\EddBookings\System\Component\AbstractBaseComponent;

/**
 * A component that provides view rendering functionality.
 *
 * @since [*next-version*]
 */
class ViewRenderer extends AbstractBaseComponent
{
    /**
     * The views directory.
     *
     * @since [*next-version*]
     *
     * @var string
     */
    protected $viewsDirectory;

    /**
     * Constructor.
     *
     * @since [*next-version*]
     *
     * @param AppInterface $app The parent app instance.
     * @param string $viewsDirectory The views directory.
     */
    public function __construct(AppInterface $app, $viewsDirectory)
    {
        parent::__construct($app);

        $this->setViewsDirectory($viewsDirectory);
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    public function onAppReady()
    {

    }

    /**
     * Gets the views directory.
     *
     * @since [*next-version*]
     *
     * @return string The views directory.
     */
    public function getViewsDirectory()
    {
        return $this->viewsDirectory;
    }

    /**
     * Sets the views directory.
     *
     * @since [*next-version*]
     *
     * @param string $viewsDirectory The new views directory.
     *
     * @return $this This instance.
     */
    public function setViewsDirectory($viewsDirectory)
    {
        $this->viewsDirectory = $viewsDirectory;

        return $this;
    }

    /**
     * Renders a view by name.
     *
     * A view name is a string containing dot-separated parts that reflect the directory
     * structure in the views folder.
     *
     * @since [*next-version*]
     *
     * @param string $viewName The view name.
     * @param array $data Array of data to pass to the view.
     *
     * @return string The rendered content.
     */
    public function renderView($viewName, array $data = array())
    {
        ob_start();
        include $this->getViewFilePath($viewName);
        $output = ob_get_clean();

        return $output;
    }

    /**
     * Gets the file path for a given view name.
     *
     * @since [*next-version*]
     *
     * @param string $viewName The view name.
     *
     * @return string The file path.
     */
    public function getViewFilePath($viewName)
    {
        $uParts   = explode('.', $viewName);
        $tParts   = array_map('trim', $uParts);
        $relPath  = implode(DIRECTORY_SEPARATOR, $tParts);
        $absPath  = $this->getViewsDirectory() . DIRECTORY_SEPARATOR . $relPath;
        $fullPath = sprintf('%s.php', $absPath);

        return $fullPath;
    }
}
