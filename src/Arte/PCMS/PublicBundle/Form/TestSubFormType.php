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
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TestSubFormType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $factory = $builder->getFormFactory();
        $builder
            ->add('id', 'hidden', array(
                'label'     => 'id',
                'required'  => false,
            ))
            ->add('Name', 'text', array(
                'label'     => '名称',
                'required'  => false,
            ))
//            ->add('SubForms', 'collection', array(
//                'type' => new TestSubFormType(),
//                'allow_add' => true,
////                'allow_delete' => true,
////                'by_reference' => false
//            ))
            ->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) use($factory){
                $data = $event->getData();
                $form = $event->getForm();

                if($data != null && $data->getSubForms()->count() != 0)
                {
//                    $form->add($factory->createNamed('Name', 'text', null, array(
//                        'auto_initialize' => false,
//                    )));
                    $form->add($factory->createNamed('SubForms', 'collection', null, array(
                        'type' => new TestSubFormType(),
                        'allow_add' => true,
//                        'allow_delete' => true,
//                        'by_reference' => false,
                        'auto_initialize' => false,
                        'cascade_validation' => true,
                    )));
                }
            })
            ->addEventListener(FormEvents::PRE_SUBMIT, function(FormEvent $event) use($factory){
                $data = $event->getData();
                $form = $event->getForm();

                if(!empty($data['SubForms']))
                {
                    $form->add($factory->createNamed('SubForms', 'collection', null, array(
                        'type' => new TestSubFormType(),
                        'allow_add' => true,
//                        'allow_delete' => true,
//                        'by_reference' => false,
                        'auto_initialize' => false,
                        'cascade_validation' => true,
                    )));
                }
            })
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Arte\PCMS\PublicBundle\Form\TestSubFormModel',
            'cascade_validation' => true,
        ));
    }

    public function getName()
    {
        return 'TestSub';
//        return 'Sub';
    }
}