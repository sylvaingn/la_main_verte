<?php

namespace App\Controller;

use App\Entity\Company;
use App\Form\CompanyType;
use App\Repository\AddressRepository;
use App\Repository\CompanyRepository;
use App\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/company")
 */
class CompanyController extends AbstractController
{

    // /**
    //  * @Route("/result", name="company_result", methods={"GET"})
    //  */
    // public function found(UserRepository $userRepository): Response
    // {

    //     /* if (empty($_GET["word"])) {
    //         return $this->render('company/found.html.twig', [
    //             'users' => $userRepository->findCompanies($_GET["city"],true),
    //         ]);
    //     }

    //     else {
    //         return $this->render('company/found.html.twig', [
    //         'users' => $userRepository->findCompanies($_GET["city"],$_GET["word"]),
    //     ]);
    //     } */
        
    //     return $this->render('company/found.html.twig', [
    //         'users' => $userRepository->findCompanies($_GET["city"],$_GET["word"]),
    //     ]);
    
    // }

    /**
     * @Route("/result", name="company_result", methods={"GET"})
     */
    public function found(Request $request, UserRepository $userRepository, AddressRepository $addressRepository)
    // public function foundListMap(UserRepository $userRepository): Response
    {
        // if ($request->isXmlHttpRequest() || $request->query->get('showJson') == 1) {

            $users = $userRepository->findCompanies($_GET["city"], $_GET["word"]);

            $companies_map = [];
            foreach ($users as $user) {

                $resultat = $userRepository->find($user);
                $address = $addressRepository->findOneBy(['id' => $resultat->getAddress()]);
                // dd($address);

                $companies_map[] = [
                    'id' => $resultat->getId(),
                    'name' => $resultat->getCompany(),
                    'postcode' => $address->getZipCode(),
                    'latitude' => $address->getLatitude(),
                    'longitude' => $address->getLongitude()
                ];
            }

            $jsonData = $companies_map;
            // dd($jsonData);

            // return new JsonResponse($jsonData);

            return $this->render('company/onglet.html.twig', [
                'users' => $users
            ]);

        // }
        // else{
        //     return new Response("echec");
        // }

    }

    /**
     * @Route("/", name="company_index", methods={"GET"})
     */
    public function index(CompanyRepository $companyRepository): Response
    {
        return $this->render('company/index.html.twig', [
            'companies' => $companyRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="company_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $company = new Company();
        $form = $this->createForm(CompanyType::class, $company);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($company);
            $entityManager->flush();

            return $this->redirectToRoute('company_index');
        }

        return $this->render('company/new.html.twig', [
            'company' => $company,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/{id}/edit", name="company_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Company $company): Response
    {
        $form = $this->createForm(CompanyType::class, $company);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('company_index');
        }

        return $this->render('company/edit.html.twig', [
            'company' => $company,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="company_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Company $company): Response
    {
        if ($this->isCsrfTokenValid('delete' . $company->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($company);
            $entityManager->flush();
        }

        return $this->redirectToRoute('company_index');
    }

    /**
     * @Route("/{company}", name="company_show", methods={"GET"})
     */
    public function show(Company $company): Response
    {
        return $this->render('company/show.html.twig', [
            'company' => $company,

        ]);
    }






}
