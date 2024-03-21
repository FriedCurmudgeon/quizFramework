 <?php // Include language arrays
include './config/lang.php'; 

// Determine the language based on the session or set Norwegian as the default language
$language = isset($_SESSION['language']) && ($_SESSION['language'] == 'en') ? $english : $norwegian;?>
 
 <!-- Cookie information bar -->
 <div id="cookie-bar" class="alert alert-info cookie-bar" role="alert">
        <?php echo $language['langCookieText']; ?>
        <button type="button" class="btn btn-sm btn-primary accept-cookie"><?php echo $language['langCookieButton']; ?></button>
    </div>

<!-- JavaScript to handle cookie acceptance -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var cookieBar = document.getElementById('cookie-bar');
        var acceptButton = document.querySelector('.accept-cookie');

        // Check if the cookie has been accepted
        var isCookieAccepted = localStorage.getItem('cookieAccepted');

        if (!isCookieAccepted) {
            // If cookie has not been accepted, show the cookie bar
            cookieBar.style.display = 'block';
        } else {
            // If cookie has been accepted, hide the cookie bar
            cookieBar.style.display = 'none';
        }

        // Handle click event on the accept button
        acceptButton.addEventListener('click', function() {
            // Set a flag in local storage to remember cookie acceptance
            localStorage.setItem('cookieAccepted', true);
            // Hide the cookie bar
            cookieBar.style.display = 'none';
        });
    });
</script>
