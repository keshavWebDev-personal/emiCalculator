<?php
	function PMT($interest,$period,$loan_amount){
		if (!empty($interest) === true and !empty($period) === true and !empty($loan_amount)  === true) {
			$interest = (float)$interest;
			$period = (float)$period;
			$loan_amount = (float)$loan_amount;
			$period = $period * 12;
			$interest = $interest / 1200;
			$amount = $interest * -$loan_amount * pow((1+$interest),$period) / (1 - pow((1+$interest), $period));
			return $amount;
		}
	}
	function showValue($value){
		echo number_format($value,2);
	}
	function showDate($date){
		echo date('jS F, Y',strtotime($date));
	}

	//If the Database doe'nt exists, make one
	$conn = mysqli_connect("localhost", "root", "");
	$sql1 = "CREATE DATABASE banking_calculator_history_data";

	if (mysqli_select_db( $conn, 'banking_calculator_history_data') === FALSE) {
		mysqli_query($conn, $sql1);
	}

	//Error Codes if the form input weren't set by the user
	$stringToAssign = "<p style='color: red; position: absolute; transform: translate(0, -4px); font-size: 0.8rem;'>This feild is Required</p>";
	if ($_SERVER['REQUEST_METHOD'] == "POST") {
		if (empty($_POST['amount'])) {
			$amountErrorMsg = $stringToAssign;
		}
		if (empty($_POST['period'])) {
			$periodErrorMsg = $stringToAssign;
		}
		if (empty($_POST['interest'])) {
			$interestErrorMsg = $stringToAssign;
		}
		if (empty($_POST['start_date'])) {
			$start_dateErrorMsg = $stringToAssign;
		}
	}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<title>Document</title>
	<link rel="stylesheet" href="style.css">
</head>
<body onload="onNavBtnClick('home-btn')">
	<div class="container">
		<div id="pDemo">Learn Mode</div>
		<div id="learnModeText">
			<span class="span-btn">This Webapp has turned into learning Mode</span>
		</div>
		<video autoplay muted loop id="myVideo">
			<source src="Assets/Calender bg.mp4" type="video/mp4">
			Your browser does not support HTML5 video.
		</video>
		<div class="stage">
			<div class="info">
				<div class="top-box">
					<div class="text">
						<p>
							<h1>
							Banking <br />
							Calculator
							</h1>
						</p>
					</div>
					<div class="logo"></div>
				</div>
				<div class="bottom-box">
					<ul>
						<li id="home-btn" onclick="onNavBtnClick('home-btn');homeFormChanger()" >
							HOME
						</li>
						<li id="emi-btn" onclick="onNavBtnClick('emi-btn');emiFormChanger()">
							EMI CALCULATOR
						</li>
						<li id="calender-btn" onclick="onNavBtnClick('calender-btn');calenderFormChanger()">
							CALENDER
						</li>
						<li id="news-btn" onclick="onNavBtnClick('news-btn');newsFormChanger()">
							NEWS
						</li>
						<li id="history-btn" onclick="onNavBtnClick('history-btn');historyFormChanger()">
							HISTORY
						</li>
					</ul>
				</div>
			</div>
			<div id="pages">
				<div id="home-page">
					<p class="hero">
						The Better Way <br> To Bank
					</p>
					<div class="Try-now" onclick="emiFormChanger();onNavBtnClick('emi-btn')">Try?</div>
					<img src="assets/iphone graphic.png" class="img" alt="">
				</div>
				<div id="emi-calculator-page">
					<?php
						if(isset($_POST['amount'])){
							$_POST['amount'] = str_replace(',','',$_POST['amount']);
							$emi = PMT($_POST['interest'],$_POST['period'],$_POST['amount']);
							$balance = $_POST['amount'];
							$payment_date = $_POST['start_date'];
						}
						?>				
					<form id="input-field-form" method="POST" action="">
						<p>
							LOAN DETAILS
						</p>
						<div class="input-field-container loan-amount-input-field-form">
							Loan Amount
							<br>
							<?php
								if (!empty($amountErrorMsg)) {
									echo $amountErrorMsg; 
								}
							?>
							<input class="input-field input-field-text" type="text" name="amount" id="" placeholder="Enter the Loan Amount">
						</div>
						<div class="input-field-container period-input-field-form">
							Period (Years)
							<br>
							<?php
								if (!empty($periodErrorMsg)) {
									echo $periodErrorMsg; 
								}
							?>
							<input class="input-field input-field-text" type="text" name="period" id="" placeholder="Enter the Period in Years">
						</div>
						<div class="input-field-container
						loan-amount-input-field-form">
							Interest
							<br>
							<?php
								if (!empty($interestErrorMsg)) {
									echo $interestErrorMsg; 
								}
							?>
							<input class="input-field input-field-text" type="text" name="interest" id="" placeholder="Enter the Interest in %">
						</div>
						<div class="input-field-container
						Start-Date-input-field-form">
							Start Date
							<?php
								if (!empty($start_dateErrorMsg)) {
									echo $start_dateErrorMsg; 
								}
							?>
							<br>
							<input class="input-field input-field-date" type="date" name="start_date" id="" placeholder="Enter the date">
						</div>
						<!-- Calculate Button -->
						<button type="submit" id="calculate-btn">Calculate</button>
					</form>
					<?php
						if ($_SERVER['REQUEST_METHOD'] == "POST") {
							//variables to store user data
							$date = date('d.m.y');
							$time = date('H:i:sa');
							$amountNew = $_POST['amount'];
							$periodNew = $_POST['period'];
							$interestNew = $_POST['interest'];
							$startDateNew = $_POST['start_date'];
							
							//Creating a new Table
							$sql2 = "CREATE TABLE `banking_calculator_history_data`.`table_$date-$time` ( `Loan Amount` INT NOT NULL , `Period` INT NOT NULL , `Interest` INT NOT NULL , `Start Date` DATE NOT NULL ) ENGINE = InnoDB;";
							mysqli_query($conn, $sql2);
							
							//Inserting Data in it
							$sql3 = "INSERT INTO `table_$date-$time` (`Loan Amount`, `Period`, `Interest`, `Start Date`) VALUES ('$amountNew', '$periodNew', '$interestNew', '$startDateNew');";
							sleep(1);
							mysqli_query($conn, $sql3);
						}
						?>
					<table id="emi-page-table">
						<tbody id="emi-page-tableBody">
							<tr class="emi-page-table-row">
								<th class="emi-page-table-data emi-page-table-head-data emi-page-table-data-header">SN</th>
								<th class="emi-page-table-data emi-page-table-head-data emi-page-table-data-payment">Payment Date</th>
								<th class="emi-page-table-data emi-page-table-head-data">Monthly EMI</th>
								<th class="emi-page-table-data emi-page-table-head-data">Interest Paid</th>
								<th class="emi-page-table-data emi-page-table-data-principal emi-page-table-head-data">Principal Paid</th>
								<th class="emi-page-table-data emi-page-table-head-data emi-page-table-data-balance">Balance</th>
							</tr>
							<?php 
								if ($_SERVER['REQUEST_METHOD'] == 'POST' and !empty($_POST['period']) === true){
									for($i = 0; $i < $_POST['period'] * 12; $i++){
										?>
							<?php 
								$interest = (($_POST['interest']/100)*$balance)/12;
								$principal = $emi - $interest;
								$balance = $balance - $principal;
								$payment_date = date('Y-m-d',strtotime("+1 month",strtotime($payment_date)));
								?>
							<tr class="emi-page-table-row">
								<td class="emi-page-table-data emi-page-table-data-header"><?php echo $i;?></td>
								<td class="emi-page-table-data emi-page-table-data-payment"><?php showDate($payment_date);?></td>
								<td class="emi-page-table-data"><?php showValue($emi);?></td>
								<td class="emi-page-table-data"><?php showValue($interest);?></td>
								<td class="emi-page-table-data emi-page-table-data-principal"><?php showValue($principal);?></td>
								<td class="emi-page-table-data emi-page-table-data-balance"><?php showValue($balance);?></td>
							</tr>
							<?php }} ?>
						</tbody>
					</table>
				</div>
				<div id="calender-page"> 					
					<table id="calender-page-table">
						<thead class="calender-page-table-thead">
							<div id="current-date" class="calender-page-table-head">
								<script>
									function getDayMonthDateName() {
										let a = new Date();
										let weekdays = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"]
										let months = [
											"January",
											"Fabruary",
											"March",
											"April",
											"May",
											"June",
											"July",
											"August",
											"September",
											"October",
											"November",
											"December"
										]
										let r = weekdays[a.getDay()] + ", " + months[a.getMonth()] + " " + a.getDate();
										document.getElementById("current-date").innerHTML = r;
									}
									getDayMonthDateName()
								</script>
							</div>
							<tr class="calender-page-table-row">
								<th class="calender-page-table-data">Sun</th>
								<th class="calender-page-table-data">Mon</th>
								<th class="calender-page-table-data">Tue</th>
								<th class="calender-page-table-data">Wed</th>
								<th class="calender-page-table-data">Thr</th>
								<th class="calender-page-table-data">Fri</th>
								<th class="calender-page-table-data">Sat</th>
							</tr>
						</thead>
						<tbody class="calender-page-table-body">
							<tr class="calender-page-table-row">
								<td class="calender-page-table-data" id="day-1">1</td>
								<td class="calender-page-table-data" id="day-2">2</td>
								<td class="calender-page-table-data" id="day-3">3</td>
								<td class="calender-page-table-data" id="day-4">4</td>
								<td class="calender-page-table-data" id="day-5">5</td>
								<td class="calender-page-table-data" id="day-6">6</td>
								<td class="calender-page-table-data" id="day-7">7</td>
							</tr>
							<tr class="calender-page-table-row">
								<td class="calender-page-table-data" id="day-8">8</td>
								<td class="calender-page-table-data" id="day-9">9</td>
								<td class="calender-page-table-data" id="day-10">10</td>
								<td class="calender-page-table-data" id="day-11">11</td>
								<td class="calender-page-table-data" id="day-12">12</td>
								<td class="calender-page-table-data" id="day-13">13</td>
								<td class="calender-page-table-data" id="day-14">14</td>
							</tr>
							<tr class="calender-page-table-row">
								<td class="calender-page-table-data" id="day-15">15</td>
								<td class="calender-page-table-data" id="day-16">16</td>
								<td class="calender-page-table-data" id="day-17">17</td>
								<td class="calender-page-table-data" id="day-18">18</td>
								<td class="calender-page-table-data" id="day-19">19</td>
								<td class="calender-page-table-data" id="day-20">20</td>
								<td class="calender-page-table-data" id="day-21">21</td>
							</tr>
							<tr class="calender-page-table-row">
								<td class="calender-page-table-data" id="day-22">22</td>
								<td class="calender-page-table-data" id="day-23">23</td>
								<td class="calender-page-table-data" id="day-24">24</td>
								<td class="calender-page-table-data" id="day-25">25</td>
								<td class="calender-page-table-data" id="day-26">26</td>
								<td class="calender-page-table-data" id="day-27">27</td>
								<td class="calender-page-table-data" id="day-28">28</td>
							</tr>
							<tr class="calender-page-table-row">
								<td class="calender-page-table-data" id="day-29">29</td>
								<td class="calender-page-table-data" id="day-30">30</td>
								<td class="calender-page-table-data" id="day-31">31</td>
							</tr>
							<script>
								let a = new Date();
								let date = a.getDate()
								document.getElementById("day-" + (date - 1)).classList.remove("highlight");	
								document.getElementById("day-" + date).classList.add("highlight");	
							</script>
						</tbody>
					</table>
				</div>
				<div id="news-page">
					<div class="open-page-btn open-page-btn-new">
						<a href="https://www.financialexpress.com/industry/banking-finance/" target="_blank">
							Open News <br>in New Tab</div>
						</a>
					<div class="open-page-btn open-page-btn-current">						
						<a href="https://www.financialexpress.com/industry/banking-finance/">
							Open News <br>on this Tab</div>
						</a>
				</div>
				<div id="history-page">
					<?php 
						$result = mysqli_query($conn, "SHOW TABLES FROM banking_calculator_history_data");
						if ($row1 = mysqli_fetch_row($result)) {

							$tablesCount = 0;
							$tableName = substr($row1[0], 15, 8);
							$tableName = str_replace(':', '', $tableName);
							$tableNamesArr[$tablesCount] = $tableName;
							$tableNamesArrRaw[$tablesCount] = $row1[0];
							$tablesCount++;

							while ($row1 = mysqli_fetch_row($result)) {
								$tableName = substr($row1[0], 15, 8);
								$tableName = str_replace(':', '', $tableName);
								$tableNamesArr[$tablesCount] = $tableName;
								$tableNamesArrRaw[$tablesCount] = $row1[0];
								$tablesCount++;
							}
							$ArrCount = count($tableNamesArr) - 1;

							for ($i2=0; $i2 < $tablesCount; $i2++) { 
								if ($i2 == 20) {
									break;
								}
								
								//Printing the time since the table creation 
								$timeUntil = intval(date('His')) - intval($tableNamesArr[$ArrCount - $i2]);
								if ($timeUntil <= 60 ) {
									if ($timeUntil == 1) {
										echo '<p class="History-table-timeSince-text">' . $timeUntil . ' Second Ago</p>';
									}
									else {
										echo '<p class="History-table-timeSince-text">' . $timeUntil . ' Seconds Ago</p>';
									}
								}elseif ($timeUntil <= 6000 ) {
									if ($timeUntil > 100 && $timeUntil < 200) {
										echo '<p class="History-table-timeSince-text">' . round($timeUntil/100, 0) . '  minute ago</p>';
									}else {
										echo '<p class="History-table-timeSince-text">' . round($timeUntil/100, 0) . '  minutes ago</p>';
									}
								}elseif ($timeUntil <= 600000) {
									if ($timeUntil > 10000 && $timeUntil < 20000) {
									echo '<p class="History-table-timeSince-text">' . round($timeUntil/10000, 0) . ' Hour Ago</p>';
									}else {
									echo '<p class="History-table-timeSince-text">' . round($timeUntil/10000, 0) . ' Hours Ago</p>';
									}
								}
					?>
						<table class="history-page-table">
							<tbody id="emi-page-tableBody">
								<tr class="emi-page-table-row">
									<th class="emi-page-table-data emi-page-table-head-data emi-page-table-data-header">SN</th>
									<th class="emi-page-table-data emi-page-table-head-data emi-page-table-data-payment">Payment Date</th>
									<th class="emi-page-table-data emi-page-table-head-data">Monthly EMI</th>
									<th class="emi-page-table-data emi-page-table-head-data">Interest Paid</th>
									<th class="emi-page-table-data emi-page-table-data-principal emi-page-table-head-data">Principal Paid</th>
									<th class="emi-page-table-data emi-page-table-head-data emi-page-table-data-balance">Balance</th>
								</tr>
								<?php 
									$conn = mysqli_connect("localhost", "root", "", "banking_calculator_history_data");
								
									//Running The Query to fetch data
									$temoraryArrayHolder = $tableNamesArrRaw[$ArrCount - $i2];
									$q = "SELECT * FROM `$temoraryArrayHolder` LIMIT 1";
									$sqliRecivedDataObject = mysqli_query($conn, $q);

									//Using the fetch data to feed all the Required Variables
									$row = mysqli_fetch_array($sqliRecivedDataObject);
									$loanAmountRecived = $row['Loan Amount'];
									$periodRecived = $row['Period'];
									$interestRecived = $row['Interest'];
									$startDateRecived = $row['Start Date'];

									//resetting the Form variable
									$balance = $loanAmountRecived;
									$interest = (($interestRecived/100)*$balance)/12;
									$emi = PMT($interestRecived,$periodRecived,$loanAmountRecived);
									$payment_date = $startDateRecived;
									
									//Building The Table
									for($i = 0; $i < $periodRecived * 12; $i++){
								?>
								<?php 
									$interest = (($interestRecived/100)*$balance)/12;
									$principal = $emi - $interest;
									$balance = $balance - $principal;
									$payment_date = date('Y-m-d',strtotime("+1 month",strtotime($payment_date)));
									?>
								<tr class="emi-page-table-row">
									<td class="emi-page-table-data emi-page-table-data-header"><?php echo $i;?></td>
									<td class="emi-page-table-data emi-page-table-data-payment"><?php showDate($payment_date);?></td>
									<td class="emi-page-table-data"><?php showValue($emi);?></td>
									<td class="emi-page-table-data"><?php showValue($interest);?></td>
									<td class="emi-page-table-data emi-page-table-data-principal"><?php showValue($principal);?></td>
									<td class="emi-page-table-data emi-page-table-data-balance"><?php showValue($balance);?></td>
								</tr>
								<?php }?>
							</tbody>
						</table>
					<?php }
						} 
						else {
							echo "<p class='history-page-text-whenEmpty'>Your history is empty,<br>try playing around the UI  <i class='far fa-smile'></i></p>";
						}
					?>
				</div>
			</div>
		</div>
	</div>
	<!-- JavaScript -->
	<script>
		//Learn Mode Code
		var tglStateSecondary = 0;
		document.getElementById('pDemo').addEventListener('click', () => {
			let state = document.querySelector('.stage').style.overflow;
			if (state == "visible") {
				document.querySelector('.stage').style.overflow = "hidden";
				document.getElementById('pages').style.background = "transparent";
				document.querySelector('.container').style.overflowY = "hidden";
				document.querySelector('.span-btn').style.animation = "none";
				document.getElementById('pDemo').style.color = "rgba(255,255,255)"
				tglStateSecondary = 0;
				resetOpacity(null, 0);
				
			}else{
				document.querySelector('.stage').style.overflow = "visible";
				document.getElementById('pages').style.background = "linear-gradient(rgba(128, 255, 0, 0.2), rgba(128, 0, 255, 0.2), rgba(0,128, 255, 0.2), rgba(128,128,255,0.2)";
				document.querySelector('.container').style.overflowY = "visible";
				document.querySelector('.span-btn').style.animation = "spanBtnAnim 3s 1";
				document.getElementById('pDemo').style.color = "rgba(50,255,150)"
				tglStateSecondary = 1;
				resetOpacity(null, 1);
			}
		})
		
		//FRONT END CODE
		function resetOpacity(id, tglState){
			const ids = [ "home-page", "emi-calculator-page", "calender-page", "news-page", "history-page" ]
			
			if (tglState !== 1 && tglStateSecondary !== 1) {
				document.getElementById(id).style.opacity = "1";				
				for (let i = 0; i < ids.length; i++) {
					if (ids[i] != id) {
						document.getElementById(ids[i]).style.opacity = "0";
					}						
				}
			}else{
				for (let i = 0; i < ids.length; i++) {
					document.getElementById(ids[i]).style.opacity = "1";
				}
			}
		}
		function resetWidthOfLi(elemid) {
			var arr = ["home-btn", "emi-btn", "calender-btn", "news-btn", "history-btn"];
			for (let i = 0; i < arr.length; i++) {
				if (arr[i] != elemid) {
					document.getElementById(arr[i]).style.boxShadow =
						"10px 10px 20px rgba(0,0,0,0.3)";
					document.getElementById(arr[i]).style.borderTop =
						"1px solid rgba(255, 255, 255, 0.08)";
					document.getElementById(arr[i]).style.borderLeft =
						"1px solid rgba(255, 255, 255, 0.08)";
					document.getElementById(arr[i]).style.background = "hsl(228, 7 %, 19 %)";
				}
			}
		}
		function onNavBtnClick(id) {
			document.getElementById(id).style.boxShadow = "none";
			document.getElementById(id).style.borderTop = "1px solid rgba(255, 255, 255, 0)";
			document.getElementById(id).style.borderLeft = "1px solid rgba(255, 255, 255, 0)";
			resetWidthOfLi(id);
		}
		function homeFormChanger() {
			document.getElementById("pages").style.transform = "translate(0, 0)";
			document.getElementById("home-page").style.opacity = "1";
			resetOpacity('home-page');
			document.getElementById("myVideo").style.opacity = "0";
			
		}
		function emiFormChanger() {
			document.getElementById("pages").style.transform = "translate(0, -20%)";
			resetOpacity('emi-calculator-page');
			document.getElementById("myVideo").style.opacity = "0";
		}
		function calenderFormChanger() {
			document.getElementById("pages").style.transform = "translate(0, -40%)";
			resetOpacity('calender-page');
			document.getElementById("myVideo").style.opacity = "1";
		}
		function newsFormChanger() {
			document.getElementById("pages").style.transform = "translate(0, -60%)";
			resetOpacity('news-page');
			document.getElementById("myVideo").style.opacity = "0";
		}
		function historyFormChanger() {
			document.getElementById("pages").style.transform = "translate(0, -80%)"; 
			resetOpacity('history-page');
			document.getElementById("myVideo").style.opacity = "0";
		}
	</script>
	<script src="https://kit.fontawesome.com/8dfe9b6887.js" crossorigin="anonymous"></script>
</body>
</html>