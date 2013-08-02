<?php

namespace Arte\PCMS\PublicBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TBProjectMasterSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('Name', 'text', array(
                'label'     => '案件名',
                'required'  => false,
            ))
            ->add('Status', 'text', array(
                'label'     => '状態',
                'required'  => false,
            ))
//            ->add('Explanation', 'text', array(
//                'label'     => 'Explanation',
//                'required'  => false,
//            ))
//            ->add('CustomerId', 'text', array(
//                'label'     => 'CustomerId',
//                'required'  => false,
//            ))
//            ->add('DeleteFlag', 'choice', array(
//                'label'     => 'DeleteFlag',
//                'choices'   => array('1' => '有効', '0' => '無効'),
//                'required'  => false,
//                'expanded'  => false,
//                'multiple'  => false,
//            ))
            ->add('PeriodStart', 'date', array(
                'label'     => '開始日',
                'required'  => false,
            ))
            ->add('PeriodEnd', 'date', array(
                'label'     => '終了日',
                'required'  => false,
            ))
//            ->add('ManagerId', 'text', array(
//                'label'     => 'ManagerId',
//                'required'  => false,
//            ))
//            ->add('EstimateFilePath', 'text', array(
//                'label'     => 'EstimateFilePath',
//                'required'  => false,
//            ))
//            ->add('ScheduleFilePath', 'text', array(
//                'label'     => 'ScheduleFilePath',
//                'required'  => false,
//            ))
        
//            ->add('TBCustomerCustomerId')
//            ->add('TBSystemUserManagerId')
            ->add('TBCustomerCustomerId', 'entity', array(
                'label' => '顧客',
                'required' => false,
                'empty_value' => '----',
                'class' => 'ArtePCMSBizlogicBundle:TBCustomer',
                'property' => 'Name',
                'query_builder' => function(EntityRepository $er) {
                    return $er->createQueryBuilder('d')
                        ->andWhere('d.DeleteFlag = :DeleteFlag')
                        ->setParameter('DeleteFlag', false);
                },
            ))
            ->add('TBSystemUserManagerId', 'entity', array(
                'label' => '管理者',
                'required' => false,
                'empty_value' => '----',
                'class' => 'ArtePCMSBizlogicBundle:TBSystemUser',
                'property' => 'Name',
                'query_builder' => function(EntityRepository $er) {
                    return $er->createQueryBuilder('d')
                        ->andWhere('d.DeleteFlag = :DeleteFlag')
                        ->setParameter('DeleteFlag', false);
                },
            ))
        ;

    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Arte\PCMS\PublicBundle\Form\TBProjectMasterSearchModel'
        ));
    }

    public function getName()
    {
        return 'TBProjectMaster';
    }
}
