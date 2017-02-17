<?php

namespace RebelCode\EddBookings\System\Module;

use \RebelCode\Bookings\Framework\Model\BaseModel;

/**
 * Description of Module
 *
 * @since [*next-version*]
 */
class Module extends BaseModel implements ModuleInterface
{

    protected $id;

    protected $name;

    protected $mainFilePath;

    protected $data;

    public function getId()
    {
        return $this->getData('id');
    }

    public function getName()
    {
        return $this->getData('name');
    }

    public function getDirectory()
    {
        return $this->getData('directory');
    }

    public function getFilePath()
    {
        return $this->getData('file_path');
    }

    public function exec()
    {
        $moduleFile = $this->getFilePath();

        file_exists($moduleFile) && require $moduleFile;
    }
}
