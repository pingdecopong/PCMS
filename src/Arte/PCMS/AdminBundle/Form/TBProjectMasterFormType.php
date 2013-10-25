<?php

namespace Arte\PCMS\AdminBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class TBProjectMasterFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('Name', 'text', array(
                'label'     => '案件名',
                'required'  => false,
            ))
            ->add('TBCustomerCustomerId', 'entity', array(
                'label' => '顧客',
                'required' => false,
                'empty_value' => '----',
                'class' => 'ArtePCMSBizlogicBundle:TBCustomer',
                'property' => 'Name',
                'query_builder' => function(EntityRepository $er) {
                    return $er->createQueryBuilder('d')
                        ->select('partial d.{id, Name}')
                        ->andWhere('d.DeleteFlag = :DeleteFlag')
                        ->setParameter('DeleteFlag', false);
                },
            ))
            ->add('PeriodStart', 'date', array(
                'label'     => '開始日',
                'required'  => false,
                'widget' => 'single_text',
                'format' => 'yyyy/MM/dd',
            ))
            ->add('PeriodEnd', 'date', array(
                'label'     => '終了日',
                'required'  => false,
                'widget' => 'single_text',
                'format' => 'yyyy/MM/dd',
            ))
            ->add('TBSystemUserManagerId', 'entity', array(
                'label' => '管理者',
                'required' => false,
                'empty_value' => '----',
                'class' => 'ArtePCMSBizlogicBundle:TBSystemUser',
                'property' => 'Name',
                'query_builder' => function(EntityRepository $er) {
                    return $er->createQueryBuilder('d')
                        ->select('partial d.{id, DisplayName}')
                        ->andWhere('d.DeleteFlag = :DeleteFlag')
                        ->setParameter('DeleteFlag', false);
                },
            ))
//            ->add('Status', 'text', array(
//                'label'     => '状態',
//                'required'  => false,
//            ))
            ->add('Status', 'choice', array(
                'label'     => '状態',
                'required'  => true,
                'choices'   => array(
                    '1'   => '仕掛り',
                    '2' => '受注',
                    '3'   => '終了',
                ),
                'expanded' => true,
                'multiple'  => false,
//                'constraints' => array(
//                    new NotBlank(),
//                ),
            ))
            ->add('Explanation', 'textarea', array(
                'label'     => '説明',
                'required'  => false,
            ))
            ->add('EstimateFilePath', 'text', array(
                'label'     => '見積ファイルパス',
                'required'  => false,
            ))
            ->add('ScheduleFilePath', 'text', array(
                'label'     => 'スケジュールファイルパス',
                'required'  => false,
            ))
            ->add('SubForms', 'collection', array(
                'type' => new TBProjectMasterSubFormType(),
                'allow_add' => true,
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
            'data_class' => 'Arte\PCMS\AdminBundle\Form\TBProjectMasterFormModel',
            'cascade_validation' => true,
        ));
    }

    public function getName()
    {
        return 'TBProjectMaster';
    }
}
