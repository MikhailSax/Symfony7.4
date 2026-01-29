<?php

namespace App\Service\Files;

use App\Entity\Product;
use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;

class UploadFileService
{
    public function __construct(

        private string           $targetDirectory,
        private FileSystem       $fileSystem,
        private LoggerInterface  $logger,
        private SluggerInterface $slugger,
    )
    {
    }

    /**
     * Метод возвращает директорию в которую всё сохраняется
     * @return string
     */
    public function getTargetDirectory(): string
    {
        return $this->targetDirectory;
    }

    /**
     * Метод принимает в себя id продукта и создаёт папку для хранения файлов
     * @param string $directoryName
     * @return void
     */
    public function createDirectory(string $directoryName): void
    {
        try {
            $path = $this->targetDirectory . $directoryName;
            $this->fileSystem->mkdir($path);
            $this->logger->info('Папка создана: ' . $directoryName);

        } catch (IOExceptionInterface $exception) {
            echo "Ошибка при создании директории: " . $exception->getMessage() . PHP_EOL;
        }
    }


    public function getCurrentDirectory(string $id): string
    {
        return $this->getTargetDirectory() . $id;
    }

    public function saveFile($file, string $id): string
    {
        if ($file) {
            $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFileName = $this->slugger->slug($originalFilename);
            $newFilename = $safeFileName . '-' . uniqid() . '.' . $file->guessExtension();
            try {
                if ($this->fileSystem->exists($this->getCurrentDirectory($id))) {
                    $this->logger->info('Папка с данным иминем уже существует.');
                    $file->move($this->getCurrentDirectory($id), $newFilename);
                    return $newFilename;
                }
                $this->createDirectory($id);
                $file->move($this->getCurrentDirectory($id), $newFilename);
                return $newFilename;

            } catch (FileException $e) {
                $this->logger->info($e->getMessage());
                return $e->getMessage();
            }
        }
    }

}
