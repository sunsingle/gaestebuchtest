<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping AS ORM;
use Doctrine\ORM\Mapping\Id;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="gbuser")
 */
class GBUserEntry implements UserInterface, \Serializable
{
	public function getRoles() {
		return array('ROLE_USER');
	}	
	public function getSalt() {
// 		return null;
		return md5("5ALT3DH45HF0R".$this->nick);
	}
	public function eraseCredentials(){}


	
	/**
	 * @ORM\Column(type="integer",length=3)
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $uid;	
	
	/**
	 * @ORM\Column(type="string",length=10, unique=true)
	 */
	protected $nick;
	
	/**
	 * @ORM\Column(type="string", length=32)
	 */
	protected $pass;
	
	protected $salt;

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
    public function getUsername()
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
    public function getPassword()
    {
        return $this->pass;
    }
    
    /** @see \Serializable::serialize() */
    public function serialize()
    {
    	return serialize(array(
    		$this->uid,
    		$this->nick,
    		$this->pass,
    		$this->salt
    	));
    }
    
    /** @see \Serializable::unserialize() */
    public function unserialize($serialized)
    {
    	list (
    		$this->uid,
    		$this->nick,
    		$this->pass,
    		$this->salt
    	) = unserialize($serialized);
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
     * Get pass
     *
     * @return string
     */
    public function getPass()
    {
        return $this->pass;
    }
}
