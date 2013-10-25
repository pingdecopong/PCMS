<?php
namespace Arte\PCMS\PublicBundle\Form;

use Arte\PCMS\BizlogicBundle\Entity\TBProjectMaster;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class TBProductionCostType extends AbstractType
{
    /**
     * @var \Arte\PCMS\BizlogicBundle\Entity\TBProjectMaster
     */
    private $tbProjectMaster;

    function __construct(TBProjectMaster $tbProjectMaster)
    {
        $this->tbProjectMaster = $tbProjectMaster;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $factory = $builder->getFormFactory();
        $TBprojectMaster = $this->tbProjectMaster;
        $users = $TBprojectMaster->getTBProjectUsersProjectMasterId();
        $userIds = array();
        foreach($users as $value)
        {
            /** @var \Arte\PCMS\BizlogicBundle\Entity\TBProjectUser $value */
            $userIds[] = $value->getSystemUserId();
        }

        //プロジェクト
        //作業項目
        //担当者
        //作業日時
        //作業工数
        //メモ
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
            ->add('TBProjectCost', 'entity', array(
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
            ))
            ->add('TBSystemUser', 'entity', array(
                'label' => '作業者',
                'required' => false,
                'empty_value' => '----',
                'class' => 'ArtePCMSBizlogicBundle:TBSystemUser',
                'property' => 'DisplayName',
                'query_builder' => function(EntityRepository $er) use($userIds) {
                    return $er->createQueryBuilder('u')
                        ->select('partial u.{id, DisplayName}')
                        ->andWhere('u.DeleteFlag = false')
                        ->andWhere('u.id IN (:ids)')
                        ->setParameter('ids', $userIds);
                    },
            ))
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
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Arte\PCMS\PublicBundle\Form\TBProductionCostModel',
//            'validation_groups' => false,
        ));
    }

    public function getName()
    {
        return 'TBProductionCost';
    }
}
