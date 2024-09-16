<?php

require_once __DIR__ .  '/../../../connect/config.php';



class Admin extends Db
{

	/**
	 * Add a new event to the events table.
	 *
	 * @param string $event_name The name of the event.
	 * @param string $date The date of the event.
	 * @param string $venue The venue of the event.
	 * @param string $time The time of the event.
	 * @param int $ticket_price The ticket price of the event.
	 * @param int $tickets_capacity The capacity of tickets for the event.
	 * @param string $thumbnail The banner image for the event.
	 * @param string|null $from_date The starting date of a date range event (nullable).
	 * @param string|null $to_date The ending date of a date range event (nullable).
	 * @return bool True if the event is added successfully, false otherwise.
	 */
	public function addEvent($event_name, $date, $venue, $time, $ticket_price, $tickets_capacity, $thumbnail, $from_date, $to_date)
	{
		$sql = "INSERT INTO events (event_name, date, venue, time, ticket_price, tickets_capacity, banner, created_at, updated_at, from_date, to_date) 
				VALUES (:event_name, :date, :venue, :time, :ticket_price, :tickets_capacity, :banner, NOW(), '0000-00-00 00:00:00', :from_date, :to_date)";
		$stmt = $this->conn->prepare($sql);
		$stmt->execute([
			'event_name' => $event_name,
			'date' => $date,
			'venue' => $venue,
			'time' => $time,
			'ticket_price' => (int)$ticket_price,
			'tickets_capacity' => $tickets_capacity,
			'banner' => $thumbnail,
			'from_date' => $from_date,
			'to_date' => $to_date
		]);
		return true;
	}



	/**
	 * Fetch all events from the events table.
	 *
	 * @return array An array of events.
	 */
	public function fetchEvents()
	{
		$sql = "SELECT *,
            CASE
                WHEN date IS NULL THEN CONCAT(from_date, ' - ', to_date)
                ELSE date
            END AS event_date,
            CASE
                WHEN date IS NULL THEN from_date
                ELSE NULL
            END AS event_from_date,
            CASE
                WHEN date IS NULL THEN to_date
                ELSE NULL
            END AS event_to_date
            FROM events
            WHERE (date >= CURDATE() OR from_date >= CURDATE())
            ORDER BY date ASC";
		$stmt = $this->conn->prepare($sql);
		$stmt->execute();
		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
		$sql = "SELECT COUNT(*) FROM reservations WHERE events_id = :event_id";
		$stmt = $this->conn->prepare($sql);
		$stmt->execute(['event_id' => $id]);
		$reservationCount = $stmt->fetchColumn();

		if ($reservationCount > 0) {
			// There are reservations for the event, return false
			return false;
		}

		// No reservations exist, perform the deletion
		$sql = "DELETE FROM events WHERE id = :id";
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
