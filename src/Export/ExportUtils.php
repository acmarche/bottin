<?php

namespace AcMarche\Bottin\Export;

use AcMarche\Bottin\Category\Repository\CategoryService;
use AcMarche\Bottin\Entity\Fiche;
use AcMarche\Bottin\Entity\Token;
use AcMarche\Bottin\Repository\FicheRepository;
use AcMarche\Bottin\Repository\SelectionRepository;
use AcMarche\Bottin\Utils\SortUtils;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

class ExportUtils
{
    public function __construct(
        private readonly RouterInterface $router,
        private readonly SelectionRepository $selectionRepository,
        private readonly CategoryService $categoryService,
        private readonly FicheRepository $ficheRepository
    ) {
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

        if ([] !== $categories) {
            $fiches = $this->categoryService->getFichesByCategoriesAndHerChildren($categories);
        } else {
            $fiches = $this->ficheRepository->findAllWithJoins();
        }

        return SortUtils::sortFiche($fiches);
    }

    public function replaceUrlToken(Fiche $fiche, ?string $message): ?string
    {
        if (null == $message) {
            return null;
        }

        $url = '';
        if (($token = $fiche->getToken()) instanceof Token) {
            $url = $this->generateUrlToken($token);
        }

        return preg_replace('#{urltoken}#', $url, $message);
    }
}
