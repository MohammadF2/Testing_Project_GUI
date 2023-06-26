<!DOCTYPE html>
<html>

<head>
    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <title>Currency Converter</title>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <link rel='stylesheet' type='text/css' href='Style/style.css'>
</head>


<?php


$rates = "0";
$res = "0";

$final_amount = "";


$inputFile = fopen("currencyInput.txt", "r");

$txt = "";

try {
    $txtFrom = '';
    $txtTo = '';

    while (!feof($inputFile)) {

        $fromCurrency = isset($_POST['from']) ? $_POST['from'] : 'USD';
        $toCurrency = isset($_POST['to']) ? $_POST['to'] : 'ILS';

        $line = fgets($inputFile);

        $currency = substr($line, 0, 3);

        $selectedFrom = ($currency == $fromCurrency) ? 'selected' : '';
        $selectedTo = ($currency == $toCurrency) ? 'selected' : '';
        $txtFrom = $txtFrom . '<option value="' . $currency . '" ' . $selectedFrom . '>' . $line . '</option>';
        $txtTo = $txtTo . '<option value="' . $currency . '" ' . $selectedTo . '>' . $line . '</option>';
    }
} catch (Exception $e) {
    // Handle JSON parse error...
}




if (isset($_POST['amo']) && !empty($_POST['amo']) && isset($_POST["submit"])) {

    $fromCurrency = $_POST['from'];
    $toCurrency = $_POST['to'];
    $amount = $_POST['amo'];

    if ($amount === 0) {
        $res = "Why do you want to convert 0 !!";
        exit();
    } else if ($amount < 0) {
        $res = "Amount of currancy can't be negative !!";
    } else {



        $final_amount = $amount;
        $req_url = 'https://v6.exchangerate-api.com/v6/2e2c6f7fc6314770c6bc3b3b/pair/' . $fromCurrency . '/' . $toCurrency;



        $response_json = file_get_contents($req_url);

        if (false !== $response_json) {
            try {
                $response = json_decode($response_json);
                if ('success' === $response->result) {
                    $rates = $response->conversion_rate;

                    $converted_amount = $amount * $rates;


                    $res = sprintf("%.2f", $converted_amount);
                }
            } catch (Exception $e) {
                // Handle JSON parse error...
            }
        }
    }
} else if (isset($_POST['amo']) && empty($_POST['amo']))
    $res = "You must fill Amount of currancy !";
?>


<body>

    <hr>
    <form action="index.php" method="post">
        <label for="Amount" name="Amount">Amount</label><br>
        <input type="number" name="amo" id="amoTXT" value="<?php echo $final_amount ?>"> <br>
        <br>
        <label for="from" name="fromLabel">From</label><br>
        <select name="from">
            <?php echo $txtFrom ?>
        </select><br>
        <label name="toLabel" for="to">To</label><br>
        <select name="to">
            <?php echo $txtTo ?>
        </select><br>
        <input type="submit" value="Convert" name="submit"> 
        <hr>
        <label for="Result" name="Result">Result</label>
        <input type="text" name="result " style="<?php if (!is_numeric($res)) echo 'color: red;' ?>" value="<?php echo $res ?>" disabled>
        <label for="Rate" name="Rate">Rate used</label>
        <input type="text" name="tatetxt" value="<?php echo $rates ?>" disabled>
    </form>
</body>

</html>