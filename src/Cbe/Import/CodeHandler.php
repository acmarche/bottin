<?php

namespace AcMarche\Bottin\Cbe\Import;

use AcMarche\Bottin\Cbe\Entity\Code;
use AcMarche\Bottin\Cbe\Repository\CodeRepository;

class CodeHandler implements ImportHandlerInterface
{
    private CodeRepository $codeRepository;

    public function __construct(CodeRepository $codeRepository)
    {
        $this->codeRepository = $codeRepository;
    }

    public static function getDefaultIndexName(): string
    {
        return 'handler_code';
    }

    /**
     * @param array|Code[] $codes
     */
    public function handle(array $codes)
    {
        dump('code');
        foreach ($codes as $data) {
            if (!$this->codeRepository->checkExist($data->code, $data->language, $data->category)) {
                $code = $data;
                $this->codeRepository->persist($code);
            }
        }
        //    $this->codeRepository->flush();
    }
}
