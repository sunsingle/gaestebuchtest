<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping AS ORM;
use Doctrine\ORM\Mapping\Id;

/**
 * @ORM\Entity
 * @ORM\Table(name="gbuser")
 */
class GBUserEntry
{
	/**
	 * @ORM\Column(type="integer",length=3)
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $uid;	
	
	/**
	 * @ORM\Column(type="string",length=10)
	 */
	protected $nick;
	
	/**
	 * @ORM\Column(type="string", length=32)
	 */
	protected $pass;

    /**
     * Get uid
     *
     * @return integer
     */
    public function getUid()
    {
        return $this->uid;
    }

    /**
     * Set nick
     *
     * @param string $nick
     *
     * @return GBUserEntry
     */
    public function setNick($nick)
    {
        $this->nick = $nick;

        return $this;
    }

    /**
     * Get nick
     *
     * @return string
     */
    public function getNick()
    {
        return $this->nick;
    }

    /**
     * Set pass
     *
     * @param string $pass
     *
     * @return GBUserEntry
     */
    public function setPass($pass)
    {
        $this->pass = $pass;

        return $this;
    }

    /**
     * Get pass
     *
     * @return string
     */
    public function getPass()
    {
        return $this->pass;
    }
}
