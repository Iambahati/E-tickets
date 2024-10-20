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
	public function createUserAccount($firstname, $lastname, $email, $contact, $password, $role)
	{
		$sql = "INSERT INTO users(first_name, last_name, email, contact, password, role) 
            VALUES(:fname, :lname, :email, :contact, :pass, :role)";
		$stmt = $this->conn->prepare($sql);
		$stmt->execute([
			'fname' => $firstname,
			'lname' => $lastname,
			'email' => $email,
			'contact' => $contact,
			'pass' => $password,
			'role' => $role
		]);
		return true;
	}

	/**
	 * @param string $email A users's email
	 * @return array
	 * @desc Returns username and password records from db based on the method parameters
	 */

	public function loginIntoAccount($email)
	{
		$sql = "SELECT id, first_name, last_name, password, role FROM users WHERE email = :email";
		$stmt = $this->conn->prepare($sql);
		$stmt->execute(['email' => $email]);
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		return $result;
	}


	public function fetchEvents()
	{
		$sql = "SELECT * 
		FROM events 
		WHERE end_datetime > NOW() 
		AND event_status = 'ACTIVE' 
		AND is_deleted = FALSE
		ORDER BY start_datetime ASC;
		";
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

	public function fetchEventById(string $id)
	{
		$sql = "SELECT * FROM events WHERE event_id = :id";
		$stmt = $this->conn->prepare($sql);
		$stmt->execute(['id' => $id]);
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		return $result;
	}

	public function orders($userId, $eventId, $no_tickets) {
		try {
			$this->conn->beginTransaction();

			// Check if enough tickets are available
			$checkSql = "SELECT ticket_quantity_available FROM events WHERE event_id = :eventId";
			$checkStmt = $this->conn->prepare($checkSql);
			$checkStmt->execute(['eventId' => $eventId]);
			$availableTickets = $checkStmt->fetchColumn();

			if ($availableTickets < $no_tickets) {
				throw new Exception("Not enough tickets available");
			}

			// Update ticket quantity
			$updateSql = "UPDATE events
						  SET ticket_quantity_available = ticket_quantity_available - :no_tickets
						  WHERE event_id = :eventId";
			$updateStmt = $this->conn->prepare($updateSql);
			$updateStmt->execute(['no_tickets' => $no_tickets, 'eventId' => $eventId]);

			// Insert into tickets table
			$insertSql = "INSERT INTO tickets (event_id, customer_id, ticket_status, quantity)
						  VALUES (:eventId, :userId, 'Purchased', :no_tickets)";
			$insertStmt = $this->conn->prepare($insertSql);
			$insertStmt->execute(['eventId' => $eventId, 'userId' => $userId, 'no_tickets' => $no_tickets]);

			$this->conn->commit();
			return true;
		} catch (Exception $e) {
			$this->conn->rollBack();
			error_log("Error in ticket purchase: " . $e->getMessage());
			throw $e;
		}
	}

	// public function orders($userId, $eventId, $no_tickets)
	// {
	// 	try {
			
			
	// 		// // SQL to insert into the tickets table, including the quantity
	// 		$insertSql = "INSERT INTO tickets (event_id, customer_id, ticket_status, quantity)
    //                       VALUES (:eventId, :userId, 'Purchased', :no_tickets)";
	// 		$insertStmt = $this->conn->prepare($insertSql);
	// 		$result = $insertStmt->execute([
	// 			'eventId' => $eventId,
	// 			'userId' => $userId,
	// 			'no_tickets' => $no_tickets
	// 		]);

	// 		if ($result) {
	// 			return true;
	// 		} else {
	// 			error_log("Failed to insert ticket. EventId: $eventId, UserId: $userId, No. of tickets: $no_tickets");
	// 			return false;
	// 		}
	// 	} catch (PDOException $e) {
	// 		error_log("Database error in orders method: " . $e->getMessage());
	// 		throw new Exception("Database error: " . $e->getMessage());
	// 	}
	// }



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

	public function getAttendedEventsHistory($userId)
	{
		$sql = "SELECT 
					e.event_id, 
					e.title, 
					e.start_datetime, 
					e.end_datetime, 
					t.purchase_date, 
					t.ticket_status 
				FROM 
					tickets t 
				JOIN 
					events e ON t.event_id = e.event_id 
				WHERE 
					t.customer_id = :userId 
				ORDER BY 
					t.purchase_date DESC";

		$stmt = $this->conn->prepare($sql);
		$stmt->execute(['userId' => $userId]);

		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
}
