<?php

namespace IO\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Form\FormError;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception;
use Symfony\Component\Process\Exception\InvalidArgumentException;


use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\DBAL\DBALException;
use IO\AdminBundle\Form\UsersType;

class DefaultController extends Controller
{
	private $addUserSuccess = false;

    public function indexAction()
    {
		$database				= $this->get('database_connection');
		$databaseName			= $this->container->getParameter('database_name');
		$databaseSize			= 0;
		$rowCount				= 0;
		$currentRowCount		= 0;
		$biggestTable			= '';
		$tables					= $database->fetchAll('select TABLE_NAME as tableName, TABLE_ROWS as rowCount, Round((DATA_LENGTH + INDEX_LENGTH) / 1024 / 1024, 1) as tableSize from information_schema.tables where TABLE_SCHEMA = ?', array($databaseName));

		foreach($tables as $tableInfo)
		{
			$databaseSize		+= $tableInfo['tableSize'];
			$rowCount			+= $tableInfo['rowCount'];
			if($currentRowCount < $tableInfo['rowCount'])
			{
				$currentRowCount	= $tableInfo['rowCount'];
				$biggestTable		= $tableInfo['tableName'];
			}
		}

		$f = $this->container->getParameter('kernel.cache_dir');
		$io = popen ( '/usr/bin/du -sk ' . $f, 'r' );
		$size = fgets ( $io, 4096);
		$size = substr ( $size, 0, strpos ( $size, "\t" ) );
		pclose ( $io );

		$filesizeMB = sprintf("%.2f", ($size / 1024));
		if($filesizeMB > 1)
		{
			$cacheBtnClass = 'btn-danger';
		}else{
			$cacheBtnClass = 'btn-success';
		}

		$templateParams = array(
			'symfonyVersion' => Kernel::VERSION,
			'symfonyEOL' => Kernel::END_OF_LIFE,
			'timezone' => date_default_timezone_get(),
			'phpVersion' => phpversion(),
			'postMaxSize' => ini_get('post_max_size'),
			'sapi' => php_sapi_name(),
			'os' => PHP_OS,
			'gdSupport' => (extension_loaded('GD')) ? 'Yes' : 'No',
			'opCacheSupport' => (extension_loaded('Zend OPcache')) ? 'Yes' : 'No',
			'serverSoftware' => (isset($_SERVER['SERVER_SOFTWARE'])) ? $_SERVER['SERVER_SOFTWARE'] : 'Unkown',
			'serverIp' => (isset($_SERVER['SERVER_ADDR'])) ? $_SERVER['SERVER_ADDR'] : '127.0.0.1',
			'dbName' => $databaseName,
			'dbSize' => $databaseSize . ' MB',
			'numRecords' => $rowCount,
			'biggestTable' => $biggestTable,
			'biggestTableSize' => $currentRowCount . ' Records',
			'cacheButtonClass' => $cacheBtnClass,
			'cacheSize' =>  $filesizeMB . ' MB'
		);

        return $this->render('IOAdminBundle:Default:index.html.twig', $templateParams);
    }

	public function list_usersAction($pageNumber = 1)
	{
		$displayPerPage		= 50;
		$startRow			= ($pageNumber - 1) * $displayPerPage;

		$userClass			= $this->container->getParameter('io_admin.user_class');
		$em = $this->getDoctrine()->getManager()->getRepository($userClass);
		$entities = $em->createQueryBuilder('u');
		$entities->addSelect('u.id');
		$entities->addSelect('u.username');
		$entities->addSelect('u.usernameCanonical');
		$entities->addSelect('u.email');
		$entities->addSelect('u.enabled');
		$entities->addSelect('u.lastLogin');
		$entities->addSelect('u.locked');
		$entities->addSelect('u.expired');
		$entities->addSelect('u.passwordRequestedAt');
		$entities->addSelect('u.roles');
		$entities->orderBy('u.id', 'ASC')->setFirstResult($startRow)
			->setMaxResults($displayPerPage);

		$paginator = new Paginator($entities, $fetchJoinCollection = true);
		$resultCount = count($paginator);
		$pageCount = ceil($resultCount / $displayPerPage);

		return $this->render('IOAdminBundle:Users:list.html.twig', array(
			'entities' => $paginator,
			'pageCount' => $pageCount,
			'currentPage' => $pageNumber
		));
	}

	public function show_userAction($userName)
	{
		$this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN', null, 'Unable to access this page!');

		$userManager = $this->container->get('fos_user.user_manager');
		$user = $userManager->findUserByUsername($userName);

		if (!$user) {
			throw $this->createNotFoundException('Unable to find User.');
		}

		$deleteForm	= $this->createDeleteForm($userName);

		return $this->render('IOAdminBundle:Users:show.html.twig', array(
			'entity'      => $user,
			'delete_form' => $deleteForm->createView(),
			'addSuccess' => $this->addUserSuccess
		));
	}

	public function edit_userAction($userName)
	{
		$this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN', null, 'Unable to access this page!');

		$userManager = $this->container->get('fos_user.user_manager');
		$user = $userManager->findUserByUsername($userName);

		if (!$user) {
			throw $this->createNotFoundException('Unable to find User.');
		}

		$userClass	= $this->container->getParameter('io_admin.user_class');
		$entity		= new $userClass();
		$editUserForm = $this->getEditUserForm($userName);
		$editUserForm->get('username')->setData($userName);
		$editUserForm->get('email')->setData($user->getEmail());
		$userRoles = $user->getRoles();
		$editUserForm->get('userRole')->setData($userRoles[0]);
		$editUserForm->get('enabled')->setData($user->isEnabled());

		return $this->render('IOAdminBundle:Users:add_edit.html.twig', array(
			'entity' => $entity,
			'edit_form' => $editUserForm->createView(),
			'isEditForm' => true,
			'delete_form' => $this->createDeleteForm($userName)->createView()
		));
	}

	public function update_userAction(Request $request)
	{
		$this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN', null, 'Unable to access this page!');

		$userClass	= $this->container->getParameter('io_admin.user_class');
		$entity = new $userClass();
		$form = $this->getEditUserForm($request->get('currentUserName'));
		$form->handleRequest($request);

		if ($form->isValid()) {
			$userName = $form->get('username')->getData();
			$email = $form->get('email')->getData();
			$password = $form->get('plainPassword')->getData();
			$role = $form->get('userRole')->getData();
			$enabled = $form->get('enabled')->getData();
			$currentUserName = $form->get('currentUserName')->getData();

			$userManager = $this->container->get('fos_user.user_manager');
			$user = $userManager->findUserByUsername($currentUserName);

			if (!$user) {
				throw $this->createNotFoundException('Unable to find User.');
			}

			$user->setUsername($userName);
			$user->setEmail($email);
			$user->setEnabled($enabled);
			if(!empty($password))
			{
				$user->setPlainPassword($password);
			}
			$user->setRoles(array($role));

			$userManager->updateUser($user, false);

			$em = $this->getDoctrine()->getManager();
			$error = false;

			try {
				$em->flush();
			} catch (DBALException $e) {
				if ($e->getPrevious() &&  0 === strpos($e->getPrevious()->getCode(), '23')) {
					$error = true;
					$message = explode(PHP_EOL, $e->getMessage());
					$lastLine = count($message) - 1;
					$form->addError(new FormError($message[$lastLine]));
				}else{
					throw $e;
				}
			}

			if(!$error)
			{
				$form->addError(new FormError('User has been updated!'));
			}
		}

		return $this->render('IOAdminBundle:Users:add_edit.html.twig', array(
			'entity' => $entity,
			'edit_form' => $form->createView(),
			'isEditForm' => false
		));
	}

	public function delete_userAction(Request $request, $userName)
	{
		$this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN', null, 'Unable to access this page!');

		$userManager = $this->container->get('fos_user.user_manager');
		$user = $userManager->findUserByUsername($userName);

		if (!$user) {
			throw $this->createNotFoundException('Unable to find User.');
		}

		$userManager->deleteUser($user);

		return $this->list_usersAction();
	}

	public function add_userAction()
	{
		if (!$this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN')) {
			return $this->redirect($this->generateUrl('fos_user_registration_register'));
		}

		$userClass	= $this->container->getParameter('io_admin.user_class');
		$entity		= new $userClass();

		return $this->render('IOAdminBundle:Users:add_edit.html.twig', array(
			'entity' => $entity,
			'edit_form' => $this->getAddUserForm()->createView(),
			'isEditForm' => false,
		));
	}

	public function create_userAction(Request $request)
	{
		$this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN', null, 'Unable to access this page!');

		$userClass	= $this->container->getParameter('io_admin.user_class');
		$entity = new $userClass();
		$form = $this->getAddUserForm();
		$form->handleRequest($request);

		if ($form->isValid()) {
			$userName = $form->get('username')->getData();
			$email = $form->get('email')->getData();
			$password = $form->get('plainPassword')->getData();
			$role = $form->get('userRole')->getData();
			$enabled = $form->get('enabled')->getData();

			$userManager = $this->container->get('fos_user.user_manager');
			$user = $userManager->createUser();
			$user->setUsername($userName);
			$user->setEmail($email);
			$user->setEnabled($enabled);
			$user->setPlainPassword($password);
			$user->addRole($role);
			$userManager->updateUser($user, false);

			$em = $this->getDoctrine()->getManager();
			$error = false;

			try {
				$em->flush();
			} catch (DBALException $e) {
				if ($e->getPrevious() &&  0 === strpos($e->getPrevious()->getCode(), '23')) {
					$error = true;
					$message = explode(PHP_EOL, $e->getMessage());
					$lastLine = count($message) - 1;
					$form->addError(new FormError($message[$lastLine]));
				}else{
					throw $e;
				}
			}

			if(!$error)
			{
				$this->addUserSuccess = true;
				return $this->show_userAction($userName);
			}
		}

		return $this->render('IOAdminBundle:Users:add_edit.html.twig', array(
			'entity' => $entity,
			'edit_form' => $form->createView(),
			'isEditForm' => false
		));
	}

	public function clear_cacheAction()
	{
		$fs = new Filesystem();
		try
		{
			$fs->remove($this->container->getParameter('kernel.cache_dir'));
		}catch (InvalidArgumentException $e)
		{
			return $this->redirect($this->generateUrl('io_admin_homepage'));
		}

		return $this->redirect($this->generateUrl('io_admin_homepage'));
	}

	private function createDeleteForm($userName)
	{
		return $this->createFormBuilder()
			->setAction($this->generateUrl('io_admin_delete_user', array('userName' => $userName)))
			->setMethod('DELETE')
			->add('submit', 'submit', array('label' => 'Delete'))
			->getForm()
			;
	}

	private function getAddUserForm()
	{
		$form = $this->createForm(new UsersType(), null, array(
			'action' => $this->generateUrl('io_admin_create_user'),
			'method' => 'POST',
		));
		$form->add('submit', 'submit', array('label' => 'Create User'));

		return $form;
	}

	private function getEditUserForm($userName)
	{
		$form = $this->createForm(new UsersType(), null, array(
			'action' => $this->generateUrl('io_admin_update_user'),
			'method' => 'PUT',
		));
		$form->add('currentUserName', 'hidden', array('data'=>$userName));
		$form->add('submit', 'submit', array('label' => 'Save'));

		return $form;
	}
}
