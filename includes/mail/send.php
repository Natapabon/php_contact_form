<?php
//DEBUG ONLY, remove if befor going live
//ini_set('display_errors', 0);

//TODO: takes care form submission [Work as the Post office]

//4*. It returns propertly info in JSON format [Receipts]
//   a. What is AJAX?
//   b. What is JSON?
//   c. How to build JSON (in PHP)?

header('Acess-Control-Allow-Origin:*');
header('Content-Type: Aplocation/json; chaset=UTF-8');

$results = [];
$visitor_name = '';
$visitor_email = '';
$visitor_message = '';

//1. Check the subission __> Validate the data [check if the package is dangerous? Is there "non-mailable" item? iligal or damage? check it]
// $_POST['firstname']
if (isset($_POST['firstname'])) {
    $visitor_name = filter_var($_POST['firstname'], FILTER_SANITIZE_STRING); // the FILTER_SANITIZE_STRING validates the information
}

if (isset($_POST['lastname'])) {
    $visitor_name .= ' '.filter_var($_POST['lastname'], FILTER_SANITIZE_STRING);
}

if (isset($_POST['email'])) {
    $visitor_email .= ' '.filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);// the FILTER_VALIDATE_EMAIL validates the email. NEVER TRUST IN THE USER WRITTING.
}

if (isset($_POST['message'])) {
    $visitor_message.= ' '.filter_var(htmlspecialchars($_POST['message']), FILTER_SANITIZE_STRING); // to avoid that someone else try to add some script into your code (Hackers adding <scrip><scrip> in the message chart) we use the htmlspecialchars . This makes that any spacial character in the message as <> and change as special character to the browser and the "suspicious code" dont damage the internal code
}

$results['name'] = $visitor_name;
$results['message'] = $visitor_message;

//2. Prepare the mail [Print out the label and put on the package / Prepare the package in certain format]
$email_subject = 'Inquiry from Portfolio Site';
$email_recipient = 'test@natpabon.com'; //the email, or AKA, "To" email that we want to send the user to us 
$email_message = sprintf('Name: %s, Email: %s, Mesage: %s', $visitor_name, $visitor_email, $visitor_message);


// Make sure you run the code in PHP 7.4 or +
// Otherwise, you would need to make $email_headers as string https://www.php.net/manual/en/function.mail.php
$email_headers = array(
    //Best practice, but it may need you to have a mail set up in noreplay@yordomain.ca
    //'From'=>'noreplay@yourdomain.ca', //why this and not @gmail.com? becuase it has an other dna that is not from your domain, so it is not matching and it could have some problems in the future.
    //'Replay-To'=>$visitor_email,

    //You can still use it, if the above notraplay is too much work
    'From'=>$visitor_email
);

//3. Send out the email [Send out the package]
$email_result = mail($email_recipient, $email_subject, $email_message, $email_headers);
if ($email_result) {
    $results['message'] = sprintf('Thank you for contacting us, %s. You will get a reply within 24 hours.', $visitor_name);
}else{
    $results['message'] = sprintf('We are sorry but the email did not go through.');
}

echo json_encode($results);