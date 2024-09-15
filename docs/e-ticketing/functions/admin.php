<?php

require_once  '../connect/config.php';


class Admin extends Db
{

    public function addEvent($id, $event_name, $date, $venue, $time, $ticket_price,  $tickets_available, $banner)
	{

		 // Convert the date string to a DateTime object
		 $inputDate = DateTime::createFromFormat('d/m/Y', $date);

		 // Get the current date
		 $currentDate = new DateTime();
	 
		 // Compare the input date with the current date
		 if ($inputDate < $currentDate) {
			 throw new Exception("Invalid date.");
		 }
	 
		$sql = "INSERT INTO events(id, event_name, date, venue, time, ticket_price, tickets_available, banner, created_at, updated_at) VALUES(:id, :event_name, :date, :venue, :time, :ticket_price, :tickets_available, :banner, NOW(), '0000-00-00 00:00:00')";
		$stmt = $this->conn->prepare($sql);

		// Get the file extension of the banner
		$bannerExtension = pathinfo($banner, PATHINFO_EXTENSION);

		// Define the allowed banner extensions
		$allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
	
		// Check if the banner extension is valid
		if (!in_array(strtolower($bannerExtension), $allowedExtensions)) {
			throw new Exception("Invalid banner file extension. Only JPG, JPEG, PNG, and GIF files are allowed.");

		}
	
		$stmt->execute(['id' => $id, 'event_name' => $event_name, 'date' => $date, 'venue' => $venue, 'time' => $time, 'ticket_price' => $ticket_price, 'tickets_available' => $tickets_available, 'banner' => $banner]);
		return true;
	}

	/* Ask Baha what's going on here */
    public function fetchEvents()
	{
		$sql = "SELECT * FROM events";
		$stmt = $this->conn->prepare($sql);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		return $result;
	}

    public function editEvent($id, $event_name, $date, $venue, $time, $ticket_price,  $tickets_available, $banner)
	{
		// Convert the date string to a DateTime object
		$inputDate = DateTime::createFromFormat('d/m/Y', $date);

		// Get the current date
		$currentDate = new DateTime();
	
		// Compare the input date with the current date
		if ($inputDate < $currentDate) {
			throw new Exception("Invalid date.");
		}

		$sql = "UPDATE events SET id = :id, event_name = :event_name, date = :date, venue = :venue, time = :time, ticket_price = :ticket_price,  tickets_available = :tickets_available, banner = :banner WHERE id = :id";
		$stmt = $this->conn->prepare($sql);


		// Get the file extension of the banner
		$bannerExtension = pathinfo($banner, PATHINFO_EXTENSION);

		// Define the allowed banner extensions
		$allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
	
		// Check if the banner extension is valid
		if (!in_array(strtolower($bannerExtension), $allowedExtensions)) {
			throw new Exception("Invalid banner file extension. Only JPG, JPEG, PNG, and GIF files are allowed.");

		}
	
		$stmt->execute(['id' => $id, 'event_name' => $event_name, 'date' => $date, 'venue' => $venue, 'time' => $time, 'ticket_price' => $ticket_price, 'tickets_available' => $tickets_available, 'banner' => $banner]); 
		return true;
	}


	public function deleteEvent($id)
	{
    	$sql = "DELETE FROM events WHERE id = :id";
    	$stmt = $this->conn->prepare($sql);
    	$stmt->execute(['id' => $id]);
     	return true;
	}


	/* Ask Baha what's going on here */
    public function getIdSOfEvents()
	{
		$sql = "SELECT * FROM events";
		$stmt = $this->conn->prepare($sql);
		$stmt->execute();
		return $stmt->rowCount(); //37
	}
}