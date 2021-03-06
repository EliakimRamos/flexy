<?php

namespace App\Controller;

use App\Entity\Image;
use App\Form\ImageType;
use App\Repository\ImageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/image')]
class ImageController extends AbstractController
{
    #[Route('/', name: 'image_index', methods: ['GET'])]
    public function index(ImageRepository $imageRepository): Response
    {
        return $this->render('image/index.html.twig', [
            'images' => $imageRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'image_new', methods: ['GET', 'POST'])]
    public function new(Request $request, SluggerInterface $slugger): Response
    {
        $image = new Image();
        $form = $this->createForm(ImageType::class, $image);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $fileImage = $form->get('path')->getData();
            if (!empty($fileImage)){
                $nameOriginal = pathinfo($fileImage->getClientOriginalName(),PATHINFO_FILENAME);
                $cleanName = $slugger->slug($nameOriginal);
                $newName = $cleanName.'-'.date('Ymd').'.'.$fileImage->guessExtension();

                try {
                    $fileImage->move(
                        $this->getParameter('upload_directory'),
                        $newName
                    );
                    $image->setPath($newName);
                } catch (FileException $e) {
                    throw $e->getMessage();
                }
            }
            $entityManager->persist($image);
            $entityManager->flush();

            return $this->redirectToRoute('image_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('image/new.html.twig', [
            'image' => $image,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'image_show', methods: ['GET'])]
    public function show(Image $image): Response
    {
        return $this->render('image/show.html.twig', [
            'image' => $image,
        ]);
    }

    #[Route('/{id}/edit', name: 'image_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Image $image, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(ImageType::class, $image);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $fileImage = $form->get('path')->getData();
            if (!empty($fileImage)){
                $nameOriginal = pathinfo($fileImage->getClientOriginalName(),PATHINFO_FILENAME);
                $cleanName = $slugger->slug($nameOriginal);
                $newName = $cleanName.'-'.date('Ymd').'.'.$fileImage->guessExtension();

                try {
                    $fileImage->move(
                        $this->getParameter('upload_directory'),
                        $newName
                    );
                    $image->setPath($newName);
                } catch (FileException $e) {
                    throw $e->getMessage();
                }
            }
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('image_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('image/edit.html.twig', [
            'image' => $image,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'image_delete', methods: ['POST'])]
    public function delete(Request $request, Image $image): Response
    {
        if ($this->isCsrfTokenValid('delete'.$image->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($image);
            $entityManager->flush();
        }

        return $this->redirectToRoute('image_index', [], Response::HTTP_SEE_OTHER);
    }
}
