<?php

namespace Aventura\Edd\Bookings\Availability\Rule\Renderer;

use \Aventura\Diary\Bookable\Availability\Timetable\Rule\RangeRuleAbstract;

/**
 * An object that can render a rule.
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
abstract class RuleRendererAbstract implements RuleRendererInterface
{

    /**
     * The rule to render.
     * 
     * @var RangeRuleAbstract
     */
    protected $_rule;

    /**
     * Constructs a new instance.
     * 
     * @param RangeRuleAbstract $rule The rule to render.
     */
    public function __construct(RangeRuleAbstract $rule)
    {
        $this->setRule($rule);
    }

    /**
     * Gets the rule.
     * 
     * @return RangeRuleAbstract
     */
    public function getRule()
    {
        return $this->_rule;
    }

    /**
     * Sets the rule to render.
     * 
     * @param RangeRuleAbstract $rule The rule to render
     * @return \Aventura\Edd\Bookings\View\RuleViewAbstract\RuleRendererAbstract This instance.
     */
    public function setRule(RangeRuleAbstract $rule)
    {
        $this->_rule = $rule;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function renderRangeStart(array $data = array())
    {
        return $this->_renderRangeStart($this->normalizeParamData($data));
    }

    /**
     * Renders the range's start.
     * 
     * @see RuleRendererAbstract::renderRangeStart()
     */
    abstract protected function _renderRangeStart(array $data = array());

    /**
     * {@inheritdoc}
     */
    public function renderRangeEnd(array $data = array())
    {
        return $this->_renderRangeEnd($this->normalizeParamData($data));
    }

    /**
     * Renders the range's end.
     * 
     * @see RuleRendererAbstract::renderRangeEnd()
     */
    abstract protected function _renderRangeEnd(array $data = array());

    /**
     * {@inheritdoc}
     */
    public function renderAvailable(array $pData = array())
    {
        $data = $this->normalizeParamData($pData);
        $name = $data['name'];
        $class = $data['class'];
        $checked = \checked($this->getRule()->isNegated(), false, false);
        $checkbox = sprintf('<input type="checkbox" name="%2$s" class="%3$s" value="1" %1$s/>', $checked, $name, $class);
        $fakeCheckbox = sprintf('<input type="hidden" name="%s" value="0" />', $name);
        return $fakeCheckbox . $checkbox;
    }

    /**
     * Normalizes parameter data with default values.
     * 
     * @param array $data The input array of data.
     * @return array Array of data, normalized with default values.
     */
    public function normalizeParamData(array $data)
    {
        $defaults = array(
                'name'  => '',
                'class' => ''
        );
        return \wp_parse_args($data, $defaults);
    }

}
