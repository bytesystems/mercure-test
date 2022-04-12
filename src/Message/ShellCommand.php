<?php

namespace App\Message;

class ShellCommand
{
    private $command;
    private $guid;

    /**
     * @param array $command
     */
    public function __construct(string $guid, array $command)
    {
        $this->guid = $guid;
        $this->command = $command;
    }

    /**
     * @return array
     */
    public function getCommand(): array
    {
        return $this->command;
    }

    /**
     * @return string
     */
    public function getGuid(): string
    {
        return $this->guid;
    }


}