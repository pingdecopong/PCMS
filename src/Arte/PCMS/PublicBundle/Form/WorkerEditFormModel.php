<?php

namespace Arte\PCMS\PublicBundle\Form;

use Symfony\Component\Validator\Constraints as Assert;

class WorkerEditFormModel
{
    /**
     * @var
     * @Assert\NotBlank(groups={"WorkerEditForm"})
     */
    private $Users;

    /**
     * @param mixed $Users
     */
    public function setUsers($Users)
    {
        $this->Users = $Users;
    }

    /**
     * @return mixed
     */
    public function getUsers()
    {
        return $this->Users;
    }

}