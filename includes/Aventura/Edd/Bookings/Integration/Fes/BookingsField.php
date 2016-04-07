<?php

namespace Aventura\Edd\Bookings\Integration\Fes;

use \Aventura\Edd\Bookings\Controller\ControllerInterface;
use \Aventura\Edd\Bookings\Plugin;

/**
 * Description of BookingsField
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
class BookingsField extends \FES_Field implements ControllerInterface
{

    public static $plugin = null;

    /**
     * @var Plugin
     */
    protected $_plugin;

    /**
     * Constructor.
     */
    public function __construct($field = '', $form = 'notset', $type = -2, $save_id = -2)
    {
        $this->setPlugin(static::$plugin);
        $this->setSingle(true)
                ->setSupports()
                ->setCharacteristics();
        parent::__construct($field, $form, $type, $save_id);
        $this->hook();
    }

    public function setSingle($single)
    {
        $this->single = $single;
        return $this;
    }
    
    public function setSupports()
    {
        $this->supports = array(
                'multiple'    => false,
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
                'template'    => 'edd-bk-bookings-enabled',
                'phoenix'     => true,
        );
        return $this;
    }
    
    public function setCharacteristics()
    {
        $this->characteristics = array(
                'name'        => 'edd-bk-bookings-enabled',
                'template'    => 'edd-bk-bookings-enabled',
                'is_meta'     => true, // in object as public (bool) $meta;
                'public'      => false,
                'required'    => true,
                'label'       => 'Enable bookings',
                'css'         => '',
                'default'     => '',
                'size'        => '',
                'help'        => '',
                'placeholder' => '',
        );
        return $this;
    }

    /**
     * Sets the title of the field on the back-end.
     */
    public function set_title()
    {
        $title = _x('EDD Bookings', 'FES Field title translation', $this->getPlugin()->getI18n()->getDomain());
        $filteredTitle = apply_filters('fes_' . $this->name() . '_field_title', $title);
        $this->supports['title'] = $filteredTitle;
    }

    /**
     * Gets the parent plugin instance.
     * 
     * @return Plugin
     */
    public function getPlugin()
    {
        return $this->_plugin;
    }

    /**
     * Sets the 
     * @param Plugin $plugin
     * @return BookingsField
     */
    public function setPlugin(Plugin $plugin)
    {
        $this->_plugin = $plugin;
        return $this;
    }

    /**
     * @see FES_Field::extending_constructor()
     */
    public function extending_constructor()
    {
        
    }

    /**
     * Registers the WordPress hooks.
     */
    public function hook()
    {
        // Code here
    }

    /**
     * Renders the field in the formbuilder.
     */
    public function render_formbuilder_field($index = -2, $insert = false)
    {
        $removable = $this->can_remove_from_formbuilder();
        ob_start();
        ?>
        <li class="edd-bk-booking">
            <?php
            $this->legend($this->title(), $this->get_label(), $removable);
            \FES_Formbuilder_Templates::hidden_field(sprintf('[%s][template]', $index), $this->template());
            ?>
            <div class="fes-form-holder">
                <?php
                \FES_Formbuilder_Templates::public_radio($index, $this->characteristics, $this->form_name);
                \FES_Formbuilder_Templates::standard($index, $this);
                \FES_Formbuilder_Templates::common_text($index, $this->characteristics);
                ?>
            </div>
        </li>
        <?php
        return ob_get_clean();
    }

    /**
     * Renders the field on the frontend.
     */
    public function render_field_frontend($userId = -2, $readOnly = -2)
    {
        if ($userId === -2) {
            $userId = get_current_user_id();
        }
        if ($readOnly === -2) {
            $readOnly = $this->readonly;
        }
        $filteredUserId = apply_filters('fes_render_commissions_email_field_user_id_frontend', $userId, $this->id);
        $filteredReadOnly = apply_filters('fes_render_commissions_email_field_readonly_frontend', $readOnly, $userId,
                $this->id);
        $value = $this->get_field_value_frontend($this->save_id, $filteredUserId, $filteredReadOnly);
        ob_start();
        ?>
        <div class="fes-fields">
            <label>
                <input type="hidden" name="<?php echo esc_attr($this->name()); ?>" value="0" />
                <input id="fes-<?php echo $this->name(); ?>"
                       type="checkbox"
                       class="checkbox"
                       data-required="<?php echo $this->required(); ?>"
                       data-type="checkbox"
                       name="<?php echo esc_attr($this->name()); ?>"
                       value="<?php echo esc_attr($value) ?>"
                       size="<?php echo esc_attr($this->characteristics['size']) ?>"
                       <?php $this->required_html5($readOnly); ?>
                       />
                <?php _e('Enable bookings', $this->getPlugin()->getI18n()->getDomain()); ?>
            </label>
            <p><small><i><?php echo $this->help(); ?></i></small></p>
        </div>
        <?php
        return ob_get_clean();
    }
    
    
    public function validate($values = array(), $save_id = -2, $user_id = -2) {
        $name = $this->name();
        // If the value is set
        if (!empty($values[$name])) {
            // Validate it as a boolean
            $enableBookings = $value[$name];
            if (filter_var($enableBookings, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) === null) {
                return __('Invalid checkbox value.', $this->getPlugin()->getI18n()->getDomain());
            }
        } else { 
            // if the url is required but isn't present
            if ($this->required()){
                return __('Please fill out this field.', 'edd_fes');
            }
        }
        return apply_filters('fes_validate_' . $this->template() . '_field', false, $values, $name, $save_id, $user_id); 
    }

    public function sanitize( $values = array(), $save_id = -2, $user_id = -2 ){
        $name = $this->name();
        if (!empty($values[$name])) {
            $values[$name] = filter_var($values[$name], FILTER_VALIDATE_BOOLEAN);
        }
        return apply_filters('fes_sanitize_' . $this->template() . '_field', $values, $name, $save_id, $user_id);
    }

}
