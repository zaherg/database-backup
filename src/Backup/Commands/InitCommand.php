<?php namespace Backup\Commands;

use Backup\Traits\Helper;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Finder\Finder;

class InitCommand extends Command
{
    use Helper;

    private $host;
    private $port;
    private $userName;
    private $password;
    private $adapter;
    private $compress;
    private $userInput;


    protected function configure()
    {
        $this->setName('init')
            ->setDescription('This command will help you setup your project');
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->getDriversList();
        $this->userInput = $this->getIo($input, $output);

        if ($this->checkFileExists($this->userInput) === false) {
            $this->userInput->note('Nothing has changed, you configuration file is still the same');
            exit;
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->userInput->section('We will start collect the information to create the config file');

        $this->getHostUrl($this->userInput);
        $this->getHostPort($this->userInput);
        $this->getHostUserName($this->userInput);
        $this->getHostPassword($this->userInput);
        $this->getAdapter($this->userInput);
        $this->shouldCompress($this->userInput);

        if ($this->confirm($this->userInput)) {
            $this->saveData();
            $this->userInput->success('We have created the configuration file successfully');
            exit;
        } else {
            $this->userInput->note('You will have to run the command again to create the configuration file.');
            exit;
        }
    }

    protected function getHostUrl(SymfonyStyle $input)
    {
        $question = new Question('Please provide your database host url');
        $question->setNormalizer(function ($value) {
            return trim($value);
        });

        $this->host = $input->askQuestion($question);
    }

    private function getHostPort(SymfonyStyle $input)
    {
        $question = new Question('Please provide your database host port', 3306);

        $this->port = $input->askQuestion($question);
    }

    private function getHostUserName(SymfonyStyle $input)
    {
        $question = new Question('Please provide your database host username');

        $this->userName = $input->askQuestion($question);
    }

    private function getHostPassword(SymfonyStyle $input)
    {
        $question = new Question('Please provide your database host password');
        $question->setHidden(true);

        $this->password = $input->askQuestion($question);
    }

    private function getAdapter(SymfonyStyle $input)
    {
        $driversInfo = $this->getDriversList();

        $question = new ChoiceQuestion('Which default adapter you want to use',
            $driversInfo['drivers'], $driversInfo['default']);

        $this->adapter = $input->askQuestion($question);
    }

    private function shouldCompress(SymfonyStyle $input)
    {
        $question = new ConfirmationQuestion('Should we compress the files after backup?', false);

        $this->compress = $input->askQuestion($question);
    }

    private function confirm(SymfonyStyle $input)
    {
        $question = new ConfirmationQuestion('Are you that you have used the correct information?');

        return $input->askQuestion($question);
    }

    private function saveData()
    {
        $this->save([
            'database_server' => [
                'host' => trim($this->host),
                'port' => (int) trim($this->port),
                'username' => trim($this->userName),
                'password' => trim($this->password)
            ],
            'adapter' => [
                'default' => trim($this->adapter)
            ],
            'path' => getcwd().'/backup',
            'compress' => (bool) trim($this->compress)
        ]);
    }

    private function checkFileExists(SymfonyStyle $input)
    {
        $overWrite = true;

        if (file_exists(getcwd().'/config.yml')) {
            $question = 'The configuration file already exists, are you sure you want to overwrite it?';
            $overWrite = $input->confirm($question, false);
        }

        return $overWrite;
    }

    private function getDriversList()
    {
        $finder = new Finder();
        $finder->in(__DIR__.'/../Classes/Drivers')->files()->sortByName();
        $iterator = $finder->getIterator();
        $drivers = [];
        foreach ($iterator as $item) {
            $drivers[] = str_replace('backup.php', '', mb_strtolower($item->getBasename()));
        }

        $default = array_search('local', $drivers, true);

        return [
            'drivers' => $drivers,
            'default' => $default
        ];
    }
}
