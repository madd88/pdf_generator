<?php
namespace App\Application\Service;

use App\Domain\Model\GeneratedFile;
use App\Application\Validator\TemplateValidator;
use setasign\Fpdi\Tcpdf\Fpdi;
use setasign\Fpdi\PdfReader\PageBoundaries;
use App\Domain\Repository\ClinicRepository;
use App\Domain\Repository\DiseasesRepository;

use Exception;

class PdfGenerator
{
    private $templatesPath;
    private $outputPath;

    public $assetsPath;
    private $logger;
    private ClinicRepository $clinicRepository;
    private DiseasesRepository $diseasesRepository;


    public function __construct(
        string $templatesPath,
        string $outputPath,
        string $assetsPath,
        $logger,
        ClinicRepository $clinicRepository,
        DiseasesRepository $diseasesRepository
    ) {
        $this->templatesPath = $templatesPath;
        $this->outputPath = $outputPath;
        $this->assetsPath = $assetsPath; // Сохраняем путь
        $this->logger = $logger;
        $this->clinicRepository = $clinicRepository;
        $this->diseasesRepository = $diseasesRepository;

    }

    public function generate(string $templateName, array $data): GeneratedFile
    {
        $this->logger->info("Generating PDF for template: $templateName");

        // Валидация данных
        $errors = TemplateValidator::validate($templateName, $data);
        if (!empty($errors)) {
            throw new \InvalidArgumentException(json_encode($errors));
        }

        $templateFile = "{$this->templatesPath}/{$templateName}.php";

        if (!file_exists($templateFile)) {
            throw new Exception("Template not found: $templateName");
        }
        $assetsPath = $this->assetsPath;

        $pdf = new Fpdi();
        $uid = uniqid();
        $filename = $uid . '.pdf';
        ob_start();
        require $templateFile;
        ob_end_clean();
        $filePath = str_replace('/', DIRECTORY_SEPARATOR, "{$this->outputPath}/{$filename}");
//        $pdf->Output('F', $filePath);
        $pdfData = $pdf->Output('', 'S'); // 'S' - возврат как строку
        if (!in_array($templateName, ['medical', 'invoice'])) {
            file_put_contents($filePath, $pdfData);
        }


        return new GeneratedFile(
            $uid,
            $filename,
            $templateName,
            $data,
            $filePath
        );
    }
}