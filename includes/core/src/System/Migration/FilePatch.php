<?php

namespace RebelCode\EddBookings\System\Migration;

/**
 * Description of FilePatch
 *
 * @since [*next-version*]
 */
class FilePatch implements PatchInterface
{

    protected $filepath;

    public function __construct($filepath)
    {
        $this->setFilepath($filepath);
    }

    public function getFilepath()
    {
        return $this->filepath;
    }

    public function setFilepath($filepath)
    {
        $this->filepath = $filepath;

        return $this;
    }

    public function apply()
    {
        require $this->getFilepath();
    }

}
