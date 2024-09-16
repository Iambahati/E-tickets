<?php

// https://github.com/PHPMailer/PHPMailer
// Import PHPMailer classes into the global namespace
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;



use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

// Load Composer's autoloader
require 'vendor/autoload.php';

/**
 * Class MailSender
 *
 * The Mailer class is used by LawEnforceTech, a crime management system, to send emails.
 *
 * It uses a PHPMailer object and a Database object, with private properties for mailer properties
 * and a public property for accessing the database object.
 *
 * The __construct() method sets up the PHPMailer and Database objects,
 * and sets mailer properties based on values hardcoded.
 */
class Mailer
{
    // Constants
    const APP_NAME = 'ETicketingAfrica';
    public static function logger(string $msg): void
    {
        $log = sprintf('[%s] [%s:%s] [%s] %s', date('D M d H:i:s', $time = microtime(true)) . sprintf('.%06d', ($time - floor($time)) * 1000000) . ' ' . date('Y', $time), 'php', 'warn', 'pid ' . getmypid(), $msg);
        error_log($log);
        file_put_contents('email_logs.txt', $log . PHP_EOL, FILE_APPEND);
    }

    public static function sendMail(string $to, string $subject, string $body): bool
    {
        try {
            // Initialize phpmailer
            $mail = new PHPMailer(true);

            // Configure SMTP
            $mail->isSMTP();
            $mail->SMTPAuth = true;
            $mail->SMTPSecure = "tls";
            $mail->Host = "smtp.gmail.com";
            $mail->Port = 587;
            $mail->Username = "noreply.ticketingapp@gmail.com";
            $mail->Password = "jrdvhynqvlmnpmlj"; //  Signing in to Google > 2Step Verification >  Security > App Passwords.

            // Set mailer properties
            $mail->setFrom("noreply.ticketingapp@gmail.com", self::APP_NAME);  //sender
            $mail->addAddress($to); //recepient
            $mail->Subject = $subject;
            $mail->Body = $body;
            $mail->IsHTML(true);

            // Sending the email
            if (!$mail->send()) {
                self::logger('Sorry, something went wrong: ' . $mail->ErrorInfo);
                return false;
            } else {
                return true;
            }
        } catch (Exception $e) {
            // Catching errors
            self::logger('Email could not be sent. Mailer error: ' . $e->getMessage());
            throw new Exception('Email could not be sent. Mailer error: ' . $e->getMessage());
        }
    }

    // Generate a QR code for each ticket
    protected static function generateQrCode($token)
    {
        $options = new QROptions([
            'version'    => 5,
            'outputType' => QRCode::OUTPUT_MARKUP_SVG,
            'eccLevel'   => QRCode::ECC_L,
            'scale'      => 8, // Adjust scale to make the QR code larger
        ]);

        // Generate the QR code
        $qrCode = new QRCode($options);
        return $qrCode->render($token);
    }


    public static function sendTicketsByEmail($email, $tickets, $eventName, $validityDates, $venue, $time)
    {
        $subject = "Your Tickets";
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = "tls";
        $mail->Host = "smtp.gmail.com";
        $mail->Port = 587;
        $mail->Username = "adm1n.tickectok@gmail.com";
        $mail->Password = "vedeijsfocdrejmn";
        $mail->setFrom("adm1n.tickectok@gmail.com", self::APP_NAME);
        $mail->addAddress($email);
        $mail->Subject = $subject;
        $mail->Body = "Hello, <br><br>I hope this email finds you well.<br><br>You are Confirmed for {$eventName} at {$venue} {$validityDates}.<br>Please check your ticket attachments for QR codes.";
        $mail->IsHTML(true);
        

        foreach ($tickets as $token) {
            // Generate a QR code for each ticket
            $qrcodeUrl = self::generateQrCode($token);
            $htmlContent = "
                <h2>Event {$eventName}</h2>
                <p>Ticket Validity: {$validityDates}</p>
                <p>Ticket Token: {$token}</p>
                <img src='{$qrcodeUrl}'>
            ";

            // create a new mPDF instance and render HTML to PDF
            $mpdf = new \Mpdf\Mpdf();
            $mpdf->WriteHTML($htmlContent);
            $pdfOutput = $mpdf->Output('', 'S'); // gets PDF as a string

            // write the PDF to a temporary file
            $pdfTempFilePath = sys_get_temp_dir() . '/' . $token . '.pdf';
            file_put_contents($pdfTempFilePath, $pdfOutput);

            // attach the PDF to the email
            $mail->addAttachment($pdfTempFilePath);
        }

        // send the email and handle errors
        if (!$mail->send()) {
            self::logger('Sorry, something went wrong: ' . $mail->ErrorInfo);
            return false;
        } else {
            // delete temporary PDF files
            foreach ($tickets as $token) {
                $pdfTempFilePath = sys_get_temp_dir() . '/' . $token . '.pdf';
                if (file_exists($pdfTempFilePath)) {
                    unlink($pdfTempFilePath);
                }
            }
            return true;
        }
    }
}
