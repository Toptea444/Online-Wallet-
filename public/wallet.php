<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Fetch user wallet information
$userId = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];
$email = $_SESSION['user_email'];

$stmt = $pdo->prepare("SELECT virtual_account, balance FROM wallets WHERE user_id = :user_id");
$stmt->execute(['user_id' => $userId]);
$wallet = $stmt->fetch(PDO::FETCH_ASSOC);
$virtualAccount = $wallet['virtual_account'];
$balance = $wallet['balance'];


// Fetch deposit history
$stmt = $pdo->prepare("SELECT * FROM transactions WHERE user_id = :user_id AND type = 'deposit' ORDER BY created_at DESC");
$stmt->execute(['user_id' => $userId]);
$depositHistory = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch withdrawal history
$stmtW = $pdo->prepare("SELECT * FROM withdraw_requests WHERE user_id = :user_id ORDER BY created_at DESC");
$stmtW->execute(['user_id' => $userId]);
$withdrawalHistory = $stmtW->fetchAll(PDO::FETCH_ASSOC);


//Txn history imgs
$images = [
    './assets/img1.jpg',
    './assets/img2.jpg',
    './assets/img3.jpg',
    './assets/img4.jpg',
    './assets/img5.jpg'
];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Theme color for mobile browsers -->
    <meta name="theme-color" content="#121212">

    <!-- Color scheme for browser theme (light or dark) -->
    <meta name="color-scheme" content="dark">



    <!-- For Android and Chrome color changes -->
    <meta name="msapplication-TileColor" content="#121212">

    <title>Wallet</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    

    <link href="https://fonts.googleapis.com/css2?family=Roboto+Condensed:ital,wght@0,100..900;1,100..900&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
      <link href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css" rel="stylesheet">
<style>
    body, .offcanvas{
  background-color:#121212 ;
  color:#fff;
}
    /*Withdraw form*/
.form-container {
  max-width: 400px;
  backgrond-color: #fff;
  padding: 15px 8px;
  font-size: 14px;
  font-family: inherit;
  color: #212121;
  display: flex;
  flex-direction: column;
  gap: 20px;
  box-sizing: border-box;
  border-radius: 10px;
  box-shadow:
    0px 0px 3px rgba(0, 0, 0, 0.084),
    0px 2px 3px rgba(0, 0, 0, 0.168);
}

.form-container button:active {
  scale: 0.95;
}

.form-container .logo-container {
  text-align: center;
  font-weight: 600;
  font-size: 18px;
}

.form-container .form {
  display: flex;
  flex-direction: column;
}

.form-container .form-group {
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.form-container .form-group label {
  display: block;
  margin-bottom: 5px;
  opacity: .8;
  font-size: 13px;
}

.form-container .form-group input, select {
  width: 100%;
  margin:auto;
  padding: 8px 10px;
  font-family: inherit;
  border: 0;
  margin-bottom: 10px;
  background-color: #121212;
}

select {
  width: 100%;
  margin:auto;
  padding: 8px 10px;
  font-family: inherit;
  border: 0;
  margin-bottom: 10px;
  background-color: #121212;
  outline:none;
}

.form-container .form-group input::placeholder {
  opacity: 0.5;
}


.form-continer .form-submit-btn {
  display: flex;
  justify-content: center;
  align-items: center;
  font-family: inherit;
  color: #fff;
  background-color: #212121;
  border: none;
  width: 100%;
  padding: 12px 16px;
  font-size: inherit;
  gap: 8px;
  margin: 12px 0;
  cursor: pointer;
  border-radius: 6px;
  box-shadow:
    0px 0px 3px rgba(0, 0, 0, 0.084),
    0px 2px 3px rgba(0, 0, 0, 0.168);
}

.form-container .form-submit-btn:hover {
  background-color: #313131;
}

input:focus {
  outline: none;
  border-color: #212121;
}
    
    
   /* General styles for the message box */
.message {
  margin: 0;
  padding: 15px 20px;
  border-radius: 8px;
  font-size: 1rem;
  font-family: "Inter", sans-serif;
  box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
  
  /* Positioning and visibility */
  position: fixed;
  top: 20px;
  right: 20px;
  z-index: 1000;
  
  /* Animation properties */
  opacity: 0;
  visibility: hidden;
  transform: translateY(-20px);
  transition: 
  opacity 0.4s ease-in-out,
  transform 0.4s ease-in-out,
  visibility 0.4s;
}

/* Show message */
.message.show {
  opacity: 1;
  visibility: visible;
  transform: translateY(0);
}

/* Specific styles for error messages */
.message.error {
  background-color: #fef2f2;
  color: #d21f3c;
  border: 1px solid #f8d7da;
}

/* Specific styles for success messages */
.message.success {
  background-color: #e9f7ef;
  color: #28a745;
  border: 1px solid #d4edda;
}

/* Fade-out effect */
.message.hide {
  opacity: 0;
  visibility: hidden;
  transform: translateY(20px);
}

 

 

.roboto-condensed-one {
  font-optical-sizing: auto;
  font-weight: 500;
  font-style: normal;
}
h1, nav, h3, h2, h4, h5{
  font-family: "Inter", sans-serif;
}
p, span{
  fot-family: "roboto-condensed-one", sans-serif;
  font-family: "Inter", sans-serif;
  text-transform: justify-content;
  font-size:14px;
}

.dark{
  background-color: #212121;
}

.press-effect {
  transition: transform 0.2s;
}

.press-effect:active {
  transform: scale(0.85);
}


.ripple {
  position: relative;
  overflow: hidden;
}

.ripple-effect {
  position: absolute;
  border-radius: 50%;
  backgound: red;
  background: rgba(255, 255, 255, 0.4);
  transform: scale(0);
  animation: ripple-animation 0.6s linear;
}

@keyframes ripple-animation {
  to {
    transform: scale(4);
    opacity: 0;
  }
}

.history p{
  font-size:14px;
}

.depositForm input{
  border:0;
  color:#fff;
  outline:#000;
  padding:7px;
  width:100%;
  border-radius: 3px;
  cursor:pointer;
}
 
.depositForm button{
  width:100%;
  padding:9px;
  color:#fff;
  font-weight: bold;
  backgound-color: #121212;
  border:0;
  cursor: pointer;
  border-raydius: 4px;000ff7f
}



  /* Overlay */
  .popup-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.8);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 950;
  }

  .popup-card {
    background: #212121;
    color: #fff;
    border-radius: 8px;
    padding: 20px;
    width: 100%;
    max-width: 400px;
    box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.5);
    animation: fadeIn 0.3s ease-in-out;
  }

  .popup-header h6 {
    font-weight: bold;
  }

  .btn-close {
    background: none;
    border: none;
    color: #fff;
    font-size: 1.2rem;
    cursor: pointer;
  }

  .btn-close:hover {
    color: #f00;
  }

  @keyframes fadeIn {
    from {
      opacity: 0;
      transform: scale(0.9);
    }
    to {
      opacity: 1;
      transform: scale(1);
    }
  }
  
    /* Card Styling */
  .bg-card {
    background-color: #212121;
    overflow: hidden;
    border-radius: 15px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5);
  }

  /* Background Image Styling */
  .background-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 1;
    filter: blur(8px); /* Apply blur effect */
  }

  .background-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }

  /* Content Styling */
  .content {
    z-index: 2;
    position: relative;
  }

  .content h3 {
    font-size: 2rem;
    color: #E0E0E0;
    text-shadow: 0 2px 5px rgba(0, 0, 0, 0.9);
  }

  .btn-lg {
    font-size: 13px;
    padding: 7px 15px;
    
  }

  /* Button Styling */
  .btn-outline-light {
    border-color: #E0E0E0;
    color: #E0E0E0;
    transition: background-color 0.3s, color 0.3s;
  }

  .btn-outline-light:hover {
    background-color: #E0E0E0;
    color: #212121;
  }

nav button{
  font-size:14px !important;
}



/* Popup Styling */
.transaction-popup {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background-color: #212121;
    color: white;
    border-radius: 10px;
    padding: 20px;
    width: 300px;
    max-width: 90%;
    z-index: 1090;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease-in-out;
    transform: translate(-50%, -50%) scale(0.9);
}

.transaction-popup.show {
    opacity: 1;
    visibility: visible;
    transform: translate(-50%, -50%) scale(1);
}

.transaction-popup-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.8);
    z-index: 1090;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease-in-out;
}

.transaction-popup-overlay.show {
    opacity: 1;
    visibility: visible;
}

 #feedbackFormform {
    max-width: 400px;
    margin: 0 auto;
    padding: 20px;
    border: 1px solid #ccc;
    border-radius: 10px;
}
 #feedbackForm  input {
  width: 100%;
  padding : 9px;
  margin: 10px 0;
  border-radius:5px;
  border:0;
  opacity:.7;
 }
     textarea {
    width: 100%;
    max-width: 500px; /* Adjust as needed */
    height: 150px; /* Adjust as needed */
    padding: 10px;
    font-family: Arial, sans-serif;
    font-size: 14px;
    color: #fff;
    border: 0;
    border-radius: 5px;
    box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1);
    resize: vertical; /* Allow resizing only vertically */
    outline: none;
    transition: border-color 0.3s ease, box-shadow 0.3s ease;
}

textarea:focus, input:focus {
    border-color: #007bff; /* Highlighted border color */
    box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
}


</style>
</head>
<body style="margin:4px;">
  
  <!-- headers + balance -->
  <div class="container">
    <nav class="">
      <div class="continer mt-3">
        <div class="d-flex align-items-center">
          <img src="./assets/profile_img.jpg" alt="Avatar" class="avatar me-2 rounded-circle" width="35px">
          <div>
          <p class="font-weight-bold mb-0"><small><strong>Hi, <?php echo $user_name; ?></strong></small></p>
       </div>
     </div>
   <div class="balance mt-2">
   <p class="badge dark text-muted">Available balance</p> <br>
   <div class="">
   <span class="small">🇳🇬 Nigerian Naira</span> <br>
   <span stle="color:#B0E0E6" class="w3-xlarge text-warning" style="letter-spacing:2px"><strong>₦<?php echo number_format($balance, 2); ?></strong></span> <br>
   <span class="small text-muted"><small>Acct No: <?php echo $virtualAccount; ?></small></span>
   </div>
   <div class="mt-2">
  <div class="row">
    <div class="col-6">
      <button class="btn press-effect fw-bold text-white dark w-100" id="openPopup1"><i class="ti ti-cash"></i> Withdraw</button>
    </div>
    <div class="col-6">
      <button class="btn press-effect fw-bold col-6 dark text-white w-100" id="openPopup"><i class="ti ti-wallet"></i> Deposit</button>
    </div>
  </div>
</div>
   </div><!-- balance -->
  </div>
 </nav>
 </div>
  





<!-- What are you testing? -->
<!--div class="container mt-3">
  <div class="press-effect p-4 position-relative bg-card">
    <!-- Background Image >
    <div class="background-overlay">
      <img src="./assets/img2.jpg" alt="Background" class="background-img">
    </div>

    <!-- Content >
    <div class="content text-center position-relative">
      <h4 class="text-light fw-bold mb-3">What are we testing here?</h4>
      <button class="btn btn-outline-light btn-lg bg-light text-dark">
        <i class="ti ti-external-link"></i> Click here
      </button>
    </div>
  </div>
</div-->







  
  <!-- Deposit history -->
  <div class="container mt-4">
    <div class="dark p-3 ripple press-effect rounded">
    <div class="d-flex justify-content-between mb-4 mt-1">
        <h6><i class="ti ti-wallet"></i> Deposit history</h6>
        <h6><a href="" class="text-decoration-none w3-text-white text-muted" style="font-size:14px" data-bs-toggle="offcanvas" data-bs-target="#fullDeposits" aria-controls="fullDeposits">View all</a></h6>
    </div>
    
    <?php if (!empty($depositHistory)): ?>
        <?php 
        // Slice the depositHistory array to show only the first 5 items
        $limitedHistory = array_slice($depositHistory, 0, 5);
        foreach ($limitedHistory as $index => $transaction): ?>
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="d-flex align-items-start me-2">
                    <img src="<?php echo $images[array_rand($images)]; ?>" alt="Txn img" class="avatar rounded-circle" width="35px">
                    <div class="d-flex flex-column ms-2">
                        <p class="mb-0 "><?php echo ucfirst($transaction['status']); ?></p>
                        <p class="mb-0 text-muted small"><small>
                            <?php echo date('d-m-Y H:i:s', strtotime($transaction['created_at'])); ?></small>
                        </p>
                    </div>
                </div>
                <p style="color:#E0E0E0" class="mb-0 fw-bold text-mutd 
                 <?php 
               echo $transaction['status'] === 'pending' ? 'text-ifo' : 
                 ($transaction['status'] === 'successful' ? 'text-success' : 'text-sucess'); 
                                        ?>">
                <?php echo "₦" . number_format($transaction['amount'], 2); ?>
             </p>
           </div>
         <hr>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No deposit transactions found.</p>
    <?php endif; ?>
</div>
</div>
  
  
  
  
  <!-- Withdrawal requests history -->
    <div class="container mt-3">
    <div class="dark history rounded p-3 ripple press-effect">
    <div class="d-flex justify-content-between mb-4 mt-1">
        <h6><i class="ti ti-cash"></i> Withdraw history</h6>
        <h6><a href="" class="text-decoration-none w3-text-white text-muted" style="font-size:14px" data-bs-toggle="offcanvas" data-bs-target="#fullWithdrawals" aria-controls="fullWithdrawals">View all</a></h6>
    </div>
    
    <?php if (!empty($withdrawalHistory)): ?>
        <?php 
        // Slice the withdrawalHistory array to show only the first 5 items
        $limitedWHistory = array_slice($withdrawalHistory, 0, 5);
        foreach ($limitedWHistory as $indexW => $transactionW): ?>
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="d-flex align-items-start me-2">
                    <img src="<?php echo $images[array_rand($images)]; ?>" alt="Txn img" class="avatar rounded-circle" width="35px">
                    <div class="d-flex flex-column ms-2">
                        <p class="mb-0"><?php echo ucfirst($transactionW['status']); ?></p>
                        <p class="mb-0 text-muted small"><small>
                            <?php echo date('d-m-Y H:i:s', strtotime($transactionW['created_at'])); ?></small>
                        </p>
                    </div>
                </div>
                <p class="mb-0 fw-bold"><?php echo "-₦" . number_format($transactionW['amount'], 2); ?></p>
            </div>
            <hr>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No withdrawal transactions found.</p>
    <?php endif; ?>
</div>
</div>
  
  









<!-- Full-Screen Deposit Offcanvas -->
<div class="offcanvas offcanvas-end w-100" tabindex="-1" id="fullDeposits" aria-labelledby="offcanvasExample">
  <div class="offcanvas-header">
    <h5 class="offcanvas-title" id="offcanvasExampleLabel">
       <i class="ti ti-wallet"></i> Deposit history</h5>
    <button type="button" class="btn-close text-reset w3-text-white" data-bs-dismiss="offcanvas" aria-label="Close"><i class="ti ti-x"></i></button>
  </div>
  <div class="offcanvas-body">
     <input style="background:#212121; border:0" type="search" id="depositSearch" class="form-control mb-4 w3-text-white" placeholder="Search deposits...">
     
     <div id="depositList">
    <?php if (!empty($depositHistory)): ?>
        <?php 
        foreach ($depositHistory as $transactions): ?>
            <div class="transaction-item" data-status="<?php echo strtolower($transactions['status']); ?>">
                <div class="d-flex align-items-center justify-content-between mb-3 press-effect">
                    <div class="d-flex align-items-start me-2">
                        <img src="<?php echo $images[array_rand($images)]; ?>" alt="Txn img" class="avatar rounded-circle" width="35px">
                        <div class="d-flex flex-column ms-2">
                            <p class="transaction-status mb-0"><?php echo ucfirst($transactions['status']); ?></p>
                            <p style="display:none" class="transaction-paystack_reference mb-0"><?php echo ($transactions['paystack_reference']); ?></p>
                            <p class="transaction-date mb-0 text-muted small"><small>
                                <?php echo date('d-m-Y H:i:s', strtotime($transactions['created_at'])); ?></small>
                            </p>
                        </div>
                    </div>
                    <p class="transaction-amount mb-0 fw-bold"><?php echo "₦" . number_format($transactions['amount'], 2); ?></p>
                </div>
                <hr>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="text-center mt-5">No deposit transactions found.</p>
    <?php endif; ?>
     </div>

     <div id="noResults" class="text-center d-none">
        <p>No deposit transactions found.</p>
     </div>
  </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('depositSearch');
    const transactionItems = document.querySelectorAll('.transaction-item');
    const noResultsMessage = document.getElementById('noResults');

    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase().trim();
        let visibleCount = 0;

        transactionItems.forEach(item => {
            const status = item.getAttribute('data-status');
            const date = item.querySelector('.transaction-date').textContent.toLowerCase();
            const amount = item.querySelector('.transaction-amount').textContent.toLowerCase();

            const isVisible = status.includes(searchTerm) || 
                              date.includes(searchTerm) || 
                              amount.includes(searchTerm);

            item.style.display = isVisible ? 'block' : 'none';
            
            if (isVisible) visibleCount++;
        });

        noResultsMessage.classList.toggle('d-none', visibleCount > 0);
    });
});
</script>





<!-- Full-Screen withdrawal Offcanvas -->
<div class="offcanvas offcanvas-start w-100" tabindex="-1" id="fullWithdrawals" aria-labelledby="offcanvasExample">
  <div class="offcanvas-header">
    <h5 class="offcanvas-title" id="offcanvasExampleLabel">
       <i class="ti ti-cash"></i> Withdrawal history</h5>
    <button type="button" class="btn-close text-reset w3-text-white" data-bs-dismiss="offcanvas" aria-label="Close"><i class="ti ti-x"></i></button>
  </div>
  <div class="offcanvas-body">
     <input style="background:#212121; border:0" type="search" id="withdrawalSearch" class="form-control mb-4 w3-text-white" placeholder="Search withdrawals...">
     
     <div id="withdrawalList">
        <?php if (!empty($withdrawalHistory)): ?>
            <?php foreach ($withdrawalHistory as $transactionsW): ?>
                <div class="transaction-item1" data-status="<?php echo strtolower($transactionsW['status']); ?>">
                    <div class="d-flex align-items-center justify-content-between mb-3  press-effect">
                        <div class="d-flex align-items-start me-2">
                       <img src="<?php echo $images[array_rand($images)]; ?>" alt="Txn img" class="avatar rounded-circle" width="35px">
                            <div class="d-flex flex-column ms-2">
                       <p class="transaction-status mb-0">
                       <?php echo ucfirst($transactionsW['status']); ?>
                                </p>
                                
                                
                       <p style="display:none" class="transaction-bankName mb-0">
                       <?php echo ($transactionsW['bank_name']); ?>
                                </p>
                       <p style="display:none" class="transaction-acctNumber mb-0">
                       <?php echo ($transactionsW['account_number']); ?>
                                </p>
                       <p style="display:none" class="transaction-acctType mb-0">
                       <?php echo ($transactionsW['account_type']); ?>
                                </p>
                                
                                
                        <p class="transaction-date mb-0 text-muted small">
                       <small><?php echo date('d-m-Y H:i:s', strtotime($transactionsW['created_at'])); ?></small>
                                </p>
                            </div>
                        </div>
                        <p class="transaction-amount mb-0 fw-bold">
                            <?php echo "-₦" . number_format($transactionsW['amount'], 2); ?>
                        </p>
                    </div>
                    <hr>
                </div>
            <?php endforeach; ?>
            <?php else: ?>
        <p class="text-center">No withdrawal transactions found.</p>
        <?php endif; ?>
     </div>

     <div id="noResults1" class="text-center d-none">
        <p class="mt-5">No withdrawal transactions found.</p>
     </div>
  </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput1 = document.getElementById('withdrawalSearch');
    const transactionItems1 = document.querySelectorAll('.transaction-item1');
    const noResultsMessage1 = document.getElementById('noResults1');

    searchInput1.addEventListener('input', function() {
        const searchTerm1 = this.value.toLowerCase().trim();
        let visibleCount1 = 0;

        transactionItems1.forEach(item1 => {
            const status1 = item1.getAttribute('data-status');
            const date1 = item1.querySelector('.transaction-date').textContent.toLowerCase();
            const amount1 = item1.querySelector('.transaction-amount').textContent.toLowerCase();

            const isVisible1 = status1.includes(searchTerm1) || 
                              date1.includes(searchTerm1) || 
                              amount1.includes(searchTerm1);

            item1.style.display = isVisible1 ? 'block' : 'none';
            
            if (isVisible1) visibleCount1++;
        });

        noResultsMessage1.classList.toggle('d-none', visibleCount1 > 0);
    });
});
</script>








<!-- Popup Container for dynamic transaction -->
<div id="transactionPopupOverlay" class="transaction-popup-overlay"></div>
<div id="transactionPopup" class="transaction-popup">
    <div id="transactionPopupDetails"></div>
    <button id="closePopup" class="btn btn-sm btn-outline-light mt-3 w-100 press-effect">Close</button>
</div>
<!-- Dynamic popup transaction-->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const transactionItems = document.querySelectorAll('.transaction-item');
    const popup = document.getElementById('transactionPopup');
    const popupOverlay = document.getElementById('transactionPopupOverlay');
    const popupDetails = document.getElementById('transactionPopupDetails');
    const closePopupBtn = document.getElementById('closePopup');

    // Add click event to each transaction item
    transactionItems.forEach(item => {
        item.addEventListener('click', function() {
            // Get all the details from the clicked transaction item
            const image = item.querySelector('img').src;
            const status = item.querySelector('.transaction-status').textContent.trim();
            const date = item.querySelector('.transaction-date').textContent.trim();
            const amount = item.querySelector('.transaction-amount').textContent.trim();
            const paystack_reference = item.querySelector('.transaction-paystack_reference').textContent.trim();

            // Populate popup with detailed HTML
            popupDetails.innerHTML = `
                <div class="text-center mb-3">
                    <img src="${image}" alt="Transaction Image" class="rounded-circle mb-3" width="70px">
                    <h6 class="mb-1">${status} Transaction</h6>
                </div>
                <div class="transaction-popup-body small">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="mall">Status:</span>
                        <strong>${status}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Date:</span>
                        <strong>${date}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Amount:</span>
                        <strong>${amount}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Ref:</span>
                        <strong>${paystack_reference}</strong>
                    </div>
                </div>
            `;

            // Show popup
            popup.classList.add('show');
            popupOverlay.classList.add('show');
        });
    });

    // Close popup when close button is clicked
    closePopupBtn.addEventListener('click', closePopup);

    // Close popup when overlay is clicked
    popupOverlay.addEventListener('click', closePopup);

    // Function to close popup
    function closePopup() {
        popup.classList.remove('show');
        popupOverlay.classList.remove('show');
    }

    // Close popup on Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closePopup();
        }
    });
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const transactionItems = document.querySelectorAll('.transaction-item1');
    const popup = document.getElementById('transactionPopup');
    const popupOverlay = document.getElementById('transactionPopupOverlay');
    const popupDetails = document.getElementById('transactionPopupDetails');
    const closePopupBtn = document.getElementById('closePopup');

    // Add click event to each transaction item
    transactionItems.forEach(item => {
        item.addEventListener('click', function() {
            // Get all the details from the clicked transaction item
            const image = item.querySelector('img').src;
            const status = item.querySelector('.transaction-status').textContent.trim();
            const date = item.querySelector('.transaction-date').textContent.trim();
            const amount = item.querySelector('.transaction-amount').textContent.trim();
            const bank = item.querySelector('.transaction-bankName').textContent.trim();
            const acctNo = item.querySelector('.transaction-acctNumber').textContent.trim();
            const acctType = item.querySelector('.transaction-acctType').textContent.trim();
             
            // Populate popup with detailed HTML
            popupDetails.innerHTML = `
                <div class="text-center mb-3">
                    <img src="${image}" alt="Transaction Image" class="rounded-circle mb-3" width="70px">
                    <h6 class="mb-1">${status} Transaction</h6>
                </div>
                <div class="transaction-popup-body small">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="mall">Status:</span>
                        <strong>${status}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Date:</span>
                        <strong>${date}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Amount:</span>
                        <strong>${amount}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Bank:</span>
                        <strong>${bank}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Acct No:</span>
                        <strong>${acctNo}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                       <span>Acct Type:</span>
                        <strong>${acctType}</strong>
                    </div>
                </div>
            `;

            // Show popup
            popup.classList.add('show');
            popupOverlay.classList.add('show');
        });
    });

    // Close popup when close button is clicked
    closePopupBtn.addEventListener('click', closePopup);

    // Close popup when overlay is clicked
    popupOverlay.addEventListener('click', closePopup);

    // Function to close popup
    function closePopup() {
        popup.classList.remove('show');
        popupOverlay.classList.remove('show');
    }

    // Close popup on Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closePopup();
        }
    });
});
</script>





<!-- Deposit Popup -->
<div id="popupCard" class="popup-overlay d-none">
  <div class="popup-card">
    <div class="popup-header d-flex justify-content-between align-items-center">
      <h6 class="mb-0"><i class="ti ti-wallet"></i> Deposit Funds</h6>
      <button class="btn-close" id="closePopup"></button>
    </div>
    <div class="popup-body mt-3">
      <form class="depositForm" action="/project/src/controllers/depositController.php" method="POST">
        <label for="amount" class="form-label text-mued small">Deposit Amount (NGN):</label>
        <input style="background:#121212" type="number" name="amount" id="amount" class="form-control press-effect mb-3 text-white" placeholder="Enter amount" required>
        <button class="btn btn-success press-effect w-100" type="submit"> <i class="ti ti-wallet"></i> Deposit</button>
      </form>
    </div>
  </div>
</div>



<!-- Withdraw funds Popup -->
<div id="popupCard1" class="popup-overlay d-none">
  <div class="popup-card">
    
    <div class="popup-header d-flex justify-content-between align-items-center mb-3">
      <div class="d-flex align-items-start me-2">
      <h6 class="mb-0"><i class="ti ti-cash"></i> Withdrawal request</h6>
      </div>
    <button  class="btn-close" id="closePopup1"><i class="ti ti-x"></i> </button>
    </div>
    <span style="color:#E0E0E0; opacity:.7" class="small">Request for a withdrawal which will be manually reviewed by me. As a starter business, paystack withdrawal API won't work.</span>
      <hr>
    <div class="popup-body mt-3 form-container">
    <form class="form w3-text-white" id="withdrawRequestForm">
    <div class="form-group">
    <label for="withdrawAmount">Amount:</label>
    <input class="press-effect" type="number" id="withdrawAmount" name="amount" step="0.01" placeholder="Enter amount" required>
    </div>
    <div class="form-group">
    <label for="accountNumber">Account Number:</label>
    <input class="press-effect" type="text" id="accountNumber" name="account_number" placeholder="Enter account number" required>
    </div>
    <div class="form-group">
    <label for="bankName">Bank Name:</label>
    <input class="press-effect" type="text" id="bankName" name="bank_name" placeholder="Bank name"required>
    </div>
    <div class="form-group">
    <label for="accountType">Account Type:</label>
    <select id="accountType" name="account_type" class="press-effect">
    <option value="">Select Account Type</option>
    <option value="Savings">Savings</option>
    <option value="Current">Current</option>
</select>
    </div>

    <button type="submit" class="form-submit-btn fw-bold bg-success btn press-effect w3-text-white mt-3"> <i class="ti ti-cash"></i> Request Withdrawal</button>
     <div id="withdrawResponseMessage" class="message"></div>
     </form>
     </div>
     </div>
     </div>










<br><br>
<br>

<div class="container mt-4">
  <h3 class="mb-3">What's Your Experience?🧐</h3>
  <div class="dark p-3">
    <div class="w3-center">
    <!--button style="background-color:#121212; border:solid 1px grey; font-size:14px;" id="showFormButton" class="btn mb-2 w3-text-white press-effect">
      Give Feedback
    </button-->
    </div>
   <form style="disply:none" id="feedbackForm" class="fadeIn" enctype="multipart/form-data">
        <h6><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" width="15" height="15" stroke-width="2">
  <path d="M18 4a3 3 0 0 1 3 3v8a3 3 0 0 1 -3 3h-5l-5 3v-3h-2a3 3 0 0 1 -3 -3v-8a3 3 0 0 1 3 -3h12z"></path>
  <path d="M13 8l3 3l-3 3"></path>
  <path d="M16 11h-8"></path>
</svg> Submit Feedback</h6>
        <span style="opacity:.7" class="">Your feedback is highly needed and appreciated. Please share your experience or issues interacting with this app.</span>
        <hr>
        <input type="hidden" name="user_name" id="user_name" placeholder="Your Name" value="<?php echo $user_name; ?>" required>
        <label style="opacity:.8; font-size:14px" for="email">Enter your email:</label>
        <input type="email" name="email" id="email" value="<?php echo $email; ?>" class="press-effect" required>
        <label style="opacity:.8; font-size:14px;" for="" class="mt-4">Tell us what you feel:</label>
        <textarea name="message" class="press-effect" id="message" placeholder="Your Feedback" required></textarea>
        
        <label style="opacity:.8; font-size:14px;" for="" class="mt-3">Share an image if necessary: </label>
        <input class="press-effect" style="padding:8px; background-color:#121212" type="file" name="image" id="image" accept="image/*">
        
         <div id="response" style="margin-top: 20px;"></div>
        <button type="submit" class="btn w-100 btn-success press-effect"> <i class="ti ti-send"></i> Submit Feedback</button>
    </form>
  </div>
</div>



  


    

  
  
  
  
  
  
  
  
  <footer style="border-top:solid 1px grey" class="mt-5 p-4">
    <div style="opacity:.8 w3-text-white" class="text-center small">App created by <a class="w3-text-white" href="tel:08165210936">Adelaja Temitope</a></div>
  </footer>
  
  
  
  
  
  
  
  












    <script>
        $(document).ready(function () {
            // Withdrawal functionality
            $('#withdrawForm').on('submit', function (e) {
                e.preventDefault();
                let withdrawAmount = $('#withdrawAmount').val();

                if (withdrawAmount <= 0) {
                    $('#responseMessage').removeClass('success').addClass('error').text('Withdrawal amount must be greater than zero.').show();
                    return;
                }

                $.ajax({
                    url: '../src/controllers/withdrawController.php',
                    type: 'POST',
                    data: { amount: withdrawAmount },
                    dataType: 'json',
                    success: function (response) {
                        const messageDiv = $('#responseMessage');
                        messageDiv.removeClass('error success');

                        if (response.success) {
                            messageDiv.addClass('success').text(response.message).show();
                            setTimeout(() => location.reload(), 2000); // Reload to show updated balance
                        } else {
                            messageDiv.addClass('error').text(response.message).show();
                        }
                    },
                    error: function () {
                        $('#responseMessage').removeClass('success').addClass('error').text('An error occurred. Please try again.').show();
                    }
                });
            });
        });
    </script>
    <script>
    $(document).ready(function () {
    $('#withdrawRequestForm').on('submit', function (e) {
        e.preventDefault();

        let withdrawAmount = parseFloat($('#withdrawAmount').val());
        let accountNumber = $('#accountNumber').val();
        let bankName = $('#bankName').val();
        let accountType = $('#accountType').val();

        // Validate input on the client side
        if (isNaN(withdrawAmount) || withdrawAmount <= 0) {
            displayMessage('Invalid withdrawal amount. Please enter a positive number.', 'error');
            return;
        }

        $.ajax({
            url: '../src/controllers/withdrawRequestController.php',
            type: 'POST',
            data: {
                amount: withdrawAmount,
                account_number: accountNumber,
                bank_name: bankName,
                account_type: accountType,
            },
            dataType: 'json',
            success: function (response) {
                $('.message').remove();
                displayMessage(response.message, response.success ? 'success' : 'error');
            },
            error: function () {
                $('.message').remove();
                displayMessage('An error occurred. Please try again.', 'error');
            },
        });
    });

    function displayMessage(message, type) {
        const messageDiv = $('<div class="message"></div>')
            .addClass(type)
            .text(message);

        $('body').append(messageDiv);

        // Show message animation
        setTimeout(() => {
            messageDiv.addClass('show');
        }, 10);

        // Remove message after some time
        setTimeout(() => {
            messageDiv.removeClass('show').addClass('hide');
            setTimeout(() => {
                messageDiv.remove();
            }, 400);
        }, 3000);
    }
});

    </script>
    <script>
     document.querySelectorAll('.ripple').forEach(function(element) {
      element.addEventListener('click', function(e) {
        const ripple = document.createElement('span');
        ripple.classList.add('ripple-effect');
        
        const rect = this.getBoundingClientRect();
        const size = Math.max(rect.width, rect.height);
        ripple.style.width = ripple.style.height = `${size}px`;
        
        const x = e.clientX - rect.left - size / 2;
        const y = e.clientY - rect.top - size / 2;
        ripple.style.left = `${x}px`;
        ripple.style.top = `${y}px`;

        this.appendChild(ripple);

        setTimeout(() => {
            ripple.remove();
        }, 600); // Match the duration of the CSS animation
    });
});
     </script>
    <script>
  const openPopup = document.getElementById('openPopup');
  const closePopup = document.getElementById('closePopup');
  const popupCard = document.getElementById('popupCard');

  openPopup.addEventListener('click', () => {
    popupCard.classList.remove('d-none');
  });

  closePopup.addEventListener('click', () => {
    popupCard.classList.add('d-none');
  });

  // Close popup when clicking outside
  window.addEventListener('click', (e) => {
    if (e.target === popupCard) {
      popupCard.classList.add('d-none');
    }
  });
</script>
 <script>
  const openPopup1 = document.getElementById('openPopup1');
  const closePopup1 = document.getElementById('closePopup1');
  const popupCard1 = document.getElementById('popupCard1');

  openPopup1.addEventListener('click', () => {
    popupCard1.classList.remove('d-none');
  });

  closePopup1.addEventListener('click', () => {
    popupCard1.classList.add('d-none');
  });

  // Close popup when clicking outside
  window.addEventListener('click', (e) => {
    if (e.target === popupCard1) {
      popupCard1.classList.add('d-none');
    }
  });
</script>
<script>
    function showMessage(message, type = 'success', duration = 4000) {
  // Create message element if it doesn't exist
  let messageEl = document.querySelector('.message');
  if (!messageEl) {
    messageEl = document.createElement('div');
    messageEl.classList.add('message');
    document.body.appendChild(messageEl);
  }

  // Reset classes and set new ones
  messageEl.className = 'message';
  messageEl.classList.add(type);

  // Set message text
  messageEl.textContent = message;

  // Show message
  requestAnimationFrame(() => {
    messageEl.classList.add('show');

    // Hide message after duration
    setTimeout(() => {
      messageEl.classList.remove('show');
      messageEl.classList.add('hide');

      // Remove from DOM after animation
      setTimeout(() => {
        messageEl.remove();
      }, 400);
    }, duration);
  });
}

    </script>
    <script>
        $(document).ready(function() {
            $("#feedbackForm").on("submit", function(e) {
                e.preventDefault();

                let formData = new FormData(this);

                $.ajax({
                    url: "../src/controllers/feedbackController.php",
                    type: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        $("#response").html("<p>" + response.message + "</p>");
                        if (response.success) {
                            $("#feedbackForm")[0].reset();
                        }
                    },
                    error: function() {
                        $("#response").html("<p>An error occurred. Please try again.</p>");
                    }
                });
            });
        });
        
    // Show/Hide Feedback Form
        document.getElementById("showFormButton").addEventListener("click", function () {
            document.getElementById("feedbackForm").style.display = "block";
            document.getElementById("showFormButton").style.display = "none";
        });

        document.getElementById("hideFormButton").addEventListener("click", function () {
            document.getElementById("feedbackForm").style.display = "none";
        });
    
    </script>





     <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>




