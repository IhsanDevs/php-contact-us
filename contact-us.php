<?php

require __DIR__ . '/vendor/autoload.php';

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

$dotenv = Dotenv\Dotenv::createUnsafeImmutable(__DIR__);
$dotenv->safeLoad();

$mailMailer = getenv('MAIL_MAILER');
$mailHost = getenv('MAIL_HOST');
$mailPort = getenv('MAIL_PORT');
$mailUsername = getenv('MAIL_USERNAME');
$mailPassword = getenv('MAIL_PASSWORD');
$mailEncryption = getenv('MAIL_ENCRYPTION');
$mailFromAddress = getenv('MAIL_FROM_ADDRESS');
$mailFromName = getenv('MAIL_FROM_NAME');
$mailReceipent = getenv('MAIL_RECEIVER');

/** @package  */
class sendEmail
{
    private $mailMailer;
    private $mailHost;
    private $mailPort;
    private $mailUsername;
    private $mailPassword;
    private $mailEncryption;
    private $mailFromAddress;
    private $mailFromName;
    private $mailReceipent;
    private $isSuccess = false;
    /**
     * @param string $mailMailer
     * @param string $mailHost
     * @param string $mailPort
     * @param string $mailUsername
     * @param string $mailPassword
     * @param string $mailEncryption
     * @param string $mailFromAddress
     * @param string $mailFromName
     * @return void
     */
    public function __construct(
        string $mailMailer,
        string $mailHost,
        string $mailPort,
        string $mailUsername,
        string $mailPassword,
        string $mailEncryption,
        string $mailFromAddress,
        string $mailFromName,
        string $mailReceipent
    ) {
        $this->set([
            'mailMailer' => $mailMailer,
            'mailHost' => $mailHost,
            'mailPort' => $mailPort,
            'mailUsername' => $mailUsername,
            'mailPassword' => $mailPassword,
            'mailEncryption' => $mailEncryption,
            'mailFromAddress' => $mailFromAddress,
            'mailFromName' => $mailFromName,
            'mailReceipent' => $mailReceipent,
        ]);
    }

    /**
     * @param array $arguments
     * @return bool
     */
    protected function set(array $arguments): void
    {
        foreach ($arguments as $argument => $value) {
            $this->$argument = $value;
        }
    }

    /**
     * @param string $target
     * @param string $message
     * @return bool
     */
    public function send($fromEmail, $fromName, $withMessage): bool
    {
        $mail = new PHPMailer(true);
        try {
            $mail->Mailer = $this->mailMailer;
            $mail->Host = $this->mailHost;
            $mail->Port = $this->mailPort;
            $mail->Username = $this->mailUsername;
            $mail->Password = $this->mailPassword;
            $mail->isHTML(false);
            $mail->SMTPAuth = true;
            $mail->SMTPSecure = $this->mailEncryption;
            $mail->Subject = 'New Message From Your Contact Us Page';
            $mail->setFrom($this->mailFromAddress, $this->mailFromName);
            $mail->Body = "From: {$fromEmail}\nName: {$fromName}\nMessage: {$withMessage}";

            $mail->addAddress($this->mailReceipent);
            $mail->send();

            $this->isSuccess = true;
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return true;
    }

    /** @return bool  */
    public function check(): bool
    {
        if ($this->isSuccess) {
            return true;
        } else {
            return false;
        }
    }
}

function init(): void
{
    global $mailMailer,
        $mailHost,
        $mailPort,
        $mailUsername,
        $mailPassword,
        $mailEncryption,
        $mailFromAddress,
        $mailFromName,
        $mailReceipent;
    /**
     * start sending email
     * when ajax request with
     * parameter email and message
     * is exist.
     */
    if (
        !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'
    ) {
        if (
            isset($_POST['email']) &&
            !empty($_POST['email']) &&
            filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) &&
            isset($_POST['message']) &&
            !empty($_POST['message']) &&
            isset($_POST['name']) &&
            !empty($_POST['name'])
        ) {
            $fromEmail = $_POST['email'];
            $fromName = $_POST['name'];
            $withMessage = $_POST['message'];

            $sendEmail = new sendEmail(
                $mailMailer,
                $mailHost,
                $mailPort,
                $mailUsername,
                $mailPassword,
                $mailEncryption,
                $mailFromAddress,
                $mailFromName,
                $mailReceipent
            );

            $sendEmail->send($fromEmail, $fromName, $withMessage);

            header('Content-type:application/json');
            // check if success when sending email
            if ($sendEmail->check()) {
                echo json_encode([
                    'status' => true,
                    'type' => 'success',
                    'message' =>
                        'Your email message has been sent successfully. We will reply to your message as soon as possible.',
                ]);
            } else {
                echo json_encode([
                    'status' => false,
                    'type' => 'danger',
                    'message' =>
                        'Your message failed to send. Please double-check the format of the message you sent. If this problem persists, please contact the developer at <a href="https://t.me/IhsanDevs" target="_blank">@IhsanDevs</a> for a fix.',
                ]);
            }

            exit();
        }
    }
}

// start
init();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Title</title>
    <meta property="og:type" content="website">
    <meta name="description" content="Description your site">
    <link rel="stylesheet" href="/assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="/assets/css/Contact-Details.css">
    <link rel="stylesheet" href="/assets/css/styles.css">
</head>

<body>
    <div class="container">
        <section class="position-relative py-4 py-xl-5">
            <div class="container position-relative">
                <div class="row mb-5">
                    <div class="col-md-8 col-xl-6 text-center mx-auto">
                        <h2>Contact us</h2>
                        <p class="w-lg-50">Curae hendrerit donec commodo hendrerit egestas tempus, turpis facilisis
                            nostra nunc. Vestibulum dui eget ultrices.</p>
                    </div>
                </div>
                <div class="row d-flex justify-content-center">


                    <!-- start info about phone, email and location -->
                    <div class="col-md-6 col-lg-4 col-xl-4">
                        <div class="d-flex flex-column justify-content-center align-items-start h-100">
                            <div class="d-flex align-items-center p-3">
                                <div
                                    class="bs-icon-md bs-icon-rounded bs-icon-primary d-flex flex-shrink-0 justify-content-center align-items-center d-inline-block bs-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor"
                                        viewBox="0 0 16 16" class="bi bi-telephone">
                                        <path
                                            d="M3.654 1.328a.678.678 0 0 0-1.015-.063L1.605 2.3c-.483.484-.661 1.169-.45 1.77a17.568 17.568 0 0 0 4.168 6.608 17.569 17.569 0 0 0 6.608 4.168c.601.211 1.286.033 1.77-.45l1.034-1.034a.678.678 0 0 0-.063-1.015l-2.307-1.794a.678.678 0 0 0-.58-.122l-2.19.547a1.745 1.745 0 0 1-1.657-.459L5.482 8.062a1.745 1.745 0 0 1-.46-1.657l.548-2.19a.678.678 0 0 0-.122-.58L3.654 1.328zM1.884.511a1.745 1.745 0 0 1 2.612.163L6.29 2.98c.329.423.445.974.315 1.494l-.547 2.19a.678.678 0 0 0 .178.643l2.457 2.457a.678.678 0 0 0 .644.178l2.189-.547a1.745 1.745 0 0 1 1.494.315l2.306 1.794c.829.645.905 1.87.163 2.611l-1.034 1.034c-.74.74-1.846 1.065-2.877.702a18.634 18.634 0 0 1-7.01-4.42 18.634 18.634 0 0 1-4.42-7.009c-.362-1.03-.037-2.137.703-2.877L1.885.511z">
                                        </path>
                                    </svg>
                                </div>
                                <div class="px-2">
                                    <h6 class="mb-0">Phone</h6>
                                    <p class="mb-0">+123456789</p>
                                </div>
                            </div>
                            <div class="d-flex align-items-center p-3">
                                <div
                                    class="bs-icon-md bs-icon-rounded bs-icon-primary d-flex flex-shrink-0 justify-content-center align-items-center d-inline-block bs-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor"
                                        viewBox="0 0 16 16" class="bi bi-envelope">
                                        <path fill-rule="evenodd"
                                            d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V4Zm2-1a1 1 0 0 0-1 1v.217l7 4.2 7-4.2V4a1 1 0 0 0-1-1H2Zm13 2.383-4.708 2.825L15 11.105V5.383Zm-.034 6.876-5.64-3.471L8 9.583l-1.326-.795-5.64 3.47A1 1 0 0 0 2 13h12a1 1 0 0 0 .966-.741ZM1 11.105l4.708-2.897L1 5.383v5.722Z">
                                        </path>
                                    </svg>
                                </div>
                                <div class="px-2">
                                    <h6 class="mb-0">Email</h6>
                                    <p class="mb-0">contact@ihsandevs.com</p>
                                </div>
                            </div>
                            <div class="d-flex align-items-center p-3">
                                <div
                                    class="bs-icon-md bs-icon-rounded bs-icon-primary d-flex flex-shrink-0 justify-content-center align-items-center d-inline-block bs-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor"
                                        viewBox="0 0 16 16" class="bi bi-pin">
                                        <path
                                            d="M4.146.146A.5.5 0 0 1 4.5 0h7a.5.5 0 0 1 .5.5c0 .68-.342 1.174-.646 1.479-.126.125-.25.224-.354.298v4.431l.078.048c.203.127.476.314.751.555C12.36 7.775 13 8.527 13 9.5a.5.5 0 0 1-.5.5h-4v4.5c0 .276-.224 1.5-.5 1.5s-.5-1.224-.5-1.5V10h-4a.5.5 0 0 1-.5-.5c0-.973.64-1.725 1.17-2.189A5.921 5.921 0 0 1 5 6.708V2.277a2.77 2.77 0 0 1-.354-.298C4.342 1.674 4 1.179 4 .5a.5.5 0 0 1 .146-.354zm1.58 1.408-.002-.001.002.001zm-.002-.001.002.001A.5.5 0 0 1 6 2v5a.5.5 0 0 1-.276.447h-.002l-.012.007-.054.03a4.922 4.922 0 0 0-.827.58c-.318.278-.585.596-.725.936h7.792c-.14-.34-.407-.658-.725-.936a4.915 4.915 0 0 0-.881-.61l-.012-.006h-.002A.5.5 0 0 1 10 7V2a.5.5 0 0 1 .295-.458 1.775 1.775 0 0 0 .351-.271c.08-.08.155-.17.214-.271H5.14c.06.1.133.191.214.271a1.78 1.78 0 0 0 .37.282z">
                                        </path>
                                    </svg>
                                </div>
                                <div class="px-2">
                                    <h6 class="mb-0">Location</h6>
                                    <p class="mb-0">12 Example Street</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End info -->



                    <!-- Start form contact -->
                    <div class="col-md-6 col-lg-5 col-xl-4">
                        <div>
                            <form class="p-3 p-xl-4" method="post" id='contact_me'>
                                <div data-alerts="alerts"></div>


                                <div class="mb-3"><input class="form-control" autocomplete="off" type="text" id="name" name="name"
                                        placeholder="Name"></div>
                                <div class="mb-3"><input class="form-control" type="email" id="email" autocomplete="off" name="email"
                                        placeholder="Email"></div>
                                <div class="mb-3"><textarea class="form-control" id="message" autocomplete="off" name="message" rows="6"
                                        placeholder="Message"></textarea></div>
                                <div><button class="btn btn-primary d-block w-100" type="submit">Send </button></div>
                            </form>
                        </div>
                    </div>
                    <!-- End form -->



                </div>
            </div>
        </section>
    </div>
    <script src="/assets/js/jquery.min.js"></script>
    <script src="/assets/bootstrap/js/bootstrap.min.js"></script>
    <script src="/assets/bootstrap/js/jquery.bsAlerts.min.js"></script>
    <script>
    var request;
    $('#contact_me').submit(function(e) {
        e.preventDefault();

        // Abort any pending request
        if (request) {
            request.abort();
        }

        var $form = $(this);

        // Let's select and cache all the fields
        var $inputs = $form.find("input, select, button, textarea");

        // Serialize the data in the form
        var serializedData = $form.serialize();

        // Let's disable the inputs for the duration of the Ajax request.
        // Note: we disable elements AFTER the form data has been serialized.
        // Disabled form elements will not be serialized.
        $inputs.prop("disabled", true);

        $.post('/contact-us.php', {
            name: $('#name').val(),
            email: $("#email").val(),
            message: $("#message").val()
        }, function(data) {
            $inputs.prop("disabled", false);
            $inputs.val('');
            $(document).trigger("add-alerts", {
                message: data.message,
                priority: data.type,
            });
        });
    });
    </script>
</body>

</html>
