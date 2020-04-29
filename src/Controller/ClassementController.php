<?php

namespace AcMarche\Bottin\Controller;

use AcMarche\Bottin\Entity\Classement;
use AcMarche\Bottin\Entity\Fiche;
use AcMarche\Bottin\Form\ClassementType;
use AcMarche\Bottin\Repository\CategoryRepository;
use AcMarche\Bottin\Repository\ClassementRepository;
use AcMarche\Bottin\Utils\PathUtils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Classement controller.
 *
 * @Route("/classement")
 * @IsGranted("ROLE_BOTTIN_ADMIN")
 */
class ClassementController extends AbstractController
{
    /**
     * @var ClassementRepository
     */
    private $classementRepository;
    /**
     * @var CategoryRepository
     */
    private $categoryRepository;
    /**
     * @var PathUtils
     */
    private $pathUtils;

    public function __construct(
        ClassementRepository $classementRepository,
        CategoryRepository $categoryRepository,
        PathUtils $pathUtils
    ) {
        $this->classementRepository = $classementRepository;
        $this->categoryRepository = $categoryRepository;
        $this->pathUtils = $pathUtils;
    }

    /**
     * Displays a form to create a new classement entity.
     *
     * @Route("/edit/{id}", name="bottin_classement_new", methods={"GET", "POST"})
     */
    public function edit(Fiche $fiche, Request $request)
    {
        $classement = new Classement();
        $classement->setFiche($fiche);

        $form = $this->createForm(ClassementType::class, $classement);

        $classements = $this->classementRepository->getByFiche($fiche);
        $classements = $this->pathUtils->setPathForClassements($classements);
        $roots = $this->categoryRepository->getRootNodes();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $categoryId = $classement->getCategorySelected();

            if (!$categoryId) {
                $this->addFlash('danger', 'La référence à la rubrique n\'a pas été trouvée');

                return $this->redirectToRoute('bottin_classement_new', ['id' => $fiche->getId()]);
            }

            $category = $this->categoryRepository->find($categoryId);

            if (!$category) {
                throw $this->createNotFoundException('La catégorie n\'a pas été trouvée.');
            }

            $classement->setCategory($category);

            /**
             * je recupere les ids du classement.
             */
            $categories = $classements->map(
                function ($obj) {
                    return $obj->getCategory();
                }
            );

            if ($categories->contains($category)) {
                $this->addFlash('danger', 'La fiche est déjà classée dans cette rubrique');

                return $this->redirectToRoute('bottin_classement_new', ['id' => $fiche->getId()]);
            }

            $category = $this->categoryRepository->getTree($category->getRealMaterializedPath());
            if ($category->getChildNodes()->count() > 0) {
                $this->addFlash('danger', 'Vous ne pouvez pas classer dans une rubrique qui contient des enfants');

                return $this->redirectToRoute('bottin_classement_new', ['id' => $fiche->getId()]);
            }

            $this->classementRepository->insert($classement);
            $this->addFlash('success', 'Le classement a bien été ajouté');

            return $this->redirectToRoute('bottin_classement_new', ['id' => $fiche->getId()]);
        }

        return $this->render(
            '@AcMarcheBottin/classement/edit.html.twig',
            [
                'fiche' => $fiche,
                'classements' => $classements,
                'roots' => $roots,
                'form' => $form->createView(),
            ]
        );
    }
}
