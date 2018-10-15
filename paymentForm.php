<!DOCTYPE html>
<html>
    <body>
        <form method="POST" action="https://hosted.flutterwave.com/processPayment.php" id="paymentForm">
            <input type="hidden" name="amount" value="200" /> <!-- Replace the value with your transaction amount -->
            <input type="hidden" name="payment_method" value="both" /> <!-- Can be card, account, both (optional) -->
            <input type="hidden" name="description" value="I Phone X, 100GB, 32GB RAM" /> <!-- Replace the value with your transaction description -->
            <input type="hidden" name="logo" value="http://brandmark.io/logo-rank/random/apple.png" /> <!-- Replace the value with your logo url (optional) -->
            <input type="hidden" name="title" value="Victor Store" /> <!-- Replace the value with your transaction title (optional) -->
            <input type="hidden" name="country" value="NG" /> <!-- Replace the value with your transaction country -->
            <input type="hidden" name="currency" value="NGN" /> <!-- Replace the value with your transaction currency -->
            <input type="hidden" name="email" value="fionotollan@yahoo.com" /> <!-- Replace the value with your customer email -->
            <input type="hidden" name="firstname" value="Olufemi" /> <!-- Replace the value with your customer firstname (optional) -->
            <input type="hidden" name="lastname"value="Olanipekun" /> <!-- Replace the value with your customer lastname (optional) -->
            <input type="hidden" name="phonenumber" value="08098787676" /> <!-- Replace the value with your customer phonenumber (optional if email is passes) -->
            <input type="hidden" name="pay_button_text" value="Complete Payment" /> <!-- Replace the value with the payment button text you prefer (optional) -->
            <input type="hidden" name="ref" value="MY_NAME_5a22a7f270abc8879" /> <!-- Replace the value with your transaction reference. It must be unique per transaction. You can delete this line if you want one to be generated for you. -->
            <input type="hidden" name="env" value="staging"> <!-- live or staging -->
            <input type="hidden" name="publicKey" value="FLWPUBK-#####YOUR_PUBLIC_KEY#####-X"> <!-- Put your public key here -->
            <input type="hidden" name="secretKey" value="FLWSECK-#####YOUR_SECRET_KEY#####-X"> <!-- Put your secret key here -->
            <input type="hidden" name="successurl" value="http://request.lendlot.com/13b9gxc1?name=success"> <!-- Put your success url here -->
            <input type="hidden" name="failureurl" value="http://request.lendlot.com/13b9gxc1?name=failed"> <!-- Put your failure url here -->
            <input type="submit" value="Submit" />
        </form>
    </body>
</html>