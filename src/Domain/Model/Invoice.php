<?php

namespace App\Domain\Model;

class Invoice
{
    private int $ext_id;
    private array $data;
    private int $year;
    private string $file_id;

    public function __construct(
        int $ext_id,
        int $year,
        array $data,
        string $file_id,

    ) {
        $this->ext_id = $ext_id;
        $this->year = $year;
        $this->data = $data;
        $this->file_id = $file_id;
    }

    public function getExtId(): int
    {
        return $this->ext_id;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function getYear(): int
    {
        return $this->year;
    }

    public function getFileId(): string
    {
        return $this->file_id;
    }

    public function toArray(): array
    {
        return [
            'ext_id'  => $this->ext_id,
            'year'    => $this->year,
            'data'    => $this->data,
            'file_id' => $this->file_id,
        ];
    }
}