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


    
    public static function sendPaymentReceiptByEmail($email, $eventName, $venue, $description, $ticketPrice, $orderHash, $totalAmount, $totalTickets)
    {
        $subject = "Receipt for Order {$orderHash}";
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = "tls";
        $mail->Host = "smtp.gmail.com";
        $mail->Port = 587;
        $mail->Username = "noreply.ticketingapp@gmail.com";
        $mail->Password = "jrdvhynqvlmnpmlj";
        $mail->setFrom("noreply.ticketingapp@gmail.com", self::APP_NAME);
        $mail->addAddress($email);
        $mail->Subject = $subject;
    
        $mail->Body = "
        <html>
        <head>
            <style>
                .email-body {
                    font-family: Arial, sans-serif;
                    line-height: 1.6;
                }
                .email-header {
                    background-color: #4CAF50;
                    color: white;
                    padding: 10px;
                    text-align: center;
                }
                .email-content {
                    padding: 20px;
                }
                .email-footer {
                    background-color: #f1f1f1;
                    padding: 10px;
                    text-align: center;
                }
                .order-details {
                    margin-top: 20px;
                    border: 1px solid #ddd;
                    padding: 10px;
                }
                .order-details th, .order-details td {
                    padding: 8px;
                    text-align: left;
                }
                .order-details th {
                    background-color: #f2f2f2;
                }
            </style>
        </head>
        <body>
            <div class='email-body'>
                <div class='email-header'>
                    <h2>Receipt for Order {$orderHash}</h2>
                </div>
                <div class='email-content'>
                    <p>Hello,</p>
                    <p>Thank you for using E-Tickets. Please find a payment receipt for order {$orderHash} attached.</p>
                    <div class='order-details'>
                        <h3>Order Details</h3>
                        <table>
                            <tr>
                                <th>Order Hash</th>
                                <td>{$orderHash}</td>
                            </tr>
                            <tr>
                                <th>Total Amount</th>
                                <td>\${$totalAmount}</td>
                            </tr>
                            <tr>
                                <th>Total Tickets</th>
                                <td>{$totalTickets}</td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class='email-footer'>
                    <p>Thank you for choosing our service!</p>
                </div>
            </div>
        </body>
        </html>
        ";
        $mail->IsHTML(true);
    
        // Generate the payment receipt PDF
        $htmlContent = "
        <html>
        <head>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    line-height: 1.6;
                }
                .receipt-header {
                    background-color: #4CAF50;
                    color: white;
                    padding: 10px;
                    text-align: center;
                }
                .receipt-content {
                    padding: 20px;
                }
                .receipt-footer {
                    background-color: #f1f1f1;
                    padding: 10px;
                    text-align: center;
                }
                .order-details {
                    margin-top: 20px;
                    border: 1px solid #ddd;
                    padding: 10px;
                }
                .order-details th, .order-details td {
                    padding: 8px;
                    text-align: left;
                }
                .order-details th {
                    background-color: #f2f2f2;
                }
            </style>
        </head>
        <body>
            <div class='receipt-header'>
                <h2>Payment Receipt for Order {$orderHash}</h2>
            </div>
            <div class='receipt-content'>
                <p>ITEM DESCRIPTION</p>
                <p>{$eventName}: {$description}</p>
                <p>Venue: {$venue}</p>
                <table class='order-details'>
                    <tr>
                        <th>UNIT COST</th>
                        <th>QTY</th>
                        <th>TOTAL</th>
                    </tr>
                    <tr>
                        <td>KES {$ticketPrice}</td>
                        <td>{$totalTickets}</td>
                        <td>KES {$totalAmount}</td>
                    </tr>
                    <tr>
                        <th colspan='2'>TOTAL</th>
                        <td>KES {$totalAmount}</td>
                    </tr>
                    <tr>
                        <th colspan='2'>PAID</th>
                        <td>KES {$totalAmount}</td>
                    </tr>
                    <tr>
                        <th colspan='2'>BALANCE</th>
                        <td>0.00 KES</td>
                    </tr>
                </table>
            </div>
            <div class='receipt-footer'>
                <p>Thank you for choosing our service!</p>
            </div>
        </body>
        </html>
        ";
    
        // create a new mPDF instance and render HTML to PDF
        $mpdf = new \Mpdf\Mpdf();
        $mpdf->WriteHTML($htmlContent);
        $pdfOutput = $mpdf->Output('', 'S'); // gets PDF as a string
    
        // write the PDF to a temporary file
        $pdfTempFilePath = sys_get_temp_dir() . '/' . $orderHash . '.pdf';
        file_put_contents($pdfTempFilePath, $pdfOutput);
    
        // attach the PDF to the email
        $mail->addAttachment($pdfTempFilePath);
    
        // send the email and handle errors
        if (!$mail->send()) {
            self::logger('Sorry, something went wrong: ' . $mail->ErrorInfo);
            return false;
        } else {
            // delete temporary PDF files
            if (file_exists($pdfTempFilePath)) {
                unlink($pdfTempFilePath);
            }
            return true;
        }
    }
}
