<?php

namespace App\Controller;

use App\Entity\Image;

use App\Entity\Candidature;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use Symfony\Component\HttpFoundation\Request;


use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


use App\Entity\Job;
use App\Form\JobType;

final class JobController extends AbstractController
{
    #[Route('/job', name: 'app_job')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $job = new Job();
        $job->setType('CDI');
        $job->setCompany('EMKA Med Tunisie');
        $job->setDiscription('Technicien méthodes électronique');
        $job->setEmail('recrutement@emkaelec.com');
        $job->setExpiresAt(new \DateTime('+30 days'));

        $image = new Image();
        $image->setUrl('https://cdn.pixabay.com/photo/2015/10/30/10/03/gold-1013618_960_720.jpg');
        $image->setAlt('job de reves');

        $entityManager->persist($image);
        $job->setImage($image);

        $entityManager->persist($job);
        $entityManager->flush();

        $entityManager->persist($job);
        $entityManager->flush();

        return $this->render('job/index.html.twig', [
            'id' => $job->getId(),
        ]);
    }





    #[Route('/job/{id}', name: 'job_show')]
    public function show(
        EntityManagerInterface $entityManager,
        int $id
    ): Response {
        $job = $entityManager
            ->getRepository(Job::class)
            ->find($id);

        if (!$job) {
            throw $this->createNotFoundException('Job introuvable');
        }

        $listCandidatures = $entityManager
            ->getRepository(Candidature::class)
            ->findBy(['job' => $job]);

        return $this->render('job/show.html.twig', [
            'job' => $job,
            'listCandidatures' => $listCandidatures,
        ]);
    }



    #[Route('/jobs', name: 'job_index')]
    public function listJobs(EntityManagerInterface $entityManager): Response
    {
        $jobs = $entityManager->getRepository(Job::class)->findAll();

        return $this->render('job/list.html.twig', [
            'jobs' => $jobs,
        ]);
    }

    #[Route('/jobs/new', name: 'job_new')]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $job = new Job();

        $form = $this->createForm(JobType::class, $job);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($job);
            $entityManager->flush();

            return $this->redirectToRoute('job_index');
        }

        return $this->render('job/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/jobs/{id}/edit', name: 'job_edit')]
    public function edit(
        Request $request,
        Job $job,
        EntityManagerInterface $entityManager
    ): Response {
        $form = $this->createForm(JobType::class, $job);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('job_index');
        }

        return $this->render('job/edit.html.twig', [
            'form' => $form,
        ]);
    }


    #[Route('/jobs/{id}/delete', name: 'job_delete', methods: ['POST'])]
    public function delete(
        Request $request,
        Job $job,
        EntityManagerInterface $entityManager
    ): Response {
        if ($this->isCsrfTokenValid(
            'delete' . $job->getId(),
            $request->request->get('_token')
        )) {
            $entityManager->remove($job);
            $entityManager->flush();
        }

        return $this->redirectToRoute('job_index');
    }

    #[Route('/candidatures', name: 'candidatures_home')]
    public function home(EntityManagerInterface $em): Response
    {
        $lesCandidats = $em
            ->getRepository(Candidature::class)
            ->findAll();

        return $this->render('job/home.html.twig', [
            'lesCandidats' => $lesCandidats,
        ]);
    }


    #[Route('/demo/candidatures', name: 'demo_candidatures')]
    public function demoCandidatures(
        EntityManagerInterface $entityManager
    ): Response {
        $job = $entityManager
            ->getRepository(Job::class)
            ->findOneBy([]);

        if (!$job) {
            throw $this->createNotFoundException('Aucun Job trouvé');
        }

        $candidature1 = new Candidature();
        $candidature1->setCandidat('Rhaiem');
        $candidature1->setContenu('formation J2EE');
        $candidature1->setDate(new \DateTime());
        $candidature1->setJob($job);

        $candidature2 = new Candidature();
        $candidature2->setCandidat('Salima');
        $candidature2->setContenu('formation Symfony');
        $candidature2->setDate(new \DateTime());
        $candidature2->setJob($job);

        $entityManager->persist($candidature1);
        $entityManager->persist($candidature2);
        $entityManager->flush();

        return $this->redirectToRoute('job_show', [
            'id' => $job->getId(),
        ]);
    }



    #[Route('/Ajouter', name: 'add_candidat', methods: ['GET', 'POST'])]
    public function ajouterCand(
        Request $request,
        EntityManagerInterface $em
    ): Response {
        $candidat = new Candidature();

        $form = $this->createFormBuilder($candidat)
            ->add('candidat', TextType::class)
            ->add('contenu', TextType::class)
            ->add('date', DateType::class, [
                'widget' => 'single_text',
            ])
            ->add('job', EntityType::class, [
                'class' => Job::class,
                'choice_label' => 'type',
            ])
            ->add('Valider', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($candidat);
            $em->flush();

            $this->addFlash('notice', 'Candidat bien ajouté');

            return $this->redirectToRoute('candidatures_home');
        }

        return $this->render('job/ajouter.html.twig', [
            'f' => $form->createView(),
        ]);
    }


    #[Route('/supp/{id}', name: 'cand_delete', methods: ['POST'])]
    public function deleteCandidature(
        Request $request,
        int $id,
        EntityManagerInterface $em
    ): Response {
        $candidat = $em
            ->getRepository(Candidature::class)
            ->find($id);

        if (!$candidat) {
            throw $this->createNotFoundException(
                'Candidature introuvable'
            );
        }

        if ($this->isCsrfTokenValid(
            'delete_cand' . $candidat->getId(),
            $request->request->get('_token')
        )) {
            $em->remove($candidat);
            $em->flush();

            $this->addFlash(
                'notice',
                'Candidature supprimée'
            );
        }

        return $this->redirectToRoute('candidatures_home');
    }





    #[Route('/editU/{id}', name: 'edit_user', methods: ['GET', 'POST'])]
    public function editCandidature(
        Request $request,
        int $id,
        EntityManagerInterface $em
    ): Response {
        $candidat = $em
            ->getRepository(Candidature::class)
            ->find($id);

        if (!$candidat) {
            throw $this->createNotFoundException(
                'Candidature introuvable'
            );
        }

        $form = $this->createFormBuilder($candidat)
            ->add('candidat', TextType::class)
            ->add('contenu', TextType::class)
            ->add('date', DateType::class, [
                'widget' => 'single_text',
            ])
            ->add('job', EntityType::class, [
                'class' => Job::class,
                'choice_label' => 'type',
            ])
            ->add('Valider', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash(
                'notice',
                'Candidature modifiée'
            );

            return $this->redirectToRoute('candidatures_home');
        }

        return $this->render('job/ajouter.html.twig', [
            'f' => $form->createView(),
        ]);
    }
}
