<?php

namespace App\Command;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Serializer\Encoder\YamlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class CreateUserFormatFromCsvXmlYmlFileCommand extends Command
{
    private EntityManagerInterface $entityManager;
    private string $datasDirectory;
    private $io;
    private UserRepository $userRepository;
    private $progressBar;

    protected static $defaultName = 'app:importfile';

    public function __construct(
        EntityManagerInterface $entityManager,
        string $datasDirectory,
        UserRepository $userRepository
    ) {
        parent::__construct();

        $this->entityManager = $entityManager;
        $this->datasDirectory = $datasDirectory;
        $this->userRepository = $userRepository;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Importer des données via CSV ou Xml ou Yaml fichier')
        ;
    }

    /**
     * [_initialize description]
     *
     * @param   InputInterface   $input   [$input description]
     * @param   OutputInterface  $output  [$output description]
     *
     * @return  void                      [return description]
     */
    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->io = new SymfonyStyle($input, $output);
        // creates a new progress bar (50 units)
        $this->progressBar = new ProgressBar($output);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->createUsers();
        return Command::SUCCESS;
    }

    private function getDataFromFile(): array
    {
        $file = $this->datasDirectory . 'user4.csv';

        // recuperer l extention du fuchier avec function pathinfo
        $fileExtention = pathinfo($file, PATHINFO_EXTENSION);

        $normalizer = [new ObjectNormalizer()];

        $encoder = [
            new CsvEncoder(),
            new XmlEncoder(),
            new YamlEncoder()
        ];

        $serializer = new Serializer($normalizer, $encoder);
        //recuper le continue de file en string
        /** @var string $fileString */
        $fileString = \file_get_contents($file);

        $data = $serializer->decode($fileString, $fileExtention);

        // on recupere juste le data result
        if (array_key_exists("results", $data)) {
            // dd($data['result']);
            return $data['results'];
        }
        return $data;
    }

    private function createUsers(): void
    {
        $d = $this->getDataFromFile();
        //dd($d);
        $this->io->section("Création des users via le ficgier CSV !!");
        $userCreated = 0;


        // starts and displays the progress bar
        $this->progressBar->start();
        foreach ($this->getDataFromFile() as $row) {
            if (array_key_exists("email", $row) && !empty($row["email"])) {
                $user = $this->userRepository->findOneBy(['email' => $row['email']]);

                if (!$user) {
                    $user = new User();

                    $user->setEmail($row['email'])
                        ->setPassword('badpassword')
                        ->setIsVerified(true);

                    $this->entityManager->persist($user);
                    $userCreated ++;
                }
                // advances the progress bar 1 unit
                $this->progressBar->advance();
            }
        }

        $this->entityManager->flush();
        if ($userCreated > 0) {
            $string = "{$userCreated} Utilisateurs crées dans DB !!";
        } else {
            $string = "Aucun user creer !!";
        }
        // ensures that the progress bar is at 100%
        $this->progressBar->finish();
        $this->io->success($string);

        // $d =  $this->getDataFromFile();
        // dd($d);
    }
}
