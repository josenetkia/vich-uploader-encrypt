<?php

namespace SfCod\VichUploaderEncrypt\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Finder\Finder;
use SfCod\VichUploaderEncrypt\Crypt\Encryption;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;

class EncryptFileCommand extends Command
{
    /**
     * @var array
     */
    protected $vichUploaderMappings;

    /**
     * @var Encryption
     */
    protected $encryption;

    /**
     * @param null|string $name
     * @param Encryption $encryption
     * @param array $vichUploaderMappings
     */
    public function __construct(Encryption $encryption, array $vichUploaderMappings)
    {
        parent::__construct();

        $this->vichUploaderMappings = $vichUploaderMappings;
        $this->encryption = $encryption;
    }

    protected function configure()
    {
        $this
            ->setName('cryptography:file')
            ->addArgument(
                'action',
                InputArgument::REQUIRED,
                sprintf(
                    'Action can be %s or %s',
                    Encryption::ACTION_ENCRYPT,
                    Encryption::ACTION_DECRYPT
                )
            )
            ->setDescription('Encrypt all file which should be encrypted.');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (empty($this->vichUploaderMappings)) {
            throw new \InvalidArgumentException('Please configure VichUploader before use command!');
        }

        $action = $input->getArgument('action');
        $helper = $this->getHelper('question');
        $data = [];
        foreach ($this->vichUploaderMappings as $key => $mapping) {
            $uploadDestination = $mapping['upload_destination'];

            $question = new ChoiceQuestion(
                sprintf('Do you want to %s(%s) files in folder: %s', $action, $key, $uploadDestination),
                ['y', 'n'],
                'y'
            );

            $answer = $helper->ask($input, $output, $question);

            if ('y' === $answer) {
                $data[] = $uploadDestination;
            }
        }

        if (empty($data)) {
            return;
        }

        $finder = new Finder();
        $finder->files()->in($data);
        $progress = new ProgressBar($output, $finder->count());

        foreach ($finder as $file) {
            $progress->setMessage($file->getRealPath());
            if ($action === Encryption::ACTION_ENCRYPT) {
                file_put_contents(
                    $file->getRealPath(),
                    $this->encryption->encrypt(
                        file_get_contents(
                            $file->getRealPath()
                        )
                    )
                );
            }

            if ($action === Encryption::ACTION_DECRYPT) {
                file_put_contents(
                    $file->getRealPath(),
                    $this->encryption->decrypt(
                        file_get_contents(
                            $file->getRealPath()
                        )
                    )
                );
            }

            $progress->advance();
        }

        $progress->finish();
        $output->writeln(
            [
                '',
                '******************',
                'Done',
                '******************',
            ]
        );
    }
}
