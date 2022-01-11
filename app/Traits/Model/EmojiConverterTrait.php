<?php

namespace DS\Traits\Model;

use DS\Component\Text\Emoji;

/**
 * DS-Framework
 *
 * @author    Dennis Stücken
 * @license   proprietary
 * @copyright Dennis Stücken
 * @link      https://www.dvlpr.de
 *
 * @version   $Version$
 * @package   DS\Interfaces
 */
trait EmojiConverterTrait
{
    /**
     * Convert Emojis to their UTF-8 hex notation
     *
     * @param array $fields
     * @param bool  $stripHtml
     */
    protected function convertEmojis(array $fields, bool $stripHtml = true)
    {
        // Convert Emojis to their UTF-8 hex notation
        $emoji = new Emoji();

        foreach ($fields as $field)
        {
            if ($this->$field)
            {
                if ($stripHtml)
                {
                    $this->$field = strip_tags($this->$field);
                }

                $this->$field = $emoji->convert($this->$field);
            }
        }
    }
}
