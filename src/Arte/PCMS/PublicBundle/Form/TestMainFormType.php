<?php
/**
 * Created by JetBrains PhpStorm.
 * User: fhirashima
 * Date: 13/08/15
 * Time: 17:47
 * To change this template use File | Settings | File Templates.
 */

namespace Arte\PCMS\PublicBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TestMainFormType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id', 'hidden', array(
                'label'     => 'id',
                'required'  => false,
            ))
            ->add('Name', 'text', array(
                'label'     => '名称',
                'required'  => false,
            ))
            ->add('Name2', 'text', array(
                'label'     => '名称2',
                'required'  => false,
            ))
            ->add('SubForms', 'collection', array(
                'type' => new TestSubFormType(),
                'allow_add' => true,
//                'allow_delete' => true,
//                'by_reference' => false
//                'auto_initialize' => false,
                'cascade_validation' => true,
            ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Arte\PCMS\PublicBundle\Form\TestMainFormModel',
            'cascade_validation' => true,
        ));
    }

    public function getName()
    {
        return 'TestMain';
//        return 'Sub';
    }
}