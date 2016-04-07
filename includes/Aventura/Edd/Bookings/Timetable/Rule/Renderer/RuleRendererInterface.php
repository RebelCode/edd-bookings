<?php

namespace Aventura\Edd\Bookings\Timetable\Rule\Renderer;

use \Aventura\Diary\Bookable\Availability\Timetable\Rule\RangeRuleAbstract;

/**
 * An object that can render a rule.
 */
interface RuleRendererInterface
{

    /**
     * Constructs a new instance.
     * 
     * @param RangeRuleAbstract $rule The rule to render.
     */
    public function __construct(RangeRuleAbstract $rule);

    /**
     * Gets the rule name.
     * 
     * @return string
     */
    public function getRuleName();
    
    /**
     * Gets the rule group.
     * 
     * @return string
     */
    public function getRuleGroup();

    /**
     * Renders the rule's range start option.
     * 
     * @param array $data Optional array of data. Accepted entries are: <br/>
     *                    "name" to specify the HTML name attribute <br/>
     *                    "class" to specify the HTML class attribute <br/>
     *                    Default: <b>array()</b>
     * @return string
     */
    public function renderRangeStart(array $data = array());

    /**
     * Renders the rule's range end option.
     * 
     * @param array $data Optional array of data. Accepted entries are: <br/>
     *                    "name" to specify the HTML name attribute <br/>
     *                    "class" to specify the HTML class attribute <br/>
     *                    Default: <b>array()</b>
     * @return string
     */
    public function renderRangeEnd(array $data = array());

    /**
     * Renders the rule's available option.
     * 
     * @param array $data Optional array of data. Accepted entries are: <br/>
     *                    "name" to specify the HTML name attribute <br/>
     *                    "class" to specify the HTML class attribute <br/>
     *                    Default: <b>array()</b>
     * @return string
     */
    public function renderAvailable(array $data = array());
    
    /**
     * Gets a renderer instance for a default value.
     * 
     * @return RuleRendererInterface
     */
    public static function getDefault();
    
}
