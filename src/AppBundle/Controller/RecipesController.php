<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Recipe;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\FileType;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class RecipesController extends Controller
{
    /**
     * @Route("/recipes", name="recipes_list")
     */
    public function listAction(Request $request){
        $recipes = $this->getDoctrine()
          ->getRepository('AppBundle:Recipe')
          ->findAll();

          $serializer = $this->get('serializer');
          $jsonContent = $serializer->serialize(
              $recipes,
              'json'
          );
          echo $jsonContent;
        return $this->render('recipes/index.html.twig', array(
          'recipes' => $recipes,
          'json' => $jsonContent
        ));
    }

    /**
     * @Route("/recipe/create", name="recipe_create")
     */
    public function createAction(Request $request){
        $recipe = new Recipe;

        $form = $this->createFormBuilder($recipe)
            ->add('folder', EntityType::class, array('class' => 'AppBundle:Folder', 'choice_label' => 'name', 'choice_value' => 'id', 'required'  => false, 'attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
            ->add('name', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
            ->add('ingredients', TextareaType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
            ->add('recipe', TextareaType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
            ->add('cookingTime', TimeType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
            //->add('picture', FileType::class, array('attr' => array('class' => 'form-control', 'type'=> 'file', 'style' => 'margin-bottom:15px')))
            ->add('save', SubmitType::class, array('attr' => array('laber' => 'Create Recipe', 'class' => 'btn btn-default', 'style' => 'margin-bottom:15px')))
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($form['folder']) {
                  $folder = $form['folder']->getData();
                  $recipe->setFolder($folder);
            }
            $name = $form['name']->getData();
            $ingredients = $form['ingredients']->getData();
            $recipeText = $form['recipe']->getData();
            $cookingTime = $form['cookingTime']->getData();
            //$picture = $form['$picture']->getData();

            $recipe->setName($name);
            $recipe->setIngredients($ingredients);
            $recipe->setRecipe($recipeText);
            $recipe->setCookingTime($cookingTime);
            //$recipe->setPicture($picture);

            $em = $this->getDoctrine()->getManager();

            $em->persist($recipe);
            $em->flush();

            //set a msg
            $this->addFlash(
                'notice',
                'Recipe: added'
            );

            return $this->redirectToRoute('recipes_list');

      }

      return $this->render('recipes/create.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/recipe/edit/{id}", name="recipe_edit")
     */
    public function editAction($id, Request $request)
    {
        $recipe = $this->getDoctrine()
            ->getRepository('AppBundle:Recipe')
            ->find($id);

        $folder = $this->getDoctrine()
            ->getRepository('AppBundle:Recipe')
            ->find($id)
            ->getFolder();

            $recipe->setFolder($folder);
            $recipe->setName($recipe->getName());
            $recipe->setIngredients($recipe->getIngredients());
            $recipe->setRecipe($recipe->getRecipe());
            $recipe->setCookingTime($recipe->getCookingTime());

        $form = $this->createFormBuilder($recipe)
            ->add('folder', EntityType::class, array('class' => 'AppBundle:Folder', 'choice_label' => 'name', 'choice_value' => 'id', 'required'  => false, 'attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
            ->add('name', TextType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
            ->add('ingredients', TextareaType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
            ->add('recipe', TextareaType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
            ->add('cookingTime', TimeType::class, array('attr' => array('class' => 'form-control', 'style' => 'margin-bottom:15px')))
            //->add('picture', FileType::class, array('attr' => array('class' => 'form-control', 'type'=> 'file', 'style' => 'margin-bottom:15px')))
            ->add('save', SubmitType::class, array('attr' => array('laber' => 'Create Recipe', 'class' => 'btn btn-default', 'style' => 'margin-bottom:15px')))
            ->getForm();


        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $folder =$form['folder']->getData();
            $name = $form['name']->getData();
            $ingredients = $form['ingredients']->getData();
            $recipeText = $form['recipe']->getData();
            $cookingTime = $form['cookingTime']->getData();
            //$picture = $form['$picture']->getData();

            $em = $this->getDoctrine()->getManager();
            $todo = $em->getRepository('AppBundle:Recipe')->find($id);

            $recipe->setFolder($folder);
            $recipe->setName($name);
            $recipe->setIngredients($ingredients);
            $recipe->setRecipe($recipeText);
            $recipe->setCookingTime($cookingTime);
            //$recipe->setPicture($picture);

            $em->flush();

            //set a msg
            $this->addFlash(
                'notice',
                'Recipe updated'
            );

            return $this->redirectToRoute('recipe_details', array('id' => $recipe->getId()));
        }

        return $this->render('recipes/edit.html.twig', array(
          'recipe' => $recipe,
          'form' => $form->createView()
        ));
    }

    /**
     * @Route("/recipe/details/{id}", name="recipe_details")
     */
    public function detailsAction($id)
    {
        $recipe = $this->getDoctrine()
            ->getRepository('AppBundle:Recipe')
            ->find($id);

        return $this->render('recipes/details.html.twig', array(
          'recipe' => $recipe
        ));
        // replace this example code with whatever you need
    }

    /**
     * @Route("/recipe/delete/{id}", name="recipe_delete")
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $recipe = $em->getRepository('AppBundle:Recipe')->find($id);
        $em->remove($recipe);
        $em->flush();

        $this->addFlash(
            'notice',
            'Recipe deleted'
        );
        return $this->redirectToRoute('recipes_list');
    }

}
