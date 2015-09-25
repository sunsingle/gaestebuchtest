<?php
namespace AppBundle\Service;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\GBEntry;
use Doctrine\ORM\EntityManager;

class GuestbookManager extends Controller
{
	private $emanager	= null;
	private $mEntriesPerPage;
	
	function __construct($maxEntries,EntityManager $em){
		$this->mEntriesPerPage = $maxEntries;
		$this->emanager = $em;
	}
	
	public function testService(){
		return "TEstergebnis (Entries: ".$this->mEntriesPerPage.")";
	}
	
	public function getGuestbook($page = 1, &$sumPages = 1)
	{
		$rep = $this->emanager->getRepository("AppBundle:GBEntry");
		$qb = $rep->createQueryBuilder('a');
		$qb->select('COUNT(a)');
		$qb->where($qb->expr()->not('a.ref=-2'));
		
		$count = $qb->getQuery()->getSingleScalarResult();
		 
		if ($count > $this->mEntriesPerPage)
		{
			$sumPages = ceil($count / $this->mEntriesPerPage);
		
			if(!is_numeric($page))
			{
				if ($page == "lastpage")
					$page = $sumPages;
				else
					$page = 0;
			}
			if ($page > $sumPages)
				$page = $sumPages;
			elseif($page < 1)
				$page = 1;
			
			return $rep->findBy(
					array(),
					array('id'=>'ASC'),
					$this->mEntriesPerPage,
					($page-1)*$this->mEntriesPerPage
					);
		}
		return $rep->findAll();
	}
}