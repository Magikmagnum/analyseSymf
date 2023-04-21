<?php

namespace App\Controller\Helpers;

use DateTimeImmutable;

class CheckHelper
{
	public function validatePassword(string $password)
	{
		if (!isset($password)) {
			$errors = 'Champs obligatoir';
		} elseif (preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,}$/", $password) == 0) {
			$errors = 'Au moins 8 caractaire, 1 Maj, 1 min et 1 chiffre';
		} else {
			$errors = false;
		}
		return $errors;
	}

	public function validate(\stdClass $content, string $var)
	{
		if (isset($content->$var) && !empty(trim($content->$var))) {
			return true;
		}
		return false;
	}

	public function validateBoolean(\stdClass $content, string $var)
	{
		if (isset($content->$var)) {
			if (is_bool($content->$var)) {
				return true;
			}
		}
		return false;
	}

	public function validateDate($date, $format = 'd-m-Y')
	{

		try {
			$dt = \DateTime::createFromFormat($format, $date);
			return $dt && $dt->format($format) === $date;
		} catch (\Throwable $th) {
			return false;
		}
		return false;
	}

	public function validateDateIntervale($date, $min)
	{
		try {
			if ($this->validateDate($date)) {
				$date1 = new DateTimeImmutable($date);
				$date2 = new DateTimeImmutable();

				$diff = $date1->diff($date2, true)->y;


				if ($diff >= $min) {
					return true;
				}
			}
		} catch (\Throwable $th) {
			return false;
		}
		return false;
	}
}
