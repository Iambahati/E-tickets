<?php

require_once __DIR__ .  '/../../connect/config.php';



class Organizer extends Db
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
	 * Registers a merchant user in the database.
	 * 
	 * @param string $name The name of the user
	 * @param string $email The email of the user
	 * @param string $password The password of the user
	 * @return bool Returns true on successful registration, false otherwise
	 */
	public function createMerchantAccount($orgname, $email, $contact, $password, $role)
	{
		$sql = "INSERT INTO users(organization_name,  email, contact, password, role) 
            VALUES(:orgname, :email, :contact, :pass, :role)";
		$stmt = $this->conn->prepare($sql);
		$stmt->execute([
			'orgname' => $orgname,
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

	public function loginIntoMerchantAccount($email)
	{
		$sql = "SELECT id, organization_name, password, role FROM users WHERE email = :email";
		$stmt = $this->conn->prepare($sql);
		$stmt->execute(['email' => $email]);
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		return $result;
	}

	public function addEvent($organizer_id, $title, $description, $location_details, $event_type, $start_datetime, $end_datetime, $ticket_price, $ticket_quantity_available, $event_photo)
	{
		$sql = "INSERT INTO events (organizer_id, title, description, location_details, event_type, start_datetime, end_datetime, ticket_price, ticket_quantity_available, event_photo, event_status, created_at, updated_at) 
				 VALUES (:organizer_id, :title, :description, :location_details, :event_type, :start_datetime, :end_datetime, :ticket_price, :ticket_quantity_available, :event_photo, 'ACTIVE', NOW(), NOW())";

		$stmt = $this->conn->prepare($sql);
		return $stmt->execute([
			'organizer_id' => $organizer_id,
			'title' => $title,
			'description' => $description,
			'location_details' => $location_details,
			'event_type' => $event_type,
			'start_datetime' => $start_datetime,
			'end_datetime' => $end_datetime,
			'ticket_price' => (int)$ticket_price,
			'ticket_quantity_available' => $ticket_quantity_available,
			'event_photo' => $event_photo
		]);
	}

	public function updateEvent($event_id, $organizer_id, $title, $description, $location_details, $event_type, $start_datetime, $end_datetime, $ticket_price, $ticket_quantity_available)
	{
		$sql = "UPDATE events 
            SET organizer_id = :organizer_id, 
                title = :title, 
                description = :description, 
                location_details = :location_details, 
                event_type = :event_type, 
                start_datetime = :start_datetime, 
                end_datetime = :end_datetime, 
                ticket_price = :ticket_price, 
                ticket_quantity_available = :ticket_quantity_available, 
                updated_at = NOW() 
            WHERE event_id = :event_id";

		$stmt = $this->conn->prepare($sql);

		return $stmt->execute([
			'organizer_id' => $organizer_id,
			'title' => $title,
			'description' => $description,
			'location_details' => $location_details,
			'event_type' => $event_type,
			'start_datetime' => $start_datetime,
			'end_datetime' => $end_datetime,
			'ticket_price' => (int)$ticket_price,
			'ticket_quantity_available' => $ticket_quantity_available,
			'event_id' => $event_id
		]);
	}


	/**
	 * Fetch all events from the events table.
	 *
	 * @return array An array of events.
	 */
	public function fetchEvents(int $userId)
	{
		$sql = "SELECT * FROM events
            WHERE (start_datetime >= CURDATE() OR end_datetime >= CURDATE())
            AND organizer_id = :userId
			AND is_deleted = FALSE
            ORDER BY start_datetime ASC";

		$stmt = $this->conn->prepare($sql);
		$stmt->execute(['userId' => $userId]);
		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $result;
	}

	public function getEventById($eventId)
	{
		$sql = "SELECT * FROM events WHERE event_id = :eventId";
		$stmt = $this->conn->prepare($sql);
		$stmt->execute(['eventId' => $eventId]);
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		return $result;
	}


	/**
	 * Edit an existing event in the events table.
	 *
	 * @param int $id The ID of the event to edit.
	 * @param string $evname The updated name of the event.
	 * @param string $venue The updated venue of the event.
	 * @param string $date The updated date of the event.
	 * @param string $time The updated time of the event.
	 * @param int $ticket_price The updated ticket price of the event.
	 * @param int $tickets_capacity The updated capacity of tickets for the event.
	 * @return bool True if the event is edited successfully, false otherwise.
	 */
	public function editEvent($id, $evname, $venue, $date, $from_date, $to_date, $time, $ticket_price, $tickets_capacity)
	{
		$sql = "UPDATE events SET event_name = :name, date = :dt, from_date = :from_date, to_date = :to_date, venue = :loc, time = :time, ticket_price = :tck_price, tickets_capacity = :tickets_capacity, updated_at = NOW() WHERE id = :id";
		$stmt = $this->conn->prepare($sql);
		$stmt->execute(['id' => $id, 'name' => $evname, 'dt' => $date, 'from_date' => $from_date, 'to_date' => $to_date, 'loc' => $venue, 'time' => $time, 'tck_price' => $ticket_price, 'tickets_capacity' => $tickets_capacity]);
		return true;
	}


	/**
	 * Delete an event from the events table.
	 *
	 * @param int $id The ID of the event to delete.
	 * @return bool True if the event is deleted successfully, false otherwise.
	 */
	public function deleteEvent($id)
	{
		// Check if there are any reservations for the event
		$sql = "SELECT COUNT(*) FROM events WHERE event_id = :event_id";
		$stmt = $this->conn->prepare($sql);
		$stmt->execute(['event_id' => $id]);

		// No reservations exist, perform the deletion
		$sql = "UPDATE events SET is_deleted = TRUE WHERE event_id = :id";
		$stmt = $this->conn->prepare($sql);
		$stmt->execute(['id' => $id]);

		return true;
	}

	public function fetchEventNameById(string $id)
	{
		$sql = "SELECT event_name FROM events WHERE id = :id";
		$stmt = $this->conn->prepare($sql);
		$stmt->execute(['id' => $id]);
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		return $result['event_name'];
	}

	/**
	 * Retrieves a list of attendees and their ticket count for a specific event
	 *
	 * This method performs a JOIN query between users and tickets tables to get
	 * attendee information along with the number of tickets they purchased for 
	 * the specified event.
	 *
	 * @param int $eventId The ID of the event to get attendees for
	 * @return array Returns an associative array containing:
	 *               - first_name (string) First name of attendee
	 *               - last_name (string) Last name of attendee
	 * 			 	 - email (string) Email address of attendee
	 *               - tickets_bought (int) Number of tickets purchased by attendee
	 */
	public function getEventAttendees($eventId)
	{
		$sql = "SELECT u.first_name, u.last_name, u.email, COUNT(t.ticket_id) AS tickets_bought
            FROM users u
            JOIN tickets t ON u.id = t.customer_id
            WHERE t.event_id = :eventId
            GROUP BY u.id";
		$stmt = $this->conn->prepare($sql);
		$stmt->execute(['eventId' => $eventId]);
		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $result;
	}

	/**
	 * Retrieves sales information for a specific event
	 * 
	 * This method fetches detailed sales data including customer information,
	 * ticket quantities, amounts and order dates for a given event ID
	 *
	 * @param int $eventId The ID of the event to get sales information for
	 * @return array An array of associative arrays containing:
	 *               - first_name: Customer's first name
	 *               - last_name: Customer's last name 
	 *               - email: Customer's email address
	 *               - total_tickets: Number of tickets purchased
	 *               - total_amount: Total purchase amount
	 *               - order_date: Date of the order
	 */
	public function getEventSales($eventId)
	{
		$sql = "SELECT u.first_name, u.last_name, u.email, o.total_tickets, o.total_amount, o.order_date
            FROM users u
            JOIN orders o ON u.id = o.user_id
            WHERE o.event_id = :eventId";
		$stmt = $this->conn->prepare($sql);
		$stmt->execute(['eventId' => $eventId]);
		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $result;
	}
}
