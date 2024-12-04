<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.12.0/css/all.css">
    <title>User Registration</title>
    <!-- Theme color for mobile browsers -->
    <meta name="theme-color" content="#121212">

    <!-- For iOS status bar style -->
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">

    <!-- For Android and Chrome color changes -->
    <meta name="msapplication-TileColor" content="#121212">
    
    <link rel="stylesheet" href="./assets/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
 <style>
   *{
     font-family: "Inter", Sans-Serif;
   
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


    .loader {
      border: 3px solid #f3f3f3; /* Light grey */
      border-top: 3px solid #3498db; /* Blue */
      border-radius: 50%;
      width: 15px;
      height: 15px;
      animation: spin 1s linear infinite;
      display: inline-block;
      margin-left: 5px;
    }

    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }
</style>
</head>
<body>
    <form id="registrationForm" class="form_container">
    <div id="responseMessage" class="message"></div>
    <div id="responseMessage" class="message"></div>
    <div class="title_container">
    <p class="title hanken-grotesk-myfont"><strong>Create an account </strong></p>
    <span class="subtitle">to get started with our app, just create an account and enjoy the experience.</span>
    </div>
    <br>
    <div class="input_container">
    <label class="input_label" for="name_field">Name</label>
    <svg fill="none" viewBox="0 0 24 24" height="24" width="24" xmlns="http://www.w3.org/2000/svg" class="icon">
        <path stroke-linejoin="round" stroke-linecap="round" stroke-width="1.5" stroke="#141B34" d="M12 11.5C14.4853 11.5 16.5 9.48528 16.5 7C16.5 4.51472 14.4853 2.5 12 2.5C9.51472 2.5 7.5 4.51472 7.5 7C7.5 9.48528 9.51472 11.5 12 11.5Z"></path>
        <path stroke-linejoin="round" stroke-linecap="round" stroke-width="1.5" stroke="#141B34" d="M20.4234 20.5C20.4234 16.9092 16.6434 14 12 14C7.35659 14 3.57661 16.9092 3.57661 20.5"></path>
    </svg>
    <input placeholder="Enter your full name" title="Name input" name="name" type="text" class="input_field" id="name_field">
</div>
   <div class="input_container">
    <label class="input_label" for="email_field">Email</label>
    <svg fill="none" viewBox="0 0 24 24" height="24" width="24" xmlns="http://www.w3.org/2000/svg" class="icon">
      <path stroke-linejoin="round" stroke-linecap="round" stroke-width="1.5" stroke="#141B34" d="M7 8.5L9.94202 10.2394C11.6572 11.2535 12.3428 11.2535 14.058 10.2394L17 8.5"></path>
      <path stroke-linejoin="round" stroke-width="1.5" stroke="#141B34" d="M2.01577 13.4756C2.08114 16.5412 2.11383 18.0739 3.24496 19.2094C4.37608 20.3448 5.95033 20.3843 9.09883 20.4634C11.0393 20.5122 12.9607 20.5122 14.9012 20.4634C18.0497 20.3843 19.6239 20.3448 20.7551 19.2094C21.8862 18.0739 21.9189 16.5412 21.9842 13.4756C22.0053 12.4899 22.0053 11.5101 21.9842 10.5244C21.9189 7.45886 21.8862 5.92609 20.7551 4.79066C19.6239 3.65523 18.0497 3.61568 14.9012 3.53657C12.9607 3.48781 11.0393 3.48781 9.09882 3.53656C5.95033 3.61566 4.37608 3.65521 3.24495 4.79065C2.11382 5.92608 2.08114 7.45885 2.01576 10.5244C1.99474 11.5101 1.99475 12.4899 2.01577 13.4756Z"></path>
    </svg>
    <input placeholder="name@mail.com" title="Inpit title" name="email" type="text" class="input_field" id="email_field">
  </div>
   <div class="input_container">
   <label class="input_label" for="password_field">Password</label>
   <svg fill="none" viewBox="0 0 24 24" height="24" width="24" xmlns="http://www.w3.org/2000/svg" class="icon">
      <path stroke-linecap="round" stroke-width="1.5" stroke="#141B34" d="M18 11.0041C17.4166 9.91704 16.273 9.15775 14.9519 9.0993C13.477 9.03404 11.9788 9 10.329 9C8.67911 9 7.18091 9.03404 5.70604 9.0993C3.95328 9.17685 2.51295 10.4881 2.27882 12.1618C2.12602 13.2541 2 14.3734 2 15.5134C2 16.6534 2.12602 17.7727 2.27882 18.865C2.51295 20.5387 3.95328 21.8499 5.70604 21.9275C6.42013 21.9591 7.26041 21.9834 8 22"></path>
      <path stroke-linejoin="round" stroke-linecap="round" stroke-width="1.5" stroke="#141B34" d="M6 9V6.5C6 4.01472 8.01472 2 10.5 2C12.9853 2 15 4.01472 15 6.5V9"></path>
      <path fill="#141B34" d="M21.2046 15.1045L20.6242 15.6956V15.6956L21.2046 15.1045ZM21.4196 16.4767C21.7461 16.7972 22.2706 16.7924 22.5911 16.466C22.9116 16.1395 22.9068 15.615 22.5804 15.2945L21.4196 16.4767ZM18.0228 15.1045L17.4424 14.5134V14.5134L18.0228 15.1045ZM18.2379 18.0387C18.5643 18.3593 19.0888 18.3545 19.4094 18.028C19.7299 17.7016 19.7251 17.1771 19.3987 16.8565L18.2379 18.0387ZM14.2603 20.7619C13.7039 21.3082 12.7957 21.3082 12.2394 20.7619L11.0786 21.9441C12.2794 23.1232 14.2202 23.1232 15.4211 21.9441L14.2603 20.7619ZM12.2394 20.7619C11.6914 20.2239 11.6914 19.358 12.2394 18.82L11.0786 17.6378C9.86927 18.8252 9.86927 20.7567 11.0786 21.9441L12.2394 20.7619ZM12.2394 18.82C12.7957 18.2737 13.7039 18.2737 14.2603 18.82L15.4211 17.6378C14.2202 16.4587 12.2794 16.4587 11.0786 17.6378L12.2394 18.82ZM14.2603 18.82C14.8082 19.358 14.8082 20.2239 14.2603 20.7619L15.4211 21.9441C16.6304 20.7567 16.6304 18.8252 15.4211 17.6378L14.2603 18.82ZM20.6242 15.6956L21.4196 16.4767L22.5804 15.2945L21.785 14.5134L20.6242 15.6956ZM15.4211 18.82L17.8078 16.4767L16.647 15.2944L14.2603 17.6377L15.4211 18.82ZM17.8078 16.4767L18.6032 15.6956L17.4424 14.5134L16.647 15.2945L17.8078 16.4767ZM16.647 16.4767L18.2379 18.0387L19.3987 16.8565L17.8078 15.2945L16.647 16.4767ZM21.785 14.5134C21.4266 14.1616 21.0998 13.8383 20.7993 13.6131C20.4791 13.3732 20.096 13.1716 19.6137 13.1716V14.8284C19.6145 14.8284 19.619 14.8273 19.6395 14.8357C19.6663 14.8466 19.7183 14.8735 19.806 14.9391C19.9969 15.0822 20.2326 15.3112 20.6242 15.6956L21.785 14.5134ZM18.6032 15.6956C18.9948 15.3112 19.2305 15.0822 19.4215 14.9391C19.5091 14.8735 19.5611 14.8466 19.5879 14.8357C19.6084 14.8273 19.6129 14.8284 19.6137 14.8284V13.1716C19.1314 13.1716 18.7483 13.3732 18.4281 13.6131C18.1276 13.8383 17.8008 14.1616 17.4424 14.5134L18.6032 15.6956Z"></path>
    </svg>
    <input placeholder="Password" title="Inpit title" name="password" type="password" class="input_field" id="password_field">
  </div>
  <button title="Sign In" type="submit" class="sign-in_btn" id="myButton" onclick="showLoader()">
    <span><strong>Register</strong></span>
  </button>
<span style="font-size:13px">Already have an account? <a href="login.php" style="color:#000">Login</a></span>
  <div class="separator">
    <hr class="line">
    <span>or</span>
    <hr class="line">
  </div>
  <button title="Sign In" type="submit" class="sign-in_ggl" readonly disabled>
      <i class="fab fa-google"></i>
    <span>
      Sign In with Google</span>
  </button>
  <span class="note">
  <span class="circle pulse orange"></span>  
    Made by <a href="tel:08165210936" style="color:#8b8e98">Temitope Adelaja</a>
</span>

</form>
    
 



  <script>
    function showLoader() {
      const button = document.getElementById("myButton");
      button.innerHTML = '<span class="loader"></span>';
      setTimeout(() => {
        button.innerHTML = 'Register';
        button.disabled = false;
      }, 4000); // Change back to original text after 4 seconds
    }
 </script>
 <script>
   $(document).ready(function () {
    $('#registrationForm').on('submit', function (e) {
        e.preventDefault(); // Prevent form submission

        $.ajax({
            url: '../src/controllers/registerController.php',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function (response) {
                // Remove any existing message first
                $('.message').remove();

                // Create a new message div
                const messageDiv = $('<div class="message"></div>');
                
                if (response.success) {
                    messageDiv.addClass('success').text(response.message);
                    $('body').append(messageDiv);
                    
                    // Add show class to trigger animation
                    setTimeout(() => {
                        messageDiv.addClass('show');
                    }, 10);

                    // Redirect and remove message
                    setTimeout(() => {
                        messageDiv.removeClass('show').addClass('hide');
                        setTimeout(() => {
                            messageDiv.remove();
                            window.location.href = 'login.php';
                        }, 400);
                    }, 2000);
                } else {
                    messageDiv.addClass('error').text(response.message);
                    $('body').append(messageDiv);
                    
                    // Add show class to trigger animation
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
            },
            error: function () {
                // Remove any existing message first
                $('.message').remove();

                // Create and show error message
                const messageDiv = $('<div class="message error"></div>')
                    .text('An error occurred. Please try again.');
                
                $('body').append(messageDiv);
                
                // Add show class to trigger animation
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
    });
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
//showMessage();
// Usage examples:
// showMessage('Operation successful!', 'success');
// showMessage('An error occurred.', 'error');


    </script>
</body>
</html>
