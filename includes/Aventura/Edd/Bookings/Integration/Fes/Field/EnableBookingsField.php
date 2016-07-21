<?php

namespace Aventura\Edd\Bookings\Integration\Fes\Field;

use \Aventura\Edd\Bookings\Integration\Fes\Field\FieldAbstract;

/**
 * Description of EnableBookingsField
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
class EnableBookingsField extends FieldAbstract
{

    const TEMPLATE = 'edd_bk_bookings_enabled';
    const META_KEY = 'edd_bk_bookings_enabled';
    const VIEWS_DIRECTORY = 'EnableBookings';

    /**
     * {@inheritdoc}
     */
    public function getTitle()
    {
        return __('Enable bookings', 'eddbk');
    }

    /**
     * {@inheritdoc}
     */
    public function getCharacteristics()
    {
        $characteristics = parent::getCharacteristics();
        return array_merge($characteristics, array(
            'checked_default' => '0',
            'checkbox_label' => ''
        ));
    }

    /**
     * Normalizes the field data prior to rendering.
     * 
     * @param array $data The input data.
     * @return array The normalized data.
     */
    protected function normalizeFieldData($data)
    {
        $data['value'] = is_null($data['value'])
            ? $data['characteristics']['checked_default']
            : $data['value'];
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
        return filter_var($input, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
    }

    /**
     * {@inheritdoc}
     */
    public function validateInput($input)
    {
        $output = filter_var($input, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        return is_null($output)
            ? __('Please recheck', 'eddbk')
            : false;
    }

    /**
     * {@inheritdoc}
     */
    protected function getViewsDirectoryName()
    {
        return static::VIEWS_DIRECTORY;
    }

}
