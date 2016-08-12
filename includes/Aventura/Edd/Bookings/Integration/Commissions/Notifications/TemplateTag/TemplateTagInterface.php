<?php

namespace Aventura\Edd\Bookings\Integration\Commissions\Notifications\TemplateTag;

/**
 * A Commissions notification email template tag.
 * 
 * @author Miguel Muscat <miguelmuscat93@gmail.com>
 */
interface TemplateTagInterface
{

    /**
     * Gets the name of the template tag.
     * 
     * @return string
     */
    public function getName();

    /**
     * Gets the description for this template tag.
     * 
     * @return string
     */
    public function getDescription();

    /**
     * Processes the template tag to generate the replacement string.
     * 
     * @param integer $downloadId The ID of the Download for the current email notification context.
     * @param integer $commissionId The ID of the Commission for the current email notification context.
     * @return string The replacement string.
     */
    public function process($downloadId, $commissionId);

}
