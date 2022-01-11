<?php
namespace DS\Cli\Format;

/**
 * The format collection stores and fetches formats
 *
 * @package    CLI
 * @subpackage Format
 */
class FormatCollection
{
    /**
     * An associative array of formats
     *
     * @var array
     */
    protected $formats;

    /**
     * An instance of the Colors class
     *
     * @var string
     */
    protected $color;

    /**
     * Set up the basic formats
     *
     */
    public function __construct()
    {
        $this->color   = new Colors;
        $this->formats = [];
        $this->addGeneric();
    }

    /**
     * Adds a format entry
     *
     * @param       $name
     * @param array $details
     */
    public function add($name, Array $details)
    {
        $this->formats[$name] = $this->textToCode($details);
    }

    /**
     * Turns a text color ie "Blue" into a color code.
     *
     * @param array $details
     *
     * @return array
     */
    public function textToCode(Array $details)
    {
        $formatted = [];
        if (isset($details['foreground']))
        {
            $formatted['foreground'] = $this->color->getForeground($details['foreground']);
        }
        if (isset($details['background']))
        {
            $formatted['background'] = $this->color->getBackground($details['background']);
        }

        return $formatted;
    }

    /**
     * Gets either a single or all formats depending on the var that is passed
     *
     * @param null $name
     *
     * @return array|false
     */
    public function get($name = null)
    {
        if (!is_null($name))
        {
            if (array_key_exists($name, $this->formats))
            {
                return $this->formats[$name];
            }

            return false;
        }
        else
        {
            return $this->formats;
        }
    }

    /**
     * Adds a generic set of formats.
     *
     * @return void
     */
    public function addGeneric()
    {
        // Questions
        $this->add('question', ['foreground' => 'cyan']);
        // Comments
        $this->add('comment', ['foreground' => 'yellow']);
        // Note
        $this->add('note', ['foreground' => 'green']);
        // Info
        $this->add('info', ['foreground' => 'cyan']);
        // Errors
        $this->add('error', ['foreground' => 'white', 'background' => 'red']);
        // Red
        $this->add('red', ['foreground' => 'red']);
    }
}