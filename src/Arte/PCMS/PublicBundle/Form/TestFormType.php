<?php

namespace Arte\PCMS\PublicBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TestFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('Name', 'text', array(
                'label'     => 'name',
                'required'  => true,
            ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Arte\PCMS\PublicBundle\Form\TestFormModel',
//            'cascade_validation' => true,
//            'validation_groups' => array('Default', 'registration')
        ));
    }

    public function getName()
    {
        return 'TestForm';
    }
}