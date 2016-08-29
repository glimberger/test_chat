<?php

namespace AppBundle\Command;


use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class ChatServerCommand extends ContainerAwareCommand
{
    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this->setName("chat:server");
    }

    /**
     * Executes the current command.
     *
     * This method is not abstract because you can use this class
     * as a concrete class. In this case, instead of defining the
     * execute() method, you set the code to execute by passing
     * a Closure to the setCode() method.
     *
     * @param InputInterface $input An InputInterface instance
     * @param OutputInterface $output An OutputInterface instance
     *
     * @return null|int null or 0 if everything went fine, or an error code
     *
     * @see setCode()
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $logger = $this->getContainer()->get('logger');

        $output->writeln("Starting websocket server ...");

        $rootDir = $this->getContainer()->getParameter('kernel.root_dir');
        $output->writeln("run 'php ". $rootDir. '/../src/AppBundle/Model/ChatServer.php"');
        $logger->info("Starting websocket server");
        $process = new Process('php ' . $rootDir . '/../src/AppBundle/Model/ChatServer.php');
        $process->start();

        while ($process->isRunning()) {

        }

        echo $process->getOutput();
    }

}