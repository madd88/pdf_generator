<?php
namespace App\Domain\Model;

class GeneratedFile
{
    private string $id;
    private string $filename;
    private string $template;
    private array $data;
    private string $filePath;
    private \DateTime $createdAt;

    public function __construct(
        string $id,
        string $filename,
        string $template,
        array $data,
        string $filePath,
        ?\DateTime $createdAt = null
    ) {
        $this->id = $id;
        $this->filename = $filename;
        $this->template = $template;
        $this->data = $data;
        $this->filePath = $filePath;
        $this->createdAt = $createdAt ?: new \DateTime();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getFilename(): string
    {
        return $this->filename;
    }

    public function getTemplate(): string
    {
        return $this->template;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function getFilePath(): string
    {
        return $this->filePath;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'filename' => $this->filename,
            'template' => $this->template,
            'data' => $this->data,
            'file_path' => $this->filePath,
            'created_at' => $this->createdAt->format('Y-m-d H:i:s')
        ];
    }
}