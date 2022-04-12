<?php

namespace App\MessageHandler;

use App\Message\ShellCommand;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Process\Process;

#[AsMessageHandler]
class ShellCommandHandler
{
    private HubInterface $hub;

    public function __construct(HubInterface $hub)
    {
        $this->hub = $hub;
    }

    public function __invoke(ShellCommand $shellCommand)
    {
        $hub = $this->hub;

        $process = new Process($shellCommand->getCommand());
        $process->start();
        $process->wait(function ($type, $buffer) use ($hub) {

            $update = new Update(
                'https://example.com/books/1',
//                'https://example.com/command/' . $shellCommand->getGuid(),
                json_encode(['line' => $buffer])
            );
            $hub->publish($update);
        });


        $update = new Update(
            'https://example.com/books/1',
            json_encode(['status' => 'done'])
        );

        $hub->publish($update);
    }
}