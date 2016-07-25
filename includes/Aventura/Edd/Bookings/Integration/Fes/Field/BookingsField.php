<?php

namespace Aventura\Edd\Bookings\Integration\Fes\Field;

use \Aventura\Edd\Bookings\Factory\ServiceFactory;
use \Aventura\Edd\Bookings\Integration\Fes\Field\FieldAbstract;

/**
 * Description of EnableBookingsField
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
class BookingsField extends FieldAbstract
{

    const TEMPLATE = 'edd_bk';
    const META_KEY = 'edd_bk';
    const VIEWS_DIRECTORY = 'Bookings';

    /**
     * {@inheritdoc}
     */
    public function getTitle()
    {
        return __('Bookings', 'eddbk');
    }

    /**
     * {@inheritdoc}
     */
    public function getCharacteristics()
    {
        $characteristics = parent::getCharacteristics();
        return array_merge($characteristics, self::getMetaCharacteristics());
    }

    /**
     * Normalizes the field data prior to rendering.
     * 
     * @param array $data The input data.
     * @return array The normalized data.
     */
    protected function normalizeFieldData($data)
    {
        $downloadId = $data['save_id'];
        $data['service'] = eddBookings()->getServiceController()->get($downloadId);
        $data['meta'] = empty($downloadId)
            ? ServiceFactory::getDefaultOptions()
            : eddBookings()->getServiceController()->getMeta($downloadId);
        return $data;
    }

    /**
     * Renders the common field: admin and frontend.
     * 
     * @param array $data The view data.
     * @return string The rendered view.
     */
    protected function renderCommon(array $data)
    {
        return $this->renderView('Common', $this->normalizeFieldData($data));
    }

    /**
     * {@inheritdoc}
     */
    public function renderBuilderField(&$data)
    {
        return $this->renderView('Builder', $data);
    }

    /**
     * {@inheritdoc}
     */
    public function renderAdminField($value, $data)
    {
        return $this->renderCommon($data);
    }

    /**
     * {@inheritdoc}
     */
    public function renderFrontendField($value, $data)
    {
        return $this->renderCommon($data);
    }

    /**
     * {@inheritdoc}
     */
    public function sanitizeInput($input)
    {
        return $input;
    }

    /**
     * {@inheritdoc}
     */
    public function validateInput($input)
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    protected function getViewsDirectoryName()
    {
        return static::VIEWS_DIRECTORY;
    }

    /**
     * Gets the field characteristics that represent the meta options.\
     * 
     * @return array
     */
    public static function getMetaCharacteristics()
    {
        return array(
            'bookings_enabled' => array(
                'enabled' => '1',
                'label'   => ''
            )
        );
    }

}
