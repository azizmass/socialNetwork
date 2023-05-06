<?php

namespace App\Controller\Covoiturage;

use App\Entity\Covoiturage\RequestCovoiturage;
use App\Repository\Accounts\MemberRepository;
use App\Repository\Covoiturage\CovoiturageRepository;
use App\Repository\Covoiturage\RequestCovoiturageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/covoiturageRequest')]
class CovoiturageRequestController extends AbstractController
{
    #[Route('/', name: 'req.create', methods: ['POST'])]
    public function create(Request $request, RequestCovoiturageRepository $repository, CovoiturageRepository $covoiturageRepository, MemberRepository $memberRepository, SerializerInterface $serializer): JsonResponse {
        try {
            $data = json_decode($request->getContent(), true);
            $sender = $memberRepository->find($data['sender_id']);
            $covoiturage = $covoiturageRepository->find($data['covoiturage_id']);
            $req = new RequestCovoiturage();
            $req->setCovoiturage($covoiturage);
            $req->setSender($sender);
            $repository->save($req, true);

            $data = $serializer->serialize($req, JsonEncoder::FORMAT, [AbstractNormalizer::GROUPS => ['ReqCov: POST']]);
            return new JsonResponse($data, Response::HTTP_CREATED, [], true);

        }catch (\Exception $e) {
            return new JsonResponse($e->getMessage(), 500, [], true);
        }
    }

    #[Route('/', name: 'req.findAll', methods: ['GET'])]
    public function findAll(RequestCovoiturageRepository $repository, SerializerInterface $serializer): JsonResponse {
        try {
            $reqs = $repository->findAll();
            $data = $serializer->serialize($reqs, JsonEncoder::FORMAT, [AbstractNormalizer::GROUPS => ['ReqCov: POST']]);
            return new JsonResponse($data, Response::HTTP_CREATED, [], true);
        }catch (\Exception $e) {
            return new JsonResponse($e->getMessage(), 500, [], true);
        }
    }
    #[Route('/covoiturage', name: 'req.findByCov', methods: ['GET'])]
    public function findByCov(Request $request, RequestCovoiturageRepository $repository, CovoiturageRepository $covoiturageRepository, SerializerInterface $serializer): JsonResponse {
        try {
            $cov = $covoiturageRepository->find($request->query->get('id'));
            $reqs = $repository->findBy(['covoiturage' => $cov]);
            $data = $serializer->serialize($reqs, JsonEncoder::FORMAT, [AbstractNormalizer::GROUPS => ['ReqCov: POST']]);
            return new JsonResponse($data, Response::HTTP_CREATED, [], true);
        }catch (\Exception $e) {
            return new JsonResponse($e->getMessage(), 500, [], true);
        }
    }
    #[Route('/sender', name: 'req.findBySender', methods: ['GET'])]
    public function findBySender(Request $request, RequestCovoiturageRepository $repository, MemberRepository $memberRepository, SerializerInterface $serializer): JsonResponse {
        try {
            $sender = $memberRepository->find($request->query->get('id'));
            $reqs = $repository->findBy(['sender' => $sender]);
            $data = $serializer->serialize($reqs, JsonEncoder::FORMAT, [AbstractNormalizer::GROUPS => ['ReqCov: POST']]);
            return new JsonResponse($data, Response::HTTP_CREATED, [], true);
        }catch (\Exception $e) {
            return new JsonResponse($e->getMessage(), 500, [], true);
        }
    }
    #[Route('/request', name: 'req.findByID', methods: ['GET'])]
    public function findByID(Request $request, RequestCovoiturageRepository $repository, SerializerInterface $serializer): JsonResponse {
        try {
            $reqs = $repository->find($request->query->get('id'));
            $data = $serializer->serialize($reqs, JsonEncoder::FORMAT, [AbstractNormalizer::GROUPS => ['ReqCov: POST']]);
            return new JsonResponse($data, Response::HTTP_CREATED, [], true);
        }catch (\Exception $e) {
            return new JsonResponse($e->getMessage(), 500, [], true);
        }
    }
    #[Route('/delete', name: 'req.delete', methods: ['DELETE'])]
    public function delete(Request $request, RequestCovoiturageRepository $repository, SerializerInterface $serializer): JsonResponse {
        try {
            $repository->remove($repository->find($request->query->get('id')), true);
            return $this->json("covoiturage deleted successfully",200);
        }catch (\Exception $e) {
            return new JsonResponse($e->getMessage(), 500, [], true);
        }
    }
}
