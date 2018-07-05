<?php
/**
 * Created by PhpStorm.
 * User: dario
 * Date: 02/06/18
 * Time: 11.18
 */

require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/lib.hphp";
require_once ($_SERVER["DOCUMENT_ROOT"]) . "/utils/auth.hphp";

$server = new \mysqli_wrapper\mysqli();

$user = new \auth\User();
$user->is_authorized(\auth\LEVEL_GOOGLE_TEACHER, \auth\User::UNAUTHORIZED_REDIRECT);
$user_info = ($user->get_info(new RetriveDocenteFromDatabase($server)));

$google_user = new \auth\GoogleConnection($user); $oauth2 = $google_user->getUserProps();
$permission = new \auth\PermissionManager($server, $user);
$permission->check('control.throw', \auth\PermissionManager::UNAUTHORIZED_REDIRECT);

class EccezzioneStatica
{
	private $result = 0;
	public function genera()
	{
		$this->result = EccezzioneStatica::buttala();
	}

	private static function buttala(): int
	{
		$a = 4;

		$a = rand() + 5 * $a;

		$a *= simula_eccezzione();

		return $a;
	}
}

class MyException extends LogicException
{
	public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
	{
		parent::__construct($message, $code, $previous);

		$a = 4;
		if($a === $a)
			throw new RuntimeException("\$a è eguale a quattro! LoL");
	}
}

function simula_eccezzione(): int
{
	if(true)
		throw new MyException("Una divisione per 0!", 900);
	return 1;
}


$eccezzione = new EccezzioneStatica();
$eccezzione->genera();
