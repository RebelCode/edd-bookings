<?php

namespace Aventura\Edd\Bookings\Integration\Fes\Field;

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
     * Normalizes the field data prior to rendering.
     * 
     * @param array $data The input data.
     * @return array The normalized data.
     */
    protected function normalizeFieldData($data)
    {
        $downloadId = $data['save_id'];
        $data['service'] = eddBookings()->getServiceController()->get($downloadId);
        // Load existing meta
        $existingMeta = empty($downloadId)
            ? array()
            : eddBookings()->getServiceController()->getMeta($downloadId);
        // Merge meta with defaults and add to data
        $meta = $this->mergeMetaDefaults($existingMeta);
        $data['meta'] = $meta;
        // Create a service and add to data
        $serviceMeta = array_merge($meta, array('id' => '0'));
        $data['service'] = eddBookings()->getServiceController()->getFactory()->create($serviceMeta);
        return $data;
    }
    
    /**
     * Merges the given Download meta with the default values for this field's defaults.
     * 
     * @param array $meta The meta array.
     * @return array The meta array merged with default values.
     */
    protected function mergeMetaDefaults(array $meta) {
        $characteristics = $this->getCharacteristics();
        $options = $characteristics['options'];
        foreach ($options as $key => $value) {
            // Get 'default' mappings. For a normal option: [key => default]. For combo, the default index verbatim
            $defaultIndex = $value['default'];
            $defaultMappings = (isset($value['combo']) && $value['combo'])
                ? $defaultIndex
                : array($key => $defaultIndex);
            // A simple merge should do. The default mappings contains meta key and default value pairs.
            $meta = array_merge($defaultMappings, $meta);
        }
        return $meta;
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
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function renderFrontendField($value, $data)
    {
        return $this->renderView('Frontend', $this->normalizeFieldData($data));
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
     * {@inheritDoc}
     *
     * @since [*next-version*]
     */
    public function validate($values = array(), $saveId = -2, $userId = -2)
    {
        $errors = parent::validate($values, $saveId, $userId);

        if ($errors) {
            return $errors;
        }

        $characts = $this->getCharacteristics();
        $options  = $characts['options'];

        if ($options['availability']['enabled'] === '0') {
            return;
        }

        $availError = $this->validateAvailability($values);

        if ($availError) {
            return $availError;
        }
    }

    /**
     * Validates submitted availability.
     *
     * @since [*next-version*]
     *
     * @param array $values The submitted values.
     *
     * @return string|null The error string on failure, null on success.
     */
    protected function validateAvailability($values = array())
    {
        if (isset($values['edd-bk-rule-type'])) {
            return;
        }

        if (!isset($values['edd-bk-rule-type']) || count($values['edd-bk-rule-type']) === 0) {
            return __('You must have at least one available time period!', 'eddbk');
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function getViewsDirectoryName()
    {
        return static::VIEWS_DIRECTORY;
    }

    /**
     * {@inheritdoc}
     *
     * @internal Effectively disables the "required" option
     */
    public function required($readonly = false)
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultCharacteristics()
    {
        $defaults = parent::getDefaultCharacteristics();
        $defaults['required'] = false;
        $defaults['options'] = array(
            'bookings_enabled' => array(
                'enabled'     => '1',
                'label'       => __('Enable bookings', 'eddbk'),
                'default'     => '1',
                'hide_others' => '1'
            ),
            'session_length'   => array(
                'enabled' => '1',
                'label'   => __('Session Length:', 'eddbk'),
                'combo'   => true,
                'default' => array(
                    'session_length' => 3600,
                    'session_unit'   => 'hours'
                )
            ),
            'min_max_sessions' => array(
                'enabled' => '1',
                'label'   => __('Number of bookable sessions:', 'eddbk'),
                'combo'   => true,
                'default' => array(
                    'min_sessions' => '1',
                    'max_sessions' => '1'
                )
            ),
            'session_cost' => array(
                'enabled' => '1',
                'label'   => __('Session Cost:', 'eddbk'),
                'default' => '0'
            ),
            'availability' => array(
                'enabled' => '1',
                'label'   => __('Availability', 'eddbk'),
                'default' => array()
            ),
            'use_customer_tz' => array(
                'enabled' => '1',
                'label'   => __("Use the customer's timezone", 'eddbk'),
                'default' => '0'
            )
        );
        return $defaults;
    }

    /**
     * @{inheritdoc}
     */
    public function getDefaultSupports()
    {
        $supports = parent::getDefaultSupports();
        $supports['permissions']['field_always_required'] = true;
        return $supports;
    }

}
