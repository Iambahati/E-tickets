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
		$sql = "SELECT email, role FROM users WHERE email = :email";
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


	/**
	 * Updates the password reset token for the specified email.
	 *
	 * @param string $email The email of the user
	 * @param string $token The password reset token
	 * @return bool Returns true on success, or false on failure
	 */
	public function passwordResetToken($email, $token)
	{
		try {
			$sql = "UPDATE users 
				SET reset_token = :token,
					reset_token_expires = DATE_ADD(NOW(), INTERVAL 5 MINUTE),
					reset_token_consumed = FALSE
				WHERE email = :email";

			$stmt = $this->conn->prepare($sql);
			return $stmt->execute([
				'token' => $token,
				'email' => $email
			]);
		} catch (PDOException $e) {
			error_log("Error updating password reset token: " . $e->getMessage());
			return false;
		}
	}


	/**
	 * Validates the password reset token.
	 *
	 * @param string $token The password reset token to validate.
	 * @return int Returns 0 if the token is valid and newly consumed, 
	 *             2 if the token is already consumed, 
	 *             3 if the token is expired, 
	 *             4 if the token is invalid, 
	 *             or 1 if an error occurred.
	 */
	public function validateResetToken($token)
	{
		try {
			$sql = "SELECT id, reset_token_expires, reset_token_consumed 
					FROM users 
					WHERE reset_token = :token";

			$stmt = $this->conn->prepare($sql);
			$stmt->execute(['token' => $token]);

			if ($stmt->rowCount() > 0) {
				$result = $stmt->fetch(PDO::FETCH_ASSOC);

				if ($result['reset_token_consumed']) {
					return 1; // Error: Already consumed
				}

				if (strtotime($result['reset_token_expires']) < time()) {
					return 2; // Error: Expired
				}

				// Token is valid, mark as consumed
				$updateSql = "UPDATE users 
							 SET reset_token_consumed = TRUE 
							 WHERE reset_token = :token";
				$updateStmt = $this->conn->prepare($updateSql);
				$updateStmt->execute(['token' => $token]);

				return 0; // Success: Valid and newly consumed
			}

			return 3; // Error: Invalid token
		} catch (PDOException $e) {
			Utils::logger("Error validating reset token",  $e->getMessage());
			return 1; // Error: Exception occurred
		}
	}

	/**
	 * Updates the password for the specified email.
	 *
	 * @param string $email The email of the user
	 * @param string $password The new password
	 * @return bool Returns true on success, or false on failure
	 */
	public function resetPassword($email, $password)
	{
		try {
			$sql = "UPDATE users 
					SET password = :password 
					WHERE email = :email";

			$stmt = $this->conn->prepare($sql);
			return $stmt->execute([
				'password' => $password,
				'email' => $email
			]);
		} catch (PDOException $e) {
			error_log("Error updating password: " . $e->getMessage());
			return false;
		}
	}


	public function fetchUserDetails($userId)
	{
		$sql = "SELECT first_name, last_name, email, contact, password FROM users WHERE id = :userId";
		$stmt = $this->conn->prepare($sql);
		$stmt->execute(['userId' => $userId]);
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		return $result;
	}

	/**
	 * Updates the details of a user in the database.
	 *
	 * @param int $userId The ID of the user to update.
	 * @param string $firstName The new first name of the user.
	 * @param string $lastName The new last name of the user.
	 * @param string $email The new email address of the user.
	 * @param string $contact The new contact number of the user.
	 * 
	 * @return bool Returns true on success, or false on failure.
	 */
	public function updateUserDetails($userId, $firstName, $lastName, $email, $contact)
	{
		try {
			$sql = "UPDATE users SET first_name = :firstName, last_name = :lastName, email = :email, contact = :contact WHERE id = :userId";

			$stmt = $this->conn->prepare($sql);

			$params = [
				'firstName' => $firstName,
				'lastName' => $lastName,
				'email' => $email,
				'contact' => $contact,
				'userId' => $userId
			];

			return $stmt->execute($params);
		} catch (PDOException $e) {
			error_log("Error updating user details: " . $e->getMessage());
			return false;
		}
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


	public function orders($userId, $eventId, $no_tickets)
	{
		try {
			$this->conn->beginTransaction();

			// Check if enough tickets are available
			$checkSql = "SELECT ticket_quantity_available, ticket_price, title, description, start_datetime, end_datetime, location_details FROM events WHERE event_id = :eventId";
			$checkStmt = $this->conn->prepare($checkSql);
			$checkStmt->execute(['eventId' => $eventId]);
			$eventData = $checkStmt->fetch(PDO::FETCH_ASSOC);

			if ($eventData['ticket_quantity_available'] < $no_tickets) {
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

			// Calculate total amount
			$totalAmount = $eventData['ticket_price'] * $no_tickets;

			// Generate random order hash with uppercase letters
			$orderHash = strtoupper(substr(bin2hex(random_bytes(8)), 0, 8));

			// Insert into orders table
			$orderSql = "INSERT INTO orders (order_hash, user_id, event_id, total_tickets, total_amount)
                     VALUES (:orderHash, :userId, :eventId, :totalTickets, :totalAmount)";
			$orderStmt = $this->conn->prepare($orderSql);
			$orderStmt->execute([
				'orderHash' => $orderHash,
				'userId' => $userId,
				'eventId' => $eventId,
				'totalTickets' => $no_tickets,
				'totalAmount' => $totalAmount
			]);

			$this->conn->commit();

			// Prepare data for email
			$email = $this->getUserEmailById($userId);
			$eventName = $eventData['title'];
			$description = $eventData['description'];
			$ticketPrice = $eventData['ticket_price'];
			$venue = $eventData['location_details'];

			// Send email
			return Mailer::sendPaymentReceiptByEmail($email, $eventName, $venue, $description, $ticketPrice, $orderHash, $totalAmount, $no_tickets);
		} catch (Exception $e) {
			$this->conn->rollBack();
			error_log("Error in ticket purchase: " . $e->getMessage());
			throw $e;
		}
	}

	public function getUserEmailById($userId)
	{
		$sql = "SELECT email FROM users WHERE id = :userId";
		$stmt = $this->conn->prepare($sql);
		$stmt->execute(['userId' => $userId]);
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		return $result['email'];
	}


	public function getTicketsByOrderHash($orderHash)
	{
		$sql = "SELECT t.ticket_id, t.event_id, t.customer_id, t.purchase_date, t.ticket_status, t.quantity
            FROM tickets t
            JOIN orders o ON t.event_id = o.event_id AND t.customer_id = o.user_id
            WHERE o.order_hash = :orderHash";
		$stmt = $this->conn->prepare($sql);
		$stmt->execute(['orderHash' => $orderHash]);
		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $result;
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
