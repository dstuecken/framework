<?php
namespace DS\Cli\Interaction;

/**
 * The input interface
 *
 * @package Input
 */
interface InputInterface
{
    /**
     * The get input function should return the input string
     *
     * @return void
     */
    public function getInput();
}