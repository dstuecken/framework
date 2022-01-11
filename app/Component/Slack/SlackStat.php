<?php

namespace DS\Component\Slack;

/**
 * DS-Framework
 *
 * @author    Dennis Stücken
 * @license   proprietary
 * @copyright Dennis Stücken
 * @link      https://www.dvlpr.de
 *
 * @version   $Version$
 * @package   DS\Component
 */
class SlackStat
{
    /**
     * @var string
     */
    public $text = '';
    
    /**
     * @var string
     */
    public $value = '';
    
    /**
     * @var bool
     */
    public $short = true;
    
    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }
    
    /**
     * @param string $text
     *
     * @return $this
     */
    public function setText($text)
    {
        $this->text = $text;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }
    
    /**
     * @param string $value
     *
     * @return $this
     */
    public function setValue($value)
    {
        $this->value = $value;
        
        return $this;
    }
    
    /**
     * @return bool
     */
    public function isShort(): bool
    {
        return $this->short;
    }
    
    /**
     * @param bool $short
     *
     * @return $this
     */
    public function setShort($short)
    {
        $this->short = $short;
        
        return $this;
    }
    
    /**
     * @param string      $text
     * @param string|null $value
     *
     * @return SlackStat
     */
    public static function factory(string $text, ?string $value = ''): self
    {
        $self = new self();
        $self->setText($text)
             ->setValue($value ?? '');
        
        return $self;
    }
}
