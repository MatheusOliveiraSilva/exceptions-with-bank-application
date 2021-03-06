<?php
use exception\InsufficientBalanceException;

// require 'exception\InsufficientBalanceException.php';
class CheckingAccount {

	private $holder;
	public $agency;
	private $number;
	private $balance;
	public static $totalOfAccounts;
	public static $operationTax;
	public $withdrawalsNotAlloweds;
	public static $operationNotRealized;

	public function __construct($holder, $agency, $number, $balance) 
		{
		$this->holder = $holder;
		$this->agency = $agency;
		$this->number = $number;
		$this->balance = $balance;

		self::$totalOfAccounts++;

		try {
			if(self::$totalOfAccounts < 1) {
				throw new \Exception("The value is smaller than 0");
			}
			self::$operationTax =30/ self::$totalOfAccounts;
		} catch(\Exception $e) {
			echo $e->getMessage();
			exit;
		}
	}

	public function __get($attribute)
	{
		Validation::protectAttribute($attribute);
		return $this->$attribute;
	}

	public function __set($attribute, $value)
	{

		Validation::protectAttribute($attribute);
		$this->$attribute = $value;
	}

	public function withdraw($value) 
	{
		Validation::verifyNumeric($value);
		if($value > $this->balance) {
			throw new InsufficientBalanceException("There is no sufficient money in your account!!!", $value, $this->balance);
		}
		$this->balance = $this->balance - $value;
		return $this;
	}


	public function deposit($value) 
	{
		Validation::verifyNumeric($value);
		$this->balance = $this->balance + $value;
		return $this;
	}

	public function transfer($value, CheckingAccount $account)
	{  
		try {

			$file = new FileReading("logTransfer.txt");

			$file->openFile();
			$file->writeFile();


			Validation::verifyNumeric($value);

			if($value <= 0) {
				throw new \Exception("The value should to be bigger than 0");
			}
			$this->sacar($value);

			$contaCorrente->deposit($value);
			$file->closeFile();

			return $this;
		} catch(\Exception $e) {
			self::$operationNotRealized++;
			throw new exception\OperationNotRealizedException("Operation not realized", 55, $e);
		} finally {
			echo "finally";
			$file->closeFile();
		}
	}
	
	public function formatBalance() 
	{
		return "R$" . number_format($this->balance, 2, ",", ".");
	}

	public function __toString()
	{
		return $this->balance;
	}

	public function setNumber($number) 
	{
		return $this->number;
	}


	public function getHolder()
	{
		return $this->holder;
	}

	public function getBalance()
	{
		return $this->formatBalance();
	}
}
