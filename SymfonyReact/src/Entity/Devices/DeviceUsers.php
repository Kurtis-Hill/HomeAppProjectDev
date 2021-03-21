<?php


namespace App\Entity\Devices;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * DeviceUsers
 *
 * @ORM\Table(name="device-users", indexes={@ORM\Index(name="Devices", columns={"deviceNameID"})})
 * @ORM\Entity(repositoryClass="App\Repository\Devices\DevicesUsersRepository")
 */
class DeviceUsers implements UserInterface
{
    /**
     * @var int
     *
     * @ORM\Column(name="deviceUserID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $deviceUserID;

    /**
     * @var Devices
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Devices\Devices")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="deviceName", referencedColumnName="deviceNameID")
     * })
     */
    private $deviceName;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="text", length=32, nullable=false)
     */
    private string $password;

    /**
     * @var array
     *
     * @ORM\Column(name="roles", type="json", nullable=false)
     */
    private array $roles;


    public function getRoles(): array
    {
        return array_unique($this->roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getSalt()
    {
        // TODO: Implement getSalt() method.
    }

    public function getUsername()
    {
        // TODO: Implement getUsername() method.
    }

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }
}
