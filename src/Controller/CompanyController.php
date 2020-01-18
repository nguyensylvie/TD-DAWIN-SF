<?php

namespace App\Controller;

use App\Entity\Company;
use App\Entity\Slot;
use App\Entity\User;
use App\Form\CompanyType;
use App\Repository\CompanyRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/company")
 */
class CompanyController extends AbstractController
{
    /**
     * @Route("/", name="company_index", methods={"GET"})
     */
    public function index(CompanyRepository $companyRepository): Response
    {
        return $this->render('company/index.html.twig', [
            'companies' => $companyRepository->findAll(),
        ]);
    }

    // Créé les créneaux pour l'entreprise
    public function createSlots(Company $company)
    {
        // Récupération des paramètres des créneaux (voir .env)
        
        // Début et fin d'un créneau (par ex: 14:00 et 16:45)
        $slotsStart = getenv('SLOTS_START');
        $slotsEnd = getenv('SLOTS_END');

        // Durée d'un créneau en minutes (par exemple 15)
        $slotsDuration = getenv('SLOTS_DURATION');

        // TODO: Générer les créneaux libres et les associer aux entreprises  
        $entityManager = $this->getDoctrine()->getManager();

        $dtStart = date_create($slotsStart);
        $dtEnd = date_create($slotsEnd);
        //$dtDuration = date_create($slotsDuration);

        foreach (new \DatePeriod($dtStart, \DateInterval::createFromDateString('15 minutes'), $dtEnd) as $dt) {
            $date = strval($dt->format('H:i'));
            $slot = new Slot();
            $slot->setCompany($company)
                 ->setStudent(null)
                 ->setTime($date);
            $company->addSlot($slot);
            $entityManager->persist($slot);
            $entityManager->persist($company);
        }
        $entityManager->flush();
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
            $this->createSlots($company);
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
     * @Route("/details/{id}", name="company_details", methods={"GET"})
     */
    public function details(Company $company): Response
    {
        return $this->render('company/details.html.twig', [
            'company' => $company,
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
        if ($this->isCsrfTokenValid('delete'.$company->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($company);
            $entityManager->flush();
        }

        return $this->redirectToRoute('company_index');
    }

    // Affecte un créneau pour un étudiant
    public function affectSlots(Slot $slot)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = new User();
        $slot->setStudent($user)
             ->getId();  
        $entityManager->persist($slot);
        $entityManager->persist($user);
        $entityManager->flush();
    }
}
