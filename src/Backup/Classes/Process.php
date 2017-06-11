<?php namespace Backup\Classes;

use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process as SYProcess;

class Process
{
    public function execute($command, SymfonyStyle $consoleOutput)
    {
        if (is_array($command)) {
            foreach ($command as $item) {
                $this->run($item, $consoleOutput);
            }
        } else {
            $this->run($command, $consoleOutput);
        }
    }

    private function run($command, SymfonyStyle $consoleOutput)
    {
        $process = new SYProcess($command);
        if ('\\' !== DIRECTORY_SEPARATOR && file_exists('/dev/tty') && is_readable('/dev/tty')) {
            $process->setTty(true);
        }

        $consoleOutput->text('<comment>[EXEC]</comment> ' . $process->getCommandLine());

        $process->run(function ($type, $line) use ($consoleOutput) {
            if (SYProcess::ERR) {
                $consoleOutput->text('<error>[ERROR]</error> : ' . $line);
            } else {
                $consoleOutput->text('<info>[INFO]</info> : ' . $line);
            }
        });

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
    }

    public function getOutput($command)
    {
        $process = new SYProcess($command);
        $process->start();
        $iterator = $process->getIterator($process::ITER_SKIP_ERR | $process::ITER_KEEP_OUTPUT);

        $outputData = null;

        foreach ($iterator as $data) {
            $outputData = $data;
        }

        return $outputData !== null ? explode(PHP_EOL, $outputData) : [];
    }
}
