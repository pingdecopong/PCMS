<?php

namespace Arte\PCMS\AdminBundle\Form;


use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TBProjectMasterSubFormType extends AbstractType
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
                'label'     => '項目名',
                'required'  => false,
            ))
            ->add('Cost', 'text', array(
                'label'     => '工数',
                'required'  => false,
            ))
            ->add('GroupFlag', 'hidden', array(
                'label'     => 'GroupFlag',
                'required'  => false,
            ))
//            ->add('Branches', 'collection', array(
//                'type' => new TBProjectMasterSubEndFormType(),
//                'allow_add' => true,
//                'allow_delete' => true,
//                'by_reference' => false
//            ))
            ->add('Group', 'submit', array(
                'label'     => 'グループ',
            ))
            ->add('Add', 'submit', array(
                'label'     => '追加',
            ))
            ->add('Up', 'submit', array(
                'label'     => '上',
            ))
            ->add('Down', 'submit', array(
                'label'     => '下',
            ))
            ->add('Delete', 'submit', array(
                'label'     => '削除',
            ))
            ->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) use($factory){
                $data = $event->getData();
                $form = $event->getForm();

                if($data != null && $data->getSubForms()->count() != 0)
                {
                    $form->add($factory->createNamed('SubForms', 'collection', null, array(
                        'type' => new TBProjectMasterSubFormType(),
                        'allow_add' => true,
//                        'allow_delete' => true,
//                        'by_reference' => false,
                        'auto_initialize' => false,
                        'cascade_validation' => true,
                        'prototype' => false,
                    )));
                }
            })
            ->addEventListener(FormEvents::PRE_SUBMIT, function(FormEvent $event) use($factory){
                $data = $event->getData();
                $form = $event->getForm();

                if(!empty($data['SubForms']))
                {
                    $form->add($factory->createNamed('SubForms', 'collection', null, array(
                        'type' => new TBProjectMasterSubFormType(),
                        'allow_add' => true,
//                        'allow_delete' => true,
//                        'by_reference' => false,
                        'auto_initialize' => false,
                        'cascade_validation' => true,
                        'prototype' => false,
                    )));
                }
            })
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Arte\PCMS\AdminBundle\Form\TBProjectMasterSubFormModel',
            'cascade_validation' => true,
        ));
    }

    public function getName()
    {
        return 'ProjectMasterSubForm';
    }

}