<?php

namespace Aventura\Edd\Bookings\Integration\Fes\Field;

/**
 * Base wrapper class for FES fields.
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
abstract class FieldAbstract extends \FES_Field
{

    /**
     * Admin render context.
     */
    const CONTEXT_ADMIN = 'admin';

    /**
     * Frontend render context.
     */
    const CONTEXT_FRONTEND = 'frontend';

    /**
     * The FES form builder template class.
     */
    const FES_TEMPLATE_CLASS = '\FES_FormBuilder_Templates';

    /**
     * The "template" name.
     */
    const TEMPLATE = '';

    /**
     * The meta save key.
     */
    const META_KEY = '';

    /**
     * {@inheritdoc}
     */
    public function __construct($field = '', $form = 'notset', $type = -2, $saveId = -2)
    {
        $this->setMetaSingle(true);
        $this->supports = $this->getDefaultSupports();
        $this->characteristics = $this->getCharacteristics();
        parent::__construct($field, $form, $type, $saveId);
    }

    /**
     * Gets the characteristics.
     * 
     * @return array
     */
    public function getCharacteristics()
    {
        return array(
            'name'        => static::META_KEY,
            'template'    => static::TEMPLATE,
            'public'      => false,
            'required'    => true,
            'label'       => $this->getTitle(),
            'css'         => '',
            'default'     => '',
            'size'        => '',
            'help'        => '',
            'placeholder' => '',
        );
    }

    /**
     * Gets the default supports.
     * 
     * @return array
     */
    public function getDefaultSupports()
    {
        return array(
            'multiple'    => false,
            'is_meta'     => true,
            'forms'       => array(
                'registration'   => false,
                'submission'     => true,
                'vendor-contact' => false,
                'profile'        => false,
                'login'          => false,
            ),
            'position'    => 'extension',
            'permissions' => array(
                'can_remove_from_formbuilder' => true,
                'can_change_meta_key'         => false,
                'can_add_to_formbuilder'      => true,
            ),
            'template'    => static::TEMPLATE,
            'title'       => $this->getTitle(),
            'phoenix'     => true,
        );
    }

    /**
     * Gets the meta "single" configuration.
     * 
     * This reflects the third parameter of the get_post_meta() and get_user_meta() function.
     * 
     * @return boolean True if meta config is single, false otherise.
     */
    public function isMetaSingle()
    {
        return $this->single;
    }

    /**
     * Sets the meta "single" configuration.
     * 
     * This reflects the third parameter of the get_post_meta() and get_user_meta() function.
     * 
     * @param boolean $single The meta "single" configuration.
     * @return FieldAbstract This instance.
     */
    public function setMetaSingle($single)
    {
        $this->single = $single;
        return $this;
    }

    /**
     * Gets the field supports.
     * 
     * @return array The field supports.
     */
    public function getSupports()
    {
        return $this->supports;
    }

    /**
     * Sets the field supports.
     * 
     * @param array $supports The field supports.
     * @return FieldAbstract This instance.
     */
    public function setSupports(array $supports)
    {
        $this->supports = $supports;
        return $this;
    }

    /**
     * Adds field supports.
     * 
     * @param array $supports The field supports to add.
     * @return FieldAbstract This instance.
     */
    public function addSupports($supports)
    {
        $this->supports = array_merge($this->supports, (array) $supports);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function extending_constructor()
    {
        $this->_construct();
    }

    /**
     * {@inheritdoc}
     */
    public function set_title()
    {
        $this->supports['title'] = $this->getTitle();
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($values = array(), $saveId = -2, $userId = -2)
    {
        // Check if value exists. Use null for no value set
        $name = $this->name();
        $input = empty($values[$name])
            ? null
            : $values[$name];
        // If not set and required, return warning message. Otherwise continue validation
        $result = (is_null($input) && $this->required())
            ? __('Please fill out this field.', 'edd_fes')
            : $this->validateInput($input);
        // Filter result and return
        $filter = sprintf('fes_validate_%s_field', $this->template());
        return apply_filters($filter, $result, $values, $saveId, $userId);
    }

    /**
     * {@inheritdoc}
     */
    public function sanitize($values = array(), $saveId = -2, $userId = -2)
    {
        $name = $this->name();
        if (isset($values[$name])) {
            $values[$name] = $this->sanitizeInput($values[$name]);
        }
        return $values;
    }

    /**
     * Prepares the required data array for frontend and admin field renders.
     * 
     * @param integer $pUserId The user ID.
     * @param boolean $pReadonly The readonly flag.
     * @param string $context The context. Either 'admin' or 'frontend'. See the CONTEXT_* class constants.
     * @return array The data array.
     */
    protected function prepareFieldRenderData($pUserId, $pReadonly, $context)
    {
        $nUserId = ($pUserId === -2)
            ? get_current_user_id()
            : $pUserId;
        $nReadonly = ($pReadonly === -2)
            ? $this->readonly
            : $pReadonly;
        // Apply filters
        $userId = apply_filters(sprintf('fes_%s_field_%s_user_id', $this->id, $context), $nUserId, $this->id);
        $readonly = apply_filters(sprintf('fes_%s_field_%s_readonly', $this->id, $context), $nReadonly, $userId,
            $this->id);
        // Get value
        $valueFn = sprintf('get_field_value_%s', $context);
        $value = call_user_func_array(array($this, $valueFn), array($this->save_id, $userId, $readonly));
        // Return data array
        return array(
            'template'        => $this->template(),
            'name'            => $this->name(),
            'css'             => $this->css(),
            'label'           => $this->label($readonly),
            'required'        => $this->required($readonly),
            'value'           => $value,
            'characteristics' => $this->characteristics,
            'supports'        => $this->supports
        );
    }

    /**
     * {@inheritdoc}
     */
    public function render_field_admin($pUserId = -2, $pReadonly = -2)
    {
        $data = $this->prepareFieldRenderData($pUserId, $pReadonly, static::CONTEXT_ADMIN);
        $data['field'] = $this->renderAdminField($data['value'], $data);
        return eddBookings()->renderView('Fes.Base.Admin', $data);
    }

    /**
     * {@inheritdoc}
     */
    public function render_field_frontend($pUserId = -2, $pReadonly = -2)
    {
        $data = $this->prepareFieldRenderData($pUserId, $pReadonly, static::CONTEXT_FRONTEND);
        $data['field'] = $this->renderFrontendField($data['value'], $data);
        return eddBookings()->renderView('Fes.Base.Frontend', $data);
    }

    /**
     * {@inheritdoc}
     */
    public function render_formbuilder_field($index = -2, $insert = false)
    {
        // Prepare data array
        $removable = $this->can_remove_from_formbuilder();
        $data = array(
            'removable' => $removable,
            'class'     => $this->id,
            'legend'    => $this->getLegend($removable)
        );
        // Get field render (data array can be modified)
        $data['field_body'] = $this->renderBuilderField($data);
        // Generate hidden field
        $hiddenFieldName = sprintf('[%s][template]', $index, $this->template());
        $hiddenField = $this->getTemplatePart('hidden_field', $hiddenFieldName, $this->template());
        // Generate field div container
        $fieldDiv = $this->getTemplatePart('field_div', $index, $this->name(), $this->characteristics, $insert);
        $publicRadio = $this->getTemplatePart('public_radio', $index, $this->characteristics, $this->form_name);
        $standard = $this->getTemplatePart('standard', $index, $this);
        // Add field being and end renders
        $data['field_begin'] = $hiddenField . $fieldDiv . $publicRadio . $standard;
        $data['field_end'] = '</div>';
        // Render the builder field
        return eddBookings()->renderView('Fes.Base.Builder', $data);
    }

    /*     * 1
     * Gets the legend.
     * 
     * @param boolean $removable
     * @return string
     */

    public function getLegend($removable)
    {
        ob_start();
        $this->legend($this->title(), $this->get_label(), $removable);
        return ob_get_clean();
    }

    /**
     * Gets the output from an FES_FormBuilder_Templates method.
     * 
     * @param string $name The name of the method.
     * @param ... $argsN Variable number of arguments to pass along to the method.
     * @return string
     */
    public function getTemplatePart($name)
    {
        $args = array_slice(func_get_args(), 1);
        ob_start();
        call_user_func_array(sprintf('%s::%s', static::FES_TEMPLATE_CLASS, $name), $args);
        return ob_get_clean();
    }

    /**
     * Constructor for extending classes.
     */
    abstract function _construct();

    /**
     * Gets the translated title for this field.
     * 
     * @return string The title string, translated if needed.
     */
    abstract function getTitle();

    /**
     * Renders the admin field.
     * 
     * @param mixed $value The field value.
     * @param array $data Additional field data. Indexes include: template, name, css, label, required and readonly.
     * @return string The rendered output.
     */
    abstract public function renderAdminField($value, $data);

    /**
     * Renders the admin field.
     * 
     * @param mixed $value The field value.
     * @param array $data Additional field data. Indexes include: template, name, css, label, required and readonly.
     * @return string The rendered output.
     */
    abstract public function renderFrontendField($value, $data);

    /**
     * Renders the form builder field(s).
     * 
     * @param $array $data Array of field data. Passed by reference.
     * @return string The rendered form builder field.
     */
    abstract public function renderBuilderField(&$data);

    /**
     * Sanitizes user input for this field.
     * 
     * @param mixed $input The user input.
     * @return mixed The sanitized output.
     */
    abstract public function sanitizeInput($input);

    /**
     * Validates user input for this field.
     * 
     * @param mixed $input The user input. If null, no user input was given.
     * @return string|boolean Any validation errors or `false` to signify validation success.
     */
    abstract public function validateInput($input);

}
