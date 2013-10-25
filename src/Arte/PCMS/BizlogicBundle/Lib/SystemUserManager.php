<?php
/**
 * ユーザー管理クラス
 */

namespace Arte\PCMS\BizlogicBundle\Lib;


use Arte\PCMS\BizlogicBundle\Entity\TBDepartment;
use Arte\PCMS\BizlogicBundle\Entity\TBSystemUser;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMInvalidArgumentException;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator;

class SystemUserManager {

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;
    /**
     * @var \Symfony\Component\Validator\Validator
     */
    private $validator;

    /**
     * @var \Symfony\Component\Security\Core\Encoder\EncoderFactory
     */
    private $encoder;

    function __construct(EntityManager $em, Validator $validator, EncoderFactory $encoder)
    {
        $this->em = $em;
        $this->validator = $validator;
        $this->encoder = $encoder;
    }

    /**
     * ユーザー一覧取得
     * @param $pageNo ページ番号
     * @param $pageSize １ページの件数
     * @param null $sortName ソートカラム名
     * @param null $sortType asc or desc
     * @param TBSystemUserSearchModel $search
     * @return array
     * @throws \Exception
     */
    public function getTBSystemUserList($pageNo, $pageSize, $sortName = null, $sortType = null, TBSystemUserSearchModel $search = null)
    {
        /* @var $queryBuilder \Doctrine\ORM\QueryBuilder */
        $queryBuilder = $this->em
            ->getRepository('ArtePCMSBizlogicBundle:TBSystemUser')
            ->createQueryBuilder('u')
            ->leftjoin('u.TBDepartmentDepartmentId', 'd')
            ->andWhere('u.DeleteFlag = false')
            ->select(array(
                'u',
                'd',
//                'partial u.{id, DisplayName}',
//                'partial c.{id, Cost, WorkDate}',
//                'partial pc.{id, Name}',
//                'partial p.{id, Name, Status}',
            ))
        ;

        //検索
        if($search == null){
            $search = new TBSystemUserSearchModel();
        }
        $errors = $this->validator->validate($search);
        ///Username
        $searchUsername = $search->getUsername();
        if($this->isValid($errors, 'Username') && isset($searchUsername))
        {
            $queryBuilder = $queryBuilder->andWhere('u.Username LIKE :Username')
                ->setParameter('Username', '%'.$searchUsername.'%');
        }
        ///Active
        $searchActive = $search->getActive();
        if($this->isValid($errors, 'Active') && isset($searchActive))
        {
            $queryBuilder = $queryBuilder->andWhere('u.Active = :Active')
                ->setParameter('Active', $searchActive);
        }
        ///DisplayName
        $searchDisplayName = $search->getDisplayName();
        if($this->isValid($errors, 'DisplayName') && isset($searchDisplayName))
        {
            $queryBuilder = $queryBuilder->andWhere('u.DisplayName LIKE :DisplayName')
                ->setParameter('DisplayName', '%'.$searchDisplayName.'%');
        }
        ///MailAddress
        $searchMailAddress = $search->getMailAddress();
        if($this->isValid($errors, 'MailAddress') && isset($searchMailAddress))
        {
            $queryBuilder = $queryBuilder->andWhere('u.MailAddress LIKE :MailAddress')
                ->setParameter('MailAddress', '%'.$searchMailAddress.'%');
        }
        ///TBDepartment
        $searchTBDepartment = $search->getTBDepartmentDepartmentId();
        if($this->isValid($errors, 'TBDepartment') && isset($searchTBDepartment))
        {
            $queryBuilder = $queryBuilder->andWhere('u.TBDepartmentDepartmentId = :TBDepartment')
                ->setParameter('TBDepartment', $searchTBDepartment);
        }

        //全件数取得
        $queryBuilderCount = clone $queryBuilder;
        $queryBuilderCount = $queryBuilderCount->select('count(u.id)');
        $queryCount = $queryBuilderCount->getQuery();
        $allCount = $queryCount->getSingleScalarResult();

        //ソート
        if($sortName != null && $sortType != null && ($sortType == 'asc' || $sortType == 'desc'))
        {
            switch($sortName)
            {
                case 'Username'://Username
                    $queryBuilder = $queryBuilder->orderBy('u.Username', $sortType);
                    $queryBuilder = $queryBuilder->addOrderBy('u.id', 'DESC');
                    break;
                case 'Active'://Active
                    $queryBuilder = $queryBuilder->orderBy('u.Active', $sortType);
                    $queryBuilder = $queryBuilder->addOrderBy('u.id', 'DESC');
                    break;
                case 'DisplayName'://DisplayName
                    $queryBuilder = $queryBuilder->orderBy('u.DisplayName', $sortType);
                    $queryBuilder = $queryBuilder->addOrderBy('u.id', 'DESC');
                    break;
                case 'MailAddress'://MailAddress
                    $queryBuilder = $queryBuilder->orderBy('u.MailAddress', $sortType);
                    $queryBuilder = $queryBuilder->addOrderBy('u.id', 'DESC');
                    break;
                case 'TBDepartment'://TBDepartment
                    $queryBuilder = $queryBuilder->orderBy('d.Name', $sortType);
                    $queryBuilder = $queryBuilder->addOrderBy('u.id', 'DESC');
                    break;
            }
        }

        $queryBuilder = $queryBuilder->setFirstResult($pageSize*($pageNo-1))
            ->setMaxResults($pageSize);

        //クエリー実行
        $entities = $queryBuilder->getQuery()->getResult();

        $result = array();
        $result['count'] = $allCount;
        $result['result'] = $entities;

        return $result;
    }

    private function isValid($data, $propertyPath)
    {
        foreach($data as $value)
        {
            /* @var $value \Symfony\Component\Validator\ConstraintViolation */
            if($value->getPropertyPath() == $propertyPath){
                return false;
            }
        }
        return true;
    }

    public function getTBSystemUsersForDropDownList()
    {
        /* @var $queryBuilder \Doctrine\ORM\QueryBuilder */
        $queryBuilder = $this->em
            ->getRepository('ArtePCMSBizlogicBundle:TBSystemUser')
            ->createQueryBuilder('u')
//            ->leftjoin('u.TBDepartmentDepartmentId', 'd')
            ->andWhere('u.DeleteFlag = false')
            ->select(array(
                'partial u.{id, DisplayName}',
//                'u',
//                'd',
//                'partial u.{id, DisplayName}',
//                'partial c.{id, Cost, WorkDate}',
//                'partial pc.{id, Name}',
//                'partial p.{id, Name, Status}',
            ))
        ;

        //クエリー実行
        $entities = $queryBuilder->getQuery()->getResult();
        return $entities;
    }

    /**
     * ユーザー取得
     * @param $id
     * @return \Arte\PCMS\BizlogicBundle\Entity\TBSystemUser
     */
    public function getTbSystemUser($id)
    {
        /* @var $queryBuilder \Doctrine\ORM\QueryBuilder */
        $queryBuilder = $this->em
            ->getRepository('ArtePCMSBizlogicBundle:TBSystemUser')
            ->createQueryBuilder('u')
            ->leftjoin('u.TBDepartmentDepartmentId', 'd')
            ->andWhere('u.DeleteFlag = false')
            ->andWhere('u.id = :id')
            ->setParameter('id', $id)
            ->select(array(
                'u',
                'd',
//                'partial u.{id, DisplayName}',
//                'partial c.{id, Cost, WorkDate}',
//                'partial pc.{id, Name}',
//                'partial p.{id, Name, Status}',
            ))
        ;

        //クエリー実行
        $entity = $queryBuilder->getQuery()->getSingleResult();
        return $entity;
    }

    /**
     * Username存在チェック
     * @param $username
     * @return bool
     */
    public function existsUsername($username)
    {
        /* @var $queryBuilder \Doctrine\ORM\QueryBuilder */
        $queryBuilder = $this->em
            ->getRepository('ArtePCMSBizlogicBundle:TBSystemUser')
            ->createQueryBuilder('u')
            ->andWhere('u.Username = :username')
            ->setParameter('username', $username)
            ->select('count(u.id)')
        ;

        //クエリー実行
        $count = $queryBuilder->getQuery()->getSingleScalarResult();

        if($count == 0){
            return false;
        }
        return true;
    }

    /**
     * ユーザー登録
     *
     * パスワードはsetPasswordで設定された内容がsaltとpasswordに分解されて保存される
     * @param TBSystemUser $systemUser ユーザーデータ
     * @param TBDepartment $department すでに登録されてある所属部署
     * @param string $password パスワード
     * @return TBSystemUser
     * @throws \Exception
     */
    public function createSystemUser(TBSystemUser $systemUser, TBDepartment $department, $password)
    {
        if(is_null($password)){
            throw new \InvalidArgumentException('password is not valid.');
        }

        $this->em->getConnection()->beginTransaction();
        try {

            //
            $systemUser->setFormPassword($password);
            $systemUser->setFormPasswordConfirm($password);

            //password crypt
            $encoder = $this->encoder->getEncoder($systemUser);
            $cryptPassword = $encoder->encodePassword($password, $systemUser->getSalt());
            $systemUser->setPassword($cryptPassword);

            $systemUser->setTBDepartmentDepartmentId($department);
            $systemUser->setDeleteFlag(false);

            //validation
            $errors = $this->validator->validate($systemUser, array('db', 'newform'));
            if(count($errors) > 0){
                throw new ORMInvalidArgumentException($errors);
            }

            $this->em->persist($systemUser);//TBSystemUserを新規登録としてDoctrineに認識させる
            $this->em->flush();//SQL発行

            $this->em->getConnection()->commit();

        }catch (\Exception $e){
            $this->em->getConnection()->rollBack();
            throw $e;
        }

        return $systemUser;
    }

    /**
     * ユーザー更新
     *
     * パスワードはsetPasswordで設定された内容がsaltとpasswordに分解されて保存される
     * @param integer $id id
     * @param TBSystemUser $systemUser ユーザーデータ
     * @param TBDepartment $department 所属部署
     * @param string $password パスワード
     * @return TBSystemUser
     * @throws \Exception
     */
    public function updateSystemUser($id, TBSystemUser $systemUser, TBDepartment $department, $password = null)
    {
        $this->em->getConnection()->beginTransaction();
        try {

            //ユーザー取得
            $tbSystemUser = $this->getTbSystemUser($id);

            //ユーザー情報設定
            $tbSystemUser->setUsername($systemUser->getUsername());
            $tbSystemUser->setActive($systemUser->getActive());
            $tbSystemUser->setSystemRoleId($systemUser->getSystemRoleId());
            $tbSystemUser->setDisplayName($systemUser->getDisplayName());
            $tbSystemUser->setDisplayNameKana($systemUser->getDisplayNameKana());
            $tbSystemUser->setNickName($systemUser->getNickName());
            $tbSystemUser->setMailAddress($systemUser->getMailAddress());
            $tbSystemUser->setLastLoginDatetime($systemUser->getLastLoginDatetime());
            $tbSystemUser->setDeleteFlag(false);
            $tbSystemUser->setFormPassword($password);
            $tbSystemUser->setFormPasswordConfirm($password);

            //password crypt
            if($password != null){
                $encoder = $this->encoder->getEncoder($systemUser);
                $cryptPassword = $encoder->encodePassword($password, $systemUser->getSalt());
                $systemUser->setPassword($cryptPassword);
            }

            //部署情報設定
            $tbSystemUser->setTBDepartmentDepartmentId($department);

            //validation
            if($password != null){
                $errors = $this->validator->validate($systemUser, array('db', 'newform'));
            }else{
                $errors = $this->validator->validate($systemUser, array('db'));
            }
            if(count($errors) > 0){
                throw new ORMInvalidArgumentException($errors);
            }

            $this->em->flush();//SQL発行

            $this->em->getConnection()->commit();

        }catch (\Exception $e){
            $this->em->getConnection()->rollBack();
            throw $e;
        }

        return $tbSystemUser;
    }

    /**
     * パスワード変更
     * @param integer $id id
     * @param string $password パスワード
     * @throws \InvalidArgumentException パラメータ不正
     * @return TBSystemUser
     * @throws \Exception 登録失敗
     */
    public function updatePassword($id, $password)
    {
        if(is_null($password)){
            throw new \InvalidArgumentException('password is not valid.');
        }

        $this->em->getConnection()->beginTransaction();
        try {

            //ユーザー取得
            $tbSystemUser = $this->getTbSystemUser($id);
            $tbSystemUser->setFormPassword($password);
            $tbSystemUser->setFormPasswordConfirm($password);

            //password crypt
            $encoder = $this->encoder->getEncoder($tbSystemUser);
            $cryptPassword = $encoder->encodePassword($password, $tbSystemUser->getSalt());
            $tbSystemUser->setPassword($cryptPassword);

            //validation
//            $errors = $this->validator->validate($tbSystemUser, array('db', 'newform'));
            $errors = $this->validator->validate($tbSystemUser, array('db', 'editform'));
            if(count($errors) > 0){
                throw new ORMInvalidArgumentException($errors);
            }

            $this->em->flush();//SQL発行

            $this->em->getConnection()->commit();

        }catch (\Exception $e){
            $this->em->getConnection()->rollBack();
            throw $e;
        }

        return $tbSystemUser;
    }


    /**
     * ログイン日時を更新
     * @param integer $id ID
     * @return TBSystemUser
     * @throws \Exception
     */
    public function updateLoginDatetime($id)
    {
        $this->em->getConnection()->beginTransaction();
        try {

            //ユーザー取得
            $tbSystemUser = $this->getTbSystemUser($id);

            //ユーザー情報設定
            $tbSystemUser->setLastLoginDatetime(new \DateTime());

            $this->em->flush();//SQL発行

            $this->em->getConnection()->commit();

        }catch (\Exception $e){
            $this->em->getConnection()->rollBack();
            throw $e;
        }

        return $tbSystemUser;
    }

    /**
     * ユーザー削除
     * @param integer $id id
     * @throws \Exception
     */
    public function deleteSystemUser($id)
    {
        $this->em->getConnection()->beginTransaction();
        try {

            //ユーザー取得
            $tbSystemUser = $this->getTbSystemUser($id);

            //ユーザー情報設定
            $tbSystemUser->setDeleteFlag(true);

            $this->em->flush();//SQL発行

            $this->em->getConnection()->commit();

        }catch (\Exception $e){
            $this->em->getConnection()->rollBack();
            throw $e;
        }
    }

}

