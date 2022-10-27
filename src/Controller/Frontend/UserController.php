<?php

declare(strict_types=1);

namespace Groshy\Controller\Frontend;

use AutoMapperPlus\AutoMapperInterface;
use Groshy\Form\Model\ProfileFormModel;
use Groshy\Form\Type\ProfileFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Talav\Component\User\Manager\UserManagerInterface;

#[Route('/user')]
class UserController extends AbstractController
{
    public function __construct(
        private UserManagerInterface $userManager,
        private AutoMapperInterface $mapper,
    ) {
    }

    #[Route('/profile')]
    public function profileAction(Request $request): Response
    {
        $user = $this->getUser();
        $model = $this->mapper->map($user, ProfileFormModel::class);
        $form = $this->createForm(ProfileFormType::class, $model);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->mapper->mapToObject($form->getData(), $user);
            $this->userManager->update($user, true);
            $this->addFlash('success', 'Profile has been updated sucessefully');
        }

        return $this->render('user/profile.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
