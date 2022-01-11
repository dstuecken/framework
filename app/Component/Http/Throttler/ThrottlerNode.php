<?php

namespace DS\Component\Http\Throttler;

use Orangesoft\Throttler\Collection\NodeInterface;

/**
 * DS-Framework
 *
 * @author    Dennis Stücken
 * @license   proprietary
 * @copyright Dennis Stücken
 * @link      https://www.dvlpr.de
 *
 * @version   $Version$
 * @package   DS\Component\Http
 */
final class ThrottlerNode implements NodeInterface
{
    /**
     * @var string
     */
    private $name;
    
    /**
     * @var int
     */
    private $weight;
    
    public static function factory(string $name, int $weight = 1): ThrottlerNode
    {
        return new self($name, $weight);
    }
    
    public function __construct(string $name, int $weight = 0)
    {
        $this->name   = $name;
        $this->weight = $weight;
    }
    
    public function getName(): string
    {
        return $this->name;
    }
    
    public function getWeight(): int
    {
        return $this->weight;
    }
}

