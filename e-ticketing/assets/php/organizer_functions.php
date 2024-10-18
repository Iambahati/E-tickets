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



	public function generateUserReservationReport($eventID)
	{
		// Fetch the data from the database for the specific event
		$sql = "SELECT r.id AS reservation_id, r.users_email, r.events_id, e.event_name, r.number_of_tickets, r.total_amount
            FROM reservations r
            INNER JOIN events e ON r.events_id = e.id
            WHERE r.events_id = :eventID";
		$stmt = $this->conn->prepare($sql);
		$stmt->execute(['eventID' => $eventID]);
		$reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);
		return $reservations;
	}

	public function fetchEventNameById(string $id)
	{
		$sql = "SELECT event_name FROM events WHERE id = :id";
		$stmt = $this->conn->prepare($sql);
		$stmt->execute(['id' => $id]);
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		return $result['event_name'];
	}
}
