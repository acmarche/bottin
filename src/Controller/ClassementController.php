<?php

namespace AcMarche\Bottin\Controller;

use AcMarche\Bottin\Classement\Handler\ClassementHandler;
use AcMarche\Bottin\Classement\Message\ClassementUpdated;
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
    /**
     * @var ClassementHandler
     */
    private $classementHandler;

    public function __construct(
        ClassementRepository $classementRepository,
        ClassementHandler $classementHandler,
        CategoryRepository $categoryRepository,
        PathUtils $pathUtils
    ) {
        $this->classementRepository = $classementRepository;
        $this->categoryRepository = $categoryRepository;
        $this->pathUtils = $pathUtils;
        $this->classementHandler = $classementHandler;
    }

    /**
     * Displays a form to create a new classement entity.
     *
     * @Route("/edit/{id}", name="bottin_classement_new", methods={"GET", "POST"})
     */
    public function edit(Fiche $fiche, Request $request)
    {
        $form = $this->createForm(ClassementType::class);

        $classements = $this->classementRepository->getByFiche($fiche);
        $classements = $this->pathUtils->setPathForClassements($classements);
        $roots = $this->categoryRepository->getRootNodes();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $request->request->get('classement');
            $categoryId = (int) $data['categorySelected'];

            try {
                $this->classementHandler->handleNewClassement($fiche, $categoryId);
                $this->dispatchMessage(new ClassementUpdated($fiche->getId()));
                $this->addFlash('success', 'Le classement a bien été ajouté');

                return $this->redirectToRoute('bottin_classement_new', ['id' => $fiche->getId()]);
            } catch (\Exception $e) {
                $this->addFlash('danger', $e->getMessage());

                return $this->redirectToRoute('bottin_classement_new', ['id' => $fiche->getId()]);
            }
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
