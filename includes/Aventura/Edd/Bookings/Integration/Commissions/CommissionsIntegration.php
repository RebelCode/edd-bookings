<?php

namespace Aventura\Edd\Bookings\Integration\Commissions;

use \Aventura\Edd\Bookings\Integration\Commissions\Notifications\TemplateTag\TemplateTagInterface;
use \Aventura\Edd\Bookings\Integration\Core\IntegrationAbstract;
use \Aventura\Edd\Bookings\Plugin;

/**
 * Description of CommissionsIntegration
 *
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
class CommissionsIntegration extends IntegrationAbstract
{

    /**
     * The template tags.
     * 
     * @var TemplateTagInterface[]
     */
    protected $templateTags;

    /**
     * {@inheritdoc}
     */
    public function __construct(Plugin $plugin = null)
    {
        parent::__construct($plugin);
        $this->setTemplateTags(array());
    }

    /**
     * Gets the template tags.
     * 
     * @return array The template tags.
     */
    public function getTemplateTags()
    {
        return $this->templateTags;
    }

    /**
     * Sets the template tags.
     * @param array $templateTags The template tags.
     * @return CommissionsIntegration This instance.
     */
    public function setTemplateTags(array $templateTags)
    {
        $this->templateTags = $templateTags;
        return $this;
    }

    /**
     * Adds a template tag.
     * 
     * @param TemplateTagInterface $templateTag The template tag instance to add.
     * @return CommissionsIntegration This instance.
     */
    public function addTemplateTag(TemplateTagInterface $templateTag)
    {
        $this->templateTags[] = $templateTag;
        return $this;
    }

    /**
     * Registers the template tags to EDD Commissions.
     * 
     * @param array $templateTags The input filter array of template tags.
     * @return array The output filter array of template tags.
     */
    public function registerTemplateTags(array $templateTags)
    {
        foreach ($this->getTemplateTags() as $toRegister) {
            $templateTags[] = array(
                'tag'         => $toRegister->getName(),
                'description' => $toRegister->getDescription()
            );
        }
        return $templateTags;
    }

    /**
     * Filters the New Sale email message.
     * 
     * @param string $message The email message.
     * @param integer $userId The user ID, for context.
     * @param float $commissionAmount The commission amount for the sale.
     * @param float $rate The commission rate.
     * @param integer $downloadId The download ID, for context.
     * @param integer $commissionId The commissions ID, for context.
     * @return string The filtered email message.
     */
    public function filterNewSaleEmail($message, $userId, $commissionAmount, $rate, $downloadId, $commissionId)
    {
        foreach ($this->getTemplateTags() as $tag) {
            $message = static::replaceTag($tag, $message, $downloadId, $commissionId);
        }
        return $message;
    }

    /**
     * Replaces a tag in a subject string with its processed counterpart.
     * 
     * @param TemplateTagInterface $tag The tag instance.
     * @param string $subject The string subject in which to search for the tag.
     * @param integer $downloadId The download ID, for context.
     * @param integer $commissionId The commissions ID, for context.
     * @return string The subject string with the replaced tags.
     */
    public static function replaceTag(TemplateTagInterface $tag, $subject, $downloadId, $commissionId)
    {
        $tagString = sprintf('{%s}', $tag->getName());
        $replacement = $tag->process($downloadId, $commissionId);
        return str_replace($tagString, $replacement, $subject);
    }

    /**
     * {@inheritdoc}
     * @return CommissionsIntegration
     */
    public function hook()
    {
        $this->getPlugin()->getHookManager()
            ->addFilter('eddc_email_template_tags', $this, 'registerTemplateTags')
            ->addFilter('eddc_sale_alert_email', $this, 'filterNewSaleEmail');
        return $this;
    }

}
