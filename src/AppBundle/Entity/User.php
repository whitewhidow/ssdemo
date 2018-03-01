<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity
 * @ORM\Table(name="user")
 * @ExclusionPolicy("all")
 */
class User
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({"cget-user", "get-user", "put-user"})
     * @Expose
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     * @Groups({"cget-user", "get-user", "post-user", "put-user"})
     * @Expose
     * @Assert\NotNull(groups={"put-user", "post-user"})
     * @Assert\NotBlank(groups={"put-user", "post-user"})
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     * @Groups({"cget-user", "get-user", "post-user", "put-user"})
     * @Expose
     * @Assert\NotNull(groups={"put-user", "post-user"})
     * @Assert\NotBlank(groups={"put-user", "post-user"})
     * @Assert\Email(groups={"put-user", "post-user"})
     */
    protected $email;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     * @Groups({"cget-user", "get-user", "post-user", "put-user"})
     * @Expose
     * @Assert\NotNull(groups={"put-user", "post-user"})
     * @Assert\NotBlank(groups={"put-user", "post-user"})
     */
    protected $company;

    public function __construct()
    {
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * @param string $company
     */
    public function setCompany($company)
    {
        $this->company = $company;
    }

}