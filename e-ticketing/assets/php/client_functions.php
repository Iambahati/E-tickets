<?php

require_once __DIR__ .  '/../../connect/config.php';

require_once __DIR__ .  '/../../mailer.php';





class Client extends Db
{
	/**
	 * Check if a user with the specified email  exists in the users table
	 *
	 * @param string $email The service number of the user to check
	 * @return int The number of row(s) in the users table that match the given email
	 */
	public function user_exists($email)
	{
		$sql = "SELECT email FROM users WHERE email = :email";
		$stmt = $this->conn->prepare($sql);
		$stmt->execute(['email' => $email]);
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		return $result;
	}
	/**
	 * Registers a new user in the database.
	 * 
	 * @param string $name The name of the user
	 * @param string $email The email of the user
	 * @param string $password The password of the user
	 * @return bool Returns true on successful registration, false otherwise
	 */
	public function createUserAccount($username, $email, $contact, $password)
	{
		$sql = "INSERT INTO users(username, email, contact, password)VALUES(:uname, :email, :phone, :pass)";
		$stmt = $this->conn->prepare($sql);
		$stmt->execute(['uname' => $username, 'email' => $email, 'phone' => $contact, 'pass' => $password]);
		return true;
	}

	/**
	 * @param string $email A users's email
	 * @return array
	 * @desc Returns username and password records from db based on the method parameters
	 */

	public function loginIntoAccount($email)
	{
		$sql = "SELECT username, password, is_admin FROM users WHERE email = :email";
		$stmt = $this->conn->prepare($sql);
		$stmt->execute(['email' => $email]);
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		return $result;
	}

	public function fetchEvents()
	{
		$sql = "SELECT *,
            CASE
                WHEN date IS NULL THEN CONCAT(from_date, ' - ', to_date)
                ELSE date
            END AS event_date
            FROM events
            WHERE (date >= CURDATE() OR from_date >= CURDATE())
            ORDER BY date ASC";
		$stmt = $this->conn->prepare($sql);
		$stmt->execute();
		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $result;
	}


	public function fetchUserIdByEmail(string $email)
	{
		$sql = "SELECT id FROM users WHERE email = :email";
		$stmt = $this->conn->prepare($sql);
		$stmt->execute(['email' => $email]);
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		return $result['id'];
	}

	public function fetchEventNameById(string $id)
	{
		$sql = "SELECT event_name FROM events WHERE id = :id";
		$stmt = $this->conn->prepare($sql);
		$stmt->execute(['id' => $id]);
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		return $result['event_name'];
	}


	public function fetchEventDateById(string $id)
	{
		$sql = "SELECT date, from_date, to_date FROM events WHERE id = :id";
		$stmt = $this->conn->prepare($sql);
		$stmt->execute(['id' => $id]);
		$result = $stmt->fetch(PDO::FETCH_ASSOC);

		if ($result) {
			if ($result['date'] === null) {
				// If the 'date' column is null, return the 'from_date' and 'to_date' columns
				$result['eventDate'] = $result['from_date'] . ' - ' . $result['to_date'];
			} else {
				// If the 'date' column has a value, return it as the event date
				$result['eventDate'] = $result['date'];
			}

			$currentDate = date('Y-m-d'); // Get the current date
			$result['hasPassed'] = $result['eventDate'] < $currentDate;

			// Convert the event date to the desired format (e.g., Thu, Jan 01, 2022)
			$result['eventDate'] = date('D, M d, Y', strtotime($result['eventDate']));
		}

		return $result;
	}


	public function createReservation($userId, $eventId, $no_tckts, $total)
	{
		$sql = "INSERT INTO reservations(number_of_tickets, total_amount, users_id, events_id)VALUES(:no_tckts, :t_amount, :userId, :eventId)";
		$stmt = $this->conn->prepare($sql);
		$stmt->execute(['no_tckts' => $no_tckts, 't_amount' => $total, 'userId' => $userId['id'], 'eventId' => $eventId]);
		return true;
	}

	/**
	 * @param string $tablename
	 * @return array
	 * @desc Returns count of Rows based on the method parameters
	 */
	public function totalCount($tableName)
	{
		$sql = "SELECT COUNT(*) FROM $tableName";
		$stmt = $this->conn->prepare($sql);
		$stmt->execute();
		$count = $stmt->fetchColumn();
		return $count;
	}

	/**
	 * Loads data from the "reservations" table.
	 *
	 * @return array An array containing the retrieved data.
	 */
	public function reservationsPerPage($current_page)
	{
		$offset = ($current_page - 1) * 5;

		$sql = "SELECT r.*, e.date
            FROM reservations AS r
            JOIN events AS e ON r.events_id = e.id
            ORDER BY e.date ASC
            LIMIT :offset, :records_per_page";

		$stmt = $this->conn->prepare($sql);
		$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
		$stmt->bindValue(':records_per_page', 5, PDO::PARAM_INT);
		$stmt->execute();
		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

		return $result;
	}

	public function getEventNameFromId($id)
	{
		$sql = "SELECT event_name FROM events WHERE id = :id";
		$stmt = $this->conn->prepare($sql);
		$stmt->execute(['id' => $id]);
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		return $result['event_name'];
	}
}


class Reservations extends Db
{

	public function makeReservation($numberOfTickets, $totalAmount, $userEmail, $userId, $eventId)
	{
		// Insert reservation details into the 'reservations' table
		$sql = "INSERT INTO `reservations` (`number_of_tickets`, `total_amount`, `users_email`, `events_id`) VALUES (?, ?, ?, ?)";
		$stmt = $this->conn->prepare($sql);
		$stmt->execute([$numberOfTickets, $totalAmount, $userEmail, $eventId]);

		// Get the last inserted ID, which is the reservation ID
		$reservationId = $this->conn->lastInsertId();

		// Decrement the tickets_capacity column in the events table
		$updateSql = "UPDATE `events` SET `tickets_capacity` = `tickets_capacity` - ? WHERE `id` = ?";
		$updateStmt = $this->conn->prepare($updateSql);
		$updateStmt->execute([$numberOfTickets, $eventId]);

		// Create a unique transaction for each reservation
		$this->createTransaction($userId);

		// Generate unique tickets for the reservation
		return $this->generateTickets($reservationId, $numberOfTickets);
	}


	protected function createTransaction($userId)
	{
		// Generate a unique transaction code
		$transactionCode = $this->generateToken();

		// Insert the user ID and transaction code into the 'transactions' table
		$sql = "INSERT INTO `transactions` (`users_id`, `transaction_code`) VALUES (?, ?)";
		$stmt = $this->conn->prepare($sql);
		$stmt->execute([$userId, $transactionCode]);

		return $transactionCode;
	}

	protected function generateTickets($reservationId, $numberOfTickets)
	{
		// Save tcks into an array since tickets could one or more
		$tickets = [];
		for ($i = 0; $i < $numberOfTickets; $i++) {
			// Generate a unique token for each ticket
			$token = $this->generateToken();

			// Insert the reservation ID and token into the 'tickets' table
			$sql = "INSERT INTO `tickets` (`reservation_id`, `token`) VALUES (?, ?)";
			$stmt = $this->conn->prepare($sql);
			$stmt->execute([$reservationId, $token]);

			// Add the token to the tickets array
			$tickets[] = $token;
		}

		return $tickets;
	}

	// Generate a unique token
	protected function generateToken()
	{
		return bin2hex(random_bytes(16));
	}

	public function getEventVenueFromId($id)
	{
		$sql = "SELECT venue FROM events WHERE id = :id";
		$stmt = $this->conn->prepare($sql);
		$stmt->execute(['id' => $id]);
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		return $result['venue'];
	}

	public function getEventTimeFromId($id)
	{
		$sql = "SELECT time FROM events WHERE id = :id";
		$stmt = $this->conn->prepare($sql);
		$stmt->execute(['id' => $id]);
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		return $result['id'];
	}

	public function confirmReservationAndSendTickets($numberOfTickets, $totalAmount, $userEmail, $userId, $eventId, $eventName, $validityDates)
	{
		// Make reservations and get the tickets
		$tickets = $this->makeReservation($numberOfTickets, $totalAmount, $userEmail, $userId, $eventId);

		$eventLocation = $this->getEventVenueFromId($eventId);
		$eventTime = $this->getEventTimeFromId($eventId);
		// Invoke Mailer class to statically access
		// sendTicketsByEmail function now which  send the tickets
		if (Mailer::sendTicketsByEmail($userEmail, $tickets, $eventName, $validityDates, $eventLocation, $eventTime)) {
			return true;
		} else {
			return false;
		}
	}
}
