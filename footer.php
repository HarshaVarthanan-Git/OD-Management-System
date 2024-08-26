<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/responsive.css">
    <style>
        body {
            margin: 0;
            transition: background-color 0.3s, color 0.3s;
        }

        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            background-color: #003d7a; /* Default light theme color */
            color: white;
            text-align: center;
            padding: 10px 0;
            font-size: 0.8em; /* Reduced text size */
            font-family: Arial, sans-serif;
            z-index: 1000;
            display: flex;
            align-items: center;
            box-shadow: 0 -2px 5px rgba(0, 0, 0, 0.1); /* Optional shadow for better visibility */
            position: fixed;
            bottom: 0;
            left: 0;
        }

        .footer-content {
            width: 100%;
            display: flex;
            justify-content: center; /* Center the content horizontally */
            align-items: center;
            position: relative;
        }

        .footer-text {
            font-size: 1em; /* Slightly larger font size for the text */
        }

        .theme-toggle-button {
            background: none;
            border: none;
            color: white;
            font-size: 1.1em; /* Slightly larger font size for the button */
            cursor: pointer;
            position: absolute;
            right: 10px; /* Position to the right */
            display: flex;
            align-items: center;
        }

        .theme-toggle-button span {
            margin-left: 5px;
            font-size: 0.8em; /* Smaller text size for the span */
        }

        .theme-toggle-button:hover {
            opacity: 0.8;
        }

        .dark-mode .footer {
            background-color: #333; /* Dark theme color */
        }

        .dark-mode .theme-toggle-button {
            color: #ffeb3b; /* Light yellow for dark theme */
        }

        .dark-mode {
            background-color: #121212; /* Dark background color */
            color: #e0e0e0; /* Light text color */
        }

        .footer-image {
            position: absolute;
            left: 10px; /* Position image to the left */
            width: 40px; /* Adjust size as needed */
            height: auto; /* Maintain aspect ratio */
        }
    </style>
</head>
<body>
    <div class="footer">
        <div class="footer-content">
            <div class="footer-text">Designed and Developed By Harsha Varthanan and Madhava.</div>
            <button class="theme-toggle-button" onclick="toggleTheme()">
                <span id="themeText">Light Mode</span>
            </button>
        </div>
    </div>

    <script>
        function toggleTheme() {
            document.body.classList.toggle('dark-mode');
            const themeText = document.getElementById('themeText');
            if (document.body.classList.contains('dark-mode')) {
                themeText.textContent = 'Light Mode';
            } else {
                themeText.textContent = 'Dark Mode';
            }
        }
    </script>
</body>
</html>
