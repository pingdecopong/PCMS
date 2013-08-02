<?php

namespace Arte\PCMS\PublicBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TBProjectMasterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('Name', 'text', array(
                'label'     => 'Name',
                'required'  => false,
            ))
            ->add('Status', 'text', array(
                'label'     => 'Status',
                'required'  => false,
            ))
            ->add('Explanation', 'text', array(
                'label'     => 'Explanation',
                'required'  => false,
            ))
            ->add('CustomerId', 'text', array(
                'label'     => 'CustomerId',
                'required'  => false,
            ))
            ->add('DeleteFlag', 'checkbox', array(
                'label'     => 'DeleteFlag',
                'required'  => false,
            ))
                ->add('PeriodStart', 'date', array(
                'label'     => 'PeriodStart',
                'required'  => false,
                ))
                ->add('PeriodEnd', 'date', array(
                'label'     => 'PeriodEnd',
                'required'  => false,
                ))
            ->add('ManagerId', 'text', array(
                'label'     => 'ManagerId',
                'required'  => false,
            ))
            ->add('EstimateFilePath', 'text', array(
                'label'     => 'EstimateFilePath',
                'required'  => false,
            ))
            ->add('ScheduleFilePath', 'text', array(
                'label'     => 'ScheduleFilePath',
                'required'  => false,
            ))
        
            ->add('TBCustomerCustomerId')
            ->add('TBSystemUserManagerId')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Arte\PCMS\PublicBundle\Entity\TBProjectMaster'
        ));
    }

    public function getName()
    {
        return 'TBProjectMaster';
    }
}
