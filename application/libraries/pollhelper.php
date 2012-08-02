<?php

class PollHelper {

	/**
	 * Filters out any empty alternatives and returns the
	 * filtered array.
	 *
	 * @param array $alternatives
	 * @return array
	 */
	public static function filterAlternatives(array $alternatives)
	{
		$filteredAlternatives = array();
		foreach ($alternatives as $key => $value) {
			if (! strlen(trim($value))) continue;
			$filteredAlternatives[] = $value;
		}

		return $filteredAlternatives;
	}

	/**
	 * Saves the poll question and its alternatives to the
	 * database.
	 *
	 * @param string $question
	 * @param array $alternatives
	 * @return mixed
	 */
	public static function createPoll($question, array $alternatives)
	{
		$poll = new Poll();
		$poll->question = $question;
		if (! $poll->save()) return false;

		foreach ($alternatives as $alternative) {
			$alt = new Alternative();
			$alt->alternative = $alternative;
			$alt->poll_id = $poll->id;
			if (! $alt->save()) return false;
		}

		return $poll->id;
	}
}