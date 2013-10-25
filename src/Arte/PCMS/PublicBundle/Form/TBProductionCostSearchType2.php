<?php
namespace Arte\PCMS\PublicBundle\Form;


use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TBProductionCostSearchType2 extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        //案件名
        //状態
        //作業日　開始
        //作業日　終了
        //作業者
        $builder
//            ->add('TBProjectMaster', 'entity', array(
//                'label' => '案件',
//                'required' => false,
//                'empty_value' => '----',
//                'class' => 'ArtePCMSBizlogicBundle:TBProjectMaster',
//                'property' => 'Name',
//                'query_builder' => function(EntityRepository $er) {
//                    return $er->createQueryBuilder('p')
//                        ->select('partial p.{id, Name}')
//                        ->andWhere('p.DeleteFlag = false');
//                },
//            ))
//            ->add('Status', 'choice', array(
//                'label'     => '状態',
//                'required'  => false,
//                'empty_value' => '指定無し',
//                'choices'   => array(
//                    '1'   => '仕掛り',
//                    '2' => '受注',
//                    '3'   => '終了',
//                ),
//                'expanded' => true,
//                'multiple'  => false,
//            ))
            ->add('PeriodStart', 'date', array(
                'label'     => '作業日　開始',
                'required'  => false,
                'widget' => 'single_text',
                'format' => 'yyyy/MM/dd',
            ))
            ->add('PeriodEnd', 'date', array(
                'label'     => '作業日　終了',
                'required'  => false,
                'widget' => 'single_text',
                'format' => 'yyyy/MM/dd',
            ))
            ->add('TBSystemUser', 'entity', array(
                'label' => '作業者',
                'required' => false,
                'empty_value' => '----',
                'class' => 'ArtePCMSBizlogicBundle:TBSystemUser',
                'property' => 'DisplayName',
                'query_builder' => function(EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->select('partial u.{id, DisplayName}')
                        ->andWhere('u.DeleteFlag = false');
                },
            ))
        ;

    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Arte\PCMS\PublicBundle\Form\TBProductionCostSearchModel2'
        ));
    }

    public function getName()
    {
        return 'TBProductionCost';
    }
}
