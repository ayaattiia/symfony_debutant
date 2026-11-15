<?php

namespace App\Controller;

use App\Entity\Image;

use App\Entity\Job;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use App\Form\JobType;
use Symfony\Component\HttpFoundation\Request;

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
public function show(EntityManagerInterface $entityManager, int $id): Response
{
    $job = $entityManager->getRepository(Job::class)->find($id);

    if (!$job) {
        throw $this->createNotFoundException('Job introuvable');
    }

    return $this->render('job/show.html.twig', [
        'job' => $job,
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
        'delete'.$job->getId(),
        $request->request->get('_token')
    )) {
        $entityManager->remove($job);
        $entityManager->flush();
    }

    return $this->redirectToRoute('job_index');
}




}
