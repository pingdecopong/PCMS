<?php
namespace Arte\PCMS\PublicBundle\Form;


use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class TBProductionCostType2 extends AbstractType
{
    /**
     * @var \Symfony\Component\Security\Core\SecurityContextInterface
     */
    private $securityContext;

    function __construct(SecurityContextInterface $securityContext)
    {
        $this->securityContext = $securityContext;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $factory = $builder->getFormFactory();
        $user = $this->securityContext->getToken()->getUser();

        //プロジェクト
        //作業項目
        //担当者
        //作業日時
        //作業工数
        //メモ
        $builder
            ->add('TBProjectMaster', 'entity', array(
                'label' => '案件',
                'required' => false,
                'empty_value' => '----',
                'class' => 'ArtePCMSBizlogicBundle:TBProjectMaster',
                'property' => 'Name',
                'query_builder' => function(EntityRepository $er) use($user) {
                    return $er->createQueryBuilder('p')
                        ->leftJoin('p.TBProjectUsersProjectMasterId', 'pu')
                        ->select('partial p.{id, Name}')
                        ->andWhere('p.DeleteFlag = false')
                        ->andWhere('pu.SystemUserId = :SystemUserId')
                        ->setParameter('SystemUserId', $user->getId());
                },
            ))
//            ->add('TBSystemUser', 'entity', array(
//                'label' => '作業者',
//                'required' => false,
//                'empty_value' => '----',
//                'class' => 'ArtePCMSBizlogicBundle:TBSystemUser',
//                'property' => 'DisplayName',
//                'query_builder' => function(EntityRepository $er) {
//                    return $er->createQueryBuilder('u')
//                        ->select('partial u.{id, DisplayName}')
//                        ->andWhere('u.DeleteFlag = false');
//                },
//            ))
            ->add('WorkDate', 'date', array(
                'label'     => '作業日',
                'required'  => false,
                'widget' => 'single_text',
                'format' => 'yyyy/MM/dd',
//                'invalid_message' => false
//                'cascade_validation' => false,
//                'read_only' => true,
            ))
            ->add('Cost', 'integer', array(
                'label'     => '作業工数',
                'required'  => false,
                'attr' => array(
                    'min' => 0,
                ),
            ))
            ->add('Note', 'textarea', array(
                'label'     => 'メモ',
                'required'  => false,
//                'constraints' => array(
//                    new NotBlank(),
//                ),
            ))
            ->add('TBProjectCost', 'choice', array(
                'label'     => '作業名',
                'required'  => true,
                'empty_value' => '----',
                'choices'   => array(
//                    '1'   => '仕掛り',
//                    '2' => '受注',
//                    '3'   => '終了',
                ),
                'expanded' => false,
                'multiple'  => false,
            ))
            ->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) use($factory){
                $data = $event->getData();
                $form = $event->getForm();

                if($data != null && $data->getTBProjectMaster() != null)
                {
                    $TBprojectMaster = $data->getTBProjectMaster();
                    $form->add('TBProjectCost', 'entity', array(
                        'label' => '作業名',
                        'required' => true,
                        'empty_value' => '----',
                        'class' => 'ArtePCMSBizlogicBundle:TBProjectCostMaster',
                        'property' => 'Name',
                        'query_builder' => function(EntityRepository $er) use($TBprojectMaster){
                            return $er->createQueryBuilder('pc')
                                ->select('partial pc.{id, Name}')
                                ->andWhere('pc.TBProjectMasterProjectMasterId = :TBProjectMaster')
                                ->andWhere('pc.DeleteFlag = false')
                                ->setParameter('TBProjectMaster', $TBprojectMaster);
                        },
                    ));


//                    $form->add($factory->createNamed('SubForms', 'collection', null, array(
//                        'type' => new TBProjectMasterSubFormType(),
//                        'allow_add' => true,
////                        'allow_delete' => true,
////                        'by_reference' => false,
//                        'auto_initialize' => false,
//                        'cascade_validation' => true,
//                        'prototype' => false,
//                    )));
                }
            })
            ->addEventListener(FormEvents::PRE_SUBMIT, function(FormEvent $event) use($factory){
                $data = $event->getData();
                $form = $event->getForm();

                if(!empty($data['TBProjectMaster']))
                {
                    $TBprojectMasterId = $data['TBProjectMaster'];
                    $form->add('TBProjectCost', 'entity', array(
                        'label' => '作業名',
                        'required' => true,
                        'empty_value' => '----',
                        'class' => 'ArtePCMSBizlogicBundle:TBProjectCostMaster',
                        'property' => 'Name',
                        'query_builder' => function(EntityRepository $er) use($TBprojectMasterId){
                            return $er->createQueryBuilder('pc')
                                ->select('partial pc.{id, Name}')
                                ->andWhere('pc.ProjectMasterId = :ProjectMasterId')
                                ->andWhere('pc.DeleteFlag = false')
                                ->setParameter('ProjectMasterId', $TBprojectMasterId);
                        },
                    ));

                }
            })



//            ->add('ProjectCostMasterId', 'text', array(
//                'label'     => 'ProjectCostMasterId',
//                'required'  => false,
//            ))
//            ->add('SystemUserId', 'text', array(
//                'label'     => 'SystemUserId',
//                'required'  => false,
//            ))
//            ->add('WorkDate', 'date', array(
//                'label'     => 'WorkDate',
//                'required'  => false,
//            ))
//            ->add('Cost', 'text', array(
//                'label'     => 'Cost',
//                'required'  => false,
//            ))
//            ->add('Note', 'textarea', array(
//                'label'     => 'Note',
//                'required'  => false,
//            ))
//            ->add('DeleteFlag', 'checkbox', array(
//                'label'     => 'DeleteFlag',
//                'required'  => false,
//            ))
//
//            ->add('TBProjectCostMasterProjectCostMasterId')
//            ->add('TBSystemUserSystemUserId')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Arte\PCMS\PublicBundle\Form\TBProductionCostModel2',
//            'validation_groups' => false,
        ));
    }

    public function getName()
    {
        return 'TBProductionCost';
    }
}
