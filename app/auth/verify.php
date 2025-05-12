<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Email - Portfolio Generator</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="stars"></div>
    
    <div class="container">
        <div class="auth-form">
            <h1>Verify Your Email</h1>
            <p class="subtitle">Enter the verification code sent to your email</p>
            
            <form id="verifyForm">
                <div class="form-group">
                    <label for="code">Verification Code</label>
                    <input type="text" id="code" name="code" required maxlength="6" pattern="[0-9]{6}" placeholder="Enter 6-digit code">
                </div>
                
                <button type="submit" class="btn">Verify Email</button>
            </form>
            
            <p class="login-link">
                Didn't receive the code? <a href="#" id="resendCode">Resend</a>
            </p>
        </div>
    </div>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const verifyForm = document.getElementById('verifyForm');
        const resendCode = document.getElementById('resendCode');
        
        verifyForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const code = document.getElementById('code').value;
            
            try {
                const response = await fetch('auth/verify.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ code: code })
                });
                
                const result = await response.json();
                
                if (result.success) {
                    alert(result.message);
                    window.location.href = result.redirect || 'dashboard.php';
                } else {
                    alert(result.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred during verification');
            }
        });
        
        resendCode.addEventListener('click', async function(e) {
            e.preventDefault();
            
            try {
                const response = await fetch('auth/resend_code.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    }
                });
                
                const result = await response.json();
                alert(result.message);
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred while resending the code');
            }
        });
    });
    </script>
</body>
</html> 