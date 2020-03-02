<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\RepositoryAwareTrait;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends Controller
{
    use RepositoryAwareTrait;

    /**
     * Finds and displays a Project entity.
     *
     * @Route("/profile", name="profile")
     */
    public function detailsAction(Request $request)
    {
        /** @var User $user */
        $user = $this->getUser();

        if ($request->getMethod() == "POST") {
            $profile = $request->get('profile');

            if (isset($profile['firstname'])) {
                $user->setFirstname($profile['firstname']);
            }

            if (isset($profile['middlename'])) {
                $user->setMiddlename($profile['middlename']);
            }

            if (isset($profile['phone'])) {
                $user->setPhone($profile['phone']);
            }

            if (isset($profile['lastname'])) {
                $user->setLastname($profile['lastname']);
            }

            if (isset($profile['username'])) {
                $user->setUsername($profile['username']);
            }

            if (isset($profile['email'])) {
                $user->setEmail($profile['email']);
            }

            $em = $this->getEm();
            $em->persist($user);
            $em->flush();
        }

        return $this->render('profile/index.html.twig', [
            'user' => $user
        ]);
    }
}
