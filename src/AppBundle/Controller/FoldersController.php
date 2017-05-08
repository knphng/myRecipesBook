<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Folder;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\JsonResponse;


class FoldersController extends Controller{
  /**
   * @Route("/folders", name="folders_list")
   * @Method({"GET"})
   */
   public function listAction(Request $request){


       $folders = $this->getDoctrine()
            ->getRepository('AppBundle:Folder')
            ->findAll();

       /*$serializer = $this->get('serializer');
       $jsonContent = $serializer->serialize(
           $folders,
           'json', array('groups' => array('id', 'name', 'recipes'))
       );*/

       $serializer = $this->get('jms_serializer')->serialize($folders, 'json');

       return new JsonResponse($serializer);
      /*return $this->render('folders/index.html.twig', array(
        'folders' => $serializer,
        //'jsonContent' => $jsonContent
    ));*/
  }

  /**
   * @Route("/folder/create/{name}", name="folder_create")
   * @Method({"POST"})
   */
  public function createAction($name, Request $request)
  {
      $folder = new Folder;

      /*$form = $this->createFormBuilder($folder)
          ->add('name', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
          ->add('save', SubmitType::class, array('attr' => array('laber' => 'Create Folder', 'class' => 'btn btn-primary', 'style' => 'margin-bottom:15px')))
          ->getForm();
          */
      //$form->handleRequest($request);
      //if ($form->isSubmitted() && $form->isValid()) {
          //$name = $form['name']->getData();

          $folder->setName($name);

          $em = $this->getDoctrine()->getManager();

          $em->persist($folder);
          $em->flush();

          //set a msg
          $this->addFlash(
              'notice',
              'Folder added'
          );

          return $this->redirectToRoute('folders_list');

      //}

  return $this->render('folders/index.html.twig' /*, array('form' => $form->createView())*/);
  }

  /**
   * @Route("/folder/edit/{id}", name="folder_edit")
   */
  public function editAction($id, Request $request)
  {
      // replace this example code with whatever you need
      return $this->render('folders/edit.html.twig');
  }

  /**
   * @Route("/folder/details/{id}", name="folder_details")
   */
  public function detailsAction($id){
      $folder = $this->getDoctrine()
          ->getRepository('AppBundle:Folder')
          ->find($id);

      $recipes = $this->getDoctrine()
          ->getRepository('AppBundle:Folder')
          ->find($id)
          ->getRecipes();


      $serializer = $this->get('serializer');

      $jsonContent = $serializer->serialize($folder, 'json');

      return $this->render('folders/details.html.twig', array(
        'recipes' => $recipes,
        'folder' => $folder,
        'json' => $jsonContent
      ));
  }

  /**
   * @Route("/folder/delete/{id}", name="folder_delete")
   */
  public function deleteAction($id)
  {
        $em = $this->getDoctrine()->getManager();
        $folder = $em->getRepository('AppBundle:Folder')->find($id);
        $em->remove($folder);
        $em->flush();

        $this->addFlash(
            'notice',
            'Folder deleted'
        );
        return $this->redirectToRoute('folders_list');
  }
}
