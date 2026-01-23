<?php
namespace App\Command;

use App\Service\Admin\AdminCommandService;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;



#[AsCommand(
    name: 'app:create-admin',
    description: 'Команда создаёт админ пользователя.',
)]
class CreateAdminCommand extends Command
{
    public function __construct(
        private AdminCommandService $adminCommandService,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $name = $input->getArgument('name');
        $lastName = $input->getArgument('lastName');
        $email = $input->getArgument('email');
        $phone = $input->getArgument('phone');
        $password = $input->getArgument('password');

        $this->adminCommandService->makeAdmin(
           name: $name,
            lastName: $lastName,
            password: $password,
            mail: $email,
            phone: $phone
        );

        $output->writeln('Имя администратора: ' . $name);
        $output->writeln('Круто работает 😎');

        return Command::SUCCESS;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('name', InputArgument::REQUIRED, 'Имя администратора')
            ->addArgument('lastName', InputArgument::REQUIRED, 'Фамилия администратора')
            ->addArgument('email', InputArgument::REQUIRED, 'Email администратора')
            ->addArgument('phone', InputArgument::REQUIRED, 'Телефон администратора')
            ->addArgument('password', InputArgument::REQUIRED, 'Пароль администратора');

    }
}

