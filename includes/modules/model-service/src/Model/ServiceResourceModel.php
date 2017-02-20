<?php

namespace RebelCode\EddBookings\Model;

use \Dhii\Storage\AdapterInterface;
use \RebelCode\Bookings\Model\Availability\TraditionalRuleAvailability;
use \RebelCode\EddBookings\Block\Html\DumpBlock;
use \RebelCode\EddBookings\CustomPostType;
use \RebelCode\Storage\WordPress\AbstractCptResourceModel;

/**
 * Service resource model.
 *
 * @since [*next-version*]
 */
class ServiceResourceModel extends AbstractCptResourceModel
{

    /**
     * Constructor.
     *
     * @param CustomPostType $postType The CPT
     * @param AdapterInterface $storageAdapter The storage adapter.
     */
    public function __construct(CustomPostType $postType, AdapterInterface $storageAdapter)
    {
        parent::__construct($postType, $storageAdapter);
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    protected function _dataToMeta(array $data)
    {
        return $data;
    }

    /**
     * {@inheritdoc}
     *
     * @since [*next-version*]
     */
    protected function _metaToData(array $meta)
    {
        // @TODO Remove after migration
        $flattenedMeta = $this->_flattenMetaArray($meta);
        $data          = $this->_removeEddBkPrefix($flattenedMeta);

        // Session unit string to instance
        $sessionUnitKey = strtoupper($data['session_unit']);
        $data['session_unit'] = SessionUnit::$sessionUnitKey();

        $data['international'] = $data['use_customer_tz'];
        unset($data['use_customer_tz']);

        $oldAvailability = unserialize($data['availability']);
        $data['availability'] = $this->_processAvailability($oldAvailability);
        
        echo new DumpBlock($data['availability']);

        return $data;
    }

    protected function _removeEddBkPrefix(array $meta)
    {
        $data = array();

        foreach ($meta as $_key => $_val) {
            if (strpos($_key, 'edd_bk_') !== 0) {
                continue;
            }
            $_newKey = substr($_key, strlen('edd_bk_'));
            $data[$_newKey] = $_val;
        }

        return $data;
    }

    protected function _processAvailability(array $meta)
    {
        $rules = $meta['rules'];
        $newRules = array();
        foreach ($rules as $rule) {
            $ruleType   = $this->_modernizeRuleType($rule['type']);

            $newRule    = eddBkContainer()->get('factory')->make($ruleType, array(
                'start'   => $rule['start'],
                'end'     => $rule['end'],
                'negated' => ! ((bool) $rule['available']),
                'type'    => $ruleType
            ));

            $newRules[] = $newRule;
        }

        $availability = new TraditionalRuleAvailability();
        $availability->setRules($newRules);

        return $availability;
    }

    protected function _modernizeRuleType($oldRuleType)
    {
        $typeSuffix = substr($oldRuleType, strlen('Aventura\\Edd\\Bookings\\Availability\\Rule\\'));
        $newRuleType   = $this->_getRuleType($typeSuffix);

        return $newRuleType;
    }

    protected function _getRuleType($oldRule)
    {
        $map = $this->_ruleMap();

        return isset($map[$oldRule])
            ? $map[$oldRule]
            : null;
    }

    protected function _ruleMap()
    {
        return array(
            'MondayTimeRule'    => 'monday_time_rule',
            'TuesdayTimeRule'   => 'tuesday_time_rule',
            'WednesdayTimeRule' => 'wednesday_time_rule',
            'ThursdayTimeRule'  => 'thursday_time_rule',
            'FridayTimeRule'    => 'friday_time_rule',
            'SaturdayTimeRule'  => 'saturday_time_rule',
            'SundayTimeRule'    => 'sunday_time_rule',
            'AllWeekTimeRule'   => 'all_week_time_rule',
            'WeekdaysTimeRule'  => 'weekdays_time_rule',
            'WeekendTimeRule'   => 'weekends_time_rule',
            'WeekNumRule'       => 'week_num_range_rule',
            'MonthRule'         => 'month_range_rule',
            'DotwRule'          => 'dotw_range_rule',
            'CustomDateRule'    => 'custom_datetime_rule',
        );
    }

    protected function createAvailability(array $ruleData)
    {
        $availability = new TraditionalRuleAvailability();
        $availability->setRules($rules);

        $rules = array();
        foreach($ruleData as $data) {
            $rules[] = new Availability\Rule\TestRangeRule(
                $data['start'],
                $data['end'],
                $data['negated']
            );
        }

        $availability->setRules($rules);

        return $availability;
    }
}
