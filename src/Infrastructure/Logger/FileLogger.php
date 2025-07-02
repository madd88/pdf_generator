<?php
namespace App\Infrastructure\Logger;

use Psr\Log\AbstractLogger;
use Psr\Log\LogLevel;

class FileLogger extends AbstractLogger
{
    private string $logFile;
    private $fileHandle;
    private array $logLevels = [
        LogLevel::EMERGENCY => 0,
        LogLevel::ALERT     => 1,
        LogLevel::CRITICAL  => 2,
        LogLevel::ERROR     => 3,
        LogLevel::WARNING   => 4,
        LogLevel::NOTICE    => 5,
        LogLevel::INFO      => 6,
        LogLevel::DEBUG     => 7,
    ];

    private int $minLogLevel = 7;

    public function __construct(string $logFile, string $minLogLevel = LogLevel::DEBUG)
    {
        $this->logFile = $logFile;

        $this->minLogLevel = $this->logLevels[$minLogLevel] ?? $this->logLevels[LogLevel::DEBUG];

        // Создаем директорию, если не существует
        $logDir = dirname($logFile);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0777, true);
        }
    }

    public function log($level, $message, array $context = []): void
    {
        $levelPriority = $this->logLevels[$level] ?? 8;
        if ($levelPriority > $this->minLogLevel) {
            return;
        }

        $logEntry = sprintf(
            "[%s] [%s] %s %s\n",
            date('Y-m-d H:i:s'),
            strtoupper($level),
            $this->interpolate($message, $context),
            !empty($context) ? json_encode($context) : ''
        );

        $this->writeToFile($logEntry);
    }

    private function interpolate(string $message, array $context = []): string
    {
        $replace = [];
        foreach ($context as $key => $val) {
            $replace['{' . $key . '}'] = $val;
        }

        return strtr($message, $replace);
    }

    private function writeToFile(string $logEntry): void
    {
        if ($this->fileHandle === null) {
            $this->fileHandle = fopen($this->logFile, 'a');
            if (!$this->fileHandle) {
                throw new \RuntimeException("Unable to open log file: {$this->logFile}");
            }
        }

        fwrite($this->fileHandle, $logEntry);
    }

    public function __destruct()
    {
        if ($this->fileHandle) {
            fclose($this->fileHandle);
        }
    }
}