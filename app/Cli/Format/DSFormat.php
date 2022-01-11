<?php
namespace DS\Cli\Format;

/**
 * The format class controls everything to do with formating
 *
 * @package    CLI
 * @subpackage Format
 */
class DSFormat extends Format
{

    /**
     * DS-FrameworkFormat constructor.
     *
     * @param null $collection
     */
    public function __construct($collection = NULL)
    {
        parent::__construct($collection);

        $this->addFormat('red', ['foreground' => 'red']);
    }

}