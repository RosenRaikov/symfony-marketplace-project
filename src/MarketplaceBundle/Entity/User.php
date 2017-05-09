<?php

namespace MarketplaceBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;


/**
 * User
 *
 * @ORM\Table(name="users")
 * @ORM\Entity(repositoryClass="MarketplaceBundle\Repository\UserRepository")
 */
class User implements UserInterface
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=50, unique=true)
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255)
     */
    private $password;

    /**
     * @var string
     *
     * @ORM\Column(name="Wallet", type="decimal", precision=10, scale=2)
     */
    private $wallet;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set username
     *
     * @param string $username
     *
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set password
     *
     * @param string $password
     *
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set wallet
     *
     * @param string $wallet
     *
     * @return User
     */
    public function setWallet($wallet)
    {
        $this->wallet = $wallet;

        return $this;
    }

    /**
     * Get wallet
     *
     * @return string
     */
    public function getWallet()
    {
        return $this->wallet;
    }

    /**
     * @var Role[]|ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="MarketplaceBundle\Entity\Role", inversedBy="users")
     * @ORM\JoinTable(name="user_roles", joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")}, inverseJoinColumns={@ORM\JoinColumn(name="role_id", referencedColumnName="id")})
     */
    private $roles;

    /**
     * @var Product[]|ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="MarketplaceBundle\Entity\Product", inversedBy="users")
     * @ORM\JoinTable(name="user_products", joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")}, inverseJoinColumns={@ORM\JoinColumn(name="product_id", referencedColumnName="id")})
     */
    private $products;


    public function __construct()
    {
        $this->products = new ArrayCollection();
        $this->roles = new ArrayCollection();
    }

    /**
     * Returns the roles granted to the user.
     *
     * <code>
     * public function getRoles()
     * {
     *     return array('ROLE_USER');
     * }
     * </code>
     *
     * Alternatively, the roles might be stored on a ``roles`` property,
     * and populated in any number of different ways when the user object
     * is created.
     *
     * @return (Role|string)[] The user roles
     */
    public function getRoles()
    {
        return array_map(function (Role $role){
            return $role->getName();
        }, $this->roles->toArray());
    }

    /**
     * @return ArrayCollection|Product[]
     */
    public function getProducts()
    {
        return array_map(function (Product $product){
            return $product;
        }, $this->products->toArray());
    }

    /**
     * @param ArrayCollection|Product[] $products
     */
    public function setProducts($products)
    {
        $this->products[] = $products;
    }



    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string|null The salt
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function addRole(Role $role){
        $this->roles->add($role);
    }
}

