<?php
namespace DS\Cli\Interaction;

/**
 * The output interface governs output classes for the CLI,
 * Should you want to create your own module for output, or use a different method.
 *
 * @package Output
 */
interface OutputInterface
{

    /**
     * The write function to simply write a string
     *
     * @param $str
     *
     * @return OutputInterface
     */
	public function write($str);

    /**
     * Write a line
     *
     * @param $str
     *
     * @return OutputInterface
     */
	public function writeln($str);
}