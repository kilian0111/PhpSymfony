<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\SongRepository;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Song;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class SongController extends AbstractController
{
    #[Route('/song', name: 'app_song')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/SongController.php',
        ]);
    }

    #[Route('/api/songs', name: 'song.getAll', methods: ['GET'])]
    public function getAllSongs(SongRepository $repository, SerializerInterface $serializer): JsonResponse
    {
        $songs = $repository->findAll($serializer);
        $jsonSongs = $serializer->serialize($songs, 'json');
        return new JsonResponse($jsonSongs, JsonResponse::HTTP_OK, [], true);
    }

    #[Route('/api/songs/{idSong}', name: 'song.get', methods: ['GET'])]
    public function getOneSong($idSong, SongRepository $repository, SerializerInterface $serializer): JsonResponse
    {
        $songs = $repository->find($idSong);
        $jsonSongs = $serializer->serialize($songs, 'json');
        return new JsonResponse($jsonSongs, JsonResponse::HTTP_OK, [], true);
    }

    
    #[Route('/api/songs', name: 'song.create', methods: ['POST'])]
    public function createSong(Request $req, SongRepository $repository, SerializerInterface $serializer, EntityManagerInterface $entityManager, UrlGeneratorInterface $urlGenerator): JsonResponse
    {
        $song = $serializer->deserialize($req->getContent(), Song::class, 'json');

        $song->setCreatedAt(new \DateTime())->setUpdatedAt(new \DateTime())->setStatus("on");

        $entityManager->persist($song);
        $entityManager->flush();    
        $jsonSongs = $serializer->serialize($song, 'json');
        $location = $urlGenerator->generate('song.get', ['idSong' => $song->getId()], urlGeneratorInterface::ABSOLUTE_URL);
        return new JsonResponse($jsonSongs, JsonResponse::HTTP_CREATED, ['Location' => $location], true);
    }

    #[Route('/api/songs/{song}', name:'song.delete', methods: ["DELETE"])]
    public function deleteSong(Song $song, EntityManagerInterface $entityManager): JsonResponse
    {
        $entityManager->remove($song);
        $entityManager->flush();
        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }

    #[Route('/api/songs/{song}', name:'song.update', methods: ["PUT", "PATCH"])]
    public function updateSong(Song $song, Request $req, SerializerInterface $serializer, EntityManagerInterface $entityManager): JsonResponse
    {
        $serializer->deserialize($req->getContent(), Song::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $song]);
        $song->setUpdatedAt(new \DateTime());
        $entityManager->flush();
        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }

}
