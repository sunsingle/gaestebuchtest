<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="gaestebuchtest")
 */
class GBEntry
{
	/**
	 * @ORM\Column(type="integer", length=10)
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 * @ORM\OneToMany(targetEntity="AppBundle\Entity\GBComments", targetEntity="entryId")
	 */
	protected $id;

	/**
	 * @ORM\Column(type="string", length=30)
	 */
	protected $name;
	
	/**
	 * @ORM\Column(type="datetime")
	 */
	protected $date;
	
	/**
	 * @ORM\Column(type="string", length=30, nullable=true)
	 */
	protected $email;
	
	/**
	 * @ORM\Column(type="text")
	 */
	protected $entry;

	/**
	 * @ORM\Column(type="integer", length=10, options={"default"=-1}, nullable=true)
	 */
	protected $ref = -1;
    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return GBEntry
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return GBEntry
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return GBEntry
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
    	
        return $this->email != "" ? "<".$this->email.">" : "";
    }

    /**
     * Set entry
     *
     * @param string $entry
     *
     * @return GBEntry
     */
    public function setEntry($entry)
    {
        $this->entry = $entry;

        return $this;
    }

    /**
     * Get entry
     *
     * @return string
     */
    public function getEntry()
    {
        return $this->entry;
    }

    /**
     * Set ref
     *
     * @param integer $ref
     *
     * @return GBEntry
     */
    public function setRef($ref)
    {
        $this->ref = $ref;

        return $this;
    }

    /**
     * Get ref
     *
     * @return integer
     */
    public function getRef()
    {
        return $this->ref;
    }
}
