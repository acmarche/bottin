<?php

namespace AcMarche\Bottin\Bce\Import;

use AcMarche\Bottin\Bce\Entity\Code;
use AcMarche\Bottin\Bce\Repository\CodeRepository;
use AcMarche\Bottin\Bce\Utils\SymfonyStyleFactory;

class CodeHandler
{
    use SymfonyStyleFactory;

    private CodeRepository $codeRepository;

    public function __construct(CodeRepository $codeRepository)
    {
        $this->codeRepository = $codeRepository;
    }

    public static function getDefaultIndexName(): string
    {
        return 'code';
    }

    /**
     * @param iterable|Code[] $codes
     */
    public function handle(iterable $codes):?object
    {
        foreach ($codes as $data) {
            if (!$this->codeRepository->checkExist($data->code, $data->language, $data->category)) {
                $code = $data;
                $this->codeRepository->persist($code);
            }
            $this->writeLn($data->code);
        }
        $this->codeRepository->flush();
    }
    public function flush(): void
    {
        // TODO: Implement flush() method.
    }
}
