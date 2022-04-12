<?php

namespace App\Controller;

use App\Message\ShellCommand;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Process\Process;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;

class PingController extends AbstractController
{
    #[Route('/console', name: 'app_console')]
    public function console(): Response
    {
        return $this->render('ping/index.html.twig', [
            'controller_name' => 'PingController',
        ]);
    }

    #[Route('/ping', name: 'app_ping')]
    public function index(MessageBusInterface $bus): Response
    {
        $command = new ShellCommand(
            Uuid::v4(),
            ['ping', 'heise.de','-c', '10']
        );

        $bus->dispatch($command);

        $command = new ShellCommand(
            Uuid::v4(),
            ['ls', '-la','--color=always']
        );

        $bus->dispatch($command);

        return $this->render('ping/index.html.twig', [
            'controller_name' => 'PingController',
        ]);
    }
    #[Route('/push', name: 'app_push')]
    public function publish(HubInterface $hub): Response
    {
        $process = new Process(['composer','update']);
        $process->start();
        $process->wait(function ($type, $buffer) use ($hub) {

            $update = new Update(
                'https://example.com/books/1',
                json_encode(['line' => $buffer])
            );
            $hub->publish($update);

            if (Process::ERR === $type) {
                echo 'ERR > '.$buffer;
            } else {
                echo 'OUT > '.$buffer;
            }
        });


        $update = new Update(
            'https://example.com/books/1',
            json_encode(['status' => 'OutOfStock'])
        );

        $hub->publish($update);

        return new Response('published!');
    }

    #[Route('/push2', name: 'app_push_2')]
    public function publish2(HubInterface $hub): Response
    {

        $process = new Process(['ls', '-la','--color=always']);
        $process->start();
        $process->wait(function ($type, $buffer) use ($hub) {

            $update = new Update(
                'https://example.com/books/1',
                json_encode(['line' => $buffer])
            );
            $hub->publish($update);
        });


        $update = new Update(
            'https://example.com/books/1',
            json_encode(['status' => 'done'])
        );

        $hub->publish($update);

        return new Response('published!');
    }

    #[Route('/message', name: 'app_push_to_messenger')]
    public function push_to_messenger(MessageBusInterface $bus): Response
    {
        $command = new ShellCommand(
            Uuid::v4(),
            ['test/test.sh']
        );

        $bus->dispatch($command);

        return new Response('pushed to messenger');
    }

    #[Route('/message2', name: 'app_push_to_messenger2')]
    public function push_to_messenger2(MessageBusInterface $bus): Response
    {
        $command = new ShellCommand(
            Uuid::v4(),
            ['ping', 'heise.de','-c', '10']
        );

        $bus->dispatch($command);

        return new Response('pushed to messenger');
    }
}
