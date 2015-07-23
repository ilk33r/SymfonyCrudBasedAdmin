<?php
/**
 * Created by PhpStorm.
 * User: ilk3r
 * Date: 06/07/15
 * Time: 19:17
 */

namespace Ecommerce\StandartBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;

/**
 * User
 */
class User extends BaseUser
{

	protected $realName;
	protected $realSurname;
	protected $gender;
	protected $userTypeApproved;
	protected $birthdate;
	protected $country;
	protected $city;
	protected $town;
	protected $telephoneNumber;
	protected $taxOffice;
	protected $taxId;
	protected $registerMailingList;

	public function __construct()
	{
		parent::__construct();
		// your own logic

		$this->roles = array('ROLE_USER');
	}

	public function setEmail($email)
	{
		$this->email = $email;
		$this->username = $email;

		return $this;
	}

	public function setEmailCanonical($emailCanonical)
	{
		$this->emailCanonical = $emailCanonical;
		$this->usernameCanonical = $emailCanonical;

		return $this;
	}

	public function setRealName($realName)
	{
		$this->realName		= $realName;

		return $this;
	}

	public function getRealName()
	{
		return $this->realName;
	}

	public function setRealSurname($realSurname)
	{
		$this->realSurname		= $realSurname;

		return $this;
	}

	public function getRealSurname()
	{
		return $this->realSurname;
	}

	public function setGender($gender)
	{
		$this->gender		= $gender;

		return $this;
	}

	public function getGender()
	{
		return $this->gender;
	}

	public function setUserTypeApproved($userTypeApproved)
	{
		$this->userTypeApproved		= $userTypeApproved;

		return $this;
	}

	public function getUserTypeApproved()
	{
		return $this->userTypeApproved;
	}

	public function setBirthdate($birthdate)
	{
		$this->birthdate		= $birthdate;

		return $this;
	}

	public function getBirthdate()
	{
		return $this->birthdate;
	}

	public function setCountry($country)
	{
		$this->country		= $country;

		return $this;
	}

	public function getCountry()
	{
		return $this->country;
	}

	public function setCity($city)
	{
		$this->city		= $city;

		return $this;
	}

	public function getCity()
	{
		return $this->city;
	}

	public function setTown($town)
	{
		$this->town		= $town;

		return $this;
	}

	public function getTown()
	{
		return $this->town;
	}

	public function setTelephoneNumber($telephoneNumber)
	{
		$this->telephoneNumber		= $telephoneNumber;

		return $this;
	}

	public function getTelephoneNumber()
	{
		return $this->telephoneNumber;
	}

	public function setTaxOffice($taxOffice)
	{
		$this->taxOffice		= $taxOffice;

		return $this;
	}

	public function getTaxOffice()
	{
		return $this->taxOffice;
	}

	public function setTaxId($taxId)
	{
		$this->taxId		= $taxId;

		return $this;
	}

	public function getTaxId()
	{
		return $this->taxId;
	}

	public function setRegisterMailingList($registerMailingList)
	{
		$this->registerMailingList		= $registerMailingList;

		return $this;
	}

	public function getRegisterMailingList()
	{
		return $this->registerMailingList;
	}
}