<?php

namespace AcMarche\Bottin\Bce\Import;

use AcMarche\Bottin\Bce\Entity\Code;
use AcMarche\Bottin\Bce\Repository\CodeRepository;
use AcMarche\Bottin\Bce\Utils\CsvReader;

class CodeHandler implements ImportHandlerInterface
{
    private CodeRepository $codeRepository;
    private CsvReader $csvReader;

    public function __construct(CodeRepository $codeRepository, CsvReader $csvReader)
    {
        $this->codeRepository = $codeRepository;
        $this->csvReader = $csvReader;
    }

    public function start(): void
    {
    }

    /**
     * @return Code[]
     *
     * @throws \Exception
     */
    public function readFile(string $fileName): iterable
    {
        return $this->csvReader->readFileAndConvertToClass($fileName);
    }

    /**
     * @param Code $data
     */
    public function handle($data)
    {
        if ($code = $this->codeRepository->checkExist($data->code, $data->language, $data->category)) {
            $code->description = $data->description;
        } else {
            $this->codeRepository->persist($data);
        }
    }

    /**
     * @param Code $data
     * @return string
     */
    public function writeLn($data): string
    {
        return $data->code;
    }

    public function flush(): void
    {
        $this->codeRepository->flush();
    }

    public static function getDefaultIndexName(): string
    {
        return 'code';
    }
}
