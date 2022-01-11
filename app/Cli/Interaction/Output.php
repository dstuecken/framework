<?php

namespace DS\Cli\Interaction;

use DS\Cli\Format\DSFormat;
use DS\Cli\Format\FormatCollection;

/**
 * The output class writes to the console.
 *
 * @package    CLI
 * @subpackage Output
 */
class Output
    implements OutputInterface
{
    /**
     * The STDOut
     *
     * @var Resource
     */
    protected $output;
    
    /**
     * @var DSFormat
     */
    private $formatter;
    
    /**
     * Build the output
     *
     * @param string $source
     */
    public function __construct($source = 'php://output')
    {
        $this->formatter = new DSFormat(
            new FormatCollection()
        );
        
        $this->output = fopen($source, 'wb+');
    }
    
    /**
     * Output a string
     *
     * @param $str
     *
     * @return Output
     */
    public function write($str)
    {
        fwrite($this->output, vsprintf($this->formatter->format($str), func_num_args() > 0 ? array_slice(func_get_args(), 1) : []));
        
        return $this;
    }
    
    /**
     * Write a single line
     *
     * @param $str
     *
     * @return Output
     */
    public function writeln($str)
    {
        fwrite($this->output, vsprintf($this->formatter->format($str), func_num_args() >= 2 ? array_slice(func_get_args(), 1) : []) . "\n");
        
        return $this;
    }
    
    /**
     * Draws a line with a given length and character
     *
     * @param        $length
     * @param string $char
     *
     * @return Output
     */
    public function hr($length, $char = '_')
    {
        $this->writeln(str_pad('', $length, $char));
        
        return $this;
    }
    
    /**
     * Read the output
     *
     * @return string
     */
    public function read()
    {
        fseek($this->output, 0);
        
        return stream_get_contents($this->output);
    }
}
