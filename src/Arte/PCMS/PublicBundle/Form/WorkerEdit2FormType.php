<?php

namespace Arte\PCMS\PublicBundle\Form;


use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class WorkerEdit2FormType extends AbstractType
{
    private $choices;

    function __construct()
    {
        $this->choices = array();
    }

    /**
     * @param mixed $choices
     */
    public function setChoices($choices)
    {
        $this->choices = $choices;
    }

    /**
     * @return mixed
     */
    public function getChoices()
    {
        return $this->choices;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('WorkerEditRoleForms', 'collection', array(
                'type' => new WorkerEditRoleFormType(),
                'allow_add' => true,
                'auto_initialize' => false,
//                'allow_delete' => true,
//                'by_reference' => false,
                'cascade_validation' => true,
                'prototype' => false,
            ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Arte\PCMS\PublicBundle\Form\WorkerEdit2FormModel',
            'cascade_validation' => true,
        ));
    }

    public function getName()
    {
        return 'WorkerEdit2Form';
    }

}