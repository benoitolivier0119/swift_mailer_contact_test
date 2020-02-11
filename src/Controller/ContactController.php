<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


/**
     * @Route("/contact")
     */
class ContactController extends AbstractController
{
    /**
     * @Route("/", name="contact")
     */
    public function index(Request $request, \Swift_Mailer $mailer)
    {
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $contact = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($contact);
            $em->flush();

            $message = (new \Swift_Message('nouveau message'))
            ->setFrom('test@gmail.com.com')
            ->setTo('admin@gmail.com.com')
            ->setBody(
                $this->renderView(
                    // templates/emails/registration.html.twig
                    'emails/notifications.html.twig',
                    ['contact' => $contact]
                ),
                'text/html'
            );

            $mailer->send($message);

            return $this->render('emails/notifications.html.twig', [
                'contact' => $contact
            ]);

            return $this->redirectToRoute('contact');
        }

        return $this->render('contact/index.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
