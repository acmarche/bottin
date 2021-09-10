<?php

namespace AcMarche\Bottin\Export;

use AcMarche\Bottin\Entity\Fiche;
use AcMarche\Bottin\Entity\Token;
use AcMarche\Bottin\Repository\SelectionRepository;
use AcMarche\Bottin\Category\Repository\CategoryService;
use AcMarche\Bottin\Utils\SortUtils;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

class ExportUtils
{
    private RouterInterface $router;
    private SelectionRepository $selectionRepository;
    private CategoryService $categoryService;

    public function __construct(
        RouterInterface $router,
        SelectionRepository $selectionRepository,
        CategoryService $categoryService
    ) {
        $this->router = $router;
        $this->selectionRepository = $selectionRepository;
        $this->categoryService = $categoryService;
    }

    public function generateUrlToken(Token $token): string
    {
        return $this->router->generate(
            'bottin_backend_fiche_show',
            ['uuid' => $token->getUuid()],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
    }

    /**
     * @return array|Fiche[]
     */
    public function getFichesBySelection(string $username): array
    {
        $selections = $this->selectionRepository->findByUser($username);
        $categories = [];
        foreach ($selections as $selection) {
            $categories[] = $selection->getCategory();
        }
        $fiches = $this->categoryService->getFichesByCategoriesAndHerChildren($categories);
        $fiches = SortUtils::sortFiche($fiches);

        return $fiches;
    }

    public function replaceUrlToken(Fiche $fiche, string $message): string
    {
        $url = '';
        if ($token = $fiche->getToken()) {
            $url = $this->generateUrlToken($token);
        }

        $body = preg_replace('#{urltoken}#', $url, $message);

        return $body;
    }
}
